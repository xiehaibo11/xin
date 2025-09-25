<?php
namespace core\model;

use think\Model;

/**
* 点赞类
*/

class Good extends Model{
	public $count = '';		//点赞数
	public $type = '';		//点赞类行（表）
	public $vid = '';		//点赞的主键或唯一索引列。
	public $userid = '';	//点赞用户id
	/*
	* 点赞
	*/
	public function gooder($type, $vid, $userid){
		if($id = $this->dGood($type, $vid)){
			$uid = $this->user($type, $vid);
			$arr = !empty($uid) ? explode(',', $uid) : [];
			$key = array_search($userid, $arr);
			$msg = '已取消点赞';
			$code = 0;
			if ($key === false){
				array_push($arr, $userid);
				$code = 1;
				$msg = '点赞成功';
			} else {
				array_splice($arr, $key, 1);
			}
			$count = count($arr);
			$uid = implode(',', $arr);
			$this->where('type', $type)->where('vid', $vid)->setField('userid', $uid);
			return ['code'=>$code,'msg'=>$msg,'goodnum'=>$count];
		} else {
			$data = [
				'type' => $type,
				'vid'  => $vid,
				'userid' => $userid
			];
			if($this->insertGetId($data)){
				return ['msg'=>'点赞成功','goodnum'=>1];
			}
		}
	}
	
	/*
	* 判断指定内容里是否有过点赞
	*/
	public function dGood($type, $vid){
		return $this->where('type' ,$type)->where('vid', $vid)->value('id');
	}
	
	/*
	* 获取指定内容的点赞用户id集合
	*/
	public function user($type, $vid){
		return $this->where('type',$type)->where('vid',$vid)->value('userid');
	}
	
	/*
	* 获取点赞数
	*/
	public function goodnum($type, $vid){
		$data = $this->user($type, $vid);
		$count = empty($data) ? 0 : count(explode(',', $data));
		return $count;
	}
	
	/* 
	* 获取用户是否点过赞
	*/
	public function rGood($type, $vid ,$userid){
		if(!empty($user = $this->user($type, $vid))){
			return in_array($userid, explode(',',$user)) ? 1 : 0;
		}
		return 0;
	}
}