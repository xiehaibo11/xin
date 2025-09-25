<?php

namespace app\fa\controller;

use app\common\model\UserAction;
use app\common\model\UserBag;
use app\fa\model\Buy;
use app\fa\model\Code;
use app\fa\model\Room;
use app\fa\model\RoomUser;
use app\index\model\User;
use think\Controller;
use think\Db;

class Index extends Base
{
    public function __construct()
    {
        $this->param = request()->param();
        $this->post = request()->post();
        $this->baseModel = new Buy();//当前模型
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * 大小發首页
     */
    public function index()
    {
        return $this->fetch('index', ['title' => '我要發']);
    }


    /**
     * 添加投注记录
     */
    public function addBetting()
    {
        if ($this->request->isPost()) {
            $data = $this->post;
            $data['userid'] = $this->user['id'];
            $data['code'] = json_decode($data['code'],true);
            $res = $this->baseModel->add($data);
            if (!$res['code']) {
                return json(['code' => 0, 'msg' => $res['msg']]);
            }
            $this->user = Db::name('user')->find($this->user['id']);
            return json(['code' => 1, 'money' => $this->user['money'], 'msg' => '投注成功','expect' => $res['expect']]);
        }
    }

    /**
     * 轮询 - 监听用户信息
     * 用户的投注情况，用户的离开加入情况
     */
    public function getRoomUser()
    {
        $res = (new RoomUser())->getRoomUser();
        $res['live'] = (new Room())->updateAction($this->user['id']);//更新活动时间  判断当前用户是否失去连接
        return json($res);
    }

    /**
     * 获取会员信息
     */
    public function getInfo()
    {
        //用户进入首页  分配房间
        $get_room = (new Room())->allotRoom();
        if (!$get_room['code']) return json(['code' => 0]);
        $this->user = Db::name('user')->find($this->user['id']);
        $setting = \core\Setting::get(['free_coupon_coin']);
        $check_time = $this->baseModel->checkExpect();
        $betting = $this->baseModel->getExpectBetting($check_time['expect'], $this->user['id']);
        $history_code = (new Code)->getHistoryCode();
        $coupon = (new UserBag())->where('param_name', 'fa_coupon')->where('userid', $this->user['id'])->find();
        $data = [
            'code' => 1,
            'data' => [
                'id' => $this->user['id'],
                'money' => $this->user['money'],
                'nickname' => $this->user['nickname'],
                'photo' => $this->user['photo'],
                'back_time' => $check_time['back_time'],
                'expect' => $check_time['expect'],
                'history_code' => $history_code,
                'betting_code' => $betting,
                'coupon' => empty($coupon) ? 0 : $coupon['num'],
                'coupon_coin' => $setting['free_coupon_coin']
            ],
        ];
        return json($data);
    }

    /**
     * 开奖之后获取初始化信息
     */
    public function getInitInfo()
    {
        $check_time = $this->baseModel->checkExpect();
        if ( $check_time['back_time']) {
            $data = [
                'code' => 1,
                'data' => [
                    'back_time' => $check_time['back_time'],
                    'expect' => $check_time['expect']
                ],
            ];
            return json($data);
        } else {
            return json(['code' => 0]);
        }
    }

    /**
     * 获取某期中奖号码
     */
    public function getExpectCode()
    {
        (new Award())->getAwards();
        $expect = $this->post['expect'];
        $code = (new Code())->where('expect', $expect)->find();
        $is_winning = (new Code())->isWinning($expect,$this->user['id']);
        if (!empty($code)) {
            $user = User::where('sid',session('sid'))->find();
            return json(['code' => 1, 'num' => $code['code'], 'is_winning' => $is_winning, 'money' => $user['money']]);
        } else {
            return json(['code' => 0]);
        }
    }

    /**
     * 获取游戏记录
     */
    public function getRecord($page = 1)
    {
        $pageSize = 7;
        $list = $this->baseModel->field('create_time, expect, bonus, code, status')->where('userid', $this->user['id'])->order(['create_time' => 'desc'])->paginate($pageSize, false, ['page' => $page]);
        $list->append(['friend_time', 'code_txt', 'winning_code']);
        return json($list);
    }



}
