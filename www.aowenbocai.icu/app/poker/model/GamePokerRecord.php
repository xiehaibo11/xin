<?php
namespace app\poker\model;

use think\Model;
use app\admin\model\User;

class GamePokerRecord extends Model
{
    protected $resultSetType = 'collection';
	public function record($userid = '')
	{
		$res = $this->where('userid', $userid)->order('id','desc')->paginate();
		return $res;	
	}
	
	public function add($data)
	{
		return $this->insert($data);
    }
    
	public function getNameAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['nickname' => $user->nickname,'username' => $user->username];
        }
        return ['nickname' => '用户不存在', 'username' => '...'];

    }
    public function getPlayInfoAttr($value, $data)
    {
        $record = json_decode($data["record"], true);
        return $record;
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
        if(isset($data['sort']) && $data['sort'] != ''){
            $sort = ['bouns ASC','bouns DESC','bet_money ASC','bet_money DESC'];
            $this->order($sort[$data['sort']]);
        }
        $res = $this->order("id DESC")->paginate(15, false,['query' => $data]);
        $res->append(['name']);
        return $res;

	}
}