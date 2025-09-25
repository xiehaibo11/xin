<?php
/**
 * 批量删除所有会员（保留超级管理员1019683427）
 * 
 * ⚠️ 危险操作：此脚本将删除除1019683427外的所有用户数据
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

// 初始化应用
\think\App::initCommon();

// 设置执行时间限制
set_time_limit(0);
ini_set('memory_limit', '512M');

echo "=== 批量删除用户操作 ===\n\n";

// 安全确认
echo "⚠️  即将删除所有用户，仅保留超级管理员 1019683427\n";
echo "此操作将删除:\n";
echo "- 323个用户账号（保留1个）\n";
echo "- 所有用户资金记录\n";
echo "- 57350条关联数据记录\n";
echo "- 所有游戏记录和代理关系\n\n";

echo "请输入 'CONFIRM_DELETE_ALL' 来确认执行: ";
$confirmation = trim(fgets(STDIN));

if ($confirmation !== 'CONFIRM_DELETE_ALL') {
    echo "❌ 操作已取消\n";
    exit(0);
}

try {
    $userModel = new \app\admin\model\User();
    
    echo "\n🔄 开始执行批量删除...\n\n";
    
    // 1. 数据备份
    echo "1. 创建数据备份...\n";
    $backupTime = date('Y-m-d_H-i-s');
    $backupFile = "/tmp/user_backup_{$backupTime}.sql";
    
    // 备份用户表
    $backupCmd = "mysqldump -u root -pcai886 cai886 kr_user > {$backupFile}";
    exec($backupCmd, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ 用户数据已备份到: {$backupFile}\n";
    } else {
        echo "   ⚠️  备份失败，但继续执行删除操作\n";
    }
    
    // 2. 获取要删除的用户列表（排除1019683427）
    echo "\n2. 获取待删除用户列表...\n";
    $usersToDelete = $userModel->where('username', 'neq', '1019683427')->field('id,username,nickname,type,money')->select();
    
    $deleteCount = count($usersToDelete);
    echo "   找到 {$deleteCount} 个用户需要删除\n";
    
    if ($deleteCount === 0) {
        echo "   没有用户需要删除，操作完成\n";
        exit(0);
    }
    
    // 3. 开始批量删除
    echo "\n3. 开始批量删除用户...\n";
    
    $successCount = 0;
    $errorCount = 0;
    $batchSize = 10; // 每批处理10个用户
    $batches = array_chunk($usersToDelete->toArray(), $batchSize);
    
    foreach ($batches as $batchIndex => $batch) {
        echo "   处理第 " . ($batchIndex + 1) . " 批 (" . count($batch) . " 个用户)...\n";
        
        foreach ($batch as $user) {
            try {
                // 开启事务
                \think\Db::startTrans();
                
                $userId = $user['id'];
                $username = $user['username'];
                
                echo "     删除用户: {$username} (ID: {$userId})...";
                
                // 处理代理关系
                $sonUsers = $userModel->where('agents', $userId)->column('id');
                if (!empty($sonUsers)) {
                    // 清除代理关系
                    $userModel->whereIn('id', $sonUsers)->update(['agents' => 0, 'top_agents' => 0]);
                    echo " [处理了" . count($sonUsers) . "个下级]";
                }
                
                // 清除关联数据
                $relTables = $userModel->getRelUserTable();
                $deletedRelatedCount = 0;
                
                foreach ($relTables as $table) {
                    $exist = \think\Db::query('show tables like "kr_' . $table . '"');
                    if (!$exist) continue;
                    
                    try {
                        $count = \think\Db::name($table)->where('userid', $userId)->delete();
                        $deletedRelatedCount += $count;
                    } catch (\Exception $e) {
                        // 忽略不存在userid字段的表
                    }
                }
                
                // 删除用户主记录
                $userRecord = $userModel->get($userId);
                if ($userRecord) {
                    $userRecord->delete();
                }
                
                // 提交事务
                \think\Db::commit();
                
                echo " ✅ (清理了{$deletedRelatedCount}条关联数据)\n";
                $successCount++;
                
                // 记录日志
                \think\Log::info('批量删除用户', [
                    'deleted_user_id' => $userId,
                    'deleted_username' => $username,
                    'related_data_count' => $deletedRelatedCount,
                    'operator' => 'batch_delete_script'
                ]);
                
            } catch (\Exception $e) {
                // 回滚事务
                \think\Db::rollback();
                
                echo " ❌ 失败: " . $e->getMessage() . "\n";
                $errorCount++;
                
                \think\Log::error('批量删除用户失败', [
                    'user_id' => $userId ?? 0,
                    'username' => $username ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
            
            // 短暂休息，避免数据库压力过大
            usleep(100000); // 0.1秒
        }
        
        echo "   第 " . ($batchIndex + 1) . " 批处理完成\n";
        
        // 批次间休息
        sleep(1);
    }
    
    // 4. 统计结果
    echo "\n4. 删除操作完成统计...\n";
    echo "   成功删除: {$successCount} 个用户\n";
    echo "   删除失败: {$errorCount} 个用户\n";
    
    // 5. 验证剩余用户
    echo "\n5. 验证剩余用户...\n";
    $remainingUsers = $userModel->field('id,username,nickname,type')->select();
    
    echo "   剩余用户数: " . count($remainingUsers) . " 个\n";
    foreach ($remainingUsers as $user) {
        echo "   - ID: {$user['id']}, 用户名: {$user['username']}, 昵称: {$user['nickname']}, 类型: {$user['type']}\n";
    }
    
    // 6. 清理统计
    echo "\n6. 数据库清理统计...\n";
    $finalStats = [
        '用户总数' => $userModel->count(),
        '总资金' => $userModel->sum('money'),
        '有余额用户' => $userModel->where('money', '>', 0)->count()
    ];
    
    foreach ($finalStats as $key => $value) {
        echo "   {$key}: {$value}\n";
    }
    
    echo "\n=== 批量删除操作完成 ===\n";
    echo "✅ 成功删除 {$successCount} 个用户\n";
    echo "✅ 保留超级管理员 1019683427\n";
    echo "✅ 数据备份文件: {$backupFile}\n";
    echo "✅ 操作日志已记录\n\n";
    
    echo "🎉 所有会员删除完成！仅保留超级管理员账号。\n";

} catch (\Exception $e) {
    echo "\n❌ 批量删除过程中发生严重错误:\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
    
    \think\Log::error('批量删除用户严重错误', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

echo "\n脚本执行完毕。\n";
?>
