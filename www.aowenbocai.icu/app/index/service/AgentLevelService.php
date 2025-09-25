<?php
namespace app\index\service;

use think\Model;
use think\Db;

/**
 * 代理等级管理服务
 */
class AgentLevelService
{
    /**
     * 获取代理等级列表
     * @param array $where 查询条件
     * @return array
     */
    public function getLevelList($where = [])
    {
        $query = Db::name('agent_level')->where('is_del', 0);
        
        if (!empty($where['status'])) {
            $query->where('status', $where['status']);
        }
        
        if (!empty($where['keyword'])) {
            $query->where('name', 'like', '%' . $where['keyword'] . '%');
        }
        
        $list = $query->order('grade asc')->select();
        
        foreach ($list as &$item) {
            $item['one_brokerage_ratio'] = $item['one_brokerage_percent'];
            $item['two_brokerage_ratio'] = $item['two_brokerage_percent'];
            $item['task_count'] = Db::name('agent_level_task')->where('level_id', $item['id'])->where('is_del', 0)->count();
        }
        
        return $list;
    }
    
    /**
     * 获取代理等级信息
     * @param int $id 等级ID
     * @return array|null
     */
    public function getLevelInfo($id)
    {
        $level = Db::name('agent_level')->where('id', $id)->where('is_del', 0)->find();
        
        if ($level) {
            $level['tasks'] = Db::name('agent_level_task')->where('level_id', $id)->where('is_del', 0)->select();
        }
        
        return $level;
    }
    
    /**
     * 创建代理等级
     * @param array $data 等级数据
     * @return bool
     */
    public function createLevel($data)
    {
        $data['add_time'] = time();
        $data['is_del'] = 0;
        
        return Db::name('agent_level')->insert($data);
    }
    
    /**
     * 更新代理等级
     * @param int $id 等级ID
     * @param array $data 更新数据
     * @return bool
     */
    public function updateLevel($id, $data)
    {
        return Db::name('agent_level')->where('id', $id)->update($data);
    }
    
    /**
     * 删除代理等级
     * @param int $id 等级ID
     * @return bool
     */
    public function deleteLevel($id)
    {
        return Db::name('agent_level')->where('id', $id)->update(['is_del' => 1]);
    }
    
    /**
     * 获取用户当前等级
     * @param int $uid 用户ID
     * @return array|null
     */
    public function getUserLevel($uid)
    {
        $user = Db::name('user')->where('id', $uid)->find();
        
        if (!$user || !$user['agent_level']) {
            return null;
        }
        
        return $this->getLevelInfo($user['agent_level']);
    }
    
    /**
     * 检查用户等级升级
     * @param int $uid 用户ID
     * @return bool
     */
    public function checkUserLevelUpgrade($uid)
    {
        $user = Db::name('user')->where('id', $uid)->find();
        
        if (!$user || $user['type'] != 2) {
            return false;
        }
        
        // 获取当前等级
        $currentLevel = $user['agent_level'] ?: 0;
        
        // 获取下一个等级
        $nextLevel = Db::name('agent_level')
            ->where('grade', '>', $currentLevel)
            ->where('status', 1)
            ->where('is_del', 0)
            ->order('grade asc')
            ->find();
            
        if (!$nextLevel) {
            return false;
        }
        
        // 检查升级条件
        $canUpgrade = $this->checkUpgradeConditions($uid, $nextLevel);
        
        if ($canUpgrade) {
            // 升级用户等级
            Db::name('user')->where('id', $uid)->update(['agent_level' => $nextLevel['id']]);
            
            // 记录升级日志
            $this->recordLevelUpgrade($uid, $currentLevel, $nextLevel['id']);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 检查升级条件
     * @param int $uid 用户ID
     * @param array $level 等级信息
     * @return bool
     */
    private function checkUpgradeConditions($uid, $level)
    {
        // 获取用户统计数据
        $stats = $this->getUserStats($uid);
        
        // 检查任务完成情况
        $tasks = Db::name('agent_level_task')->where('level_id', $level['id'])->where('is_del', 0)->select();
        
        foreach ($tasks as $task) {
            switch ($task['type']) {
                case 'spread_count':
                    if ($stats['spread_count'] < $task['target_value']) {
                        return false;
                    }
                    break;
                case 'brokerage_amount':
                    if ($stats['total_brokerage'] < $task['target_value']) {
                        return false;
                    }
                    break;
                case 'order_amount':
                    if ($stats['total_order_amount'] < $task['target_value']) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * 获取用户统计数据
     * @param int $uid 用户ID
     * @return array
     */
    private function getUserStats($uid)
    {
        return [
            'spread_count' => Db::name('user')->where('spread_uid', $uid)->count(),
            'total_brokerage' => Db::name('user_brokerage')->where('uid', $uid)->where('pm', 1)->sum('price') ?: 0,
            'total_order_amount' => 0 // 这里需要根据实际订单表计算
        ];
    }
    
    /**
     * 记录等级升级日志
     * @param int $uid 用户ID
     * @param int $oldLevel 原等级
     * @param int $newLevel 新等级
     */
    private function recordLevelUpgrade($uid, $oldLevel, $newLevel)
    {
        $data = [
            'uid' => $uid,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'upgrade_time' => time(),
            'remark' => '自动升级'
        ];
        
        Db::name('agent_level_upgrade_log')->insert($data);
    }
    
    /**
     * 获取等级升级记录
     * @param int $uid 用户ID
     * @return array
     */
    public function getLevelUpgradeLog($uid)
    {
        return Db::name('agent_level_upgrade_log')
            ->where('uid', $uid)
            ->order('upgrade_time desc')
            ->select();
    }
}
