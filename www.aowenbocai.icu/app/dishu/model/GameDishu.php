<?php
namespace app\dishu\model;

use think\Model;
use app\admin\model\User;

class GameDishu extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    // public function getGiftInfoAttr($value, $data)
    // {
    //     $gift_info = PluginZhuawawaSetting::get($data['gift_id']);
    //     return [
    //         'title' => $gift_info['title'],
    //         'bet_money' => $gift_info['bet_money']
    //     ];
    // }

    public function hisList($userid)
    {
        $this->where('userid', $userid);
        $res = $this->order('id', 'desc')->paginate(20);
        return $res;
    }

    public function getPlayInfoAttr($value, $data)
    {
        $mouse = ['1' => '萌萌鼠', '2' => '矿矿鼠', '3' => '海盗鼠', '4' => '呆呆鼠', '5' => '调皮鼠'];
        $_data['mouse'] = $mouse[$data['dishu_id']];
        return $_data;
    }

    public function getNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['nickname' => $user->nickname,'username' => $user->username];
        }
        return ['nickname' => '用户不存在', 'username' => '...'];

    }

    public function getDishuNameAttr($value, $data)
    {
        $mouse = ['1' => '萌萌鼠', '2' => '矿矿鼠', '3' => '海盗鼠', '4' => '呆呆鼠', '5' => '调皮鼠'];
        return  $mouse[$data['dishu_id']];
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
            $this->where('dishu_id', $data['name']);
        }
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC','bouns DESC','bet_money ASC','bet_money DESC'];
            $this->order($sort[$data['sort']]);
        }
        $res = $this->order("id DESC")->paginate(15, false,['query' => $data]);
        $res->append(['dishuName', 'name']);
        return $res;
    }
    public function addHis()
    {
        
    }
}
