<?php

namespace app\fa\model;

use app\admin\model\User;
use think\Db;
use think\Model;
use think\Validate;

class RoomUser extends Model
{
    protected $name = 'plugin_fa_user';//表名
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     *  删除未活动的会员
     */
    public function  deleteRoomUser() {
        $res = $this->select();
        foreach ($res as $v) {
            if ($v['action_time'] < date('Y-m-d H:i:s', time() - 15)) {//删除15秒未活动的会员
                $this->where('userid', $v['userid'])->delete();
            }
        }
    }

    /**
     *  获取有人的房间  且人数不超过  7
     */
    public function  getPlaceRoom() {
        $res = $this->field('room_id')->group('room_id')->having('count(id) < 7')->order('count(id) desc')->find();
        return $res;
    }

    /**
     *  获取当前会员的房间人员
     */
    public function  getRoomUser() {
        $user = User::where('sid',session('sid'))->find();
        $res = $this->where('userid', $user['id'])->find();
        if (empty($res)) return ['code' => 0];
        $res = $this->where('room_id', $res['room_id'])->select();
        $buyModel = new Buy();
        $expect = $buyModel->checkExpect();
        $expect = $expect['expect'];
        $user_list = [];
        foreach ($res as $v){
            if ($user['id'] != $v['userid']) {
                if ($v['action_time'] < date('Y-m-d H:i:s', time() - 15)) {//删除15秒未活动的会员
                    $this->where('room_id', $v['room_id'])->where('userid', $v['userid'])->delete();
                }
                $user_info = User::where('id', $v['userid'])->find();
                $betting_code = $buyModel->getExpectBetting($expect, $v['userid']);
                array_push($user_list, [
                    'userid' => $user_info['id'],
                    'nickname' => $user_info['nickname'],
                    'photo' => $user_info['photo'],
                    'betting' => $betting_code
                ]);
            }
        }
        $data = [
            'user_list' => $user_list
        ];
        return ['code' => 1, 'data' => $data];
    }


}
