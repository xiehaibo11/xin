<?php

namespace app\attack\model;

use app\admin\model\User;
use think\Db;
use think\Model;
use think\Validate;

class RoomUser extends Model
{
    protected $name = 'plugin_attack_room_user';//表名
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
            if ($v['action_time'] < date('Y-m-d H:i:s', time() - 15)) {//删除10秒未活动的会员
                $this->where('userid', $v['userid'])->delete();
            }
        }
    }

    /**
     *  获取有人的房间  且人数不超过  2
     */
    public function  getPlaceRoom() {
        $res = $this->field('room_id')->group('room_id')->having('count(id) < 2')->order('count(id) desc')->find();
        return $res;
    }

    /**
     *  获取当前会员的房间人员
     */
    public function  getRoomUser($user_id) {
        $room_user = $this->where('userid', $user_id)->find();
        if (empty($room_user)) return ['err' => 1];
        $res = $this->where('room_id', $room_user['room_id'])->select();

        $user_list = [];
        foreach ($res as $v){
            if ($user_id != $v['userid']) {
                if ($v['action_time'] < date('Y-m-d H:i:s', time() - 15)) {//删除10秒未活动的会员
                    $this->where('room_id', $v['room_id'])->where('userid', $v['userid'])->delete();
                }
                $user_info = User::where('id', $v['userid'])->find();
                array_push($user_list, [
                    'userid' => $user_info['id'],
                    'nickname' => $user_info['nickname'],
                    'photo' => $user_info['photo'],
                ]);
            }
        }

        if (!count($user_list)) {//房间内无会员的时候 更新房主
            (new Room())->where('id', $room_user['room_id'])->where('create_user', 'neq',  $user_id)->update(['create_user' => $user_id]);
        }
        $data = [
            'user_list' => $user_list
        ];
        return ['err' => 0, 'data' => $data];
    }


}
