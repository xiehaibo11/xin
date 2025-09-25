<?php

namespace app\attack\model;

use app\index\model\User;
use think\Db;
use think\Model;
use think\Validate;

class Room extends Model
{
    protected $name = 'plugin_attack_room';//表名
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 创建房间
     */
    public function  add($data){
        (new RoomUser())->where('userid', $data['create_user'])->delete();
        $check_user = $this->where('type', 2)->where('create_user', $data['create_user'])->find();
        if (!empty($check_user)) {
            $this->where(['id' => $check_user['id']])->update(['action_time' => date('Y-m-d H:i:s', time())]);
            return ['code' => 1, 'password' => $check_user['password']];
        }
        $check_password = $this->where('type', 2)->where('password', $data['password'])->find();
        if (!empty($check_password)) {
            $data['password'] = getRandChar(6);
            return $this->add($data);
        }
        $data['action_time'] = date('Y-m-d H:i:s', time());
        $data['type'] = 2;
        $res = $this->allowField(['type', 'create_user', 'password', 'money', 'action_time'])->save($data);
        if (!$res) {
            return ['code' => 0, 'msg' => '数据执行失败'];
        }

        return ['code' => 1,  'msg' => '数据执行成功', 'password' => $data['password']];
    }

    /**
     * 用户进入房间
     * 1、检查是否有未活动的会员  删除房间内的会员
     * @param $room_id int 房间id
     * @return array  返回加入房间的所有用户
    */
    public function allotRoom($room_id = ''){
        $user = User::where('sid',session('sid'))->find();
        $roomUserModel = new RoomUser();
        $roomUserModel->deleteRoomUser();//删除未活动的会员
        $this->deleteRoom();//删除未活动的房间
        $roomUserModel->where('userid', $user['id'])->delete();//删除当前会员的房间信息
        $ready = 0;
        $ready_money = 0;
        $record_id = 0;
        if (!$room_id) {//没有通过密码进入
            $get_room = $roomUserModel->getPlaceRoom();
            if (empty($get_room)) {//如果没找到房间  新建一个房间  //如果没有满足条件的房间  获取空房间
                $get_room = $this->where('type', 1)->where('action_time', '<', date('Y-m-d H:i:s', time()-50))->find();
                if (empty($get_room)) {
                    $room_id = $this->insertGetId(['action_time' => date('Y-m-d H:i:s')]);
                } else {
                    $room_id = $get_room['id'];
                }
            } else {
                $room_id = $get_room['id'];
            }
        } else {//通过密码进入
            $room_user_length = $roomUserModel->where('room_id', $room_id)->count();//判断房间内会员数量
            if ($room_user_length >= 2) {
                return ['code' => 0, 'msg' => '房间内人员已满'];
            }

        }

        //用户加入房间记录增加
        $res = $roomUserModel->save([
            'room_id' => $room_id,
            'userid' => $user['id'],
            'ready' => $ready,
            'ready_money' => $ready_money,
            'record_id' => $record_id,
            'action_time' => date('Y-m-d H:i:s')
        ]);
        if (!$res) {
            return ['code' => 0, 'msg' => '加入房间失败'];
        } else {
            return ['code' => 1];
        }

    }

    /**
     * 玩家进入房间
     */
    public function  joinRoom($data){
        $validate = new Validate([
            'password|密码' => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $room_info = $this->where('type', 2)->where('password', $data['password'])->find();
        if (empty($room_info)) {
            return ["code" => 0, "msg" => '房间不存在'];
        }
        $checkAgents = (new \app\admin\model\User())->checkAgents($room_info['create_user'], $data['user_id']);
        if (!$checkAgents['code']) {
            return ["code" => 0, "msg" => '您没有权限进入房间'];
        }
        return $this->allotRoom($room_info['id']);
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
        (new Room())->update([
            'action_time' => date('Y-m-d H:i:s'),
            'id' => $user_room_info['room_id']
        ]);
        return true;
    }

    /**
     *  删除未活动的密码房间 5分钟未活动的
     */
    public function  deleteRoom() {
        $res = $this->where('type', 2)->where('action_time','<= time',date('Y-m-d H:i:s', time() - 60 * 5))->delete();
    }





}
