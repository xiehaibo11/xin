<?php
namespace app\fish50\model;

use think\Model;
use app\admin\model\User;

class PluginFishHis extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getUserNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['nickname' => $user->nickname,'username' => $user->username];
        }
        return ['nickname' => '用户不存在', 'username' => '...'];
    }
    
    public function getPlayInfoAttr($value, $data)
    {
    }
    public function hisList($userid)
    {
        $this->where('userid', $userid);
        $res = $this->order('id', 'desc')->paginate(20);
        return $res;
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

        if(isset($data['name']) && $data['name'] != ''){
            $this->where('name', $data['name']);
        }
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC', 'bouns DESC']; 
            $this->order($sort[$data['sort']]);
        }
        return $this->order('id DESC')->paginate(15, false,['query' => $data]);
    }
}
