<?php
/**
 * 简化版批量删除所有会员（保留1019683427）
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

// 初始化应用
\think\App::initCommon();

echo "=== 简化版批量删除用户 ===\n";
echo "保留超级管理员: 1019683427\n";
echo "删除所有其他用户\n\n";

try {
    $userModel = new \app\admin\model\User();
    
    // 1. 获取要删除的用户
    $usersToDelete = $userModel->where('username', 'neq', '1019683427')->field('id,username')->select();
    $totalCount = count($usersToDelete);
    
    echo "找到 {$totalCount} 个用户需要删除\n\n";
    
    if ($totalCount === 0) {
        echo "没有用户需要删除\n";
        exit(0);
    }
    
    $successCount = 0;
    $errorCount = 0;
    
    // 2. 逐个删除用户
    foreach ($usersToDelete as $user) {
        try {
            $userId = $user['id'];
            $username = $user['username'];
            
            echo "删除用户: {$username} (ID: {$userId})...";
            
            // 开启事务
            \think\Db::startTrans();
            
            // 清除代理关系
            $userModel->where('agents', $userId)->update(['agents' => 0, 'top_agents' => 0]);
            
            // 清除关联数据（简化版，只清除主要表）
            $mainTables = ['money_history', 'game_money_history', 'user_bag', 'statis'];
            foreach ($mainTables as $table) {
                try {
                    \think\Db::name($table)->where('userid', $userId)->delete();
                } catch (\Exception $e) {
                    // 忽略错误
                }
            }
            
            // 删除用户主记录
            $userRecord = $userModel->get($userId);
            if ($userRecord) {
                $userRecord->delete();
            }
            
            // 提交事务
            \think\Db::commit();
            
            echo " ✅\n";
            $successCount++;
            
        } catch (\Exception $e) {
            \think\Db::rollback();
            echo " ❌ " . $e->getMessage() . "\n";
            $errorCount++;
        }
        
        // 每删除10个用户休息一下
        if (($successCount + $errorCount) % 10 === 0) {
            echo "已处理 " . ($successCount + $errorCount) . " 个用户，休息1秒...\n";
            sleep(1);
        }
    }
    
    echo "\n=== 删除完成 ===\n";
    echo "成功删除: {$successCount} 个用户\n";
    echo "删除失败: {$errorCount} 个用户\n";
    
    // 3. 验证剩余用户
    $remainingUsers = $userModel->field('id,username,nickname')->select();
    echo "剩余用户数: " . count($remainingUsers) . " 个\n";
    
    foreach ($remainingUsers as $user) {
        echo "- {$user['username']} ({$user['nickname']})\n";
    }
    
    echo "\n✅ 批量删除操作完成！\n";

} catch (\Exception $e) {
    echo "\n❌ 发生错误: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . "\n";
    echo "行号: " . $e->getLine() . "\n";
}

echo "\n脚本执行完毕。\n";
?>
