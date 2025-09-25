<?php
namespace core\game;

use core\base\GridGameBase;
use core\model\GridCache;
use core\model\GridRoom;
use core\model\GridBuy;

class GridGame extends GridGameBase{
	private $plan = '';//下注的选项
	private $money = '';//追加的金额
	private $userid = '';//下注用户id
	private $type = '';//所下注的游戏缩写
	
	private $overtime = 500;/**房间超时时长 */
	private $lost_time = 30;
	
	public function __construct($type = '', $userid = '', $plan = '', $money = ''){
		$this->type = $type;
		$this->userid = $userid;
		$this->plan = $plan;
		$this->money = $money;
	}
	
	/**
     * 投注
     * @param mixed $plan 下注的选项
     * @param int $money 追加的金额
     * @return array
     */
	public function bet($plan = '', $money = '')
	{
		$money = intval($money);
		if($money <= 0 && $plan == ''){
			return ['err'=> 3, 'msg'=>'请输入下注金额'];
		}
		if(empty($this->plan) && $plan == ''){
			return ['err'=>2, 'msg'=>'请选择下注选项'];
		}
		$cache = new GridCache;
		$all_money = $money;
		if($plan != ''){
			$plan = json_decode($plan, true);
			foreach ($plan as $value) {
				$all_money += $value['money'];
			}
		}
		/**判断之前该用户有没有投注该游戏 */
		$before_data = $cache->info($this->userid, $this->type);
		if($before_data){
			$before_data = $before_data->toArray();
			$all_money += $before_data['money'];
		}

		if($all_money > $this->money){
			return ['err' => 4, 'msg' => '余额不足,请先充值'];
		}
		$data = $cache->bet($this->plan, $money, $this->userid, $this->type, $before_data,$plan);
		$data['money'] = $data['err'] > 0 ? $this->money : ($this->money - $all_money);
		$data['time'] = date("Y-m-d H:i:s");
		return $data;/**返回参数中应有当前余额（该余额非账户余额）*/
	}
	
	/**
	 * 确认投注并清除缓存数据
	 */
	public function checkOrder()
	{
		$cache = new GridCache;
		$info = $cache->where('type', $this->type)->where('userid', $this->userid)->find();
		if(!$info){
			return ['err' => 1, 'msg' => '请先投注'];
		}
		$info = $info->toArray();
		$cache->where('id', $info['id'])->delete();
		return ['err' => 0, 'data' => $info];
	}

	/**
     * 获取初始化信息
     * @return array
     */
	// public function getInfo($code_his = '', $money = '', $issue = '', $lost_time = '', $roomNum = "")
	public function getInfo($code_his = '', $money = '', $issue = '', $config = '', $roomNum = "")
	{	
		(new GridRoom)->begin($this->userid, $this->type, $roomNum);
		$this->issue = $issue;
		$bet_his = (new GridCache)->info($this->userid, $this->type);
		if($bet_his){
			$bet_his = $bet_his->toArray();
			$bet_his['plan'] = json_decode($bet_his['plan'], true);
			$money = $money - $bet_his['money'];
		}
		$lost_time = isset($config['refresh_time']) ? $config['refresh_time'] : ($this->lost_time);

		$classIssue = new Issue($config);
		$issue = $classIssue->timeToIssue();
		// $issue = empty($issue) ? ($this->issue) : $issue;
		$data = [
			'issue' => $issue['issue'],
			'lost_time' => $lost_time,
			'pass_time' => $issue['pass_time'],
			'ure_time' => $issue['ure_time'],
			'money' => $money,
			'bet_his' => $bet_his,
			// 'code_his' => $code_his(?历史记录)
		];

		// $data['room'] = $this->room();
		return $data;
	}
	

	/**轮循修改用户活动时间 (暂时先这样)*/
	public function setActionTime($userid, $type)
	{
		if($userid != '' && $type != ''){
			(new GridRoom)->updateAction($userid, $type);
		}
	}
	/**
	 * 获取用户房间信息
	 * @param type 游戏类型
	 * @param userid 用户id
	 * @param overtime 无操作时长
	 */
	public function room($type = '', $userid = '', $overtime = '')
	{
		if(($userid == '' && $this->userid == '') || ($type == '' && $this->type == '')){
			return ['err' => 1, 'msg' => '参数错误'];
		}
		$userid = $userid != '' ? $userid : ($this->userid);
		$type = $type != '' ? $type : ($this->type);
		$overtime = $overtime == '' ? $this->overtime : $overtime;
		$res  = (new GridRoom)->room($type, $userid, $overtime);
		$res->visible(['userid']);
		$res->append(['userInfo']);
		return $res;
	}
	
	/**
     * 获取当前期的开奖情况
     * @return array
     */
	public function getStatus()
	{
		if($money = (new Prize)->isWin()){
			return ['err' => 0, 'data' => $money];
		}
		return ['err' => 1, 'msg' => '未中奖'];
	}
	
	/**
     * 清除当前游戏缓存
     * @return boolean
     */
	public function clean()
	{
		$cache = new GridCache;
		if($cache->info($this->userid, $this->type)){
			$res = $cache->clean($this->userid, $this->type);
			$result = $res ? ['err' => 0, 'msg' => '已撤销'] : ['err' => 2, 'msg' => '撤销失败'];
		}
		return ['err' => 1, 'msg' => '您还没有可撤销的数据'];	
	}

	public function clean_before()
	{
		//清除之前将缓存数据存入记录表
		if((new GridCache)->betList()){
			return (new GridCache)->clean();
		}
		return false;
	}
	
	/**
     * 撤销
     * @return boolean
     */
	public function del()
	{
		if((new GridCache)->del()){
			return true;
		}
		return false;
	}
	
	/**
     * 获取用户投注历史记录
     * @return array
     */
	public function betList($type = '', $userid = '')
	{
		return (new GridBuy)->history($type, $userid);
	}
}