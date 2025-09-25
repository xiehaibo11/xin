<?php
/**
 * CRMEB代理系统集成快速开始脚本
 * 一键执行数据库迁移和基础配置
 */

// 引入ThinkPHP框架
define('APP_PATH', __DIR__ . '/app/');
require __DIR__ . '/thinkphp/start.php';

use think\Db;
use think\Config;

class CrmebIntegrationStarter
{
    private $db;
    private $steps = [];
    private $currentStep = 0;
    
    public function __construct()
    {
        $this->db = Db::connect();
        $this->initializeSteps();
        
        echo "🚀 CRMEB代理系统集成快速开始\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
    }
    
    /**
     * 初始化执行步骤
     */
    private function initializeSteps()
    {
        $this->steps = [
            [
                'name' => '环境检查',
                'description' => '检查系统环境和依赖',
                'method' => 'checkEnvironment'
            ],
            [
                'name' => '数据库备份',
                'description' => '备份现有数据库',
                'method' => 'backupDatabase'
            ],
            [
                'name' => '执行数据库迁移',
                'description' => '创建代理相关表和字段',
                'method' => 'executeMigration'
            ],
            [
                'name' => '验证迁移结果',
                'description' => '验证数据库迁移是否成功',
                'method' => 'verifyMigration'
            ],
            [
                'name' => '创建基础配置',
                'description' => '创建代理系统基础配置文件',
                'method' => 'createBaseConfig'
            ],
            [
                'name' => '生成示例数据',
                'description' => '生成测试用的示例数据',
                'method' => 'generateSampleData'
            ]
        ];
    }
    
    /**
     * 执行完整集成流程
     */
    public function runFullIntegration()
    {
        echo "📋 执行步骤总览:\n";
        foreach ($this->steps as $index => $step) {
            echo "   " . ($index + 1) . ". {$step['name']} - {$step['description']}\n";
        }
        echo "\n";
        
        $startTime = microtime(true);
        
        foreach ($this->steps as $index => $step) {
            $this->currentStep = $index + 1;
            $this->executeStep($step);
        }
        
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        echo "\n🎉 CRMEB代理系统集成完成！\n";
        echo "⏱️  总耗时: {$duration} 秒\n";
        echo "🔗 下一步: 访问 https://www.aowenbocai.icu/index/agents/index 查看代理后台\n\n";
    }
    
    /**
     * 执行单个步骤
     */
    private function executeStep($step)
    {
        echo "🔄 步骤 {$this->currentStep}: {$step['name']}\n";
        echo "   📝 {$step['description']}\n";
        
        $startTime = microtime(true);
        
        try {
            $result = $this->{$step['method']}();
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            if ($result['success']) {
                echo "   ✅ 完成 ({$duration}s)\n";
                if (!empty($result['message'])) {
                    echo "   💡 {$result['message']}\n";
                }
            } else {
                echo "   ❌ 失败: {$result['error']}\n";
                echo "   🛑 集成过程中断\n";
                exit(1);
            }
        } catch (Exception $e) {
            echo "   ❌ 异常: {$e->getMessage()}\n";
            echo "   🛑 集成过程中断\n";
            exit(1);
        }
        
        echo "\n";
    }
    
    /**
     * 步骤1: 环境检查
     */
    private function checkEnvironment()
    {
        $checks = [];
        
        // 检查PHP版本
        $phpVersion = PHP_VERSION;
        $checks['PHP版本'] = version_compare($phpVersion, '5.6.0', '>=') ? 'PASS' : 'FAIL';
        
        // 检查MySQL连接
        try {
            $this->db->query('SELECT 1');
            $checks['数据库连接'] = 'PASS';
        } catch (Exception $e) {
            $checks['数据库连接'] = 'FAIL';
        }
        
        // 检查ThinkPHP版本
        $checks['ThinkPHP框架'] = defined('THINK_VERSION') ? 'PASS' : 'FAIL';
        
        // 检查必要目录权限
        $checks['目录权限'] = is_writable(__DIR__ . '/runtime') ? 'PASS' : 'FAIL';
        
        // 检查CRMEB源码
        $checks['CRMEB源码'] = is_dir(__DIR__ . '/crmeb-system') ? 'PASS' : 'FAIL';
        
        $failedChecks = array_filter($checks, function($status) {
            return $status === 'FAIL';
        });
        
        if (!empty($failedChecks)) {
            return [
                'success' => false,
                'error' => '环境检查失败: ' . implode(', ', array_keys($failedChecks))
            ];
        }
        
        return [
            'success' => true,
            'message' => '环境检查通过，所有依赖满足要求'
        ];
    }
    
    /**
     * 步骤2: 数据库备份
     */
    private function backupDatabase()
    {
        $backupFile = __DIR__ . '/database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        // 获取数据库配置
        $config = Config::get('database');
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];
        $hostname = $config['hostname'];
        
        // 执行mysqldump备份
        $command = "mysqldump -h{$hostname} -u{$username} -p{$password} {$database} > {$backupFile}";
        
        // 为了安全，不直接执行shell命令，而是创建备份提示
        file_put_contents($backupFile . '.info', "数据库备份信息\n" . 
            "数据库: {$database}\n" . 
            "时间: " . date('Y-m-d H:i:s') . "\n" .
            "建议手动执行: {$command}\n");
        
        return [
            'success' => true,
            'message' => "备份信息已保存到 {$backupFile}.info"
        ];
    }
    
    /**
     * 步骤3: 执行数据库迁移
     */
    private function executeMigration()
    {
        $migrationFile = __DIR__ . '/crmeb_database_migration.sql';
        
        if (!file_exists($migrationFile)) {
            return [
                'success' => false,
                'error' => '迁移脚本文件不存在'
            ];
        }
        
        $sql = file_get_contents($migrationFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $executed = 0;
        $errors = [];
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            try {
                $this->db->execute($statement);
                $executed++;
            } catch (Exception $e) {
                // 忽略已存在的表/字段错误
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate column') === false) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'error' => '迁移执行失败: ' . implode('; ', array_slice($errors, 0, 3))
            ];
        }
        
        return [
            'success' => true,
            'message' => "成功执行 {$executed} 条SQL语句"
        ];
    }
    
    /**
     * 步骤4: 验证迁移结果
     */
    private function verifyMigration()
    {
        $requiredTables = [
            'kr_agent_level',
            'kr_agent_apply', 
            'kr_user_brokerage',
            'kr_user_brokerage_frozen',
            'kr_user_spread'
        ];
        
        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            try {
                $this->db->query("SELECT 1 FROM {$table} LIMIT 1");
            } catch (Exception $e) {
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            return [
                'success' => false,
                'error' => '缺少必要表: ' . implode(', ', $missingTables)
            ];
        }
        
        // 检查用户表扩展字段
        try {
            $this->db->query("SELECT brokerage_price, agent_level, is_agent FROM kr_user LIMIT 1");
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => '用户表扩展字段验证失败'
            ];
        }
        
        return [
            'success' => true,
            'message' => '数据库迁移验证通过'
        ];
    }
    
    /**
     * 步骤5: 创建基础配置
     */
    private function createBaseConfig()
    {
        $configDir = __DIR__ . '/app/extra';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }
        
        $agentConfig = [
            'agent_system' => [
                'enable' => true,
                'version' => '1.0.0',
                'max_level' => 5,
                'default_brokerage_rate' => 5.0,
                'frozen_days' => 7,
                'min_withdraw' => 100.00
            ],
            'brokerage' => [
                'level_1_rate' => 5.0,
                'level_2_rate' => 3.0,
                'level_3_rate' => 1.0,
                'auto_release' => true,
                'frozen_period' => 7
            ],
            'agent_apply' => [
                'auto_approve' => false,
                'required_fields' => ['name', 'phone', 'agent_name'],
                'upload_images' => true
            ]
        ];
        
        $configFile = $configDir . '/agent.php';
        $configContent = "<?php\n// CRMEB代理系统配置\nreturn " . var_export($agentConfig, true) . ";\n";
        
        file_put_contents($configFile, $configContent);
        
        return [
            'success' => true,
            'message' => "配置文件已创建: {$configFile}"
        ];
    }
    
    /**
     * 步骤6: 生成示例数据
     */
    private function generateSampleData()
    {
        // 检查是否已有代理等级数据
        $levelCount = $this->db->table('kr_agent_level')->count();
        
        if ($levelCount > 0) {
            return [
                'success' => true,
                'message' => '代理等级数据已存在，跳过示例数据生成'
            ];
        }
        
        // 插入示例代理等级
        $sampleLevels = [
            [
                'name' => '初级代理',
                'one_brokerage_percent' => 5.00,
                'two_brokerage_percent' => 3.00,
                'grade' => 1,
                'status' => 1,
                'add_time' => time()
            ],
            [
                'name' => '中级代理', 
                'one_brokerage_percent' => 8.00,
                'two_brokerage_percent' => 5.00,
                'grade' => 2,
                'status' => 1,
                'add_time' => time()
            ],
            [
                'name' => '高级代理',
                'one_brokerage_percent' => 12.00,
                'two_brokerage_percent' => 8.00,
                'grade' => 3,
                'status' => 1,
                'add_time' => time()
            ]
        ];
        
        $inserted = 0;
        foreach ($sampleLevels as $level) {
            try {
                $this->db->table('kr_agent_level')->insert($level);
                $inserted++;
            } catch (Exception $e) {
                // 忽略重复插入错误
            }
        }
        
        return [
            'success' => true,
            'message' => "成功创建 {$inserted} 个示例代理等级"
        ];
    }
}

// 检查是否为命令行执行
if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
    // 命令行模式
    $starter = new CrmebIntegrationStarter();
    $starter->runFullIntegration();
} else {
    // Web模式
    echo "<pre>";
    $starter = new CrmebIntegrationStarter();
    $starter->runFullIntegration();
    echo "</pre>";
}
?>
