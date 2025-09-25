<?php
namespace app\common\model;

use app\admin\model\Ext;
use core\Setting;
use think\Model;
use app\index\model\User;
use app\admin\model\ExtShowList;

class MoneyHistory extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['username'];
    }
    
    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['nickname'];
    }
    
    /**
     * 获取器--获取模块
     */
    public function getExtNameAttr($value, $data)
    {
        
        $ext = ltrim($data['ext_name'], '/');
        $ext = [$ext,'/'.$ext];
        $res = (new ExtShowList)->where('name', 'in', $ext)->column('title');

        $res = empty($res) ? '系统' : $res[0];
        return $res;
    }

    /**获取器： */
    public function getLotteryNameAttr($value, $data)
    {
        $remark = explode(':', $data['remark']);
        if (!isset($remark[1]))  return '未知彩种';
        $lottery = explode('|', $remark[1]);
        $name = strtolower($lottery[0]);
        $res = (new ExtShowList)->field('title,image')->where('name', 'in', [$name, '/'.$name])->where('type',1)->find();
        if(!$res){
            return '未知彩种';
        }
        return $res->title;

    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getPhotoAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['photo'];
    }

    /**
     * 获取资金明细中type对应的类型
     */
    public function getTypeList()
    {
        $type = [
            0 => '投注',
            1 => '中奖',
            2 => '充值',
            3 => '兑换',
            4 => '活动',
            5 => '提成',
            6 => '返点',
            7 => '游戏币转出',
            8 => '游戏币转入',
        ];
        return $type;
    }

    /**
     * 获取资金明细中type对应的类型
     */
    public function getTypeNameAttr($value,$data)
    {
        $type = $this->getTypeList();
        return $type[$data['type']];
    }

    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }
    /**
     * 获取充值金额减去1%的手续费
     */

     public function getRechargeAttr($value, $data)
     {
        $money = $data['total'] * 0.99;
        return $money;
     }

    /**
     * 明细生成
     * @param  array  $data
     * @return array
     */
    public function write($data = []) {
        $data['ext_name'] = isset($data['ext_name']) ? $data['ext_name'] : Request()->module();
        $exe_info = ExtShowList::where('name', $data['ext_name'])->whereOr('name', '/' . $data['ext_name'])->find();
        if ($exe_info and $exe_info['type'] == 0)  return (new GameMoneyHistory)->write($data);
        $userid = $data['userid'];

        $user = User::get($userid);
        if (!$user) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }

        //获取操作前后金额
        $data['before'] = $user['money'];
        $data['after'] = $data['before'] + $data['money'];

        if ($data['after'] < 0) {
            return ['code' => 0, 'msg' => '用户余额不足'];
        }
        //设置用户金额
        $user->money = $data['after'];
        /**累积中奖 */
        if(isset($data['type']) && $data['type'] == 1){
            $awardNum = $user->award + $data['money'];
            $user->award = $this->dealForm($awardNum);
        }
        /**累积战绩 */
        if(isset($data['hm'])){
            $recordNum = $user->record + $data['money'];
            $user->record = $this->dealForm($recordNum);
            unset($data['hm']); 
        }
        $user->save();
        $data['create_ip'] = request()->ip();

        if($data['ext_name'] == 'game'){
            $data['ext_name'] .= '/'.strtolower(Request()->controller());
        }
        $result = $this->validate([
            'userid|用户'  => 'require',
            'money|金额' => 'number',
            'ext_name|模块名' => 'alphaDash',
            'before' => 'number',
            'after' => 'number',
        ])->insert($data);

        if (false === $result) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '记录添加成功','afterMoney' => $data['after'], 'beforeMoney' => $data['before']];
    }

    /**处理小数点后位数 */
    public function dealForm($num)
    {
        $localKey = mb_stripos($num, '.');
        if($localKey){
            $num = mb_substr($num, 0, $localKey + 3);
        }
        return $num;
    }

    public function getList($words = '', $starttime = '', $endtime = '', $pagesize = 14, $where = '', $order = '')
    {
        $data = request()->get();
        if ($words || !empty($data['words'])) {
            $words = !empty($data['words']) ? $data['words'] : !$words;
            $userid =(new User)->where('username|nickname', 'like', "%$words%")->column('id');

            $this->where(function($query) use ($words, $userid){
                $query->whereOr('userid', 'in', $userid)
                ->whereOr('create_ip|remark', 'like', "%{$words}%");
            });
        }
        if ($starttime|| !empty($data['starttime'])) {
            $starttime = !empty($data['starttime']) ? $data['starttime'] : !$starttime;
            $this->where('create_time', '>=', $starttime);
        }
        if ($endtime || !empty($data['endtime'])) {
            $endtime = !empty($data['endtime']) ? $data['endtime'] : !$endtime;
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->where('create_time', '<', $endtime);
        }
        if(!empty($data['ext'])){
            $ext = ltrim($data['ext'],'/');
            $ext = [$ext, '/'.$ext];
            $this->where('ext_name', 'in' ,$ext);
        }
        if($where){
            $this->where($where);
        }
        if ($order) {
            $this->order($order);
        }
        if (!$pagesize) {
            $list = $this->order('id desc')->select();
        } else {
            $list = $this->order('id desc')->paginate($pagesize, false, ['query' => request()->get()]);
        }
        return $list;
    }

    /**
     * 资金明细变化总数
     * @param array $where 内包含所有用户的资金明细变化总数
     */
    public function getSum($where){
        return  $this->field('Sum(money) as total')->where($where)->find();
     }
      /**
     * 资金明细
     * @param array $where 内包含所有用户的资金明细变化总数
     */
    public function agentList($where){
        return  $this->field('userid,money,remark,create_time,type')->where($where)->order("id desc")->paginate(14);
    }

    /**
     * 获取战绩排行数据 
     *@param array $where 查询条件
     *@param int $pagesize 每页显示数量
     *@retrun  
     */
    public function listSort($where = [], $pagesize = 10)
    {
        return $this->field("userid, SUM(money) as money")->where($where)->where('type = 1 or type =4')->having('money > 0')->group("userid")->order('money desc, userid asc')->paginate($pagesize,100);
    }

    /**获取自己的排行数 */
    public function getSelfRanking($userid)
    {
        $info =  $this->field("SUM(money) as money")->where(['create_time' => ['>=' ,date("Y-m-d")], 'userid' => $userid])->where('type = 1 or type =4')->find()->toarray();
        $money = 0;
        if($info['money']){
            $money = $info['money'];
        }
        $count = $this->field("SUM(money) as money")->where(['create_time' => ['>=' ,date("Y-m-d")]])->where('type = 1 or type =4')->having('money > ' .$money)->group("userid")->count();
        return ['sort' => $count + 1, 'money'=> $money];
    }

    /**获取总的统计数据 */
    public function getStaisc($userid = '', $timeway = '')
    {
        $typeToStr = ['spend', 'award', 'recharge', 'change'];
        $data = [];
        foreach ($typeToStr as $key => $value) {
            $data[$value] = $this->getMoneySumByType($key, $userid, $timeway) ?? 0;
        }
        return $data;
    }

    public function getMoneySumByType($type = '', $userid = '',$timeway)
    {
        if($timeway){
            $this->where(['create_time' => $timeway]);
        }
        if (!empty($userid)) {
            if (is_array($userid)) {
                $this->where('userid', 'in', $userid);
            } else {
                $this->where('userid', $userid);
            }
        } else {
            $test_user = (new \app\admin\model\User())->where('type', 0)->column('id');
            if (!empty($test_user)) {
                $this->whereNotIn('userid', $test_user);
            }
        }
        $res = $this->where('type', $type)->sum('money');
        $res = $res ? round($res, 2) : 0;
        if($type == 0 || $type == 3){
            $res = $res ? -$res : 0;
        }
        return $res;
    }
    /**
     * @param string $start
     * @param string $end
     * @param string $type
     * @param string $ext
     * @return float|int
     */
    public function getSumMoney($start = '', $end = '', $type = '', $ext = '')
    {
        if(!empty($start)){
            $this->where('create_time', '>=', $start);
        } 
        if(!empty($end)){
            $this->where('create_time', '<=', $end." 23:59:59");
        }
        if(!empty($type)){
            $type = $type == 6 ? 0 : $type;
            $this->where('type',$type);
        }
        if(!empty($ext)){
            $this->where('ext_name','in', $ext);
        }
        return  $this->sum("money");
        
    }
   
   
    /**
     * 每天更新统计数据
     * @param  $time  统计的时间
     * @param $type 是否只更新指定时间
     */
    public function newSatis($time = '', $type = false)
    {
        $time = $time ? date("Y-m-d", (strtotime($time) + 3600*24)) : date("Y-m-d");
        $time = (strtotime($time) >= time()) ?  date("Y-m-d") : $time;//防止指定时间超过今天
        $statis = new Statis;
        $last = $statis->order("id DESC")->find();
        $userArray = (new User)->whereIn('type', [1, 2])->column(['id']);
        /**查询统计表中数据是否有 */
        if($last){
            /**判断统计时间是否是当天 */
            if($last['statis_time'] == $time.' 00:00:00'){
                return  ['err' => 2, 'msg' => '今天的数据已统计'];
            }
            if (!$type) {//补期
                $time_differ = ceil((strtotime($time) - strtotime($last['statis_time']))/86400);
                for ($i = 1; $i <= $time_differ; $i++) {
                    $my_time = date("Y-m-d", strtotime($last['statis_time']) + 3600*24 * $i);
                    $res = $this->newSatis($my_time, true);
                    if ($res['err']) {
                        $res['time'] = $my_time;
                        return $res;
                    }
                }
                return ['err' => 0];
            }
        } else {//查询资金明细的第一条记录时间
            if (!$type) {
                $info = $this->whereIn('userid', $userArray)->order("id ASC")->find();
                if (!$info) return  ['err' => 3, 'msg' => '没有交易记录'];
                $info_time = date("Y-m-d", strtotime($info['create_time']));
                return $this->newSatis($info_time, true);
            }
        }
        return $this->addSatis($time);
    }

    /**
     * 每天更新统计数据
     * @param  $time  统计的时间
     */
    private function addSatis($time)
    {
        $statis = new Statis;
        $last = $statis->order("id DESC")->find();
        $userArray = (new User)->whereIn('type', [1, 2])->column(['id']);
        $userList = (new User)->whereIn('type', [1, 2])->column(['id','money', 'type']);

        $this->where('create_time', '<', $time);
        $this->where('create_time', '>=', date("Y-m-d", (strtotime($time) - 3600*24)));
        $list = $this->field("SUM(money) as allMoney, userid, type")->whereIn('userid', $userArray)->group('userid,type')->select()->toArray();
        $data = [];
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'send', 'royalty', 'rebate', 'game_out', 'game_in'];
        $statisArr = [];
        $game_model = new GameMoneyHistory();
        $setting = Setting::get(['recharge_award']);
        foreach ($list as $key => $value) {
            if(!array_key_exists($value['userid'], $userList)){
                continue;
            }
            if(!isset($data[$value['userid']]['money'])){
                $startis = $statis->field('spend,award,recharge,change,send,royalty,rebate,game_out,game_in')->where('userid', $value['userid'])->order("id DESC")->find();
                if($startis){
                    $statisArr[$value['userid']] = $startis->toArray();
                }
                $data[$value['userid']]['userid'] = $value['userid'];
                $data[$value['userid']]['user_type'] = $userList[$value['userid']]['type'];
                $after = $this->where('userid', $value['userid'])->where('create_time', "<", $time)->order("create_time DESC")->find();
                $data[$value['userid']]['money'] = empty($after) ? 0 : $after['after'];//上次统计余额
                $game_after = $game_model->where('userid', $value['userid'])->where('create_time', "<", $time)->order("create_time DESC")->find();
                $data[$value['userid']]['game_money'] = empty($game_after) ? 0 : floatval($game_after['after']);//上次游戏币余额
            }
            $data[$value['userid']][$typeToStr[$value['type']]] = round(abs($value['allMoney']), 4);
        }
        foreach ($data as $key => &$value) {
            unset($userList[$value['userid']]);
            if(isset($statisArr[$value['userid']])){
                $stt = $statisArr[$value['userid']];
                $value['spend'] = $this->initData($value, $stt, 'spend');
                $value['award'] = $this->initData($value, $stt, 'award');
                $value['recharge'] = $this->initData($value, $stt, 'recharge');
                $value['change'] = $this->initData($value, $stt, 'change');
                $value['send'] = $this->initData($value, $stt, 'send');
                $value['royalty'] = $this->initData($value, $stt, 'royalty');
                $value['rebate'] = $this->initData($value, $stt, 'rebate');
                $value['game_out'] = $this->initData($value, $stt, 'game_out');
                $value['game_in'] = $this->initData($value, $stt, 'game_in');
            }
            $value['gain'] = $value['award'] - $value['spend'] + $value['send'] + $value['rebate'] + $value['royalty'] - $value['game_out'] + $value['game_in'] + $value['game_money']/$setting['recharge_award'];
            $value['statis_time'] = $time;
        }

        /**处理没有资金明细但是有过头统计数据的人*/
        if(!empty($userList) && !empty($last)){
            foreach ($userList as $keyuser => $values) {
                $res = $statis->where('userid', $keyuser)->order("id DESC")->find();
                if($res){
                    $res = $res->toArray();
                    $data[$keyuser] = $res;
                    unset($data[$keyuser]['id']);
                    $data[$keyuser]['statis_time'] = $time;
                }
            }
        }
        $res = $statis->allowField(true)->saveAll($data);
        if($res){
            return ['err' => 0];
        }
        return ['err' => 1];
    }

    /** 处理数据小数点*/
    public function initData($data, $stt = '', $field = '')
    {
        $res = round(isset($data[$field]) ? ($data[$field] + floatval($stt[$field])) : floatval($stt[$field]),2);
        //$res = round(floatval($stt[$field], 2));
        return $res;
    }

}
