<?php
/**
 * 检查用户数据统计
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

// 初始化应用
\think\App::initCommon();

echo "=== 用户数据统计检查 ===\n\n";

try {
    $userModel = new \app\admin\model\User();
    
    echo "1. 用户类型统计...\n";
    
    // 统计各类型用户数量
    $userStats = [
        '测试会员 (type=0)' => $userModel->where('type', 0)->count(),
        '普通会员 (type=1)' => $userModel->where('type', 1)->count(),
        '代理会员 (type=2)' => $userModel->where('type', 2)->count(),
        '订单会员 (type=6)' => $userModel->where('type', 6)->count(),
        '跟单会员 (type=7)' => $userModel->where('type', 7)->count(),
    ];
    
    $totalUsers = 0;
    foreach ($userStats as $type => $count) {
        echo "   {$type}: {$count} 个\n";
        $totalUsers += $count;
    }
    echo "   总用户数: {$totalUsers} 个\n\n";
    
    echo "2. 重要账号检查...\n";
    
    // 检查重要账号
    $importantUsers = $userModel->where('username', 'in', [
        'xie080886', '1019683427', '系统保底', '系统跟单', 'test'
    ])->field('id,username,nickname,type,money,create_time')->select();
    
    if ($importantUsers) {
        foreach ($importantUsers as $user) {
            echo "   ID: {$user['id']}, 用户名: {$user['username']}, 昵称: {$user['nickname']}, 类型: {$user['type']}, 余额: {$user['money']}\n";
        }
    }
    echo "\n";
    
    echo "3. 资金统计...\n";
    
    // 统计总资金
    $totalMoney = $userModel->sum('money');
    $usersWithMoney = $userModel->where('money', '>', 0)->count();
    $maxMoney = $userModel->max('money');
    
    echo "   总资金: {$totalMoney}\n";
    echo "   有余额用户数: {$usersWithMoney} 个\n";
    echo "   最大余额: {$maxMoney}\n\n";
    
    echo "4. 代理关系统计...\n";
    
    // 统计代理关系
    $agentsWithSubs = $userModel->where('type', 2)
                               ->where('id', 'in', function($query) use ($userModel) {
                                   $query->name('user')->where('agents', '>', 0)->field('agents');
                               })->count();
    
    $usersWithAgents = $userModel->where('agents', '>', 0)->count();
    
    echo "   有下级的代理数: {$agentsWithSubs} 个\n";
    echo "   有上级的用户数: {$usersWithAgents} 个\n\n";
    
    echo "5. 关联数据统计...\n";
    
    // 统计关联数据
    $relTables = $userModel->getRelUserTable();
    $totalRelatedRecords = 0;
    
    foreach ($relTables as $table) {
        $exist = \think\Db::query('show tables like "kr_' . $table . '"');
        if (!$exist) continue;
        
        try {
            $count = \think\Db::name($table)->count();
            if ($count > 0) {
                echo "   {$table}: {$count} 条记录\n";
                $totalRelatedRecords += $count;
            }
        } catch (\Exception $e) {
            // 忽略错误
        }
    }
    
    echo "   关联数据总计: {$totalRelatedRecords} 条记录\n\n";
    
    echo "=== 统计完成 ===\n";
    echo "⚠️  删除所有会员将影响:\n";
    echo "   - {$totalUsers} 个用户账号\n";
    echo "   - {$totalMoney} 总资金\n";
    echo "   - {$totalRelatedRecords} 条关联记录\n";
    echo "   - 所有代理关系和游戏记录\n\n";
    
    echo "🚨 重要提醒:\n";
    echo "   - 此操作不可逆转\n";
    echo "   - 建议先进行数据备份\n";
    echo "   - 确保保留重要的系统账号\n";

} catch (\Exception $e) {
    echo "❌ 检查过程中发生错误:\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
}

echo "\n检查脚本执行完毕。\n";
?>
