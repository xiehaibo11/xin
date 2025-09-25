<?php
namespace core\model;

use think\Model;
use core\model\GridSave;
use core\model\GridBuy;

/*
* 押宝游戏缓存类
*/
class GridCache extends Model{
	protected $updateTime = false;
	protected $createTime = false;

	//投注缓存
	public function bet($plan, $money, $userid, $type, $before, $planArr)
	{
		$new_money = $money;
		$newArr = [$plan => $money];
		$new_plan = json_encode($newArr);
		/**续投 */
		if($planArr){
			$newArr = [];
			foreach ($planArr as $key => $value) {
				$newArr[$value['plan']] =  $value['money'];
				$new_money += $value['money'];
			}
			$new_plan = json_encode($newArr);
		}
		if(!empty($before)){
			$new_money += $before['money'];
			$before_plan = json_decode($before['plan'], true);
			foreach ($newArr as $key => $value) {
				if(array_key_exists($key, $before_plan)){
					$before_plan[$key] += $value;
				} else {
					$before_plan[$key] = $value;
				}
			}
			$new_plan = json_encode($before_plan);
			$data_id = $before['id'];
		}
		$data = [
			'type' => $type,
			'userid' => $userid,
			'plan' => $new_plan,
			'money' => $new_money
		];

		if(isset($data_id)){
			$res = $this->save($data, ['id' => $data_id]);
		}else{
			$res = $this->save($data);
		}
		if($res){
			return ['err' => 0, 'msg' => '投注成功'];
		}
		return ['err' => 1, 'msg' => '投注失败'];
	}
	

	public function copyLastBet($plan, $money, $userid, $type, $before)
	{

	}

	//获取缓存的初始化信息
	public function info($userid = '', $type = '')
	{
		// return $this->where('userid', $userid)->where('type', $type)->column('plan,money');
		return $this->field('plan,money,id')->where('userid', $userid)->where('type', $type)->find();
	}
	
	//清除当前游戏缓存
	public function clean($userid = '', $type = '')
	{	
		return $this->where('type', $type)->where('userid', $userid)->delete();
	}
	
	//撤销
	public function del($type = '', $userid = ''){
		$res = $this->where('type', $type)->where('userid', $userid)->select();
		foreach($res as $info){
			$money = $info['money'];
		}
		$his = [
			'userid' => $userid,
			'money' => $money,
			'ext_name' => "{$type}/del",
			'remark' => "{$type}撤销投注"
		];
		if((new GridSave)->updateData($his, $type)){
			return $this->where('userid', $userid)->where('type', $type)->delete();
		}
		return false;
	}
	
	//获取当前游戏投注记录
	public function betList()
	{
		$data = $this->where('type', $type)->select();
		//将缓存数据写入游戏buy表中
		if(!empty($data)){
			foreach($data as $info){
				unset($info['id']);
				$info['except'] = (new GridBuy)->except();
				return (new GridBuy)->save($info);
			}
		}
		return false;
	}
}