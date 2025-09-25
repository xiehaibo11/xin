<?php
namespace core\model;

use Think\model;
use app\common\model\GameMoneyHistory;
use app\common\model\UserAction;

class GridSave extends Model
{
	public function updateData($data = '', $type = '')
    {
		$GameMoneyHistory = new GameMoneyHistory;
		$UserAction = new UserAction;
		if($data['money'] < 0){
			$content = "{$type}游戏下注:{$data['money']}";
		}else if($data['money'] > 0){
			$content = "{$type}游戏，获得奖金:{$data['money']}";
		}
		// 写资金明细,资金明细会自动更新用户资金
		$res = $GameMoneyHistory->write($data);
		//用户动态添加	
		$action = $UserAction->write([
			'userid' => $data['userid'],
			'content' => $content,
			'ext_name' => $data['ext_name']
		]);
		if($res['code'] == 1 && $action['code'] == 1){
			return true;
		}
		return false;
    }
}