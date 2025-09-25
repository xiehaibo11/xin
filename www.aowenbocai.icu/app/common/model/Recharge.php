<?php
namespace app\common\model;

use think\Model;
use app\admin\model\User;
use core\Setting;

class Recharge extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /** 
     * 获取器 - 获取用户信息
     */
    public function getUserinfoAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user) $user = $user->toArray();
        switch ($data['statuss']) {
            case 1:
                $status = '<span class = "redSuccess">充值成功</span>';
                break;
            case 0:
                $status = '<span class = "greenError">未完成充值</span>';
                break;
            case 2:
                $status = '<span class = "greenError">拒绝充值</span>';
                break;
        }
        return ['nickname' => $user['nickname'] , 'status' => $status,'username' => $user['username'], 'agents' => $user['agents']];
    }
    
    /**获取充值列表*/
    public function getList()
    {
        return $this->getGetData();
    }

    /**获取充值统计数据*/
    public function getStatis($userid = '')
    {
        return $this->getGetData(1, $userid);
    }


    public function getGetData($type = 0, $userid = '', $where = [])
    {
        $data = request()->get();
        if($data){
            if(isset($data['today'])){
                $this->where('create_time', '>=' , date("Y-m-d"));
            }
            if(!empty($data['starttime'])){
                $this->where('create_time', '>' , $data['starttime']);
            }
            if(!empty($data['endtime'])){
                $this->where('create_time', '<' , $data['endtime']);
            }
            if(isset($data['words']) && $data['words'] != ''){
                $words = $data['words'];
                $user = (new User)->where('nickname|username', 'like', "%{$words}%")->column('id');
                $this->where('userid', 'in', $user);
            }
            if(isset($data['retype']) && $data['retype'] != ''){
                $typeList = ['微信充值', '支付宝充值','微信扫码充值','支付宝扫码充值', '其他三方充值', '银行卡转账充值'];
                $this->where('name', 'like' , "%".$typeList[$data['retype']]."%");
            }
            if(isset($data['reresult']) && $data['reresult'] != ''){
                $this->where('statuss', $data['reresult']);
            }
        }
        if($userid != ''){
            $this->where('userid', 'in', $userid);
        }
        if(!empty($where)){
            $this->where($where);
        }
        // $order=getOrder($data);//排序
        if($type){
            return $this->field("SUM(money) as money, statuss")->group('statuss')->select();
        }else{
            if(!empty($data['sort_field']) || !empty($data['type'])){
                $data['sort_field'] = !empty($data['type']) ? $data['type'] : $data['sort_field'];
                $sort = isset($data['sort_asc']) ? $data['sort_asc'] : $data['sort'];
                $sort = !$sort ?  'desc' : 'asc';
                $order = [
                    $data['sort_field'] => $sort
                ];
            } else {
                $order = [
                    "id" => 'desc'
                ];
            }
            return $this->order($order)->paginate(14, false,['query' => $data]);
        }
    }

    public function agentsSum($userid)
    {
        if($data = request()->get()){
            if(isset($data['today'])){
                $this->where('create_time', '>=' , date("Y-m-d"));
            }
            if(!empty($data['starttime'])){
                $this->where('create_time', '>' , $data['starttime']);
            }
            if(!empty($data['endtime'])){
                $this->where('create_time', '<' , $data['endtime']);
            }
            if(isset($data['retype']) && $data['retype'] != ''){
                $typeList = ['微信充值', '支付宝充值','微信扫码充值','支付宝扫码充值'];
                $this->where('name', 'like' , "%".$typeList[$data['retype']]."%");
            }
            if(isset($data['reresult']) && $data['reresult'] != ''){
                $this->where('statuss', $data['reresult']);
            }
        }
        if(!$userid){
            return 0;
        }
        if(is_array($userid)){
            $this->where('userid', 'in', $userid);
        }else{
            $this->where('userid', $userid);
        }

        return $this->where('statuss',1)->SUM('money');
    }

    /**
     * 充值余额
     * @return Boolean true表示业务处理成功 false表示处理失败
     */
    public function diamonds($money, $userid, $type)
    {
        /**获取系统设置充值比例及对应的额外奖励 */
        $setting = Setting::get(['diamonds_recharge', 'recharge_award', 'recharge_send_times', 'recharge_change']);
        $diamonds_list = json_decode($setting['diamonds_recharge'], true);
        $diamonds = 0;
        $other_money = 0;
        $isInner = 0;/**用于判断赠送金额 */
        $percent = [];
        foreach ($diamonds_list as $value) {
            $percent[$value['money']] = round($value['award']/$value['lottery_money'], 4);
            if($value['money'] == $money){
                $isInner = 1;
                $diamonds = $value['lottery_money'];
                $other_money = $value['award'];
                $allCoin = $value['coin'];
                break;
            }
        }
        krsort($percent);
        /**根据输入金额确认赠送金额 */
        if(!$isInner){
            $diamonds = $money;
            $allCoin =  $money * $setting['recharge_award'];
            foreach ($percent as $key => $val) {
                if($money > $key){
                    $other_money = $diamonds * $val;
                    break;
                }
            }
        }
        /**充值数额对应的余额写入明细 */
        $data = [
            'type' => 2,
            'userid' => $userid,
            'money' => $diamonds,
            'ext_name' => 'pay',
            'remark' => '余额充值'
        ];
        (new MoneyHistory())->write($data);
        if ($other_money) {//充值赠送
            $user_recharge_times = $this->where('userid', $userid)->where('statuss', 1)->whereTime('create_time', '>=', date('Y-m-d', time()))->count('id');
            if ($user_recharge_times < $setting['recharge_send_times']) {
                $data = [
                    'type' => 4,
                    'userid' => $userid,
                    'money' => $other_money,
                    'ext_name' => 'pay',
                    'remark' => '充值赠送'
                ];
                (new MoneyHistory)->write($data);
            }
        }
        //增加会员经验
        (new User())->where('id', $userid)->setInc('spent', $diamonds);
        (new User())->where('id', $userid)->setInc('recharge_money', $diamonds * floatval($setting['recharge_change'])/100);
        /**============结束===========*/
        //转换金豆
        if ($type == 1 || !$type) {
            $data = [
                'type' => 7,
                'userid' => $userid,
                'money' => -$diamonds,
                'ext_name' => 'pay',
                'remark' => '兑换游戏币'
            ];
            $res = (new MoneyHistory())->write($data);
            if ($res['code']) {
                $data = [
                    'type' => 8,
                    'userid' => $userid,
                    'money' => $allCoin,
                    'ext_name' => 'pay',
                    'remark' => '彩金兑换'
                ];
                (new GameMoneyHistory)->write($data);
            }
        }
        return true;
    }

    /**
     * 资金转换
     *  $type  1 彩金转游戏币   2  游戏币转彩金
     *  $num  金额
    */
    public function MoneyChange($userid, $type, $num)
    {
        $setting = Setting::get(['recharge_award']);
        switch ($type) {
            case 1:
                $data = [
                    'type' => 7,
                    'userid' => $userid,
                    'money' => -$num,
                    'remark' => '兑换游戏币'
                ];
                $res = (new MoneyHistory())->write($data);
                $allCoin =  $num * $setting['recharge_award'];
                if ($res['code']) {
                    $data = [
                        'type' => 8,
                        'userid' => $userid,
                        'money' => $allCoin,
                        'remark' => '彩金兑换'
                    ];
                    $res = (new GameMoneyHistory)->write($data);
                    return $res;
                }
                return $res;
                break;
            case 2:
                $money =  $num / $setting['recharge_award'];
                $data = [
                    'type' => 8,
                    'userid' => $userid,
                    'money' => $money,
                    'remark' => '游戏币转换'
                ];
                $res = (new MoneyHistory())->write($data);
                if ($res['code']) {
                    $data = [
                        'type' => 7,
                        'userid' => $userid,
                        'money' => -$num,
                        'remark' => '兑换彩金'
                    ];
                    $res = (new GameMoneyHistory)->write($data);
                    return $res;
                }
                return $res;
                break;
        }
    }
    
}