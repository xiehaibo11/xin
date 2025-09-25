<?php
namespace app\common\model;

use core\Setting;
use think\Model;
use app\index\model\User;

class Statis extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**获取器--获取用户信息 用户上级代理*/
    public function getNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            $user = $user->toArray();
            $type = ['测试会员', '普通会员', '代理会员'];
            return ['username' => $user['username'], 'nickname' => $user['nickname'], 'type' => $type[$user['type']],'agents' =>$user['agents']];
        }
        return ['username' => '...', 'nickname' => '用户不存在', 'type' => '...', 'agents' =>''];
    }
    
    public function getList_1($userid = '', $type = 0)
    {
        $data = request()->get();
        if((empty($data['endtime']) && empty($data['starttime'])) || $type == 1){
            $this->where('statis_time', date("Y-m-d"));
        }
        if($type == 1){
            return $this->field("SUM(spend) as spend,SUM(award) as award,SUM(recharge) as recharge,SUM(send) as send,SUM(percentage) as percentage,SUM(gain) as gain,SUM('change') as _change")->find();
        }
        if($userid != ''){
            $this->where('userid', 'in', $userid);
        }
        /**若前后日期一样则只查当日统计数据 */
        if (!empty($data['starttime'])) {
            $start = $data['starttime'];
            $end = empty($data['endtime']) ?  date("Y-m-d") : $data['endtime'];
            if($start != $end){
                $this->where(function ($query) use ($start, $end){
                    $query->where('statis_time', $start)->whereor('statis_time', $end);
                });
            }else{
                $data['starttime'] = '';
            }
        }
        
        if (!empty($data['endtime']) && empty($data['starttime'])) {
           $this->where('statis_time', $data['endtime']);
        }
        
        if(!empty($data['starttime'])){
            $res = $this->order('statis_time DESC')->select();
            return $res;
        }
        if(!empty($data['sort'])){
            $postSort = explode('_', $data['sort']);
            $sortArr = ['spend+0','award+0','recharge+0','change+0','send+0','percentage+0','gain+0'];
            $ordersort = ['ASC','DESC'];
            $order = $sortArr[$postSort[0]]." ".$ordersort[$postSort[1]];
            $this->order($order);
            
        };
        
        return $this->order('statis_time DESC')->paginate(14, false, ['query' => request()->get()]);
    }

    public function getGraphData($mou,$userid = '')
    {
        $year = date("Y");
        if($mou == 1001){
            $start = $year."-01-01";
            $end = ($year + 1)."-01-01"; 
        }else{
            $mou += 1;
            $nowM = $mou < 10 ? '0'.$mou : $mou;
            $nextM = $mou + 1;
            $nextM = $nextM < 10 ? '0'.$nextM : $nextM;
            $start = $year."-".$nowM."-01";
            $end = $nowM < 12 ? $year."-".$nextM."-01" : ($year + 1)."-01-01"; 
        }
        
        if($userid == ''){
            $userid = (new User)->column("id");
        }
        // 初始化数据
        $data = ['spend' => 0,'recharge' => 0, 'change' => 0,'send' => 0, 'award' => 0, 'percentage' => 0,'gain' => 0];
        foreach($userid as $value){
            $alldata = $this->where('statis_time', '>', $start)->where('statis_time', '<=', $end)->where('userid', $value)->select();
            $alldata = $alldata->toArray();
            $count = count($alldata);
            $spend = $alldata[0]['spend'];
            $recharge = $alldata[0]['recharge'];
            $change = $alldata[0]['change'];
            $send = $alldata[0]['send'];
            $award = $alldata[0]['award'];
            $percentage = $alldata[0]['percentage'];
            $gain = $alldata[0]['gain'];
            if($count > 1){
                $spend -= $alldata[$count-1]['spend'];
                $recharge -= $alldata[$count-1]['recharge'];
                $change -= $alldata[$count-1]['change'];
                $send -= $alldata[$count-1]['send'];
                $award -= $alldata[$count-1]['award'];
                $percentage -= $alldata[$count-1]['percentage'];
                $gain -= $alldata[$count-1]['gain'];
            }
            $data['spend'] += $spend;
            $data['recharge'] += $recharge;
            $data['change'] += $change;
            $data['send'] += $send;
            $data['award'] += $award;
            $data['percentage'] += $percentage;
            $data['gain'] += $gain;
        }
        return $data;
    }

    /**网站总数据查询 */
    public function getAllStatis()
    {
        $data = request()->get();
        if(empty($data['endtime']) && empty($data['starttime'])){
            $this->where('statis_time', date("Y-m-d"));
        }
        $field = "SUM(spend) as spend,SUM(award) as award,SUM(recharge) as recharge,SUM(send) as send,SUM(percentage) as percentage,SUM(gain) as gain,SUM('change') as _change";
        if(!empty($data['startTime'])){
            $start = $this->where('statis_time', $data['startTime'])->field($field)->find()->toArray();
            $end = empty($data['endtime']) ?  date("Y-m-d") : $data['endtime'];
            if($start != $end){
                $end = $this->where('statis_time', $end)->field($field)->find()->toArray();
                $data = [];
                foreach ($end as $key => $value) {
                    $data[$key] = $value - $start[$key];
                }
                return $data;
            }else{
                return $start;
            }
        }
        if(!empty($data['endtime']) && empty($data['startTime'])){
            return $this->where('statis_time', $data['endtime'])->field($field)->find()->toArray();
        };
        return $this->field($field)->find()->toArray();
    }

    /**代理-会员统计 */
    public function agentsMember($userid)
    {
        $data = request()->get();
        $dataNull = [
            'spend' => 0,
            'recharge' => 0,
            '_change' => 0,
            'send' => 0,
            'award' => 0,
            'percentage' => 0,
            'gain' => 0,
        ];
        if(!$userid){
            return $dataNull;
        }
        $field = "SUM(spend) as spend,SUM(award) as award,SUM(recharge) as recharge,SUM(send) as send,SUM(percentage) as percentage,SUM(gain) as gain,SUM(`change`) as _change";
        
        $isStarttime = !empty($data['starttime']);
        if($isStarttime){
            $start = $this->field($field)->where('userid', 'in', $userid)->where('statis_time', $data['starttime'])->select()->toArray()[0];
        }

        $isEndtime = !empty($data['endtime']);
        if($isEndtime){
            $end = $this->field($field)->where('userid', 'in', $userid)->where('statis_time', $data['endtime'])->select()->toArray()[0];
        }

        if(!$isEndtime){
            $time = date("Y-m-d");
            $today = $this->field($field)->where('userid', 'in', $userid)->where('statis_time', $time)->select()->toArray()[0];
        }
        if($isStarttime){
            $finsh = $isEndtime ? $end : $today;
            $return = [
                'spend' => $finsh['spend'] - $start['spend'],
                'recharge' => $finsh['recharge'] - $start['recharge'],
                '_change' => $finsh['_change'] - $start['_change'],
                'send' => $finsh['send'] - $start['send'],
                'award' => $finsh['award'] - $start['award'],
                'percentage' => $finsh['percentage'] - $start['percentage'],
                'gain' => $finsh['gain'] - $start['gain']
            ];
        }else{
            $return = $isEndtime ? $end : $today;
        }
        //格式化 小数点后两位
        foreach ($return as $key => &$value) {
            $localKey = mb_stripos($value, '.');
            if($localKey){
                $value = mb_substr($value, 0, $localKey + 3);
            }
        }
        return $return;
    }

    /**获取代理统计 */
    public function getAgentsStatistics($userid, $is_today = 1, $is_self = false)
    {
        $user_model = (new \app\web\model\User());
        $all_user = $user_model->getAllAgents($userid);//当前会员下面的所有下级
        if ($is_self) $all_user = [$userid];
        $statistics = $this->getStatisticsData($all_user, $is_today);
        $return = $statistics['data'];
        $isStarttime = !empty($statistics['starttime']);
        $isEndtime = !empty($statistics['endtime']);
        if($isStarttime and $isEndtime){
            $return['register_num'] = $user_model->whereIn('id', $all_user)
                ->where('create_time', '>', $statistics['starttime'])
                ->where('create_time', '<', $statistics['endtime'])->count('id');//新注册人数
            $return['agent_num'] = $user_model->whereIn('id', $all_user)->where('id', 'neq', $userid)
                ->where('create_time', '<', $statistics['endtime'])->count('id');//下级人数
        } elseif ($isStarttime and !$isEndtime) {
            $return['register_num'] = $user_model->whereIn('id', $all_user)
                ->where('create_time', '>', $statistics['starttime'])->count('id');//新注册人数
            $return['agent_num'] = count($all_user) - 1;//下级人数
        } elseif (!$isStarttime and $isEndtime) {
            $return['register_num'] = $user_model->whereIn('id', $all_user)
                ->where('create_time', '<', $statistics['endtime'])->count('id');//新注册人数
            $return['agent_num'] = $user_model->whereIn('id', $all_user)->where('id', 'neq', $userid)
                ->where('create_time', '<', $statistics['endtime'])->count('id');//下级人数
        } elseif (!$isStarttime and !$isEndtime) {
            $return['register_num'] = count($all_user) - 1;
            $return['agent_num'] =  $return['register_num'];
        }
        $self_data = $this->getSelfStatistics($userid);
        $return['self_rebate'] = $self_data['rebate'];
        $return['agent_rebate'] = $return['rebate'] - $self_data['rebate'];
        return $return;
    }

    /**获取代理统计 */
    public function getSelfStatistics($userid)
    {
        return $this->getStatisticsData([$userid])['data'];
    }

    /**获取代理统计 */
    public function getStatisticsData($all_user, $is_today = 1)
    {
        $data = request()->param();
        $field = "SUM(spend) as spend,SUM(award) as award,SUM(recharge) as recharge,SUM(send) as send,
        SUM(gain) as gain,SUM(`change`) as _change,SUM(`royalty`) as royalty,SUM(`rebate`) as rebate,SUM(`money`) as money,SUM(`game_money`) as game_money";
        if (isset($data['endtime']) and $data['endtime']) {
            $data['endtime'] = date("Y-m-d", strtotime($data['endtime']) + 3600*24);
        }
        if (isset($data['time']) and $data['time']) {
            switch ($data['time']) {
                case 1://今天
                    $data['starttime'] = date("Y-m-d");
                    $data['endtime'] = 0;
                    break;
                case 2://昨天
                    $data['starttime'] = date("Y-m-d", strtotime("-1 day"));
                    $data['endtime'] = date("Y-m-d");
                    break;
                case 3://本月
                    $data['starttime'] = date("Y-m-01",time());
                    $data['endtime'] = 0;
                    break;
                case 4://上一月
                    $timestamp = time();
                    $data['starttime'] = date('Y-m-01', strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
                    $data['endtime'] = date('Y-m-d', strtotime($data['starttime'] . " +1 month -1 day"));
                    break;
            }
        }

        $isStarttime = !empty($data['starttime']);
        if($isStarttime){
            $start = $this->field($field)->where('userid', 'in', $all_user)->where('statis_time', $data['starttime'])->select()->toArray()[0];
        }

        $isEndtime = !empty($data['endtime']);
        if($isEndtime){
            $end = $this->field($field)->where('userid', 'in', $all_user)->where('statis_time', $data['endtime'])->select()->toArray()[0];
        }
        if(!$isEndtime){
            $time = date("Y-m-d");
            $today = $this->field($field)->where('userid', 'in', $all_user)->where('statis_time', $time)->select()->toArray();
            $my_today = $is_today ? $this->getTodayStatistics($all_user) : [];
            if (!empty($my_today)) {
                $typeToStr = ['spend', 'award', 'recharge', '_change', 'send', 'royalty', 'rebate', 'money', 'gain', 'game_money'];
                foreach ($today as &$v) {
                    foreach ($typeToStr as $row) {
                        $v[$row] += isset($my_today[$row]) ? $my_today[$row] : 0;
                        if ($row == 'money' || $row == 'game_money') $v[$row] = isset($my_today[$row]) ? $my_today[$row] : $v[$row];
                    }
                }
            }
            $today = $today[0];
        }
        $setting = Setting::get(['recharge_award']);
        if($isStarttime){
            $finish = $isEndtime ? $end : $today;
            $return = [
                'spend' => round($finish['spend'] - $start['spend'], 2),
                'recharge' => round($finish['recharge'] - $start['recharge'], 2),
                '_change' => round($finish['_change'] - $start['_change'], 2),
                'send' => round($finish['send'] - $start['send'], 2),
                'award' => round($finish['award'] - $start['award'], 2),
                'money' => round($finish['money'] + $finish['game_money']/$setting['recharge_award'], 2),
                'royalty' => round($finish['royalty'] - $start['royalty'], 2),
                'rebate' => round($finish['rebate'] - $start['rebate'], 2),
                'gain' => round($finish['gain'] - $start['gain'], 2)
            ];
        }else{
            $return = $isEndtime ? $end : $today;
        }
        return ['data' => $return, 'starttime' => $data['starttime'], 'endtime' => $data['endtime']];
    }

    /**
     * 获取今天代理的统计
     * $is_self 是否统计下级
     */
    public function getTodayStatistics($userid, $is_self = false)
    {
        if (!is_array($userid)) {//当前会员下面的所有下级
            $all_user = (new \app\web\model\User())->getAllAgents($userid);
        } else {
            $all_user = $userid;
        }
        if ($is_self) $all_user = [$userid];
        $moneyHistory = new MoneyHistory();
        $today = date("Y-m-d");
        $moneyHistory->field("SUM(money) as allMoney, userid, type");
        $moneyHistory->where('userid', 'IN', $all_user);
        $list = $moneyHistory->where('create_time', '>=', $today)->group('userid,type')->select()->toArray();
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'send', 'royalty', 'rebate', 'game_out', 'game_in'];
        $res = [];
        foreach ($list as $key => $value) {
            if(!isset($res[$value['userid']])){
                $userinfo = \app\web\model\User::get($value['userid']);
                if(!$userinfo){
                    continue;
                }
                $res[$value['userid']]['money'] = $userinfo['money'];
                $res[$value['userid']]['game_money'] = $userinfo['game_money'];
            }
            $res[$value['userid']][$typeToStr[$value['type']]] = round(abs($value['allMoney']), 4);
        }
        foreach ($all_user as $user_v) {
            $userinfo = \app\web\model\User::get($user_v);
            if(!$userinfo){
                continue;
            }
            $res[$user_v]['money'] = $userinfo['money'];
            $res[$user_v]['game_money'] = $userinfo['game_money'];
            foreach ($typeToStr as $row) {
                $res[$user_v][$row] = isset($res[$user_v][$row]) ? $res[$user_v][$row] : 0;
            }
        }
        foreach ($res as &$value) {
            $value['_change'] = $value['change'];
            $value['gain'] = $value['award'] - $value['spend'] + $value['send'] + $value['rebate'] + $value['royalty'] - $value['game_out'] + $value['game_in'];
        }
        $new_res = [];
        foreach ($res as $v) {
            foreach ($v as $key => $row) {
                $new_res[$key] = isset($new_res[$key]) ?  $new_res[$key] + $row : $row;
            }
        }
        return $new_res;
    }


}