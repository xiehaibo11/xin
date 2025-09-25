<?php

namespace app\attack\controller;

use app\attack\model\Auto;
use app\attack\model\Buy;
use app\attack\model\Room;
use app\attack\model\RoomUser;
use app\common\model\UserAction;
use app\attack\model\BuyNpc;
use app\common\model\UserBag;
use app\index\model\User;
use core\Setting;
use think\Db;

class Index extends Base
{
    public function __construct()
    {
        $this->param = request()->param();
        $this->post = request()->post();
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * 首页
     */
    public function index()
    {
        //判断当前用户是否有未完成的对局
        (new Buy())->checkBuy($this->user['id']);
        $betting_info = (new Buy())
            ->where('create_time', '>', date('Y-m-d H:i:s', time() - 10))
            ->where('(status = 0 and userid = '. $this->user['id'] . ') or  (status = 0 and enemy_userid = ' .$this->user['id']. ')')
            ->find();
        if (!empty($betting_info)) {
           return redirect(url('playIndex'));
        }
        return $this->fetch('index', ['title' => '攻城']);
    }

    /**
     * 添加投注记录
     */
    public function addBettingNpc()
    {
        if ($this->request->isGet()) {
            $model = new BuyNpc();
            $data = $this->param;
            $data['userid'] = $this->user['id'];
            $res = $model->add($data);
            if (!$res['code']) {
                return json(['err' => 1, 'msg' => $res['msg']]);
            }
            return json(['err' => 0, 'enemy_betting' => $res['enemy_betting'], 'status' => $res['status'], 'bonus' => $res['bonus'], 'money' => $res['afterMoney']]);
        }
    }

    /**
     * 获取会员信息
     */
    public function getInfo()
    {
        $setting = Setting::get(['free_coupon_coin']);
        $coupon = (new UserBag())->where('param_name', 'poker_coupon')->where('userid', $this->user['id'])->find();
        $data = [
            'id' => $this->user['id'],
            'money' => intval($this->user['money']),
            'nickname' => $this->user['nickname'],
            'coupon' => empty($coupon) ? 0 : $coupon['num'],
            'coupon_coin' => $setting['free_coupon_coin']
        ];
        return json($data);
    }

    /**
     * NPC - 开奖记录
     */
    public function getHistoryCode($page = 1)
    {
        $model = new BuyNpc();
        $pageSize = 10;
        $res = $model->field('id,enemy_betting,create_time')->where('userid', $this->user['id'])->order('create_time','desc')->paginate($pageSize, false, ['page' => $page]);
        $res = $res->toArray();
        return json(['err' => 0, 'data' => $res]);
    }

    /**
     * NPC - 获取游戏记录
     */
    public function getNpcRecord($page = 1)
    {
        $model = new BuyNpc();
        $pageSize = 10;
        $list = $model->field('id,userid,betting,enemy_betting,create_time, money, bonus, status')->where('userid', $this->user['id'])->order(['create_time' => 'desc'])->paginate($pageSize, false, ['page' => $page]);
        $list = $list->toArray();
        return json(['err' => 0, 'data' => $list]);
    }

    /**
     * 对战 - 开奖记录
     */
    public function getEnemyHistory($page = 1)
    {
        $model = new Buy();
        $pageSize = 10;
        $res = $model->field('id, betting, enemy_betting, create_time')->where('userid', $this->user['id'])->order('create_time','desc')
            ->paginate($pageSize, false, ['page' => $page])->each(function($item, $key){
                $item->win_status = (new Buy())->getWinStatus($item);
            });
        $res = $res->toArray();

        return json(['err' => 0, 'data' => $res]);
    }

    /**
     * 对战 - 获取游戏记录
     */
    public function getRecord($page = 1)
    {
        $model = new Buy();
        $pageSize = 10;
        $list = $model->field('id,userid,betting,enemy_betting,create_time, money, bonus, status')->where('userid', $this->user['id'])->order(['create_time' => 'desc'])
            ->paginate($pageSize, false, ['page' => $page])->each(function($item, $key){
                $item->win_status = (new Buy())->getWinStatus($item);
            });
        $list = $list->toArray();
        return json(['err' => 0, 'data' => $list]);
    }

    /**
     * 获取会员信息
     */
    public function getInitInfo()
    {
        $data = [
            'id' => $this->user['id'],
            'money' => intval($this->user['money']),
            'nickname' => $this->user['nickname']
        ];
        //判断当前用户是否有未完成的对局
        $betting_info = (new Buy())
            ->where('create_time', '>', date('Y-m-d H:i:s', time() - 30))
            ->where('(status = 0 and userid = '. $this->user['id'] . ') or (status = 0 and enemy_userid = ' . $this->user['id'] . ')')
            ->find();
        if (!empty($betting_info)) {
            if ($betting_info['userid'] == $this->user['id']) {
                $enemy_userid = $betting_info['enemy_userid'];
            } else {
                $enemy_userid = $betting_info['userid'];
            }
            $enemy_info = (new RoomUser())->where('userid', $enemy_userid)->find();
            if (!empty($enemy_info)) {
                (new Room())->allotRoom($enemy_info['room_id']);
            }
        }
        return json($data);
    }

    /**
     * 玩家对战首页
     */
    public function playIndex()
    {
//        $model = new Auto();
//        $res = $model->delete_info($this->user['id']);

        //检测是否是房主
        $user_info = (new RoomUser())->where('userid', $this->user['id'])->find();
        if (empty($user_info)) {
            $create_user = (new Room())->where('create_user', $this->user['id'])->find();
            if (!empty($create_user)) {
                (new Room())->allotRoom($create_user['id']);
            } else {
                return redirect(url('index'));
            }
        }
        //判断当前用户是否有未完成的对局
        $betting_info = (new Buy())
            ->where('create_time', '>', date('Y-m-d H:i:s', time() - 30))
            ->where('(status = 0 and userid = '. $this->user['id'] . ') or (status = 0 and enemy_userid = ' . $this->user['id'] . ')')
            ->find();
        if (!empty($betting_info)) {
            if ($betting_info['userid'] == $this->user['id']) {
                $enemy_userid = $betting_info['enemy_userid'];
            } else {
                $enemy_userid = $betting_info['userid'];
            }
            $enemy_info = (new RoomUser())->where('userid', $enemy_userid)->find();
            if (!empty($enemy_info)) {
                (new Room())->allotRoom($enemy_info['room_id']);
            }
        }
        return $this->fetch('play_index', ['title' => '欢乐攻城']);
    }

    /**
     * 创建房间
     */
    public function createRoom()
    {
        $model = new Room();
        $data['create_user'] = $this->user['id'];
        $data['password'] = getRandChar(6);
        $res = $model->add($data);
        if (!$res['code']) {
            return json(['err' => 1, 'msg' => $res['msg']]);
        }
        return json(['err' => 0, 'password' => $res['password']]);
    }

    /**
     * 进入房间 -- 密码房
     */
    public function joinRoom()
    {
        if ($this->request->isGet()) {
            $model = new Room();
            $data = $this->param;
            $data['user_id'] = $this->user['id'];
            $res = $model->joinRoom($data);
            if (!$res['code']) {
                return json(['err' => 1, 'msg' => $res['msg']]);
            }
            return json(['err' => 0]);
        }
    }

    /**
     * 轮询 - 监听用户信息
     * 用户的离开加入情况
     */
    public function getRoomUser()
    {
        $res = (new RoomUser())->getRoomUser($this->user['id']);
        $res['live'] = (new Room())->updateAction($this->user['id']);//更新活动时间  判断当前用户是否失去连接
        return json($res);
    }

    /**
     * 准备
     */
    public function readyBetting()
    {
        if (!isset($this->param['money']) || !$this->param['money']) return json(['err' => 1, 'msg' => '请选择投注金额']);

        $room_user = (new RoomUser())->where('userid', $this->user['id'])->find();
        if (empty($room_user)) return json(['err' => 1, 'msg' => '非法请求']);
        if ($room_user['ready']) return json(['err' => 0, 'msg' => '已准备']);
        $room_all_length = (new RoomUser())->where('room_id', $room_user['room_id'])->count();
        if ($room_all_length != 2) {
            return json(['err' => 1, 'msg' => '请等待对手进入房间再准备!']);
        }
        $res = (new RoomUser())->update([
            'ready' => 1,
            'ready_money' => $this->param['money'],
            'id' => $room_user['id']
        ]);
        if (!$res) {
            return json(['err' => 1, 'msg' => '数据执行错误']);
        }
        return json(['err' => 0, 'msg' => '已准备']);
    }

    /**
     * 长轮询  获取对手是否准备
     */
    public function checkEnemyReady()
    {
        $room_user = (new RoomUser())->where('userid', $this->user['id'])->find();
        if (!$room_user['ready']) return json(['err' => 1, 'msg' => '未准备']);

        $start_time = time();
        set_time_limit(0);//无限请求超时时间
        while (true){
            sleep(1);
            $room_enemy_user = (new RoomUser())->where('room_id', $room_user['room_id'])->where('userid','neq', $this->user['id'])->find();
            if (empty($room_enemy_user)) {
                (new RoomUser())->where('userid', $this->user['id'])
                    ->update(['ready' => 0, 'ready_money' => 0, 'record_id' => 0]);//完成对局初始化
                return json(['err' => 1, 'msg' => '对手已离开']);
                exit();
            }
            if  ($room_enemy_user['ready'] == 1) {
                $res = (new Buy())->addReady($room_user['room_id'], $this->user['id'] );
                if (!$res['code']) {
                    return json(['err' => 1, 'msg' => $res['msg']]);
                } else {
                    $user = User::where('sid',session('sid'))->find();
                    return json(['err' => 0, 'msg' => $res['msg'], 'betting_money' => $res['money'], 'money' => $user['money'], 'time' => 30]);
                }
                exit();
            } elseif ($room_enemy_user['ready'] == 2) {
                $betting_info = (new Buy())->where('status = 0 and ((userid = '. $this->user['id'] . ' and enemy_userid = ' .$room_enemy_user['userid']. ') or (userid = '. $room_enemy_user['userid'] . ' and enemy_userid = ' .$this->user['id']. '))')->find();
                if (empty($betting_info)) {
                    return json(['err' => 1, 'msg' => '对局已结算']);
                    exit();
                }
                $user = User::where('sid',session('sid'))->find();
                return json(['err' => 0, 'msg' => '双方已准备,请出兵', 'betting_money' => $betting_info['money'], 'money' => $user['money'], 'time' => (new Buy())->getSurplusTime($betting_info->getData('create_time'))]);
                exit();
            }
            //10秒未准备 则返回前台
            $now_time = time();
            if(($now_time - $start_time) > 10){
                return json(['err' => 2, 'msg' => '请求超时']);
                exit();
            }
        }

    }

    /**
     * 出兵
     */
    public function addBetting()
    {
        if ($this->request->isGet()) {
            $model = new Buy();
            $data = $this->param;
            $res = $model->add($data, $this->user['id']);
            if (!$res['code']) {
                return json(['err' => 1, 'msg' => $res['msg']]);
            }
            return json(['err' => 0]);
        }

    }

    /**
     * 长轮询  获取对手是否出兵 并返回结果
     */
    public function checkEnemyBetting()
    {
        $model = new Buy();
        $userid = $this->user['id'];
        $room_user = (new RoomUser())->where('userid', $userid)->find();
        if (!$room_user['record_id']) return json(['err' => 1, 'msg' => '您当前没有未完成的对局!']);
        $start_time = time();
        set_time_limit(0);//无限请求超时时间
        while (true){
            sleep(1);
            $betting_info = $model->find($room_user['record_id']);
            $surplusTime = (new Buy())->getSurplusTime($betting_info->getData('create_time'));
            if (($betting_info['enemy_betting'] and $betting_info['betting']) || $surplusTime <= 0) {//双方已出兵  发奖
                $award_res = $model->openAward($betting_info);
                $user = User::where('sid', session('sid'))->find();
                if ($award_res['code']) {
                    (new RoomUser())->where('userid', $userid)
                        ->update(['ready' => 0, 'ready_money' => 0, 'record_id' => 0]);//完成对局初始化
                    if ($award_res['code'] == 2) {
                        return json(['err' => 0, 'win_status' => 7, 'money' => $user['money']]);
                        exit();
                    }

                    if ($userid == $betting_info['userid']) {
                        $res = $model->award($betting_info->getData('betting'), $betting_info->getData('enemy_betting'));
                    } else {
                        $res = $model->award($betting_info->getData('enemy_betting'), $betting_info->getData('betting'));
                    }
                    return json(['err' => 0, 'win_status' => $res['win_status'], 'enemy_betting' => $res['enemy_betting'], 'money' => $user['money'], 'bonus' => $award_res['bonus']]);
                    exit();
                }
            }

            //10秒未准备 则返回前台
            $now_time = time();
            if(($now_time - $start_time) > 30){
                return json(['err' => 2, 'msg' => '请求超时']);
                exit();
            }
        }
    }

    /**
     * 托管
     */
    public function addAuto()
    {
        if ($this->request->isGet()) {
            $model = new Auto();
            $data = $this->param;
            $res = $model->add($data, $this->user['id']);
            if (!$res['code']) {
                return json(['err' => 1, 'msg' => $res['msg']]);
            }
            return json(['err' => 0]);
        }

    }

    /**
     * 托管
     */
    public function removeAuto()
    {
        $model = new Auto();
        $res = $model->delete_info($this->user['id']);
        if($res['code']) {
            return json(['err' => 0, 'msg' => '删除成功']);
        }else{
            return json(['err' => 1, 'msg' => '删除失败']);
        }
    }



}