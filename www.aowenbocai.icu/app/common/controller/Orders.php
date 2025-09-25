<?php
namespace app\common\controller;

use app\common\model\MoneyHistory;
use app\lottery\model\LotteryCom;
use app\web\model\User;
use think\Controller;
use app\index\model\ExtShowList;
use app\lottery\controller\Lottery;
use core\Setting;

class Orders extends Controller
{
    private $user;
    public function __construct()
    {
        parent::__construct();
        $user = User::get(['sid' => session('sid')]);
        $this->user = $user;
    }

    /**
      *  自购类订单详情
     */
    public function index()
    {
        $data = request()->get();

        $find = $this->getDetails($data['id'], $data['lottery_id']);
        if(isset($find['err'])){
            $this->error('您没有权限查看该订单');
        }
        $view = $find['is_join'] == 1 ? 'join' : 'index';
        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['isGain','lottery_unit','coin_lottery']);
        $this->assign('system', collection($system));
       return $this->fetch($view, ['data' => collection($find)]);
    }

     /**订单详情的数据处理 */
     public function getDetails($id, $lottery_id)
     {
         $data['id'] = $id;
         $data['lottery_id'] = $lottery_id;
        $lottery = $this->checkLottery($data['id'], $data['lottery_id']);
        /**跟据$lottery中的name初始化类 */
        $buyClass = LotteryCommon::getModel($lottery["name"], 'buy');
        $res = $buyClass->find($data['id']);
        if (!$res) return $this->error('订单已不存在');
        $append = ['nickname','betting', 'expect', 'status_txt'];
        if($res->is_join == 1){
            array_push($append, 'join');
            array_push($append, 'progress');
        }
        $res->append($append);
        if(!$res){
            $this->error('参数错误2');
        }
        $res = $res->toArray();
        $fristBuy = (isset($this->user['id']) && $this->user['id'] == $res['userid']) || session('?admin_sid') ? 1 : 0;
        if($res['is_join'] == 1){
            /**合买查看状态 */
            switch ($res['show']) {
                case '0':
                    $res['isShow'] = 1;
                    break;
                case '1':
                    $res['isShow'] = strtotime($res['end_time']) < time() ? 1 : 0;
                    break;
                case '2':
                    $res['isShow'] =  $fristBuy ? 1 : 0;
                    break;
                case '3':
                    $userids = array_keys($res['join']);
                    $res['isShow'] = in_array($this->user['id'], $userids) ? 1 : 0;
                    break;
            }
        }
        $res['nickname'] = $fristBuy ? $res['nickname'] : mb_substr($res['nickname'], 0, 1, 'utf-8')."***";
        $find = array_merge($lottery, $res);
        if(isset($find['progress'])){
            $find['joinNum'] = count($find['join']);
            $endtime = strtotime($find['end_time']);
            $find['joinStatus'] = $endtime <= time() || $find['progress'] == 100 ? 0 : 1;
        }
        /**处理开奖号码,追号不处理 */
        $expectNum = count($find['expect']);
        $find['ischase'] = $expectNum == 1 ? 0 : 1;
        $lastCode = $find['expect'][0]['code'];
        if(!$find['ischase'] && !empty($lastCode)){
            $find['betting'] = $buyClass->setCode($lastCode, $find['play_num']);
        }
        if (!isset($find['play_num'][0]['multiple'])) {
         $find['show_expect'] = 1;
        } else {
         $find['show_expect'] = 0;
        }
        $find['fristBuy'] = $fristBuy;
        $find['multiple'] = $find['expect'][0]['multiple'];
        return $find;
     }

     /**合买刷新 */
     public function  reIndex($lottery_id, $id)
     {
        $lottery = $this->checkLottery($id, $lottery_id);
        $buyClass = LotteryCommon::getModel($lottery["name"], 'buy');
        $buyClass = new $buyClass;
        $res = $buyClass->field('status,end_time,id,total_money,total_share,assure_money,ext_name')->where('id',$id)->find();
        $res->status_num = $res->getData('status');
        $append = ['join', 'progress'];
        $res->append($append);
        $res = $res->toarray();
        $res['joinNum'] = count($res['join']);
        $res['joinStatus'] = strtotime($res['end_time']) <= time() || $res['progress'] == 100 ? 0 : 1;
        $res['joinList'] = $this->joinList($lottery["name"],$id);
        return json(['err' => 0, 'data' => $res]);
     }

     /**前台获取地址*/
     public function getJoinList($lotteryid, $buyid)
     {
        $lottery = $this->checkLottery($buyid, $lotteryid);
        $res = $this->joinList($lottery["name"], $buyid);
        $res['err'] = 0;
        return json($res);
     }

     /**获取合买参与人 */
     public function joinList($name, $buyid)
     {
        /**跟据$lottery中的name初始化类  */
        $buyClass = LotteryCommon::getModel($name, 'join');
        $buyClass = new $buyClass;
        $res = $buyClass->where('buy_id', $buyid)->order('id ASC')->paginate(10);
        $res->append(['username', 'user_buy']);
        $res = $res->toArray();
        if(isset($this->user['id'])){
            $first = $buyClass->where('buy_id', $buyid)->where('userid', $this->user['id'])->find();
            $res['first'] = $first ? ($first->append(['user_buy'])->toArray()) : [];
        }
        return $res;
     }

     /**获取期号状态 */
     public function reloadExpect($id, $lottery_id)
     {
        $lottery = $this->checkLottery($id, $lottery_id);
        $ExpectClass = LotteryCommon::getModel($lottery["name"], 'expect');
        $list = (new $ExpectClass)->field('expect,multiple,bonus,status,id,ext_name')->where('buy_id', $id)->order('expect ASC')->select()->append(['newStatus','code']);
        $list = $list->toArray();
        return json(['err' => 0, 'data' => $list]);
     }

     /**判断彩种是否存在 */
     public function checkLottery($id, $lotteryid)
     {
        $id = intval($id);
        if(!$id || !$lotteryid){
            return $this->error('非法操作');
        }
        $lottery = strtolower(explode('|', $lotteryid)[0]);
        $res = (new ExtShowList)->field('name,title,image,remark')->where('name', 'in', [$lottery, '/'.$lottery])->where('type',1)->find();
        if(!$res){
            return $this->error('参数错误1');
        }
        $res->name = $lottery;
        return $res->toArray();
     }
   
     /**
     * 合买购买操作
     */
    public function buyJoin()
    {
        if (request()->isPost()) {
            if(!$this->user){
                return json(['err' => '1', '请先登录']);
            }
            $data = request()->post();
            $lottery = strtolower(explode('|', $data['lottery_id'])[0]);
            $res = (new ExtShowList)->where('name', 'in', [$lottery, '/'.$lottery])->where('status',0)->where('type',1)->find();
            if(!$res){
                $this->error('该彩种暂未开启');
            }
            $buyClass = LotteryCommon::getModel($lottery, 'buy');
            unset($data['lottery_id']);
            $data['userid'] = $this->user['id'];
            $res = $buyClass->joinAdd($data, $this->user);
            return $res;
        }
    }

    /**用户追号停止追某一期 */
    public function stopLottery()
    {
        if (request()->isPost()) {
            if(!$this->user){
                return json(['err' => '1', '请先登录']);
            }
            $data = request()->post();
            $lottery = strtolower(explode('|', $data['lottery_id'])[0]);
            $nameModel = LotteryCommon::getModel($lottery, 'buy');
            $res = $nameModel->field('userid,assure_money,total_money,id,is_join,lottery_id,ext_name')->where('lottery_id', $data['lottery_id'])->find();
            if(!$res){
                return json(['err' => 2, 'msg' => '信息错误']);
            }
            $res = $res->toArray();
            if($res['userid'] != $this->user['id']){
                return json(['err' => 3, 'msg' => '该操作只能发起人本人操作']);
            }
            $result = $nameModel->stopLottery($res, $data['id']);
            return json($result);
        }

    }

    /**撤单 */
    public function returnTicket($lottery_id)
    {
        $name = strtolower(explode('|', $lottery_id)[0]);
        $checkName = (new ExtShowList)->where('name','/'.$name)->where('type', 1)->find();
        if(!$checkName){
            return json(['err' => 5, 'msg' => '该彩种不存在']);
        }
        $buy_model = LotteryCommon::getModel($name, 'buy');
        $buyRes = $buy_model->where('lottery_id', $lottery_id)->find();
        if(!$buyRes){
            return json(['err' => 1, 'msg' => '该单已损坏，无法撤单<01>']);
        }
        if($buyRes['status'] > 1){
            return json(['err' => 1, 'msg' => '当前订单不能撤单<01>']);
        }
        if($buyRes['is_join']){
            return json(['err' => 1, 'msg' => '合买订单不能撤']);
        }
        $buyRes->append(['joinData'])->toArray();
        $joinData = $buyRes['joinData'];
        //判断是否为追号
        $expect_model = LotteryCommon::getModel($name, 'expect');
        $expect_list = $expect_model->where('buy_id', $buyRes['id'])->order('expect asc')->select()->toArray();
        $expect_num = count($expect_list);
        if (!$expect_num) {
            return json(['err' => 1, 'msg' => '该单已损坏，无法撤单<02>']);
        }
        $total_multiple = 0;
        $back_multiple = 0;
        $back_data = [];
        $is_open = 1;//第一期是否截止
       foreach ($expect_list as $key => $value) {
           $total_multiple += $value['multiple'];
            $expect_time = (new Lottery())->issueToTime($name, $value['expect']);
            if (time() < $expect_time and $key == 0) $is_open = 0;
            if (time() < $expect_time and $value['status'] < 2) {
                $back_data[] = $value['id'];
                $back_multiple += $value['multiple'];
            }
        }
        if (empty($back_data)) {
            return json(['err' => 1, 'msg' => '当前订单不能撤单<02>']);
        }
        $backMoney = floatval($buyRes['total_money'] / $total_multiple * 100) * $back_multiple / 100;
        if (!$backMoney) return json(['err' => 1, 'msg'=>'该订单撤单金额为0']);

        $return = [];
        foreach ($joinData as $key => $value) {
            $return[$key] = ['money' => $value['money']/$buyRes['total_money'] * $backMoney, 'userid' => $value['userid'],'remark' => '用户撤单:'.$buyRes['lottery_id']];
        }
        $money = new MoneyHistory();
        foreach ($return as $key => $value) {
            $res = $money->write($value);
            $result = $res['code'] == 1 ? true : false;
        }
        if($result){
            if (!$is_open) {
                $expect_model->whereIn('id', $back_data)->setField('status', 8);
                $buy_model->where('id', $buyRes['id'])->setField('status', 8);
            } else {
                $expect_model->whereIn('id', $back_data)->setField('status', 5);
            }
            return json(['err' => 0, 'msg'=>'撤单成功']);
        }
        return json(['err' => 3, 'msg'=>'撤单失败']);
    }
}




