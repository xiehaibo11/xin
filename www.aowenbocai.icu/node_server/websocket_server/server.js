const WebSocket = require('ws');
const redis = require('redis');
const http = require('http');
const url = require('url');

// 创建HTTP服务器
const server = http.createServer();

// 创建WebSocket服务器
const wss = new WebSocket.Server({ 
    server,
    path: '/lottery-ws',
    // 添加CORS支持
    verifyClient: (info) => {
        // 这里可以添加客户端验证逻辑
        return true;
    }
});

// Redis客户端
const redisClient = redis.createClient({
    host: 'localhost',
    port: 6379,
    retry_strategy: (options) => {
        if (options.error && options.error.code === 'ECONNREFUSED') {
            console.error('Redis服务器拒绝连接');
        }
        if (options.total_retry_time > 1000 * 60 * 60) {
            console.error('Redis重试时间超过1小时，停止重试');
            return new Error('重试时间超时');
        }
        if (options.attempt > 10) {
            return undefined;
        }
        return Math.min(options.attempt * 100, 3000);
    }
});

// 连接状态管理
const connections = new Map();
const lotterySubscribers = new Map(); // 按彩种分组的订阅者

// WebSocket连接处理
wss.on('connection', (ws, request) => {
    const clientId = generateClientId();
    const clientIP = request.socket.remoteAddress;
    
    console.log(`新客户端连接: ${clientId} from ${clientIP}`);
    
    // 存储连接信息
    connections.set(clientId, {
        ws,
        ip: clientIP,
        connectedAt: new Date(),
        subscribedLotteries: new Set(),
        lastHeartbeat: Date.now()
    });

    // 发送连接成功消息
    ws.send(JSON.stringify({
        type: 'connection',
        status: 'success',
        clientId,
        timestamp: Date.now()
    }));

    // 心跳检测
    const heartbeatInterval = setInterval(() => {
        if (ws.readyState === WebSocket.OPEN) {
            ws.ping();
        }
    }, 30000);

    ws.on('pong', () => {
        const client = connections.get(clientId);
        if (client) {
            client.lastHeartbeat = Date.now();
        }
    });

    // 处理客户端消息
    ws.on('message', (data) => {
        try {
            const message = JSON.parse(data.toString());
            handleClientMessage(clientId, message);
        } catch (error) {
            console.error('解析客户端消息失败:', error);
            ws.send(JSON.stringify({
                type: 'error',
                message: '消息格式错误'
            }));
        }
    });

    // 连接关闭处理
    ws.on('close', () => {
        console.log(`客户端断开连接: ${clientId}`);
        clearInterval(heartbeatInterval);
        
        const client = connections.get(clientId);
        if (client) {
            // 从彩种订阅中移除
            client.subscribedLotteries.forEach(lotteryName => {
                const subscribers = lotterySubscribers.get(lotteryName);
                if (subscribers) {
                    subscribers.delete(clientId);
                    if (subscribers.size === 0) {
                        lotterySubscribers.delete(lotteryName);
                    }
                }
            });
        }
        
        connections.delete(clientId);
    });

    // 错误处理
    ws.on('error', (error) => {
        console.error(`WebSocket错误 ${clientId}:`, error);
    });
});

// 处理客户端消息
function handleClientMessage(clientId, message) {
    const client = connections.get(clientId);
    if (!client) return;

    switch (message.type) {
        case 'subscribe':
            subscribeLottery(clientId, message.lotteryName);
            break;
        case 'unsubscribe':
            unsubscribeLottery(clientId, message.lotteryName);
            break;
        case 'heartbeat':
            client.lastHeartbeat = Date.now();
            client.ws.send(JSON.stringify({
                type: 'heartbeat',
                timestamp: Date.now()
            }));
            break;
        default:
            console.log(`未知消息类型: ${message.type}`);
    }
}

// 订阅彩种
function subscribeLottery(clientId, lotteryName) {
    const client = connections.get(clientId);
    if (!client) return;

    // 添加到客户端订阅列表
    client.subscribedLotteries.add(lotteryName);
    
    // 添加到彩种订阅者列表
    if (!lotterySubscribers.has(lotteryName)) {
        lotterySubscribers.set(lotteryName, new Set());
    }
    lotterySubscribers.get(lotteryName).add(clientId);

    console.log(`客户端 ${clientId} 订阅彩种: ${lotteryName}`);
    
    // 发送订阅确认
    client.ws.send(JSON.stringify({
        type: 'subscribed',
        lotteryName,
        timestamp: Date.now()
    }));
}

// 取消订阅彩种
function unsubscribeLottery(clientId, lotteryName) {
    const client = connections.get(clientId);
    if (!client) return;

    // 从客户端订阅列表移除
    client.subscribedLotteries.delete(lotteryName);
    
    // 从彩种订阅者列表移除
    const subscribers = lotterySubscribers.get(lotteryName);
    if (subscribers) {
        subscribers.delete(clientId);
        if (subscribers.size === 0) {
            lotterySubscribers.delete(lotteryName);
        }
    }

    console.log(`客户端 ${clientId} 取消订阅彩种: ${lotteryName}`);
    
    // 发送取消订阅确认
    client.ws.send(JSON.stringify({
        type: 'unsubscribed',
        lotteryName,
        timestamp: Date.now()
    }));
}

// 广播开奖消息
function broadcastLotteryUpdate(lotteryName, data) {
    const subscribers = lotterySubscribers.get(lotteryName);
    if (!subscribers || subscribers.size === 0) {
        return;
    }

    const message = JSON.stringify({
        type: 'lottery_update',
        lotteryName,
        data,
        timestamp: Date.now()
    });

    let successCount = 0;
    let failCount = 0;

    subscribers.forEach(clientId => {
        const client = connections.get(clientId);
        if (client && client.ws.readyState === WebSocket.OPEN) {
            try {
                client.ws.send(message);
                successCount++;
            } catch (error) {
                console.error(`发送消息失败 ${clientId}:`, error);
                failCount++;
                // 移除失效连接
                connections.delete(clientId);
                subscribers.delete(clientId);
            }
        } else {
            // 清理失效连接
            subscribers.delete(clientId);
            failCount++;
        }
    });

    console.log(`广播 ${lotteryName} 更新: 成功${successCount}, 失败${failCount}`);
}

// 生成客户端ID
function generateClientId() {
    return 'client_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// Redis消息订阅
redisClient.on('connect', () => {
    console.log('Redis连接成功');
});

redisClient.on('error', (err) => {
    console.error('Redis连接错误:', err);
});

// 订阅开奖消息
redisClient.subscribe('lottery_update', 'prize', (err, count) => {
    if (err) {
        console.error('Redis订阅失败:', err);
    } else {
        console.log(`已订阅 ${count} 个Redis频道`);
    }
});

redisClient.on('message', (channel, message) => {
    try {
        console.log(`收到Redis消息 [${channel}]:`, message);
        
        if (channel === 'lottery_update') {
            const data = JSON.parse(message);
            broadcastLotteryUpdate(data.lotteryName, data);
        } else if (channel === 'prize') {
            // 处理开奖消息
            const params = new URLSearchParams(message);
            const lotteryName = params.get('name');
            if (lotteryName) {
                broadcastLotteryUpdate(lotteryName, {
                    type: 'prize_update',
                    message: '开奖结果已更新'
                });
            }
        }
    } catch (error) {
        console.error('处理Redis消息失败:', error);
    }
});

// 定期清理失效连接
setInterval(() => {
    const now = Date.now();
    const timeout = 60000; // 60秒超时

    connections.forEach((client, clientId) => {
        if (now - client.lastHeartbeat > timeout) {
            console.log(`清理超时连接: ${clientId}`);
            if (client.ws.readyState === WebSocket.OPEN) {
                client.ws.terminate();
            }
            connections.delete(clientId);
            
            // 从订阅中移除
            client.subscribedLotteries.forEach(lotteryName => {
                const subscribers = lotterySubscribers.get(lotteryName);
                if (subscribers) {
                    subscribers.delete(clientId);
                }
            });
        }
    });
}, 30000);

// 启动服务器
const PORT = process.env.WS_PORT || 8080;
server.listen(PORT, () => {
    console.log(`WebSocket服务器启动在端口 ${PORT}`);
    console.log(`WebSocket路径: ws://localhost:${PORT}/lottery-ws`);
});

// 优雅关闭
process.on('SIGINT', () => {
    console.log('正在关闭WebSocket服务器...');
    
    // 通知所有客户端服务器即将关闭
    connections.forEach((client) => {
        if (client.ws.readyState === WebSocket.OPEN) {
            client.ws.send(JSON.stringify({
                type: 'server_shutdown',
                message: '服务器即将关闭'
            }));
            client.ws.close();
        }
    });
    
    redisClient.quit();
    server.close(() => {
        console.log('WebSocket服务器已关闭');
        process.exit(0);
    });
});

module.exports = { wss, broadcastLotteryUpdate };