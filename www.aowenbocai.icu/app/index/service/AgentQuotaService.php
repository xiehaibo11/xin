<?php
namespace app\index\service;

use think\Db;

/**
 * 代理额度管理服务
 */
class AgentQuotaService
{
    /**
     * 获取下级代理额度列表
     * @param int $agentId 代理ID
     * @param array $where 查询条件
     * @param int $page 页码
     * @param int $limit 每页数量
     * @return array
     */
    public function getSubAgentQuotaList($agentId, $where = [], $page = 1, $limit = 20)
    {
        $query = Db::name('agent_quota')
            ->alias('aq')
            ->join('user u', 'aq.sub_agent_id = u.id')
            ->where('aq.agent_id', $agentId);
            
        if (!empty($where['keyword'])) {
            $query->where('u.username|u.nickname', 'like', '%' . $where['keyword'] . '%');
        }
        
        if (isset($where['status']) && $where['status'] !== '') {
            $query->where('aq.status', $where['status']);
        }
        
        $total = $query->count();
        $list = $query->field('aq.*, u.username, u.nickname, u.avatar')
            ->order('aq.add_time desc')
            ->page($page, $limit)
            ->select();
            
        foreach ($list as &$item) {
            $item['add_time_text'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['update_time_text'] = $item['update_time'] ? date('Y-m-d H:i:s', $item['update_time']) : '';
            $item['status_text'] = $item['status'] ? '正常' : '冻结';
            $item['usage_rate'] = $item['quota_amount'] > 0 ? round($item['used_amount'] / $item['quota_amount'] * 100, 2) : 0;
        }
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * 获取代理额度信息
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @return array|null
     */
    public function getAgentQuota($agentId, $subAgentId)
    {
        $quota = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->where('sub_agent_id', $subAgentId)
            ->find();
            
        if ($quota) {
            $subAgent = Db::name('user')->where('id', $subAgentId)->find();
            $quota['sub_agent_info'] = $subAgent;
            $quota['usage_rate'] = $quota['quota_amount'] > 0 ? round($quota['used_amount'] / $quota['quota_amount'] * 100, 2) : 0;
        }
        
        return $quota;
    }
    
    /**
     * 设置下级代理额度
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param float $quotaAmount 额度金额
     * @param string $remark 备注
     * @return bool
     */
    public function setAgentQuota($agentId, $subAgentId, $quotaAmount, $remark = '')
    {
        // 检查下级代理是否存在且为代理类型
        $subAgent = Db::name('user')->where('id', $subAgentId)->where('type', 2)->find();
        if (!$subAgent) {
            return false;
        }
        
        // 检查是否为直接下级
        if ($subAgent['agents'] != $agentId) {
            return false;
        }
        
        $existQuota = $this->getAgentQuota($agentId, $subAgentId);
        
        if ($existQuota) {
            // 更新现有额度
            $beforeAmount = $existQuota['quota_amount'];
            $result = Db::name('agent_quota')
                ->where('agent_id', $agentId)
                ->where('sub_agent_id', $subAgentId)
                ->update([
                    'quota_amount' => $quotaAmount,
                    'available_amount' => $quotaAmount - $existQuota['used_amount'],
                    'update_time' => time()
                ]);
                
            if ($result) {
                // 记录变更日志
                $this->addQuotaLog($agentId, $subAgentId, $quotaAmount > $beforeAmount ? 1 : 2, 
                    abs($quotaAmount - $beforeAmount), $beforeAmount, $quotaAmount, $remark, $agentId);
            }
        } else {
            // 创建新额度
            $data = [
                'agent_id' => $agentId,
                'sub_agent_id' => $subAgentId,
                'quota_amount' => $quotaAmount,
                'used_amount' => 0,
                'available_amount' => $quotaAmount,
                'status' => 1,
                'add_time' => time(),
                'update_time' => time()
            ];
            
            $result = Db::name('agent_quota')->insert($data);
            
            if ($result) {
                // 记录变更日志
                $this->addQuotaLog($agentId, $subAgentId, 1, $quotaAmount, 0, $quotaAmount, $remark, $agentId);
            }
        }
        
        return $result;
    }
    
    /**
     * 调整代理额度
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param float $amount 调整金额（正数增加，负数减少）
     * @param string $remark 备注
     * @return bool
     */
    public function adjustAgentQuota($agentId, $subAgentId, $amount, $remark = '')
    {
        $quota = $this->getAgentQuota($agentId, $subAgentId);
        
        if (!$quota) {
            return false;
        }
        
        $newQuotaAmount = $quota['quota_amount'] + $amount;
        
        if ($newQuotaAmount < 0) {
            return false; // 额度不能为负数
        }
        
        $result = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->where('sub_agent_id', $subAgentId)
            ->update([
                'quota_amount' => $newQuotaAmount,
                'available_amount' => $newQuotaAmount - $quota['used_amount'],
                'update_time' => time()
            ]);
            
        if ($result) {
            // 记录变更日志
            $this->addQuotaLog($agentId, $subAgentId, $amount > 0 ? 1 : 2, 
                abs($amount), $quota['quota_amount'], $newQuotaAmount, $remark, $agentId);
        }
        
        return $result;
    }
    
    /**
     * 冻结/解冻代理额度
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param int $status 状态（1正常，0冻结）
     * @param string $remark 备注
     * @return bool
     */
    public function changeQuotaStatus($agentId, $subAgentId, $status, $remark = '')
    {
        $quota = $this->getAgentQuota($agentId, $subAgentId);
        
        if (!$quota || $quota['status'] == $status) {
            return false;
        }
        
        $result = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->where('sub_agent_id', $subAgentId)
            ->update([
                'status' => $status,
                'update_time' => time()
            ]);
            
        if ($result) {
            // 记录变更日志
            $this->addQuotaLog($agentId, $subAgentId, $status ? 4 : 3, 
                0, $quota['quota_amount'], $quota['quota_amount'], $remark, $agentId);
        }
        
        return $result;
    }
    
    /**
     * 使用额度
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param float $amount 使用金额
     * @param string $remark 备注
     * @return bool
     */
    public function useQuota($agentId, $subAgentId, $amount, $remark = '')
    {
        $quota = $this->getAgentQuota($agentId, $subAgentId);
        
        if (!$quota || $quota['status'] != 1) {
            return false;
        }
        
        if ($quota['available_amount'] < $amount) {
            return false; // 可用额度不足
        }
        
        $result = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->where('sub_agent_id', $subAgentId)
            ->update([
                'used_amount' => $quota['used_amount'] + $amount,
                'available_amount' => $quota['available_amount'] - $amount,
                'update_time' => time()
            ]);
            
        return $result;
    }
    
    /**
     * 释放额度
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param float $amount 释放金额
     * @param string $remark 备注
     * @return bool
     */
    public function releaseQuota($agentId, $subAgentId, $amount, $remark = '')
    {
        $quota = $this->getAgentQuota($agentId, $subAgentId);
        
        if (!$quota) {
            return false;
        }
        
        $newUsedAmount = max(0, $quota['used_amount'] - $amount);
        $newAvailableAmount = $quota['quota_amount'] - $newUsedAmount;
        
        $result = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->where('sub_agent_id', $subAgentId)
            ->update([
                'used_amount' => $newUsedAmount,
                'available_amount' => $newAvailableAmount,
                'update_time' => time()
            ]);
            
        return $result;
    }
    
    /**
     * 添加额度变更日志
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param int $type 类型（1增加，2减少，3冻结，4解冻）
     * @param float $amount 变更金额
     * @param float $beforeAmount 变更前金额
     * @param float $afterAmount 变更后金额
     * @param string $remark 备注
     * @param int $operatorId 操作人ID
     * @return bool
     */
    private function addQuotaLog($agentId, $subAgentId, $type, $amount, $beforeAmount, $afterAmount, $remark, $operatorId)
    {
        $data = [
            'agent_id' => $agentId,
            'sub_agent_id' => $subAgentId,
            'type' => $type,
            'amount' => $amount,
            'before_amount' => $beforeAmount,
            'after_amount' => $afterAmount,
            'remark' => $remark,
            'operator_id' => $operatorId,
            'add_time' => time()
        ];
        
        return Db::name('agent_quota_log')->insert($data);
    }
    
    /**
     * 获取额度变更日志
     * @param int $agentId 代理ID
     * @param int $subAgentId 下级代理ID
     * @param int $page 页码
     * @param int $limit 每页数量
     * @return array
     */
    public function getQuotaLogs($agentId, $subAgentId = 0, $page = 1, $limit = 20)
    {
        $query = Db::name('agent_quota_log')
            ->alias('aql')
            ->join('user u', 'aql.sub_agent_id = u.id')
            ->join('user op', 'aql.operator_id = op.id')
            ->where('aql.agent_id', $agentId);
            
        if ($subAgentId > 0) {
            $query->where('aql.sub_agent_id', $subAgentId);
        }
        
        $total = $query->count();
        $list = $query->field('aql.*, u.username as sub_agent_name, op.username as operator_name')
            ->order('aql.add_time desc')
            ->page($page, $limit)
            ->select();
            
        $typeTexts = [1 => '增加', 2 => '减少', 3 => '冻结', 4 => '解冻'];
        
        foreach ($list as &$item) {
            $item['add_time_text'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['type_text'] = $typeTexts[$item['type']] ?? '未知';
        }
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * 获取代理额度统计
     * @param int $agentId 代理ID
     * @return array
     */
    public function getQuotaStats($agentId)
    {
        $stats = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->field('
                COUNT(*) as total_agents,
                SUM(quota_amount) as total_quota,
                SUM(used_amount) as total_used,
                SUM(available_amount) as total_available,
                COUNT(CASE WHEN status = 1 THEN 1 END) as active_agents,
                COUNT(CASE WHEN status = 0 THEN 1 END) as frozen_agents
            ')
            ->find();
            
        return $stats ?: [
            'total_agents' => 0,
            'total_quota' => 0,
            'total_used' => 0,
            'total_available' => 0,
            'active_agents' => 0,
            'frozen_agents' => 0
        ];
    }

    /**
     * 获取代理总额度
     * @param int $agentId 代理ID
     * @return float
     */
    public function getTotalQuota($agentId)
    {
        $total = Db::name('agent_quota')
            ->where('agent_id', $agentId)
            ->sum('quota_amount');

        return floatval($total);
    }



}
