<?php
/**
 * CRMEB代理系统数据库迁移验证脚本
 * 用于验证数据库迁移是否成功完成
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

use think\Db;
use think\Config;

class CrmebMigrationVerifier
{
    private $db;
    private $results = [];
    
    public function __construct()
    {
        $this->db = Db::connect();
        echo "🔍 CRMEB代理系统数据库迁移验证开始...\n\n";
    }
    
    /**
     * 执行完整验证
     */
    public function runFullVerification()
    {
        $this->verifyUserTableExtension();
        $this->verifyAgentTables();
        $this->verifyIndexes();
        $this->verifyDataIntegrity();
        $this->generateReport();
    }
    
    /**
     * 验证用户表扩展字段
     */
    private function verifyUserTableExtension()
    {
        echo "📋 1. 验证用户表扩展字段...\n";
        
        $requiredFields = [
            'brokerage_price' => 'decimal(12,2)',
            'agent_level' => 'int(10)',
            'spread_open' => 'tinyint(1)',
            'spread_uid' => 'int(10)',
            'spread_time' => 'int(11)',
            'is_promoter' => 'tinyint(1)',
            'spread_count' => 'int(11)',
            'division_name' => 'varchar(255)',
            'division_type' => 'tinyint(1)',
            'division_status' => 'tinyint(1)',
            'is_agent' => 'tinyint(1)',
            'division_percent' => 'int(11)',
            'division_end_time' => 'int(11)',
            'division_invite' => 'int(11)'
        ];
        
        $existingFields = $this->getTableFields('kr_user');
        $missingFields = [];
        
        foreach ($requiredFields as $field => $type) {
            if (!in_array($field, $existingFields)) {
                $missingFields[] = $field;
                echo "   ❌ 缺少字段: {$field}\n";
            } else {
                echo "   ✅ 字段存在: {$field}\n";
            }
        }
        
        $this->results['user_table_extension'] = [
            'status' => empty($missingFields) ? 'PASS' : 'FAIL',
            'missing_fields' => $missingFields,
            'total_fields' => count($requiredFields),
            'existing_fields' => count($existingFields)
        ];
        
        echo "\n";
    }
    
    /**
     * 验证代理相关表
     */
    private function verifyAgentTables()
    {
        echo "📋 2. 验证代理相关表...\n";
        
        $requiredTables = [
            'kr_agent_level' => '代理等级表',
            'kr_agent_apply' => '代理申请表',
            'kr_user_brokerage' => '用户佣金明细表',
            'kr_user_brokerage_frozen' => '用户佣金冻结表',
            'kr_user_spread' => '用户推广关系表',
            'kr_spread_apply' => '分销申请表',
            'kr_agent_level_task' => '代理等级任务表',
            'kr_agent_level_task_record' => '代理等级任务记录表'
        ];
        
        $missingTables = [];
        $tableStats = [];
        
        foreach ($requiredTables as $table => $description) {
            if ($this->tableExists($table)) {
                $count = $this->getTableRowCount($table);
                echo "   ✅ 表存在: {$table} ({$description}) - {$count} 条记录\n";
                $tableStats[$table] = $count;
            } else {
                $missingTables[] = $table;
                echo "   ❌ 表缺失: {$table} ({$description})\n";
            }
        }
        
        $this->results['agent_tables'] = [
            'status' => empty($missingTables) ? 'PASS' : 'FAIL',
            'missing_tables' => $missingTables,
            'table_stats' => $tableStats
        ];
        
        echo "\n";
    }
    
    /**
     * 验证索引
     */
    private function verifyIndexes()
    {
        echo "📋 3. 验证数据库索引...\n";
        
        $requiredIndexes = [
            'kr_user' => ['idx_spread_uid', 'idx_is_promoter', 'idx_agent_level', 'idx_is_agent'],
            'kr_agent_level' => ['idx_grade', 'idx_status'],
            'kr_agent_apply' => ['idx_uid', 'idx_status', 'idx_add_time'],
            'kr_user_brokerage' => ['idx_uid', 'idx_pm', 'idx_status', 'idx_add_time'],
            'kr_user_brokerage_frozen' => ['idx_uid_status', 'idx_frozen_time'],
            'kr_user_spread' => ['idx_uid', 'idx_spread_uid', 'idx_spread_time']
        ];
        
        $indexStats = [];
        
        foreach ($requiredIndexes as $table => $indexes) {
            if ($this->tableExists($table)) {
                $existingIndexes = $this->getTableIndexes($table);
                $missingIndexes = array_diff($indexes, $existingIndexes);
                
                if (empty($missingIndexes)) {
                    echo "   ✅ 表 {$table} 索引完整\n";
                    $indexStats[$table] = 'COMPLETE';
                } else {
                    echo "   ⚠️  表 {$table} 缺少索引: " . implode(', ', $missingIndexes) . "\n";
                    $indexStats[$table] = 'PARTIAL';
                }
            }
        }
        
        $this->results['indexes'] = [
            'status' => in_array('PARTIAL', $indexStats) ? 'WARNING' : 'PASS',
            'index_stats' => $indexStats
        ];
        
        echo "\n";
    }
    
    /**
     * 验证数据完整性
     */
    private function verifyDataIntegrity()
    {
        echo "📋 4. 验证数据完整性...\n";
        
        // 检查代理等级数据
        $agentLevels = $this->db->table('kr_agent_level')->count();
        echo "   📊 代理等级数量: {$agentLevels}\n";
        
        // 检查代理用户数量
        $agentUsers = $this->db->table('kr_user')->where('is_agent', 1)->count();
        echo "   👥 代理用户数量: {$agentUsers}\n";
        
        // 检查推广关系
        $spreadRelations = $this->db->table('kr_user')->where('spread_uid', '>', 0)->count();
        echo "   🔗 推广关系数量: {$spreadRelations}\n";
        
        // 检查佣金记录
        $brokerageRecords = $this->db->table('kr_user_brokerage')->count();
        echo "   💰 佣金记录数量: {$brokerageRecords}\n";
        
        // 检查邀请码生成
        $inviteCodes = $this->db->table('kr_user')->where('division_invite', '>', 0)->count();
        echo "   🎫 邀请码生成数量: {$inviteCodes}\n";
        
        // 数据一致性检查
        $inconsistencies = [];
        
        // 检查代理用户是否都有邀请码
        $agentWithoutInvite = $this->db->table('kr_user')
            ->where('is_agent', 1)
            ->where('division_invite', 0)
            ->count();
        
        if ($agentWithoutInvite > 0) {
            $inconsistencies[] = "有 {$agentWithoutInvite} 个代理用户没有邀请码";
            echo "   ⚠️  有 {$agentWithoutInvite} 个代理用户没有邀请码\n";
        }
        
        // 检查推广关系的完整性
        $invalidSpreadRelations = $this->db->query("
            SELECT COUNT(*) as count FROM kr_user u1 
            LEFT JOIN kr_user u2 ON u1.spread_uid = u2.id 
            WHERE u1.spread_uid > 0 AND u2.id IS NULL
        ");
        
        if ($invalidSpreadRelations[0]['count'] > 0) {
            $inconsistencies[] = "有 {$invalidSpreadRelations[0]['count']} 个无效的推广关系";
            echo "   ⚠️  有 {$invalidSpreadRelations[0]['count']} 个无效的推广关系\n";
        }
        
        $this->results['data_integrity'] = [
            'status' => empty($inconsistencies) ? 'PASS' : 'WARNING',
            'agent_levels' => $agentLevels,
            'agent_users' => $agentUsers,
            'spread_relations' => $spreadRelations,
            'brokerage_records' => $brokerageRecords,
            'invite_codes' => $inviteCodes,
            'inconsistencies' => $inconsistencies
        ];
        
        echo "\n";
    }
    
    /**
     * 生成验证报告
     */
    private function generateReport()
    {
        echo "📊 验证报告生成...\n";
        echo "=" . str_repeat("=", 50) . "\n";
        echo "🎯 CRMEB代理系统数据库迁移验证报告\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $overallStatus = 'PASS';
        $warnings = 0;
        $errors = 0;
        
        foreach ($this->results as $category => $result) {
            $status = $result['status'];
            $statusIcon = $this->getStatusIcon($status);
            
            echo "📋 " . $this->getCategoryName($category) . ": {$statusIcon} {$status}\n";
            
            if ($status === 'FAIL') {
                $errors++;
                $overallStatus = 'FAIL';
            } elseif ($status === 'WARNING') {
                $warnings++;
                if ($overallStatus !== 'FAIL') {
                    $overallStatus = 'WARNING';
                }
            }
        }
        
        echo "\n" . str_repeat("-", 50) . "\n";
        echo "🎯 总体状态: " . $this->getStatusIcon($overallStatus) . " {$overallStatus}\n";
        echo "📊 统计信息:\n";
        echo "   ✅ 通过项目: " . $this->countStatus('PASS') . "\n";
        echo "   ⚠️  警告项目: {$warnings}\n";
        echo "   ❌ 错误项目: {$errors}\n";
        echo "   📅 验证时间: " . date('Y-m-d H:i:s') . "\n";
        
        // 详细信息
        if ($overallStatus !== 'PASS') {
            echo "\n📋 详细信息:\n";
            foreach ($this->results as $category => $result) {
                if ($result['status'] !== 'PASS') {
                    echo "\n🔍 " . $this->getCategoryName($category) . ":\n";
                    $this->printDetailedInfo($category, $result);
                }
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        
        if ($overallStatus === 'PASS') {
            echo "🎉 恭喜！CRMEB代理系统数据库迁移验证通过！\n";
            echo "✅ 所有必要的表和字段都已正确创建\n";
            echo "✅ 数据完整性检查通过\n";
            echo "🚀 可以开始下一步的代码集成工作\n";
        } elseif ($overallStatus === 'WARNING') {
            echo "⚠️  CRMEB代理系统数据库迁移基本完成，但有一些警告\n";
            echo "💡 建议检查上述警告项目，但不影响基本功能\n";
            echo "🚀 可以继续进行代码集成工作\n";
        } else {
            echo "❌ CRMEB代理系统数据库迁移存在错误\n";
            echo "🔧 请根据上述错误信息修复问题后重新验证\n";
            echo "⏸️  建议修复所有错误后再进行代码集成\n";
        }
        
        echo "\n";
    }
    
    /**
     * 获取表字段列表
     */
    private function getTableFields($tableName)
    {
        try {
            $fields = $this->db->query("SHOW COLUMNS FROM {$tableName}");
            return array_column($fields, 'Field');
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * 检查表是否存在
     */
    private function tableExists($tableName)
    {
        try {
            $result = $this->db->query("SHOW TABLES LIKE '{$tableName}'");
            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * 获取表记录数
     */
    private function getTableRowCount($tableName)
    {
        try {
            return $this->db->table($tableName)->count();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * 获取表索引列表
     */
    private function getTableIndexes($tableName)
    {
        try {
            $indexes = $this->db->query("SHOW INDEX FROM {$tableName}");
            return array_unique(array_column($indexes, 'Key_name'));
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * 获取状态图标
     */
    private function getStatusIcon($status)
    {
        switch ($status) {
            case 'PASS': return '✅';
            case 'WARNING': return '⚠️';
            case 'FAIL': return '❌';
            default: return '❓';
        }
    }
    
    /**
     * 获取分类名称
     */
    private function getCategoryName($category)
    {
        $names = [
            'user_table_extension' => '用户表扩展',
            'agent_tables' => '代理相关表',
            'indexes' => '数据库索引',
            'data_integrity' => '数据完整性'
        ];
        
        return $names[$category] ?? $category;
    }
    
    /**
     * 统计指定状态的数量
     */
    private function countStatus($status)
    {
        $count = 0;
        foreach ($this->results as $result) {
            if ($result['status'] === $status) {
                $count++;
            }
        }
        return $count;
    }
    
    /**
     * 打印详细信息
     */
    private function printDetailedInfo($category, $result)
    {
        switch ($category) {
            case 'user_table_extension':
                if (!empty($result['missing_fields'])) {
                    echo "   缺少字段: " . implode(', ', $result['missing_fields']) . "\n";
                }
                break;
                
            case 'agent_tables':
                if (!empty($result['missing_tables'])) {
                    echo "   缺少表: " . implode(', ', $result['missing_tables']) . "\n";
                }
                break;
                
            case 'data_integrity':
                if (!empty($result['inconsistencies'])) {
                    foreach ($result['inconsistencies'] as $inconsistency) {
                        echo "   - {$inconsistency}\n";
                    }
                }
                break;
        }
    }
}

// 执行验证
try {
    $verifier = new CrmebMigrationVerifier();
    $verifier->runFullVerification();
} catch (Exception $e) {
    echo "❌ 验证过程中发生错误: " . $e->getMessage() . "\n";
    echo "🔧 请检查数据库连接和配置\n";
}
?>
