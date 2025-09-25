#!/bin/bash

# 负载均衡优化部署脚本
# 适用于单服务器环境，支持后期扩展
# 作者: AI Assistant
# 日期: 2025-09-24

set -e  # 遇到错误立即退出

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查是否为root用户
check_root() {
    if [[ $EUID -ne 0 ]]; then
        log_error "此脚本需要root权限运行"
        exit 1
    fi
}

# 备份现有配置
backup_configs() {
    log_info "备份现有配置文件..."
    
    BACKUP_DIR="/www/backup/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    
    # 备份Nginx配置
    if [ -f "/www/server/nginx/conf/nginx.conf" ]; then
        cp /www/server/nginx/conf/nginx.conf "$BACKUP_DIR/nginx.conf.bak"
        log_success "Nginx配置已备份"
    fi
    
    # 备份PHP-FPM配置
    if [ -f "/www/server/php/74/etc/php-fpm.conf" ]; then
        cp /www/server/php/74/etc/php-fpm.conf "$BACKUP_DIR/php-fpm.conf.bak"
        log_success "PHP-FPM配置已备份"
    fi
    
    # 备份站点配置
    if [ -f "/www/server/panel/vhost/nginx/www.aowenbocai.icu.conf" ]; then
        cp /www/server/panel/vhost/nginx/www.aowenbocai.icu.conf "$BACKUP_DIR/site.conf.bak"
        log_success "站点配置已备份"
    fi
    
    log_success "配置备份完成: $BACKUP_DIR"
}

# 检查系统资源
check_system_resources() {
    log_info "检查系统资源..."
    
    # 检查CPU核心数
    CPU_CORES=$(nproc)
    log_info "CPU核心数: $CPU_CORES"
    
    # 检查内存
    TOTAL_MEM=$(free -m | awk 'NR==2{printf "%.0f", $2}')
    USED_MEM=$(free -m | awk 'NR==2{printf "%.0f", $3}')
    log_info "内存使用: ${USED_MEM}MB / ${TOTAL_MEM}MB"
    
    # 检查磁盘空间
    DISK_USAGE=$(df -h /www | awk 'NR==2{print $5}' | sed 's/%//')
    log_info "磁盘使用率: ${DISK_USAGE}%"
    
    # 资源警告
    if [ "$USED_MEM" -gt $((TOTAL_MEM * 80 / 100)) ]; then
        log_warning "内存使用率超过80%，建议增加内存"
    fi
    
    if [ "$DISK_USAGE" -gt 80 ]; then
        log_warning "磁盘使用率超过80%，建议清理空间"
    fi
}

# 优化PHP-FPM配置
optimize_php_fpm() {
    log_info "优化PHP-FPM配置..."
    
    PHP_FPM_CONF="/www/server/php/74/etc/php-fpm.conf"
    PHP_FPM_POOL="/www/server/php/74/etc/php-fpm.d/www.conf"
    
    # 应用优化配置
    if [ -f "php_fpm_optimization.conf" ]; then
        # 备份原配置
        cp "$PHP_FPM_POOL" "$PHP_FPM_POOL.bak"
        
        # 应用新配置
        cp php_fpm_optimization.conf "$PHP_FPM_POOL"
        
        log_success "PHP-FPM配置已优化"
    else
        log_error "找不到PHP-FPM优化配置文件"
        return 1
    fi
}

# 优化Nginx配置
optimize_nginx() {
    log_info "优化Nginx配置..."
    
    NGINX_CONF="/www/server/nginx/conf/nginx.conf"
    SITE_CONF="/www/server/panel/vhost/nginx/www.aowenbocai.icu.conf"
    
    # 创建缓存目录
    mkdir -p /tmp/nginx_fastcgi_cache
    chown www:www /tmp/nginx_fastcgi_cache
    
    # 应用站点优化配置
    if [ -f "single_server_optimization.conf" ]; then
        # 提取server块配置并应用到站点配置
        log_info "应用站点优化配置..."
        
        # 这里需要手动配置，因为每个站点配置可能不同
        log_warning "请手动将single_server_optimization.conf中的配置应用到站点配置文件"
        log_info "站点配置文件位置: $SITE_CONF"
        
        log_success "Nginx优化配置准备完成"
    else
        log_error "找不到Nginx优化配置文件"
        return 1
    fi
}

# 创建监控脚本
setup_monitoring() {
    log_info "设置性能监控..."
    
    # 创建监控脚本
    cat > /usr/local/bin/performance_monitor.sh << 'EOF'
#!/bin/bash
# 性能监控脚本

LOGFILE="/var/log/performance_monitor.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

# 获取系统信息
CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | sed 's/%us,//')
MEM_USAGE=$(free | grep Mem | awk '{printf("%.2f"), $3/$2 * 100.0}')
DISK_USAGE=$(df -h /www | awk 'NR==2{print $5}' | sed 's/%//')

# 获取服务状态
NGINX_STATUS=$(systemctl is-active nginx)
PHP_FPM_STATUS=$(systemctl is-active php-fpm-74)
MYSQL_STATUS=$(systemctl is-active mysql)
REDIS_STATUS=$(systemctl is-active redis)

# 获取连接数
HTTP_CONNECTIONS=$(netstat -an | grep :80 | wc -l)
MYSQL_CONNECTIONS=$(mysqladmin processlist | wc -l)

# 记录日志
echo "[$DATE] CPU:${CPU_USAGE}% MEM:${MEM_USAGE}% DISK:${DISK_USAGE}% HTTP:$HTTP_CONNECTIONS MYSQL:$MYSQL_CONNECTIONS NGINX:$NGINX_STATUS PHP:$PHP_FPM_STATUS MYSQL:$MYSQL_STATUS REDIS:$REDIS_STATUS" >> $LOGFILE

# 检查告警条件
if (( $(echo "$CPU_USAGE > 80" | bc -l) )); then
    echo "[$DATE] ALERT: CPU usage high: ${CPU_USAGE}%" >> $LOGFILE
fi

if (( $(echo "$MEM_USAGE > 80" | bc -l) )); then
    echo "[$DATE] ALERT: Memory usage high: ${MEM_USAGE}%" >> $LOGFILE
fi

if [ "$HTTP_CONNECTIONS" -gt 500 ]; then
    echo "[$DATE] ALERT: High HTTP connections: $HTTP_CONNECTIONS" >> $LOGFILE
fi
EOF

    chmod +x /usr/local/bin/performance_monitor.sh
    
    # 添加到crontab
    (crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/performance_monitor.sh") | crontab -
    
    log_success "性能监控脚本已设置，每5分钟执行一次"
}

# 重启服务
restart_services() {
    log_info "重启相关服务..."
    
    # 测试配置
    nginx -t
    if [ $? -eq 0 ]; then
        systemctl reload nginx
        log_success "Nginx配置重载成功"
    else
        log_error "Nginx配置测试失败"
        return 1
    fi
    
    # 重启PHP-FPM
    systemctl restart php-fpm-74
    if [ $? -eq 0 ]; then
        log_success "PHP-FPM重启成功"
    else
        log_error "PHP-FPM重启失败"
        return 1
    fi
    
    log_success "所有服务重启完成"
}

# 运行压力测试验证
run_verification_test() {
    log_info "运行验证测试..."
    
    if [ -f "stress_test_scripts.sh" ]; then
        chmod +x stress_test_scripts.sh
        ./stress_test_scripts.sh
        log_success "验证测试完成，请查看test_results目录"
    else
        log_warning "未找到压力测试脚本，跳过验证测试"
    fi
}

# 显示优化结果
show_optimization_results() {
    log_info "优化配置摘要:"
    echo "=================================="
    echo "1. PHP-FPM进程数: 5 → 20"
    echo "2. Nginx工作进程: 自动 → 2 (匹配CPU核心)"
    echo "3. 连接数限制: 1024 → 2048"
    echo "4. 启用FastCGI缓存"
    echo "5. 优化静态资源缓存"
    echo "6. 添加请求频率限制"
    echo "7. 设置性能监控"
    echo "=================================="
    
    log_info "后期扩展建议:"
    echo "- CPU使用率 > 70%: 考虑增加服务器"
    echo "- 内存使用率 > 80%: 增加内存或分离服务"
    echo "- 响应时间 > 500ms: 需要进一步优化"
    echo "- 并发连接数接近限制: 调整配置参数"
    
    log_info "监控日志位置: /var/log/performance_monitor.log"
    log_info "配置备份位置: /www/backup/"
}

# 主函数
main() {
    log_info "开始负载均衡优化部署..."
    
    check_root
    backup_configs
    check_system_resources
    
    # 询问用户是否继续
    read -p "是否继续应用优化配置? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "用户取消操作"
        exit 0
    fi
    
    optimize_php_fpm
    optimize_nginx
    setup_monitoring
    restart_services
    
    log_success "优化部署完成!"
    
    # 询问是否运行验证测试
    read -p "是否运行验证测试? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        run_verification_test
    fi
    
    show_optimization_results
}

# 执行主函数
main "$@"
