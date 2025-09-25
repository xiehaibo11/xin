<?php

namespace app\fa\model;

use app\index\model\User;
use think\Db;
use think\Model;
use think\Validate;

class Room extends Model
{
    protected $name = 'plugin_fa_room';//表名
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 用户分配房间
     * 1、检查是否有未活动的会员  删除房间内的会员
     * 2、获取用户不满的房间 并且取得人数越多的房间进入
     * 3、如果没有查找到满足条件的房间  则新建房间
     * @return array  返回加入房间的所有用户
    */
    public function allotRoom(){
        $user = User::where('sid',session('sid'))->find();
        $roomUserModel = new RoomUser();
        $roomUserModel->where('userid', $user['id'])->delete();
        $roomUserModel->deleteRoomUser();

        $get_room = $roomUserModel->getPlaceRoom();

        if (empty($get_room)) {//如果没找到房间  新建一个房间  //如果没有满足条件的房间  获取空房间
            $get_room = $this->where('action_time', '<', date('Y-m-d H:i:s', time()-50))->find();
            if (empty($get_room)) {
                $room_id = $this->insertGetId(['action_time' => date('Y-m-d H:i:s')]);
            } else {
                $room_id = $get_room['id'];
            }
        } else {
            $room_id = $get_room['room_id'];
        }
        //用户加入房间记录增加
        $res = $roomUserModel->save([
            'room_id' => $room_id,
            'userid' => $user['id'],
            'action_time' => date('Y-m-d H:i:s')
        ]);
        if (!$res) {
            return ['code' => 0];
        } else {
            return ['code' => 1];
        }

    }

    /**
     * 更新活动时间  包括玩家的活动时间 和 房间的活动时间
    */
    public function  updateAction($user_id){
        $user_room_info =(new RoomUser())->where('userid', $user_id)->find();
        if (empty($user_room_info)) {
            return false;
        }
        (new RoomUser())->update([
            'action_time' => date('Y-m-d H:i:s'),
            'id' => $user_room_info['id']
        ]);
        return true;
    }



}
