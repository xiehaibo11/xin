<?php
namespace app\common\model;

use think\Model;
use app\index\model\User;

class GameDzpHistory extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 -- 获取用户名
     */
    public function getNickNameAttr($value, $data)
    {
        $user = (new User)->find($data['userid']);
        if(!empty($user)){
            $user = $user->toArray();
            $username = $user['nickname'] ?? $user['usernmae'];
            return ['username' => $username, 'photo' => $user['photo']];
        } else {
            return ['username' => '会员不存在', 'photo' => ''];
        }
    }
    /**
     * 获取器 -- 获取时间差
     */
    public function getTimeAttr($value, $data)
    {
        $time = strtotime($data['create_time']);
        $diff = floor ((time() - $time) / 60);
        switch($diff){
            case 0 :
                $difftime = "刚刚";
                break;
            case $diff < 60 :
                $difftime = $diff."分钟前";
                break;
            case $diff < (60 * 24) :
                $difftime =   floor ($diff / 60).'小时前';
                break;
            case $diff > (60 * 24) :
                $difftime =   floor ($diff / (60 * 24)).'天前';
                break;
        };
        return $difftime; 
    }

    /**
     * 获取列表
     * @param int $num 每页显示数
     * @param array $where 查询条件
     * @param int $type 查询类型，0是普通查询 1是统计
     * @retrun $res
     */
    public function getList($num, $where = [], $type = 0)
    {     
        if($data = request()->get()){
            if($data['starttime']){
                $this->where(["create_time" => [ ">" , $data['starttime']]]);
            }

            if($data['endtime']){
                $this->where(["create_time" => [ "<" , $data['endtime']]]);
            }

            if($words = $data['words']){
                $user = (new User)->where('nickname', 'like', "%{$words}%")->column('id');
                if($user){
                    $this->where('userid', 'in', $user);
                }
                $this->whereOr('name', 'like', "%{$words}%");
            }
        }

        if($where){
            $this->where($where);
        }
        $order = getOrder($data);
        if($type == 1){
            $res = $this->field("SUM(num) as num, name")->group('name')->select();
        }else{
            $res = $this->order($order)->paginate($num);
        }
        return $res;
    }
    
    public function addData($userid,$data)
    {
        $msg = explode('+', $data['msg']);
        $new = [
            'userid' => $userid,
            'photo' => $data['photo'],
            'name' => trim($msg[0]),
            'num' => trim($msg[1])
        ];
        $this->insert($new);
    }
}