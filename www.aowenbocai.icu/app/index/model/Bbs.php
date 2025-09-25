<?php
namespace app\index\model;

use think\Validate;
use think\Db;
use core\model\Good;
use think\Model;
use app\index\controller\Base;

class Bbs extends Model
{
	protected $resultSetType = 'collection';
    protected $updateTime = false;
	
    /**
	 * 帖子发送
	 * @param int $data 添加内容
	 */
    public function add($data)
    {
        $res = $this->allowField('uid,title,content,create_time')->insert($data);
        return $res;
    }

	/**
	 * 帖子信息验证
	 * @param int $data 验证内容
	 * @return array
	 */
	public function Vali($data)
	{
		$validate = new Validate([
			//'title'  => 'require|max:30',
			'content' => 'require'
		]);
		if ($validate->check($data)) {
			return false;
		}
		return $validate->getError();
		
	}
	
	/**
	 * 主页帖子信息
	 * @param int $length 内容显示长度
	 * @return array
	 */
	public function mainData($userid = ''){
		$this->order(['create_time' => 'desc']);
		if(!empty($userid)) {
			$this->where('userid', $userid);
		}
		$data = $this->select()->toArray();
		return $data;
	}

	public function fans($id='')
	{
		return Db('fans')->where('userid',$id)->value('to_userid');
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
	 * 获取帖子图片
	 * @return array
	 */
	public function getImginfoAttr($value, $data)
	{
		$list = explode(',', $data['images']);
		$count = count($list);
		if ($count > 3) {
			$count = 3;
			$list = array_slice($list, 0, 3);
		}
		return [
			'images' => $list,
			'imgnum' => $count
		];
	}
	
	/**
	 * 获取帖子所有图片
	 * @return array
	 */
	 
	public function getImgallAttr($value, $data){
		$list = explode(',', $data['images']);
		$count = count($list);
		return [
			'images' => $list,
			'imgnum' => $count
		];
	}
	 
	/**
	 * 获取内容
	 * @return array
	 */
	public function getGoodnumAttr($value, $data)
	{
		$count = (new Good)->goodnum('bbs',$data['id']);
		return $count;
	}

	/**
	 * 获取内容
	 * @param int $length 内容显示长度
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
	 * 获取列表
	 * @param int $userid 用户ID
	 * @return array
	 */
	public function getList($uid ='', $row = 10)
	{

		if(!empty($uid)) {
			$this->where('userid', $uid);
		}
		$res = $this->order(['create_time' => 'desc'])->paginate($row);		
		return $res;
	}

	/**
	 * 用户点赞
	 * @param int $userid 用户ID
	 * @param int $id 帖子ID
	 * @return array
	 */
	public function good($userid= '', $id = '', $type = 'bbs'){
		$data = (new Good)->gooder($type, $id, $userid);
		return $data;
	}
	
	/**
	* 用户评论数自增
	* @param int $id 帖子ID
	* @return array
	*/
	public function takenum($id){
		return $this->where('id',$id)->setInc('takenum');
	}
	
	/**
	* 详情页所有回复信息
	* @param int $id 回复帖子id
	* @param int $length 帖子内容显示长度
	*/
	public function detailMain($userid, $length = 1000){
		$res = Db('bbs_reply')->where(['pid' => $userid,'tid' => 0])->paginate(10);
		$data = array();
		foreach ($res as &$arr){
			$user = User::get($arr['userid']);
			$arr['nickname'] = $user['nickname'];
			$arr['photo'] = $user['photo'];
			if(mb_strlen($arr['content'])>$length){
				$arr['content'] = mb_substr($arr['content'], 0, $length, 'utf-8').'...';
			}
			$arr=$this->reyDetail($arr);
			array_push($data, $arr);
		}
		return $data;
	}
	
	/**
	* 详情页信息分页统计
	* @param int $userid 用户ID
	*/
	public function reyMain($uid){
		return $map = Db('bbs_reply')->where('tid', $uid)->paginate();
	}
	
	/**
	* 详情页回贴详情信息
	* @param int $id 回复帖子id
	* @param int $length 帖子内容显示长度
	*/
	public function reyDetail($arr){
		$map = Db('bbs_reply')->where('tid', $arr['id'])->paginate();
		if ($map) {
			foreach($map as &$v){
				$user = User::get($v['userid']);
				$v['nickname'] = $user['nickname'];
				$v['photo'] = $user['photo'];
				array_push($arr, $v);
			}
				return $arr;
		}
	}
}