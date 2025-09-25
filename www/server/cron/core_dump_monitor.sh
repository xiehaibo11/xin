#!/bin/bash
# 核心转储安全监控脚本
# 监控核心转储配置和文件

LOG_FILE="/www/wwwroot/www/server/cron/core_dump_monitor.log"
ALERT_FILE="/www/wwwroot/www/server/cron/core_dump_alert.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

# 记录日志函数
log_message() {
    echo "[$DATE] $1" >> "$LOG_FILE"
}

# 发送警报函数
send_alert() {
    echo "[$DATE] ALERT: $1" >> "$ALERT_FILE"
    echo "[$DATE] ALERT: $1" >> "$LOG_FILE"
}

# 检查fs.suid_dumpable配置
check_suid_dumpable() {
    log_message "开始检查fs.suid_dumpable配置..."
    
    local current_value=$(sysctl -n fs.suid_dumpable)
    if [ "$current_value" != "0" ]; then
        send_alert "fs.suid_dumpable配置异常: 当前值=$current_value, 应为0"
        return 1
    else
        log_message "fs.suid_dumpable配置正常: $current_value"
    fi
    
    # 检查/proc/sys/fs/suid_dumpable
    local proc_value=$(cat /proc/sys/fs/suid_dumpable)
    if [ "$proc_value" != "0" ]; then
        send_alert "/proc/sys/fs/suid_dumpable值异常: 当前值=$proc_value, 应为0"
        return 1
    fi
    
    return 0
}

# 检查sysctl.conf配置
check_sysctl_config() {
    log_message "开始检查sysctl.conf配置..."
    
    if ! grep -q "fs.suid_dumpable.*=.*0" /etc/sysctl.conf; then
        send_alert "sysctl.conf中未找到fs.suid_dumpable=0配置"
        return 1
    else
        log_message "sysctl.conf配置正常"
    fi
    
    return 0
}

# 扫描核心转储文件
scan_core_dumps() {
    log_message "开始扫描核心转储文件..."
    
    # 检查常见的核心转储位置
    local core_locations=(
        "/var/crash"
        "/tmp"
        "/var/tmp"
        "/home"
        "/root"
    )
    
    local found_cores=0
    
    for location in "${core_locations[@]}"; do
        if [ -d "$location" ]; then
            # 查找真正的核心转储文件（排除目录和已知的非核心转储文件）
            local core_files=$(find "$location" -maxdepth 2 -type f \( -name "core" -o -name "core.[0-9]*" -o -name "*.core" -o -name "*.dump" \) 2>/dev/null | grep -v -E "(backup|config|corefx|node_modules)")
            if [ -n "$core_files" ]; then
                send_alert "在 $location 发现核心转储文件: $core_files"
                found_cores=$((found_cores + 1))
            fi
        fi
    done
    
    if [ $found_cores -eq 0 ]; then
        log_message "未发现核心转储文件"
    else
        send_alert "总共发现 $found_cores 个位置存在核心转储文件"
    fi
}

# 检查ulimit设置
check_ulimit_settings() {
    log_message "开始检查ulimit设置..."
    
    local core_limit=$(ulimit -c)
    log_message "当前ulimit -c设置: $core_limit"
    
    # 检查系统默认的ulimit设置
    if [ -f /etc/security/limits.conf ]; then
        local limits_core=$(grep -E "^\s*\*\s+.*\s+core\s+" /etc/security/limits.conf 2>/dev/null)
        if [ -n "$limits_core" ]; then
            log_message "limits.conf中的core设置: $limits_core"
        fi
    fi
}

# 检查apport配置
check_apport_config() {
    log_message "开始检查apport配置..."
    
    # 检查apport是否启用
    if systemctl is-active apport >/dev/null 2>&1; then
        log_message "apport服务正在运行"
        
        # 检查apport配置
        if [ -f /etc/default/apport ]; then
            local apport_enabled=$(grep "enabled=" /etc/default/apport | cut -d'=' -f2)
            log_message "apport启用状态: $apport_enabled"
        fi
    else
        log_message "apport服务未运行"
    fi
}

# 检查核心转储模式
check_core_pattern() {
    log_message "开始检查核心转储模式..."
    
    local core_pattern=$(sysctl -n kernel.core_pattern)
    log_message "当前核心转储模式: $core_pattern"
    
    # 如果使用管道模式，检查处理程序
    if [[ "$core_pattern" == \|* ]]; then
        local handler=$(echo "$core_pattern" | cut -d' ' -f1 | sed 's/^|//')
        if [ -f "$handler" ]; then
            log_message "核心转储处理程序存在: $handler"
        else
            send_alert "核心转储处理程序不存在: $handler"
        fi
    fi
}

# 主监控逻辑
main() {
    log_message "开始核心转储安全监控检查"
    
    check_suid_dumpable
    check_sysctl_config
    scan_core_dumps
    check_ulimit_settings
    check_apport_config
    check_core_pattern
    
    log_message "核心转储安全监控检查完成"
    
    # 保持日志文件大小
    tail -n 1000 "$LOG_FILE" > "$LOG_FILE.tmp" && mv "$LOG_FILE.tmp" "$LOG_FILE"
}

# 执行主函数
main
