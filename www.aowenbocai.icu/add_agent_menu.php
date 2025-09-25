<?php
/**
 * 添加代理管理菜单到管理后台
 */

// 设置应用目录
define('APP_PATH', __DIR__ . '/app/');

// 引入ThinkPHP框架
require_once __DIR__ . '/thinkphp/start.php';

use core\Setting;

echo "=== 添加代理管理菜单 ===\n";

try {
    // 获取当前菜单配置
    $currentMenuTree = Setting::get('menu_tree');
    $menuTree = json_decode($currentMenuTree, true);
    
    if (!$menuTree) {
        echo "❌ 无法获取当前菜单配置\n";
        exit;
    }
    
    echo "✅ 当前菜单配置获取成功\n";
    echo "当前菜单组数量: " . count($menuTree) . "\n";
    
    // 检查是否已存在代理管理菜单
    $agentMenuExists = false;
    foreach ($menuTree as $group) {
        if ($group['title'] === '代理管理') {
            $agentMenuExists = true;
            break;
        }
    }
    
    if ($agentMenuExists) {
        echo "ℹ️  代理管理菜单已存在，跳过添加\n";
    } else {
        // 添加代理管理菜单组
        $agentMenuGroup = [
            'title' => '代理管理',
            'nav' => [
                [
                    'title' => '代理列表',
                    'url' => 'admin/AgentManage/index',
                    'icon' => 'glyphicon glyphicon-list'
                ],
                [
                    'title' => '创建代理',
                    'url' => 'admin/AgentManage/create',
                    'icon' => 'glyphicon glyphicon-plus'
                ],
                [
                    'title' => '代理层级',
                    'url' => 'admin/AgentManage/hierarchy',
                    'icon' => 'glyphicon glyphicon-tree-deciduous'
                ],
                [
                    'title' => '代理统计',
                    'url' => 'admin/AgentManage/statistics',
                    'icon' => 'glyphicon glyphicon-stats'
                ],
                [
                    'title' => '原代理管理',
                    'url' => 'admin/User/agent_list',
                    'icon' => 'glyphicon glyphicon-user'
                ]
            ]
        ];
        
        // 将代理管理菜单插入到合适的位置（通常在用户管理之后）
        $insertPosition = 1; // 默认插入位置
        
        // 寻找用户管理菜单的位置
        foreach ($menuTree as $index => $group) {
            if (strpos($group['title'], '用户') !== false || strpos($group['title'], '会员') !== false) {
                $insertPosition = $index + 1;
                break;
            }
        }
        
        // 插入代理管理菜单
        array_splice($menuTree, $insertPosition, 0, [$agentMenuGroup]);
        
        echo "✅ 代理管理菜单已添加到位置: $insertPosition\n";
    }
    
    // 检查并添加代理相关的子菜单到现有的用户管理中
    foreach ($menuTree as &$group) {
        if (strpos($group['title'], '用户') !== false || strpos($group['title'], '会员') !== false) {
            // 检查是否已有代理相关菜单
            $hasAgentMenu = false;
            foreach ($group['nav'] as $nav) {
                if (strpos($nav['title'], '代理') !== false) {
                    $hasAgentMenu = true;
                    break;
                }
            }
            
            if (!$hasAgentMenu) {
                // 添加代理管理到用户管理菜单中
                $group['nav'][] = [
                    'title' => '代理管理',
                    'url' => 'admin/User/agent_list',
                    'icon' => 'glyphicon glyphicon-user'
                ];
                echo "✅ 已在用户管理中添加代理管理菜单\n";
            }
            break;
        }
    }
    
    // 保存更新后的菜单配置
    $newMenuTreeJson = json_encode($menuTree, JSON_UNESCAPED_UNICODE);
    $result = Setting::set('menu_tree', $newMenuTreeJson);
    
    if ($result) {
        echo "✅ 菜单配置保存成功\n";
        
        // 显示更新后的菜单结构
        echo "\n=== 更新后的菜单结构 ===\n";
        foreach ($menuTree as $index => $group) {
            echo ($index + 1) . ". {$group['title']}\n";
            foreach ($group['nav'] as $navIndex => $nav) {
                echo "   " . ($navIndex + 1) . ". {$nav['title']} ({$nav['url']})\n";
            }
            echo "\n";
        }
        
        echo "🎉 代理管理菜单添加完成！\n";
        echo "\n现在您可以在管理后台看到以下新功能：\n";
        echo "1. 代理管理 -> 代理列表 (完整的代理管理界面)\n";
        echo "2. 代理管理 -> 创建代理 (直接创建代理账号)\n";
        echo "3. 代理管理 -> 代理层级 (可视化代理层级结构)\n";
        echo "4. 代理管理 -> 代理统计 (代理数据统计)\n";
        echo "5. 增强的代理接管功能 (一键登录代理后台)\n";
        
        echo "\n🔗 访问地址：\n";
        echo "管理后台: https://www.aowenbocai.icu/admin.php/index\n";
        echo "代理后台: https://www.aowenbocai.icu/index/agents/index\n";
        
    } else {
        echo "❌ 菜单配置保存失败\n";
    }
    
} catch (Exception $e) {
    echo "❌ 错误: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
}

echo "\n=== 脚本执行完成 ===\n";
?>
