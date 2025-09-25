<?php
namespace app\index\service;

use think\Db;

/**
 * 代理佣金管理服务
 */
class AgentBrokerageService
{
    /**
     * 佣金类型配置
     */
    const BROKERAGE_TYPES = [
        'register' => '推广注册佣金',
        'recharge' => '推广充值佣金',
        'game_bet' => '游戏投注佣金',
        'level_one' => '一级推广佣金',
        'level_two' => '二级推广佣金',
        'level_three' => '三级推广佣金'
    ];
    
    /**
     * 计算推广佣金
     * @param int $uid 用户ID
     * @param string $type 佣金类型
     * @param float $amount 基础金额
     * @param array $extra 额外参数
     * @return bool
     */
    public function calculateBrokerage($uid, $type, $amount, $extra = [])
    {
        // 获取用户的推广关系链
        $spreadChain = $this->getSpreadChain($uid);
        
        if (empty($spreadChain)) {
            return false;
        }
        
        // 获取佣金配置
        $config = $this->getBrokerageConfig();
        
        foreach ($spreadChain as $level => $spreadUid) {
            if ($level > 3) break; // 最多3级
            
            $brokerageRate = $this->getBrokerageRate($spreadUid, $level, $type);
            
            if ($brokerageRate > 0) {
                $brokerageAmount = $amount * $brokerageRate / 100;
                
                // 添加佣金记录
                $this->addBrokerageRecord($spreadUid, $uid, $type, $brokerageAmount, $level, $extra);
            }
        }
        
        return true;
    }
    
    /**
     * 获取推广关系链
     * @param int $uid 用户ID
     * @return array
     */
    private function getSpreadChain($uid)
    {
        $chain = [];
        $currentUid = $uid;
        $level = 1;
        
        while ($level <= 3) {
            $user = Db::name('user')->where('id', $currentUid)->find();
            
            if (!$user || !$user['spread_uid']) {
                break;
            }
            
            $spreadUser = Db::name('user')->where('id', $user['spread_uid'])->find();
            
            if (!$spreadUser || $spreadUser['type'] != 2) {
                break;
            }
            
            $chain[$level] = $user['spread_uid'];
            $currentUid = $user['spread_uid'];
            $level++;
        }
        
        return $chain;
    }
    
    /**
     * 获取佣金比例
     * @param int $agentId 代理ID
     * @param int $level 推广级别
     * @param string $type 佣金类型
     * @return float
     */
    private function getBrokerageRate($agentId, $level, $type)
    {
        // 获取代理等级信息
        $agent = Db::name('user')->where('id', $agentId)->find();
        
        if (!$agent || !$agent['agent_level']) {
            return 0;
        }
        
        $agentLevel = Db::name('agent_level')->where('id', $agent['agent_level'])->find();
        
        if (!$agentLevel) {
            return 0;
        }
        
        // 根据级别返回对应的佣金比例
        switch ($level) {
            case 1:
                return $agentLevel['one_brokerage_percent'];
            case 2:
                return $agentLevel['two_brokerage_percent'];
            case 3:
                return 0; // 三级佣金比例，可以根据需要配置
            default:
                return 0;
        }
    }
    
    /**
     * 添加佣金记录
     * @param int $agentId 代理ID
     * @param int $fromUid 来源用户ID
     * @param string $type 佣金类型
     * @param float $amount 佣金金额
     * @param int $level 推广级别
     * @param array $extra 额外信息
     * @return bool
     */
    public function addBrokerageRecord($agentId, $fromUid, $type, $amount, $level = 1, $extra = [])
    {
        $fromUser = Db::name('user')->where('id', $fromUid)->find();
        
        $data = [
            'uid' => $agentId,
            'link_id' => $fromUid,
            'type' => $type,
            'title' => $this->getBrokerageTitle($type, $level),
            'price' => $amount,
            'mark' => $this->getBrokerageMark($type, $fromUser['nickname'] ?: $fromUser['username'], $amount, $extra),
            'status' => 1,
            'frozen_time' => 0,
            'add_time' => time(),
            'pm' => 1
        ];
        
        $result = Db::name('user_brokerage')->insert($data);
        
        if ($result) {
            // 更新用户佣金余额
            Db::name('user')->where('id', $agentId)->setInc('brokerage_price', $amount);
        }
        
        return $result;
    }
    
    /**
     * 获取佣金标题
     * @param string $type 佣金类型
     * @param int $level 推广级别
     * @return string
     */
    private function getBrokerageTitle($type, $level)
    {
        $levelText = ['', '一级', '二级', '三级'];
        $typeText = self::BROKERAGE_TYPES[$type] ?? '推广佣金';
        
        return $levelText[$level] . $typeText;
    }
    
    /**
     * 获取佣金备注
     * @param string $type 佣金类型
     * @param string $nickname 用户昵称
     * @param float $amount 佣金金额
     * @param array $extra 额外信息
     * @return string
     */
    private function getBrokerageMark($type, $nickname, $amount, $extra = [])
    {
        switch ($type) {
            case 'register':
                return "推广用户{$nickname}注册成功，获得佣金{$amount}元";
            case 'recharge':
                $rechargeAmount = $extra['recharge_amount'] ?? 0;
                return "推广用户{$nickname}充值{$rechargeAmount}元，获得佣金{$amount}元";
            case 'game_bet':
                $betAmount = $extra['bet_amount'] ?? 0;
                return "推广用户{$nickname}游戏投注{$betAmount}元，获得佣金{$amount}元";
            default:
                return "推广用户{$nickname}，获得佣金{$amount}元";
        }
    }
    
    /**
     * 获取佣金列表
     * @param int $agentId 代理ID
     * @param array $where 查询条件
     * @param int $page 页码
     * @param int $limit 每页数量
     * @return array
     */
    public function getBrokerageList($agentId, $where = [], $page = 1, $limit = 20)
    {
        $query = Db::name('user_brokerage')->where('uid', $agentId);
        
        if (!empty($where['type'])) {
            $query->where('type', $where['type']);
        }
        
        if (!empty($where['status'])) {
            $query->where('status', $where['status']);
        }
        
        if (!empty($where['start_time'])) {
            $query->where('add_time', '>=', strtotime($where['start_time']));
        }
        
        if (!empty($where['end_time'])) {
            $query->where('add_time', '<=', strtotime($where['end_time']));
        }
        
        $total = $query->count();
        $list = $query->order('add_time desc')
            ->page($page, $limit)
            ->select();
            
        foreach ($list as &$item) {
            $item['add_time_text'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['status_text'] = $item['status'] ? '正常' : '冻结';
            $item['pm_text'] = $item['pm'] ? '收入' : '支出';
        }
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }
    
    /**
     * 冻结佣金
     * @param int $id 佣金记录ID
     * @param string $reason 冻结原因
     * @return bool
     */
    public function freezeBrokerage($id, $reason = '')
    {
        $brokerage = Db::name('user_brokerage')->where('id', $id)->find();
        
        if (!$brokerage || $brokerage['status'] == 0) {
            return false;
        }
        
        $result = Db::name('user_brokerage')->where('id', $id)->update([
            'status' => 0,
            'frozen_time' => time(),
            'mark' => $brokerage['mark'] . ' [冻结原因：' . $reason . ']'
        ]);
        
        if ($result) {
            // 减少用户佣金余额
            Db::name('user')->where('id', $brokerage['uid'])->setDec('brokerage_price', $brokerage['price']);
        }
        
        return $result;
    }
    
    /**
     * 解冻佣金
     * @param int $id 佣金记录ID
     * @return bool
     */
    public function unfreezeBrokerage($id)
    {
        $brokerage = Db::name('user_brokerage')->where('id', $id)->find();
        
        if (!$brokerage || $brokerage['status'] == 1) {
            return false;
        }
        
        $result = Db::name('user_brokerage')->where('id', $id)->update([
            'status' => 1,
            'frozen_time' => 0
        ]);
        
        if ($result) {
            // 增加用户佣金余额
            Db::name('user')->where('id', $brokerage['uid'])->setInc('brokerage_price', $brokerage['price']);
        }
        
        return $result;
    }
    
    /**
     * 获取佣金统计
     * @param int $agentId 代理ID
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return array
     */
    public function getBrokerageStats($agentId, $startDate = '', $endDate = '')
    {
        $query = Db::name('user_brokerage')->where('uid', $agentId)->where('pm', 1);
        
        if ($startDate) {
            $query->where('add_time', '>=', strtotime($startDate));
        }
        
        if ($endDate) {
            $query->where('add_time', '<=', strtotime($endDate));
        }
        
        $totalBrokerage = $query->sum('price') ?: 0;
        $totalCount = $query->count();
        $frozenBrokerage = $query->where('status', 0)->sum('price') ?: 0;
        $availableBrokerage = $totalBrokerage - $frozenBrokerage;
        
        return [
            'total_brokerage' => $totalBrokerage,
            'total_count' => $totalCount,
            'frozen_brokerage' => $frozenBrokerage,
            'available_brokerage' => $availableBrokerage
        ];
    }
    
    /**
     * 获取代理总佣金
     * @param int $agentId 代理ID
     * @return float
     */
    public function getTotalBrokerage($agentId)
    {
        $total = Db::name('user_brokerage')
            ->where('uid', $agentId)
            ->where('status', 1)
            ->where('pm', 1)
            ->sum('price');

        return floatval($total);
    }

    /**
     * 获取佣金配置
     * @return array
     */
    private function getBrokerageConfig()
    {
        // 这里可以从配置表或缓存中获取佣金配置
        return [
            'register_brokerage' => 10, // 注册佣金
            'recharge_rate' => 0.5, // 充值佣金比例
            'game_bet_rate' => 0.1 // 游戏投注佣金比例
        ];
    }
}
