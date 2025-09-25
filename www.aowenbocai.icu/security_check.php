<?php
/**
 * 安全配置检查脚本
 * 运行此脚本检查系统安全配置状态
 */

// 只允许从命令行运行
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line.');
}

echo "=== 系统安全配置检查 ===\n\n";

// 1. 检查PHP配置
echo "1. PHP安全配置检查:\n";
$phpChecks = [
    'display_errors' => ['expected' => 'Off', 'current' => ini_get('display_errors') ? 'On' : 'Off'],
    'expose_php' => ['expected' => 'Off', 'current' => ini_get('expose_php') ? 'On' : 'Off'],
    'allow_url_fopen' => ['expected' => 'Off', 'current' => ini_get('allow_url_fopen') ? 'On' : 'Off'],
    'allow_url_include' => ['expected' => 'Off', 'current' => ini_get('allow_url_include') ? 'On' : 'Off'],
    'session.cookie_httponly' => ['expected' => 'On', 'current' => ini_get('session.cookie_httponly') ? 'On' : 'Off'],
    'session.cookie_secure' => ['expected' => 'On', 'current' => ini_get('session.cookie_secure') ? 'On' : 'Off'],
];

foreach ($phpChecks as $setting => $check) {
    $status = $check['current'] === $check['expected'] ? '✓' : '✗';
    echo "   {$status} {$setting}: {$check['current']} (期望: {$check['expected']})\n";
}

// 2. 检查文件权限
echo "\n2. 文件权限检查:\n";
$fileChecks = [
    '.env' => '600',
    'app/config.php' => '644',
    'public/index.php' => '755',
    'runtime' => '755',
    'public/uploads' => '755'
];

foreach ($fileChecks as $file => $expectedPerm) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $currentPerm = substr(sprintf('%o', fileperms($fullPath)), -3);
        $status = $currentPerm === $expectedPerm ? '✓' : '✗';
        echo "   {$status} {$file}: {$currentPerm} (期望: {$expectedPerm})\n";
    } else {
        echo "   ? {$file}: 文件不存在\n";
    }
}

// 3. 检查安全类文件
echo "\n3. 安全类文件检查:\n";
$securityFiles = [
    'core/PasswordHash.php',
    'core/SessionSecurity.php',
    'core/XSSProtection.php',
    'core/SecurityMonitor.php',
    'core/SecurityMiddleware.php'
];

foreach ($securityFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    $status = file_exists($fullPath) ? '✓' : '✗';
    echo "   {$status} {$file}\n";
}

// 4. 检查敏感文件
echo "\n4. 敏感文件检查:\n";
$sensitiveFiles = [
    '.env' => '数据库配置文件',
    'app/database.php' => '数据库配置',
    'security_recommendations.md' => '安全建议文档'
];

foreach ($sensitiveFiles as $file => $description) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $perms = fileperms($fullPath);
        $readable = ($perms & 0x0004) ? '其他用户可读' : '其他用户不可读';
        $status = ($perms & 0x0004) ? '⚠' : '✓';
        echo "   {$status} {$file} ({$description}): {$readable}\n";
    }
}

// 5. 检查日志目录
echo "\n5. 日志目录检查:\n";
$logDirs = [
    '/www/wwwlogs',
    'runtime/log'
];

foreach ($logDirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? '可写' : '不可写';
        $status = is_writable($dir) ? '✓' : '✗';
        echo "   {$status} {$dir}: {$writable}\n";
    } else {
        echo "   ? {$dir}: 目录不存在\n";
    }
}

// 6. 生成安全建议
echo "\n6. 安全建议:\n";
$recommendations = [
    "定期更新ThinkPHP框架版本",
    "定期更换数据库密码和授权令牌",
    "监控安全日志文件 /www/wwwlogs/security.log",
    "定期备份数据库和重要文件",
    "考虑部署Web应用防火墙(WAF)",
    "启用HTTPS并配置HSTS",
    "定期进行安全扫描和渗透测试"
];

foreach ($recommendations as $index => $recommendation) {
    echo "   " . ($index + 1) . ". {$recommendation}\n";
}

echo "\n=== 检查完成 ===\n";
echo "如需详细的安全配置说明，请查看 security_recommendations.md 文件\n";
