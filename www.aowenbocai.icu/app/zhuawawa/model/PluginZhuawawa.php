<?php
namespace app\zhuawawa\model;

use think\Model;
use app\admin\model\User;

class PluginZhuawawa extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getGiftInfoAttr($value, $data)
    {
        $gift_info = PluginZhuawawaSetting::get($data['gift_id']);
        return [
            'title' => $gift_info['title'],
            'bet_money' => $gift_info['bet_money']
        ];
    }

    public function getNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['nickname' => $user->nickname,'username' => $user->username];
        }
        return ['nickname' => '用户不存在', 'username' => '...'];

    }

    public function getGetNameAttr($value, $data)
    {
        return $data['is_get'] ? '抓取成功' : '抓取失败';
    }
    public function hisList($userid)
    {
        $this->where('userid', $userid);
        $res = $this->order('id', 'desc')->paginate(20);
        $res->append(['gift_info']);
        $res->hidden(['gift_id']);
        return $res;
    }

    public function getPlayInfoAttr($value, $data)
    {
        $gift_info = PluginZhuawawaSetting::get($data['gift_id']);
        return [
            'gift' => $gift_info['title'],
            'bouns' => json_decode($gift_info['gift'],true)['value'],
            'bet_money' => $gift_info['bet_money'],
            'status' => $data['is_get'] ? '中奖' : '未中奖',
        ];
    }
    public function getList()
    { 
        $data = request()->get();
        if(!empty($data['username'])){
            $userid = (new User)->where('username|nickname', 'like', "%".$data['username']."%")->column("id");
            $this->where('userid', 'in', $userid);
        }
        if(!empty($data['starttime'])){
            $this->where('create_time', ">=", $data['starttime']);
        }

        if(!empty($data['endtime'])){
            $this->where('create_time', "<=", $data['endtime'].' 29:59:59');
        }

        if(isset($data['isget']) && $data['isget'] != ''){
            $this->where('is_get', $data['isget']);
        }
        if(isset($data['name']) && $data['name'] != ''){
            $this->where('gift_id', $data['name']);
        }
        $res = $this->order("id DESC")->paginate(15, false,['query' => $data]);
        $res->append(['GiftInfo', 'Name', 'GetName']);
        return $res;
    }

    public function addHis()
    {
        
    }
}
