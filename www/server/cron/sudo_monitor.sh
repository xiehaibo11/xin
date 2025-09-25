#!/bin/bash
# Sudo安全监控脚本
# 监控sudo配置变化和使用情况

LOG_FILE="/www/wwwroot/www/server/cron/sudo_monitor.log"
ALERT_FILE="/www/wwwroot/www/server/cron/sudo_alert.log"
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

# 检查NOPASSWD配置
check_nopasswd() {
    log_message "开始检查NOPASSWD配置..."
    
    # 检查/etc/sudoers
    if grep -q "NOPASSWD" /etc/sudoers 2>/dev/null; then
        send_alert "在/etc/sudoers中发现NOPASSWD配置"
    fi
    
    # 检查/etc/sudoers.d/目录中的活跃NOPASSWD配置
    local nopasswd_files=$(grep -r "^[^#]*NOPASSWD" /etc/sudoers.d/ 2>/dev/null | grep -v ".backup:")
    if [ -n "$nopasswd_files" ]; then
        send_alert "在sudoers.d目录中发现活跃的NOPASSWD配置: $nopasswd_files"
    else
        log_message "未发现活跃的NOPASSWD配置"
    fi
    
    log_message "NOPASSWD配置检查完成"
}

# 检查sudo配置文件权限
check_sudo_permissions() {
    log_message "开始检查sudo配置文件权限..."
    
    # 检查/etc/sudoers权限
    local sudoers_perms=$(ls -la /etc/sudoers | awk '{print $1}')
    if [ "$sudoers_perms" != "-r--r-----" ]; then
        send_alert "sudoers文件权限异常: $sudoers_perms (应为-r--r-----)"
    fi
    
    # 检查sudoers.d目录权限
    local sudoers_d_perms=$(ls -ld /etc/sudoers.d | awk '{print $1}')
    if [ "$sudoers_d_perms" != "drwxr-x---" ]; then
        send_alert "sudoers.d目录权限异常: $sudoers_d_perms (应为drwxr-x---)"
    fi
    
    log_message "sudo配置文件权限检查完成"
}

# 检查sudo配置语法
check_sudo_syntax() {
    log_message "开始检查sudo配置语法..."
    
    local syntax_check=$(visudo -c 2>&1)
    if [[ $syntax_check != *"parsed OK"* ]]; then
        send_alert "sudo配置语法错误: $syntax_check"
    else
        log_message "sudo配置语法正常"
    fi
}

# 分析sudo使用日志
analyze_sudo_usage() {
    log_message "开始分析sudo使用情况..."
    
    # 检查最近的sudo使用记录
    local recent_sudo=$(grep "sudo:" /var/log/auth.log 2>/dev/null | tail -5)
    if [ -n "$recent_sudo" ]; then
        log_message "最近的sudo使用记录:"
        echo "$recent_sudo" | while read line; do
            log_message "  $line"
        done
    fi
    
    # 检查失败的sudo尝试
    local failed_sudo=$(grep "sudo.*FAILED" /var/log/auth.log 2>/dev/null | tail -3)
    if [ -n "$failed_sudo" ]; then
        send_alert "发现失败的sudo尝试: $failed_sudo"
    fi
}

# 检查可疑的sudo配置变化
check_sudo_changes() {
    log_message "开始检查sudo配置变化..."
    
    # 检查sudoers文件的修改时间
    local sudoers_mtime=$(stat -c %Y /etc/sudoers)
    local current_time=$(date +%s)
    local time_diff=$((current_time - sudoers_mtime))
    
    # 如果在过去24小时内修改过
    if [ $time_diff -lt 86400 ]; then
        send_alert "sudoers文件在过去24小时内被修改过"
    fi
    
    # 检查sudoers.d目录中的文件
    find /etc/sudoers.d -name "*.backup" -o -name "*~" | while read backup_file; do
        log_message "发现备份文件: $backup_file"
    done
}

# 主监控逻辑
main() {
    log_message "开始sudo安全监控检查"
    
    check_nopasswd
    check_sudo_permissions
    check_sudo_syntax
    analyze_sudo_usage
    check_sudo_changes
    
    log_message "sudo安全监控检查完成"
    
    # 保持日志文件大小
    tail -n 1000 "$LOG_FILE" > "$LOG_FILE.tmp" && mv "$LOG_FILE.tmp" "$LOG_FILE"
}

# 执行主函数
main
