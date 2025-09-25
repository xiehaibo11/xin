<?php
namespace core\model;

use think\Model;
use core\game\Issue;

class GridBuy extends Model
{
	//购买下注
	public function buy($data = '', $type = '')
	{
		$data['except'] = $this->except();
		if($id = $this->save($data)){
			return $id;
		}
		return false;
	}
	
	//获取当前用户所押注的期号
	public function except($config = '')
	{
		return (new Issue)->timeToIssue($config);
	}
	
	//获取用户的投注历史
	public function history($type = '', $userid = ''){
		return $this->where('type', $type)->where('userid', $userid)->order('id desc')->paginate();
	}
}