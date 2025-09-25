<?php
/**
 * 检查管理员用户信息
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

// 初始化应用
\think\App::initCommon();

echo "=== 检查管理员用户信息 ===\n\n";

try {
    $userModel = new \app\admin\model\User();
    
    echo "1. 查找可能的超级管理员账号...\n";
    
    // 查找用户名包含1019683427或xie080886的用户
    $admins = $userModel->where('username', 'in', ['1019683427', 'xie080886'])->whereOr('id', 1)->select();
    
    if ($admins) {
        foreach ($admins as $admin) {
            echo "   用户ID: {$admin['id']}\n";
            echo "   用户名: {$admin['username']}\n";
            echo "   昵称: {$admin['nickname']}\n";
            echo "   类型: {$admin['type']}\n";
            echo "   状态: {$admin['status']}\n";
            echo "   SID: " . substr($admin['sid'], 0, 20) . "...\n";
            echo "   创建时间: {$admin['create_time']}\n";
            echo "   ---\n";
        }
    } else {
        echo "   未找到匹配的管理员账号\n";
    }
    
    echo "\n2. 查找所有type=2的代理用户（前10个）...\n";
    $agents = $userModel->where('type', 2)->limit(10)->select();
    
    if ($agents) {
        foreach ($agents as $agent) {
            echo "   ID: {$agent['id']}, 用户名: {$agent['username']}, 昵称: {$agent['nickname']}\n";
        }
    } else {
        echo "   未找到代理用户\n";
    }
    
    echo "\n3. 检查管理员权限表...\n";
    $adminModel = new \app\admin\model\Admin();
    $adminList = $adminModel->with('user')->select();
    
    if ($adminList) {
        foreach ($adminList as $adminRecord) {
            $user = $adminRecord['user'];
            if ($user) {
                echo "   管理员ID: {$adminRecord['id']}\n";
                echo "   关联用户ID: {$adminRecord['userid']}\n";
                echo "   用户名: {$user['username']}\n";
                echo "   昵称: {$user['nickname']}\n";
                echo "   权限: {$adminRecord['auth']}\n";
                echo "   ---\n";
            }
        }
    } else {
        echo "   未找到管理员权限记录\n";
    }
    
    echo "\n4. 检查当前session中的管理员信息...\n";
    $admin_sid = session('admin_sid');
    if ($admin_sid) {
        echo "   当前session SID: " . substr($admin_sid, 0, 20) . "...\n";
        
        $currentAdmin = $userModel->where('sid', $admin_sid)->find();
        if ($currentAdmin) {
            echo "   当前登录管理员: {$currentAdmin['username']}\n";
            echo "   昵称: {$currentAdmin['nickname']}\n";
        } else {
            echo "   无法通过SID找到当前管理员\n";
        }
    } else {
        echo "   当前未登录或session已过期\n";
    }
    
    echo "\n=== 检查完成 ===\n";

} catch (\Exception $e) {
    echo "❌ 检查过程中发生错误:\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
}

echo "检查脚本执行完毕。\n";
?>
