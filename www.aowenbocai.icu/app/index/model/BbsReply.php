<?php
namespace app\index\model;

use think\Model;
use app\index\model\Bbs;

class BbsReply extends Model
{
	protected $resultSetType = 'collection';
	
	/**
	* 详情页回贴信息
	* @param int $id 回复帖子id
	* @param int $length 帖子内容显示长度
	*/
	public function detail($id,$length = 1000){
		$res = $this->where(['pid' => $id,'tid' => 0])->order(['create_time' => 'desc'])->paginate(10);
		return $res;
	}

	/**
	* 详情页回贴详情信息
	* @param int $tid 回复帖子tid
	*/
	public function reyDetail($id){
		return $map = $this->where('tid', $id)->order(['create_time' => 'desc'])->paginate($row);
	}

	/**
	* 获取发帖用户信息
	* @return array
	*/
	public function getUserinfoAttr($value,$data)
	{
		$user = User::get($data['userid']);
		return [
			'nickname' => $user['nickname'],
			'photo' => $user['photo']
		];
	}
	
	/**
	* 获取回复内容
	* @return array
	*/ 
	public function getContentAttr($value, $data, $length = 1000)
	{
		if(mb_strlen($value) > $length){
			$res = mb_substr($value, 0, $length, 'utf-8').'...';
			return $res;
		}
		return $value;
		
	}
	
	/**
	* 获取发帖时间
	* @return array
	*/
	public function getCreatetimeAttr($value, $data){
		$time = strtotime($data['create_time']);
		return $this->timeTran($time);
	}
	
	/**
	* 获取时间间隔
	* @param int $timeInt 时间戳
	* @return string
	*/
	public function timeTran($timeInt,$format='Y-m-d H:i:s'){
		$d=time()-$timeInt;
		if($d<0){
			return $timeInt;
		}else{
			if($d<60){
				return '刚刚';
			}else{
				if($d<3600){
					return floor($d/60).'分钟前';
				}else{
					if($d<86400){
						return floor($d/3600).'小时前';
					}else{
						if($d<2592000){//30天内
							return floor($d/86400).'天前';
						}else{
							return date($format,$timeInt);
						}
					}
				}
			}
		}
	}
	
	/**
	* 获取回复内容的回复列表
	* @return array
	*/ 
	public function getDataAttr($value ,$data){
		$res = $this->where('tid', $data['id'])->order(['create_time' => 'desc'])->paginate();
		$res->append(['userinfo']);
		return $res;
		
	}
	
	/**
	* 回复帖子
	* @return array
	*/
	public function reply($data, $userid){
		$data['content'] = strip_tags($data['content']);
		$data['userid'] = $userid;
		$data['create_time'] = date('Y-m-d H:i:s',time());
		if($id = $this->insertGetId($data)){
			$reply = $this->where('id',$id)->select();
			$res = $reply->toArray();
			return ['pid'=>$res[0]['pid'],'id'=>$res[0]['id']];
		}
	}
	
	/**
	* 回复帖子信息
	* @return array
	*/
	public function reyinfo($id = ''){
		$res = $this->where('id',$id)->select();
		return $res->append(['userinfo']);
	}
} 