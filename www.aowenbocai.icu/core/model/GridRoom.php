<?php
namespace core\model;

use think\Model;
use app\index\model\User;

class GridRoom extends Model{
	protected $updateTime = false;
	protected $createTime = false;
	protected $resultSetType = 'collection';
	

	/**获取器--获取用户名 */
	public function getUserInfoAttr($value, $data)
	{
		$user = new User;
		$info = $user->get($data['userid']);
		if($info){
			$info = $info->toArray();
			return ['nickname' => $info['nickname'], 'username' => $info['username']];
		}
		return ['nickname' => '用户不存在', 'username' => '..'];
	}

	public function begin($userid = '', $type = '', $num = '')
	{
		/**
		 * 先清除上次进来数据--理论上一个用户只能在一个游戏里，所以应删除所有类型的数据 
		 * 情况一 退出当前游戏进来后就不在是当前房间，过期时间内（目前写的就是这种情况）
		 * 情况二 退出后且在活动时间内重新进入还是在原来的房间（这种情况就没必要删除数据） 
		*/
		if(!empty($this->where('userid', $userid)->select())){
			$this->where('userid', $userid)->delete();
		}
		/**删除活动时间少于5分钟的用户--任一一个押宝类 */
		$start = time() - 300;
		$this->where('action_time', '<=' , $start)->delete();

		//查询未满房间,找出人数未满最多人的加入 或者建立新房间
		$num = $num == '' ? 4 : $num;
		$roomid = $this->group('roomid')->having("count(roomid) < $num")->where('type', $type)->order('count(roomid) desc')->limit(1)->value('roomid');
		$data['userid'] = $userid; 
		$data['action_time'] = time();
		$data['type'] = $type;
		if(empty($roomid)){
			$newId = $this->where('type', $type)->max('roomid');
			$roomid = $newId + 1;
		}
		$data['roomid'] = $roomid;
		$this->save($data);
	}
	

	/**更新活动时间 */
	public function updateAction($userid, $type)
	{
		$this->save(['action_time' => time()],['userid' => $userid, 'type' => $type]);
	}

	//获取用户所在的房间信息
	public function room($type = '', $userid = '', $overTime)
	{
		/**删除活动时间少于5分钟的用户--任一一个押宝类 */
		$start = time() - $overTime;
		$this->where('action_time', '<=' , $start)->where('type', $type)->delete();
		$roomid = $this->where('type', $type)->where('userid', $userid)->value('roomid');
		return $this->where('type', $type)->where('userid','neq', $userid)->where('roomid', $roomid)->select();
	}
	
	//用户长时间不操作，将踢出游戏
	public function del($type = '', $userid = '')
	{
		return $this->where('type', $type)->where('userid', $userid)->delete();
	}
}