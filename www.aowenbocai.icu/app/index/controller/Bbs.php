<?php
namespace app\index\controller;
use app\index\model\User;
use think\Controller;
use app\index\model\BbsReply as RBbs;
use think\Fileuplod;
use app\index\model\Bbs as ABbs;
use core\model\Good;

class Bbs extends Base
{

    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }
	
    /**
    * 主贴信息
    */
    public function index()
    {
        return $this->fetch('index', ['title'=>'圈子']);
    }
	
    /**
	* 主页贴子信息
	* @param int $length 帖子内容显示长度
	*/
	public function mainData($userid='', $length = 60)
	{
		$Bbs = new ABbs;
		if($userid === ''){
        	$userid = $this->user['id'];
        }
        $userid == 0 ? $userid = '' : $userid;
		$res = $Bbs->getList($userid);
		$data = $res->append(['userinfo', 'goodnum', 'imginfo'])->hidden(['gooder','images']);
		foreach ($res as &$item) {
			$item['good'] = (new Good)->rGood('bbs', $item['id'],$this->user['id']); 
			if(mb_strlen($item['content']) > $length){
				$item['content'] = mb_substr($item['content'], 0, $length, 'utf-8');
			}
		}
		return  $res;
	}
	
    /**
    * 帖子详情
    */
    public function detail()
    {
        return $this->fetch(detail, ['title'=>'帖子详情']);
    }

    /**
     * 回复详情
     */
    public function r_detail()
    {
        return $this->fetch(r_detail, ['title'=>'回复详情']);
    }
	
	/**
	* 详情页主贴信息
	* @param int $id 帖子ID
	*/
	public function detailOne($id)
	{
		$uid = $this->user['id'];
		//遍历主贴
		$main = ABbs::get($id);//主贴信息
		$data = $main->append(['userinfo','goodnum','Imgall'])->hidden(['images','gooder']);
		$main->good = (new Good)->rGood('bbs', $id, $uid);		
		return $main;
	}
	
	/**
    * 帖子详情
	* @param int $id 帖子ID
	* @return array
    */
	public function detailInfo($id, $length = 1000)
	{
		$res = (new RBbs)->detail($id);
		$data = $res->append(['userinfo','data']);
		return $res;
	}
	
	/**
    * 回复帖子的回复详情
	* @param int $id 帖子ID
	* @return array
    */ 
	public function replyInfo($id)
	{
		$res = (new RBbs)->reyDetail($id);
		$data = $res->append(['userinfo']);
		return $res;	
	}
	
	/**
    * 帖子回复查询
	* @param int $id 帖子ID
	* @return array
    */ 
	public function reyinfo($id){
		$data = (new RBbs)->reyinfo($id);
		return $data;
	}
	
    /**
    * 点赞
	* @param int $id 帖子ID
	* @return array
    */
    public function gooder($id)
    {
        $userid = $this->user['id'];	
		$data = (new ABbs)->good($userid,$id);
		return $data;
    }
	
    /**
    * 发帖页面
    */
    public function add()
    {
        return $this->fetch(add, ['title' => '发贴']);
    }
	
    /**
    * 发帖
    */
    public function begin_add()
    {
	//验证发帖信息是否为空。
		$data['title'] = \XSSProtection::clean($this->post['title']);
		$data['content'] = \XSSProtection::filterHtml($this->post['content']);

		// 检查是否包含恶意代码
		if(\XSSProtection::containsMalicious($data['content'])){
			return ['code' => 1,'msg' => '内容包含不安全的代码'];
		}

		if($Validate = (new ABbs)->Vali($data)){
			return ['code' => 1,'msg' => $Validate];
		}
		$imgurl = explode(',',$this->post['imgurl']);
		array_shift($imgurl);
		$a = array('.png','.jpg','.jpeg');
		$images = array();
		foreach($imgurl as $img){
			$tmp = strrchr($img,'.');
			if(!in_array($tmp,$a)){
				return ['code' => '2','msg' => '上传图片格式有误'];
			}
			$map = explode('/',$img);
			$length = count($map);
			if($length != 5 && $map[1] != 'uploads'){
				return ['code' => '3','msg' => '上传图片数据有误'];
			}
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$img) && copy($_SERVER['DOCUMENT_ROOT'].$img, $_SERVER['DOCUMENT_ROOT'].'/uploads/images/bbs/'.$map[4])){
				unlink($_SERVER['DOCUMENT_ROOT'].$img);
				array_push($images,'/uploads/images/bbs/'.$map[4]);
			}else{
				return ['code' => '4','msg' => '图片上传失败'];
			}
		}
		$data['images'] = implode(',', $images);
		$data['userid'] = $this->user['id'];
		$data['create_time'] = date('Y-m-d H:i:s', time());
		if ((new ABbs)->add($data)) {
			return ['code' => 0,'msg' => '发表成功'];
		}
		return  ['code' => 5,'msg' => '发表失败'];
    }
	
    /**
    * 回帖
    */
    public function reply()
    {
        $data=$this->post;
		$res = (new RBbs)->reply($data, $this->user['id']);
		if(empty($data['tid'])){
			(new ABbs)->takenum($res['pid']);
		}
		$map = (new RBbs)->reyinfo($res['id']);
		return $map;
	}
	
	/**
	* 个人圈子显示信息
	**/
	public function circle(){
		$uid = $this->user['id'];
		$data=(new ABbs)->fans($uid);
		$str='';
		foreach($data as $v){
			$str .= $v['to_userid'].',';
		}
		dump($str);exit;
	} 
	
	/**
	* 临时图片删除
	**/
	public function imgdel(){
		$data=$this->post;
		unlink($_SERVER['DOCUMENT_ROOT'].$data['img']);
	}
	/**
	* jquery插件上传图片
	*/
	public function base64Upload($base64_data)
	{
		$res = base64_upload($base64_data, 'uploads/images/tmp/');
		if (!$res) {
			return ['code' => 1, 'msg' => '图片上传错误！'];
		}
		return ['code' => 0, 'data' => $res];
	}
	
	/**
    * 获取详情页分页
	* @param int $id 帖子ID
	* @return array
    */
	public function detailPage($id,$length = 1000){
		$res = (new ABbs)->reyMain($id);
		$data = ($res->toArray());
		unset($data['data']);
		return $data;
	}
	
	/**
	* 详情页回贴信息
	* @param int $id 帖子ID
	* @param int $length 帖子内容显示长度
	*/
	public function detailMain($id, $length = 1000){
		$res = (new ABbs)->detailMain($id,$length);
		$page = $this->detailPage($id);
		$data = ['data'=>$res]+$page;
		return $data;
	}
}
