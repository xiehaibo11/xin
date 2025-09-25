#!/bin/bash
# SUID/SGID权限监控脚本
# 监控系统中的SUID/SGID文件变化

LOG_FILE="/www/wwwroot/www/server/cron/suid_sgid_monitor.log"
ALERT_FILE="/www/wwwroot/www/server/cron/suid_sgid_alert.log"
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

# 检查指定文件的SUID/SGID权限
check_file_permissions() {
    local file="$1"
    local expected_perms="$2"
    
    if [ -f "$file" ]; then
        current_perms=$(ls -la "$file" | awk '{print $1}')
        if [ "$current_perms" != "$expected_perms" ]; then
            send_alert "权限异常: $file 当前权限: $current_perms, 预期权限: $expected_perms"
            return 1
        fi
    else
        log_message "文件不存在: $file"
        return 1
    fi
    return 0
}

# 扫描新的SUID/SGID文件
scan_new_suid_sgid() {
    log_message "开始扫描新的SUID/SGID文件..."
    
    # 查找所有SUID文件
    find /usr/bin /bin /usr/sbin /sbin -perm -4000 -type f 2>/dev/null | while read file; do
        case "$file" in
            "/usr/bin/gpasswd"|"/usr/bin/chfn"|"/usr/bin/chsh"|"/usr/bin/newgrp"|"/usr/bin/mount"|"/usr/bin/umount")
                send_alert "检测到已移除SUID权限的文件重新获得SUID权限: $file"
                ;;
            *)
                log_message "发现SUID文件: $file"
                ;;
        esac
    done
    
    # 查找所有SGID文件
    find /usr/bin /bin /usr/sbin /sbin -perm -2000 -type f 2>/dev/null | while read file; do
        case "$file" in
            "/usr/bin/chage")
                send_alert "检测到已移除SGID权限的文件重新获得SGID权限: $file"
                ;;
            *)
                log_message "发现SGID文件: $file"
                ;;
        esac
    done
}

# 主监控逻辑
main() {
    log_message "开始SUID/SGID权限监控检查"
    
    # 检查已移除SUID权限的文件
    check_file_permissions "/usr/bin/gpasswd" "-rwxr-xr-x"
    check_file_permissions "/usr/bin/chfn" "-rwxr-xr-x"
    check_file_permissions "/usr/bin/chsh" "-rwxr-xr-x"
    check_file_permissions "/usr/bin/newgrp" "-rwxr-xr-x"
    check_file_permissions "/usr/bin/mount" "-rwxr-xr-x"
    check_file_permissions "/usr/bin/umount" "-rwxr-xr-x"
    
    # 检查已移除SGID权限的文件
    check_file_permissions "/usr/bin/chage" "-rwxr-xr-x"
    
    # 扫描新的SUID/SGID文件
    scan_new_suid_sgid
    
    log_message "SUID/SGID权限监控检查完成"
    
    # 保持日志文件大小
    tail -n 1000 "$LOG_FILE" > "$LOG_FILE.tmp" && mv "$LOG_FILE.tmp" "$LOG_FILE"
}

# 执行主函数
main
