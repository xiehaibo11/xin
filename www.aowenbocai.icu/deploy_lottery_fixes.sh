#!/bin/bash

# 彩票系统修复部署脚本
# 用于部署开奖号码更新优化和WebSocket实时推送系统

echo "开始部署彩票系统修复..."

# 检查Node.js环境
if ! command -v node &> /dev/null; then
    echo "错误: Node.js 未安装"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "错误: npm 未安装"
    exit 1
fi

# 检查MySQL连接
if ! mysql -u root -p -e "SELECT 1" &> /dev/null; then
    echo "警告: 无法连接MySQL数据库，请检查数据库配置"
fi

# 1. 安装WebSocket服务器依赖
echo "安装WebSocket服务器依赖..."
cd node_server/websocket_server
npm install
if [ $? -ne 0 ]; then
    echo "错误: WebSocket服务器依赖安装失败"
    exit 1
fi
cd ../..

# 2. 执行数据库优化脚本
echo "执行数据库优化..."
if [ -f "database_optimization.sql" ]; then
    read -p "是否执行数据库优化脚本? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        mysql -u root -p < database_optimization.sql
        if [ $? -eq 0 ]; then
            echo "数据库优化完成"
        else
            echo "警告: 数据库优化脚本执行失败，请手动执行"
        fi
    fi
fi

# 3. 创建systemd服务文件
echo "创建WebSocket服务..."
sudo tee /etc/systemd/system/lottery-websocket.service > /dev/null <<EOF
[Unit]
Description=Lottery WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$(pwd)/node_server/websocket_server
ExecStart=/usr/bin/node server.js
Restart=always
RestartSec=10
Environment=NODE_ENV=production
Environment=WS_PORT=8080

# 日志
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=lottery-websocket

[Install]
WantedBy=multi-user.target
EOF

# 4. 创建零点任务服务
sudo tee /etc/systemd/system/lottery-zero-task.service > /dev/null <<EOF
[Unit]
Description=Lottery Zero Task Service
After=network.target mysql.service redis.service

[Service]
Type=simple
User=www-data
WorkingDirectory=$(pwd)/node_server/zero_task
ExecStart=/usr/bin/node index.js
Restart=always
RestartSec=10
Environment=NODE_ENV=production

# 日志
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=lottery-zero-task

[Install]
WantedBy=multi-user.target
EOF

# 5. 重新加载systemd并启动服务
echo "启动服务..."
sudo systemctl daemon-reload

# 启动WebSocket服务
sudo systemctl enable lottery-websocket
sudo systemctl start lottery-websocket

# 检查WebSocket服务状态
if sudo systemctl is-active --quiet lottery-websocket; then
    echo "✓ WebSocket服务启动成功"
else
    echo "✗ WebSocket服务启动失败"
    sudo systemctl status lottery-websocket
fi

# 重启零点任务服务（如果存在）
if sudo systemctl is-enabled lottery-zero-task &> /dev/null; then
    sudo systemctl restart lottery-zero-task
    echo "✓ 零点任务服务已重启"
else
    sudo systemctl enable lottery-zero-task
    sudo systemctl start lottery-zero-task
    echo "✓ 零点任务服务已启动"
fi

# 6. 配置Nginx反向代理（如果需要）
read -p "是否配置Nginx WebSocket代理? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    sudo tee /etc/nginx/sites-available/lottery-websocket > /dev/null <<EOF
upstream lottery_websocket {
    server 127.0.0.1:8080;
}

server {
    listen 80;
    server_name ws.yourdomain.com;  # 请修改为你的域名
    
    location /lottery-ws {
        proxy_pass http://lottery_websocket;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        
        # WebSocket特定配置
        proxy_read_timeout 86400;
        proxy_send_timeout 86400;
    }
}
EOF

    sudo ln -sf /etc/nginx/sites-available/lottery-websocket /etc/nginx/sites-enabled/
    sudo nginx -t && sudo systemctl reload nginx
    echo "✓ Nginx WebSocket代理配置完成"
fi

# 7. 创建日志轮转配置
sudo tee /etc/logrotate.d/lottery-services > /dev/null <<EOF
/var/log/lottery-websocket.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}

/var/log/lottery-zero-task.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF

# 8. 创建监控脚本
tee monitor_lottery_services.sh > /dev/null <<'EOF'
#!/bin/bash

# 彩票服务监控脚本

check_service() {
    local service_name=$1
    if sudo systemctl is-active --quiet $service_name; then
        echo "✓ $service_name 运行正常"
        return 0
    else
        echo "✗ $service_name 服务异常"
        sudo systemctl status $service_name --no-pager -l
        return 1
    fi
}

check_websocket() {
    local response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/health 2>/dev/null)
    if [ "$response" = "200" ]; then
        echo "✓ WebSocket服务健康检查通过"
        return 0
    else
        echo "✗ WebSocket服务健康检查失败"
        return 1
    fi
}

echo "=== 彩票服务状态检查 ==="
echo "检查时间: $(date)"
echo

check_service "lottery-websocket"
check_service "lottery-zero-task"
check_websocket

echo
echo "=== 服务资源使用情况 ==="
sudo systemctl show lottery-websocket --property=CPUUsageNSec,MemoryCurrent
sudo systemctl show lottery-zero-task --property=CPUUsageNSec,MemoryCurrent

echo
echo "=== 最近错误日志 ==="
sudo journalctl -u lottery-websocket --since "5 minutes ago" --no-pager -q
sudo journalctl -u lottery-zero-task --since "5 minutes ago" --no-pager -q
EOF

chmod +x monitor_lottery_services.sh

# 9. 创建定时监控任务
(crontab -l 2>/dev/null; echo "*/5 * * * * $(pwd)/monitor_lottery_services.sh >> /var/log/lottery-monitor.log 2>&1") | crontab -

echo
echo "=== 部署完成 ==="
echo "✓ WebSocket服务器已安装并启动"
echo "✓ 数据库优化脚本已准备就绪"
echo "✓ 系统服务已配置"
echo "✓ 监控脚本已创建"
echo
echo "服务状态检查:"
sudo systemctl status lottery-websocket --no-pager -l
echo
echo "WebSocket服务地址: ws://localhost:8080/lottery-ws"
echo "监控脚本: ./monitor_lottery_services.sh"
echo
echo "注意事项:"
echo "1. 请确保Redis服务正在运行"
echo "2. 请检查防火墙设置，确保8080端口可访问"
echo "3. 如需外网访问，请配置相应的域名和SSL证书"
echo "4. 定期检查服务日志: sudo journalctl -u lottery-websocket -f"
echo
echo "如有问题，请检查日志文件或运行监控脚本进行诊断。"
EOF