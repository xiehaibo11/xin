<?php
namespace app\poker\model;

use app\common\model\UserBag;
use app\index\model\User;
use core\Setting;
use think\Model;
use think\Validate;
use app\common\model\UserAction;
use app\common\model\GameMoneyHistory;
use think\Db;

class GamePoker extends Model
{
    //protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function initUser($userid)
    {
        $this->userid = $userid;
        $this->save();
        return $this;
    }

    private $betMoney = [100, 200, 500, 1000, 2000, 5000];
    
    public function bet($bet_money = '')
    {
        if ($this->status == 1) {
            //return ['code' => 0];
        }
        $poker_coupon = (new UserBag())->where('userid', $this->userid)->where('param_name', 'poker_coupon')->find();
        if (($bet_money < 100 || $bet_money > 5000) && (!empty($poker_coupon) || !$poker_coupon['num'])) {
            return ['code' => 0];
        }
        $this->status = 1;
        if (!empty($poker_coupon) and $poker_coupon['num']) {
            $this->pay_type = 2;
            $setting = Setting::get(['free_coupon_coin']);
            $money = $setting['free_coupon_coin'];
            $log = [[
                'msg' => '起投 一张免费券',
                'poker' => $this->getPoker()
            ]];
            (new UserBag())->where('userid', $this->userid)->where('param_name', 'poker_coupon')->setDec('num');
        } else {
            $this->pay_type = 1;
            $money = $bet_money;
            $log = [[
                'msg' => '起投 ' . $money . '金币',
                'poker' => $this->getPoker()
            ]];
            $res = (new GameMoneyHistory)->write([
                'userid' => $this->userid,
                'ext_name' => 'poker',
                'money' => -$money,
                'remark' => '翻扑克-起投'
            ]);
            if (!$res['code']) {
                return ['code' => 0, 'msg' => '余额不足，请充值', 'errCode' => 1];
            }
        }

        $this->list = json_encode($log);
        $this->bet_money = $money;
        $this->money = $money;
        $this->save();
        $sp = $this->getSp($log);
        $user = (new User())->find($this->userid);
        return ['data' => end($log), 'sp' => $this->parseSp($sp), 'err' => 0, 'status' => 1, 'money' => $money, 'afterMoney' => $user['money']];
    }

    private $bet_over = ['1', '2', '3', '4']; //大 小 红 黑
    public function betNext($bet_code = '')
    {
        if (!isset($bet_code) || $this->status != 1) {
            return ['data' => 0, 'msg' => '押注错误！'];
        }
        $log = json_decode($this->list, true);
        $log_length = count($log);
        $end_log = end($log)['poker'];
        $poker = $this->getPoker($log);

        $hua = '4';
        if ($poker['hua'] < 2) {
            $hua = '3';
        }
        // 大小通杀
        $dax = 0;
        if ($end_log['number'] < $poker['number']) {
            $dax = '1';
        } else if ($end_log['number'] > $poker['number']) {
            $dax = '2';
        }
        
        $sp = $this->getSp($log);
		$sp = $this->parseSp($sp);
        $log[$log_length - 1]['sp'] = $sp[$bet_code - 1];
        $log[$log_length - 1]['bet_code'] = $bet_code;
        if ($bet_code == $hua || $bet_code == $dax) {
            $money = intval($this->money * $sp[$bet_code - 1]);
            $log[] = [
                'msg' => '猜中 ' . $money . '金币',
                'poker' => $poker
            ];
            $this->list = json_encode($log);
            $this->money = $money;
            $this->save();
            $sp = $this->getSp($log);
            return ['data' => end($log), 'sp' => $this->parseSp($sp), 'money' => $money, 'err' => 0, 'status' => 1];
        } else {
            $money = intval($this->money * $sp[$bet_code - 1]);
            $log[] = [
                'msg' => '猜错',
                'poker' => $poker
            ];
            $this->list = json_encode($log);
			$data = [
                'bet_money' => $this->bet_money,
                'bouns' => 0,
                'userid' => $this->userid,
                'record' => $this->list,
                'pay_type' => $this->pay_type
            ];
			$res = (new GamePokerRecord())->add($data);
            if(!$res){
				return ['err'=>1, 'msg'=>'添加记录失败'];
			}
            $this->list = '';
            $this->status = 0;
            $this->money = 0;
            $this->save();
            return ['err' => 0, 'status' => 0, 'data' => ['poker' => $poker]];
        }
    }

    public function over()
    {
        if ($this->status == 0) {
            return ['err' => 1, 'msg' => '还没有开始，或者已经结束了哦！'];
        }

        $log = json_decode($this->list, true);
        if (count($log) == 1) {
            return ['err' => 2, 'msg' => '请选择下张扑克的结果！'];
        }
        $res = (new GameMoneyHistory)->write([
            'userid' => $this->userid,
            'ext_name' => 'poker',
            'money' =>  $this->money,
            'type' => 1,
            'remark' => '翻扑克-奖金结算'
        ]);
		$data = [
            'bet_money' => $this->bet_money,
            'bouns' => $this->money,
            'userid' => $this->userid,
            'record' => $this->list,
            'pay_type' => $this->pay_type
        ];
		$result = (new GamePokerRecord)->add($data);
		if(!$result){
			return ['err'=>1, 'msg'=>'添加记录失败'];
		}
        $this->list = '';
        $this->status = 0;
        $this->money = 0;
        $this->save();
        return ['err' => 0, 'msg' => '结算成功', 'status' => 0, 'afterMoney' => $res['afterMoney']];
    }

    private $huaKeys = ['hong', 'fang', 'hei', 'mei'];
    private $keysHua = ['hong' => 0, 'fang' => 1, 'hei' => 2, 'mei' => 3];

    public function getPoker($log = [])
    {
        $allPoker = $this->parsePoker($log);
        // 取得花色
        $hua = rand(0, count($allPoker) - 1);
        // 取花色字符串[$allPoker 的 key]
        $husStr = $this->huaKeys[$hua];
        // 取得对应的下标数字
        $huaKey = $this->keysHua[$husStr];
        // 取得号码
        $number = rand(0, count($allPoker[$husStr]) - 1);
        return ['hua' => $huaKey, 'number' => $allPoker[$husStr][$number]];
    }

    public function parsePoker($log = [])
    {
        $allPoker = $this->createPoker();
        // 去掉已经发了的牌
        foreach ($log as $item) {
            $poker  = $item['poker'];
            // 取得花色的键
            $huaIndex = $this->huaKeys[$poker['hua']];
            // 指针赋值给变量
            $huaArr = &$allPoker[$huaIndex];            
            // 获取要移除的号码的键
            $index = array_search($poker['number'], $huaArr);
            
            // 移除对应花色的号码
            array_splice($huaArr, $index, 1);
            // 如果花色下面号码为空就移除该花色
            if (count($huaArr) == 0) {
                array_splice($this->huaKeys, $poker['hua']);
                array_splice($allPoker, $huaIndex, 1);
            }
        }
        return $allPoker;
    }

    /**
     * 生成一副扑克牌
     */
    public function createPoker()
    {
        // 对应 红桃 方片 黑桃 梅花
        $allPoker = ['hong' => [], 'fang' => [], 'hei' => [], 'mei' => []];
        
        // 生成一副完整的扑克
        foreach ($allPoker as &$item) {
            for ($i = 1; $i <= 13; $i ++) {
                $item[] = $i;              
            }
        }
        return $allPoker;
    }
	
    // 赔率
    private $sp = 0.9;
    public function getSp($log = [])
    {
        
        $end_log = end($log)['poker'];        
        $allPoker = $this->parsePoker($log);
        $spArr = ['da' => 0, 'xiao' => 0, 'hong' => 0, 'hei' => 0];
        foreach ($allPoker as $k => $item) {
            foreach ($item as $value) {
                if ($value > $end_log['number']) {
                    $spArr['da'] ++;
                }
                
                if ($value < $end_log['number']) {
                    $spArr['xiao'] ++;
                }

                if ($k == 'hong' || $k == 'fang') {
                    $spArr['hong'] ++;
                } else {
                    $spArr['hei'] ++;
                }
            }
        }
        $count = $spArr['hong'] + $spArr['hei'];
        foreach ($spArr as &$item) {
            $item = $item == 0 ? 0 : $this->sp / ($item / $count);
            if ($item >0 && $item < 1.02) {
                $item = 1.02;
            }
        }

        return [
            $spArr['da'],
            $spArr['xiao'],
            $spArr['hong'],
            $spArr['hei']
        ];
    }
	
	/*
	* 获取进行中的缓存数据
	*/
	public function getInit()
	{
		$res = ['err' => 0, 'status' => 0];
		if ($this->status == 1) {
			$log = json_decode($this->list, true);
			$sp = $this->getSp($log);
			$res['data'] = $log;
			$res['sp'] = $this->parseSp($sp);
			$res['status'] = 1;
			$res['money'] = $this->money;
		}
		return $res;
	}
	
	//
	private function parseSp($sp)
	{
		foreach ($sp as &$item) {
			$item = number_format(floor($item*100)/100
			, 2);
		}
		return $sp;
	}
}
