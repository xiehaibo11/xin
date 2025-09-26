/**
 * WebSocket客户端用于实时彩票开奖推送
 */
class LotteryWebSocket {
    constructor(url) {
        this.url = url;
        this.ws = null;
        this.reconnectInterval = null;
        this.heartbeatInterval = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000;
        this.isConnecting = false;
        this.isManualClose = false;
        
        // 事件监听器
        this.listeners = {
            open: [],
            close: [],
            error: [],
            message: [],
            lottery_update: [],
            connection_status: []
        };
        
        // 订阅的彩种列表
        this.subscribedLotteries = new Set();
        
        this.connect();
    }

    // 连接WebSocket
    connect() {
        if (this.isConnecting || (this.ws && this.ws.readyState === WebSocket.OPEN)) {
            return;
        }

        this.isConnecting = true;
        console.log('正在连接WebSocket服务器...');

        try {
            this.ws = new WebSocket(this.url);
            this.setupEventListeners();
        } catch (error) {
            console.error('WebSocket连接失败:', error);
            this.isConnecting = false;
            this.scheduleReconnect();
        }
    }

    // 设置事件监听器
    setupEventListeners() {
        this.ws.onopen = (event) => {
            console.log('WebSocket连接成功');
            this.isConnecting = false;
            this.reconnectAttempts = 0;
            
            // 重新订阅之前的彩种
            this.subscribedLotteries.forEach(lotteryName => {
                this.subscribe(lotteryName);
            });
            
            // 开始心跳
            this.startHeartbeat();
            
            this.emit('open', event);
            this.emit('connection_status', { connected: true });
        };

        this.ws.onclose = (event) => {
            console.log('WebSocket连接关闭:', event.code, event.reason);
            this.isConnecting = false;
            this.stopHeartbeat();
            
            this.emit('close', event);
            this.emit('connection_status', { connected: false });
            
            // 如果不是手动关闭，则尝试重连
            if (!this.isManualClose) {
                this.scheduleReconnect();
            }
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket错误:', error);
            this.emit('error', error);
        };

        this.ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            } catch (error) {
                console.error('解析WebSocket消息失败:', error);
            }
        };
    }

    // 处理接收到的消息
    handleMessage(data) {
        console.log('收到WebSocket消息:', data);
        
        switch (data.type) {
            case 'connection':
                console.log('连接确认:', data);
                break;
            case 'lottery_update':
                this.emit('lottery_update', data);
                break;
            case 'subscribed':
                console.log(`订阅成功: ${data.lotteryName}`);
                break;
            case 'unsubscribed':
                console.log(`取消订阅成功: ${data.lotteryName}`);
                break;
            case 'heartbeat':
                // 心跳响应
                break;
            case 'server_shutdown':
                console.warn('服务器即将关闭:', data.message);
                break;
            default:
                this.emit('message', data);
        }
    }

    // 发送消息
    send(data) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
            return true;
        } else {
            console.warn('WebSocket未连接，无法发送消息');
            return false;
        }
    }

    // 订阅彩种
    subscribe(lotteryName) {
        this.subscribedLotteries.add(lotteryName);
        return this.send({
            type: 'subscribe',
            lotteryName: lotteryName
        });
    }

    // 取消订阅彩种
    unsubscribe(lotteryName) {
        this.subscribedLotteries.delete(lotteryName);
        return this.send({
            type: 'unsubscribe',
            lotteryName: lotteryName
        });
    }

    // 开始心跳
    startHeartbeat() {
        this.stopHeartbeat();
        this.heartbeatInterval = setInterval(() => {
            this.send({
                type: 'heartbeat',
                timestamp: Date.now()
            });
        }, 30000);
    }

    // 停止心跳
    stopHeartbeat() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
            this.heartbeatInterval = null;
        }
    }

    // 安排重连
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('达到最大重连次数，停止重连');
            return;
        }

        if (this.reconnectInterval) {
            clearTimeout(this.reconnectInterval);
        }

        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts);
        console.log(`${delay}ms后尝试第${this.reconnectAttempts + 1}次重连`);
        
        this.reconnectInterval = setTimeout(() => {
            this.reconnectAttempts++;
            this.connect();
        }, delay);
    }

    // 手动关闭连接
    close() {
        this.isManualClose = true;
        this.stopHeartbeat();
        
        if (this.reconnectInterval) {
            clearTimeout(this.reconnectInterval);
            this.reconnectInterval = null;
        }
        
        if (this.ws) {
            this.ws.close();
        }
    }

    // 添加事件监听器
    on(event, callback) {
        if (this.listeners[event]) {
            this.listeners[event].push(callback);
        }
    }

    // 移除事件监听器
    off(event, callback) {
        if (this.listeners[event]) {
            const index = this.listeners[event].indexOf(callback);
            if (index > -1) {
                this.listeners[event].splice(index, 1);
            }
        }
    }

    // 触发事件
    emit(event, data) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`事件监听器错误 [${event}]:`, error);
                }
            });
        }
    }

    // 获取连接状态
    getReadyState() {
        return this.ws ? this.ws.readyState : WebSocket.CLOSED;
    }

    // 是否已连接
    isConnected() {
        return this.ws && this.ws.readyState === WebSocket.OPEN;
    }
}

// 全局WebSocket实例
let globalWebSocket = null;

// 创建或获取全局WebSocket实例
export function getWebSocket() {
    if (!globalWebSocket) {
        const wsUrl = process.env.VUE_APP_WS_URL || 'ws://localhost:8080/lottery-ws';
        globalWebSocket = new LotteryWebSocket(wsUrl);
    }
    return globalWebSocket;
}

// 关闭全局WebSocket连接
export function closeWebSocket() {
    if (globalWebSocket) {
        globalWebSocket.close();
        globalWebSocket = null;
    }
}

export default LotteryWebSocket;