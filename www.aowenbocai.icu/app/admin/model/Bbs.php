<?php
namespace app\admin\model;

use think\Model;
use think\Validate;
use app\index\model\BbsReply;
use core\model\Good;

class Bbs extends BaseModel
{

	protected $updateTime = false;
	protected $resultSetType = 'collection';

	public function getUserInfoAttr($value, $data)
	{
		$info = (new User)->get($data['userid']);
		if($info){
			$info = $info->toArray();
			return ['username' => $info['username'], 'nickname' => $info['nickname']];
		}else{
			return ['username' => '信息错误', 'nickname' => '或该用户已被注销'];
		}
	}

	public function getMinContentAttr($value, $data)
	{
		$content = strlen($data['content']) < 18 ? $data['content'] : mb_substr($data['content'], 0,18, 'UTF-8')."···";
		return $content;
	}

	/**
	 * 获取器--获取评论数
	 */
	public function getReplyNumAttr($value, $data)
	{
		$num = (new BbsReply)->where('pid', $data['id'])->count();
		return $num;
	}

	/**
	 * 获取器--获取评论数
	 */
	public function getGoodNumAttr($value, $data)
	{
		$num = (new Good)->where('vid', $data['id'])->find(['userid']);
		$num = $num ? $num : 0;
		return $num;
	}
	
	/**杨俊之前的 */
	//后台贴子列表
	public function index(){
		return $data=$this->order('create_time desc')->paginate(20);
	}
	//搜索帖子id
	public function info($id){
		return $this->where('id',$id)->paginate(20);
	}
	//帖子搜索用户名
	public function username($tmp){		
		$data = $this->where('userid','in',"{$tmp}")->paginate(20);
		return $data;
	}
	//帖子时间查询用户名
	public function userTime($start='',$end='',$tmp=''){
		if(empty($start) && !empty($end)){
			$res=$this->where('userid','in',"{$tmp}")->where('create_time','<=',$end)->paginate(20);
		}else if(!empty($start) && empty($end)){
			$res=$this->where('userid','in',"{$tmp}")->where('create_time','>=',$start)-paginate(20);
		}else if(!empty($start) && !empty($end)){
			$res=$this->where('userid','in',"{$tmp}")->where('create_time','>=',$start)->where('create_time','<=',$end)->paginate(20);
		}
		return $res;
	}
	//帖子时间查询
	public function serchTime($start='',$end=''){
		if(empty($start) && !empty($end)){
			$res=$this->where('create_time','<=',$end)->paginate(20);
		}else if(!empty($start) && empty($end)){
			$res=$this->where('create_time','>=',$start)->paginate(20);
		}else if(!empty($start) && !empty($end)){
			$res=$this->where('create_time','>=',$start)->where('create_time','<=',$end)->paginate(20);
		}
		return $res;
	}

	//主帖修改
	public function mod($data,$id){
		return $this->where('id',$id)->update($data);
	}
	//主贴删除
	public function del($id){
		return $this->where('id',$id)->delete();
	}
}