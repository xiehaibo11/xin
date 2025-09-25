<?php
/**
 * 检查代理系统相关信息
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

// 初始化应用
\think\App::initCommon();

echo "=== 代理系统检查 ===\n\n";

try {
    // 1. 检查代理相关表
    echo "1. 检查数据库表结构...\n";
    $tables = \think\Db::query("SHOW TABLES LIKE '%agent%'");
    if (empty($tables)) {
        echo "   没有找到专门的代理表\n";
    } else {
        foreach ($tables as $table) {
            echo "   找到表: " . array_values($table)[0] . "\n";
        }
    }
    
    // 2. 检查用户表中的代理用户
    echo "\n2. 检查用户表中的代理用户...\n";
    $agentUsers = \think\Db::name('user')->where('type', 2)->field('id,username,nickname,type')->limit(10)->select();
    echo "   代理用户数量: " . count($agentUsers) . " 个\n";
    
    if (!empty($agentUsers)) {
        echo "   代理用户列表:\n";
        foreach ($agentUsers as $agent) {
            echo "   - ID: {$agent['id']}, 用户名: {$agent['username']}, 昵称: {$agent['nickname']}\n";
        }
    }
    
    // 3. 检查用户表结构
    echo "\n3. 检查用户表结构...\n";
    $userTableStructure = \think\Db::query("DESCRIBE kr_user");
    $agentRelatedFields = [];
    foreach ($userTableStructure as $field) {
        if (stripos($field['Field'], 'agent') !== false) {
            $agentRelatedFields[] = $field;
        }
    }
    
    if (!empty($agentRelatedFields)) {
        echo "   代理相关字段:\n";
        foreach ($agentRelatedFields as $field) {
            echo "   - {$field['Field']}: {$field['Type']} ({$field['Comment']})\n";
        }
    }
    
    // 4. 检查是否存在Agents控制器
    echo "\n4. 检查Agents控制器...\n";
    $agentsControllerPaths = [
        '/www/wwwroot/www.aowenbocai.icu/app/index/controller/Agents.php',
        '/www/wwwroot/www.aowenbocai.icu/app/web/controller/Agents.php',
        '/www/wwwroot/www.aowenbocai.icu/app/admin/controller/Agents.php'
    ];
    
    foreach ($agentsControllerPaths as $path) {
        if (file_exists($path)) {
            echo "   找到控制器: $path\n";
        }
    }
    
    // 5. 检查代理登录相关的session
    echo "\n5. 检查代理登录session机制...\n";
    echo "   代理登录应该设置的session:\n";
    echo "   - agentname: 代理用户名\n";
    echo "   - agentId: 代理用户ID\n";
    
    // 6. 分析代理登录逻辑
    echo "\n6. 代理登录逻辑分析...\n";
    echo "   当前代理登录流程:\n";
    echo "   1. 访问 /index/Login/agent 显示登录页面\n";
    echo "   2. 提交到 /index/login/agentLogin 处理登录\n";
    echo "   3. 使用 Agents 模型的 login 方法验证\n";
    echo "   4. 登录成功后跳转到 Agents/index\n";
    
    echo "\n=== 问题分析 ===\n";
    echo "❌ 缺少 Agents 模型类\n";
    echo "❌ 可能缺少 Agents 控制器\n";
    echo "❌ 代理登录逻辑不完整\n";
    
    echo "\n=== 建议修复方案 ===\n";
    echo "1. 创建 Agents 模型类\n";
    echo "2. 创建 Agents 控制器\n";
    echo "3. 修复代理登录逻辑\n";
    echo "4. 检查前端资源文件\n";

} catch (\Exception $e) {
    echo "\n❌ 检查过程中发生错误:\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
}

echo "\n检查完成。\n";
?>
