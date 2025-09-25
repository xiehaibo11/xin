# PHP安全风险修复方案

## 风险概述
根据安全扫描报告，发现以下安全风险：
1. PHP未禁用危险函数：putenv
2. expose_php配置问题
3. 代码中使用了不安全的函数

## 修复步骤

### 1. 更新PHP配置文件

#### 1.1 禁用putenv函数
在宝塔面板中：
1. 进入【软件商店】→【运行环境】→【PHP 7.4】→【设置】
2. 点击【禁用函数】标签
3. 在禁用函数列表中添加：`putenv`
4. 保存并重启PHP

#### 1.2 确认expose_php设置
在php.ini中确认以下配置：
```ini
expose_php = Off
```

### 2. 代码安全修复

#### 2.1 修复Demo.php中的file_get_contents使用
**文件位置**: `app/index/controller/Demo.php`
**问题**: 直接使用file_get_contents访问外部URL，存在安全风险
**修复方案**: 使用安全的cURL方法替代

#### 2.2 修复User.php中的file_get_contents使用  
**文件位置**: `app/admin/controller/User.php`
**问题**: 使用file_get_contents获取IP地理位置信息
**修复方案**: 使用cURL并添加安全验证

#### 2.3 修复Node.js中的eval使用
**文件位置**: `node_server/zero_task/openCode.js`
**问题**: 使用eval解析JSON数据
**修复方案**: 使用JSON.parse替代eval

#### 2.4 修复支付模块中的eval使用
**文件位置**: `extend/alipay/pay/lotusphp_runtime/MVC/Action.php`
**问题**: 第三方支付库中使用eval
**修复方案**: 更新到安全版本或替换实现

### 3. 安全增强配置

#### 3.1 完整的PHP安全配置建议
```ini
# 基础安全配置
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off

# 会话安全
session.cookie_httponly = On
session.cookie_secure = On
session.use_strict_mode = On

# 文件上传安全
file_uploads = On
upload_max_filesize = 10M
max_file_uploads = 5

# 禁用危险函数
disable_functions = passthru,exec,system,chroot,chgrp,chown,shell_exec,popen,proc_open,pcntl_exec,ini_alter,ini_restore,dl,openlog,syslog,readlink,symlink,popepassthru,pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wifcontinued,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,imap_open,apache_setenv,putenv,eval
```

### 4. 验证修复效果

#### 4.1 运行安全检查脚本
```bash
php /www/wwwroot/www.aowenbocai.icu/security_check.php
```

#### 4.2 检查PHP配置
```bash
php -m | grep -E "(curl|openssl|json)"
php -i | grep -E "(expose_php|disable_functions)"
```

### 5. 监控和维护

#### 5.1 定期安全检查
- 每月运行一次安全扫描
- 监控异常访问日志
- 定期更新PHP版本

#### 5.2 日志监控
- 启用错误日志记录
- 监控安全相关的访问模式
- 设置异常告警机制

## 注意事项

1. **备份重要性**: 修改配置前请备份原始文件
2. **测试环境**: 建议先在测试环境验证修复效果
3. **业务影响**: 禁用某些函数可能影响现有功能，需要充分测试
4. **持续监控**: 安全是持续过程，需要定期检查和更新

## 紧急联系

如果修复过程中遇到问题：
1. 立即恢复备份文件
2. 检查错误日志
3. 联系技术支持团队
