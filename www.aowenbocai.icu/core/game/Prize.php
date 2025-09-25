<?php
namespace core\game;

use core\base\PrizeBase;

class Prize extends PrizeBase
{
    /**
     *投注内容配置，根据传入不同的参数判断奖金计算的方式
     * @param array $plan 投注的内容(array($code:$money))
     */
    public function setPlan($plan = '')
	{
		return $this->plan　= $plan;
	}

    /**
	* 开奖赔率信息+游戏类型
	* @param array $code 开奖内容
    */
    public function setCode($code = '')
	{
		/* $code = [
			'0,1' => 1.92,
			'2,6' => 4.75,
			'7,18' => 11.4,
			'19' => 22.4,
			'20' => 100
			'type' => '2',
			'num' => '[1,6,18]',
			'count' => ''
		];  */
		return $this->setCode = $code;
	}
    
	private function sp()
	{
		$arr = $this->setCode;
		unset($arr['type']);
		unset($arr['num']);
		if(isset($arr['count'])){
			unset($arr['count']);
		}
		return $keys = array_keys($arr);
	}

    /**
     * 判断是否中奖
     * @return boolean
     */
    public function isWin()
	{
		//猜多个名次排序（直选）
		if($this->setCode['count']){
			if($this->code == array_keys($this->plan)){
				return $this->bonus($status = true);

			}
			return false;
		}
		$length = count($this->code);
		//开奖号码只有一位
		if($length == 1){
			if(in_array($this->code, array_keys($this->plan))){
				return $this->bonus();

			}
			return false;
		//开奖号码有多位（每位赔率不同）
		} else {
			foreach($this->code as $prize){
				if(in_array($prize, array_keys($this->plan))){
					$data[] = $prize;
				}
			}
			if(!empty($data)){
				return $this->bonus();
			}
			return false;
		}
	}

    /**
     * 奖金
	 * @return float
     */
    public function bonus()
	{
		//猜多个名次排序（直选）
		if($this->setCode['count']){
			return $plan[$this->code] * $this->setCode;
		}	
		$length = count($this->code);
		$sp = $this->sp();
		$i = 0;
		$money = 0;
		//开奖号码只有一位
		if($length == 1){
			foreach($sp as &$key){

				$codes = explode(',', $key);
				switch (count($codes)){
					case 1:
						if($this->code == $key){
							$money += $plan[$this->code] * $this->setCode[$key];
						}
					break;
					case 2:
						if($codes[0] <= $this->code  && $this->code  <= $codes[1]){
							$money += $plan[$this->code] * $this->setCode[$key];

						}
					break;
				}		
			}
			return $money;
		//开奖号码有多位（多位赔率不同）
		} else {
			foreach($this->code as $data){
				foreach($sp as &$key){
					$codes = explode(',', $key);
					switch (count($codes)){
						case 1:
							if($data == $key){
								$money += $plan[$this->code] * $this->setCode[$key];
							}
						break;
						case 2:
							if($codes[0] <= $data && $data <= $codes[1]){
								$money += $plan[$this->code] * $this->setCode[$key];

							}
						break;
					}	
				}
			}
			return $money;
		}
	}

    
	/**默认的最大概率 */
	private $max_sp = 1000;

	/**
     * 自动生成一个开奖号码
	 * @param array $list 中奖项（sign 中奖标识 multiple 倍数 sp 中奖概率）
	 * @param int $sp 最大概率是默认概率的多少倍
	 * @param int $length 开奖号长度
     * @return array 生成的开奖号码
     */
    public function createCode($list = '', $max_sign = '', $sp = 1, $length = 1)
	{
		if(!$list){
			return false;
		}
		/**根据需要更改最大概率，然后随机生成一个概率数 $rand_sp*/
		$max = $this->max_sp * $sp;
		$rand_sp = mt_rand(1, $max);
		/**根据概率选择中奖项 */
		$start = 0;
		foreach ($list as $key => $value) {
		   $end = $start + $value['sp'];
		   if($rand_sp > $start && $rand_sp < $end){
			    $sign = json_decode($value['sign'], true);
			    $count = getType($sign) == 'array' ?  count($sign) : 1;
				$_sign = $sign;
				$num = $count;
				$sp_award = $value['multiple'];
		   }
		   $start += $value['sp'];
		}
		/** 根据选项生成中奖号码 */

		/** 根据选项生成中奖号码, 如果长度和中奖标识相同，开奖号码便是标识，若不同还要随机生成数据填充 */
		$code = $_sign;
		if($num != $length){
			$diff_num = $length - $num;
			$code = $this->randNum($diff_num, $max_sign, $code);
		}
		return ['code' => $code, 'sp_award' => $sp_award, 'sign' => $_sign];
	}

	private function randNum($num,$max,$common)
	{
		for ($i=0; $i < $num; $i++) { 
			$rand_num = mt_rand(1,$max);
			if(in_array($rand_num, $common)){
				$rand_num = $this->mtRand($rand_num, $common, $max);
			}
			array_push($common, $rand_num);
		}
		return $common;
	}

	private function mtRand($num, $array, $max)
	{
		$rand_num = mt_rand(1,$max);
		if(in_array($rand_num, $array)){
			$rand_num = $this->mtRand($rand_num, $array, $max);
		}
		return $rand_num;
	}

	/**
	 * 派奖
	 * @param array $award 开奖信息
	 * @param array $list 押宝信息
	 * 先测试单个押宝，开奖号只有一个标识的
	 */
	public function openAward($award, $list)
	{
		$result = $award['code'] == $award['sign'] ? 1 : 0;
		if($result){
			// $str = is_array($award['code']) ? $award['code'] : [$award['code']];
			$str = $award['code'];
		}else{
			$str = array_values(array_intersect($award['code'], $award['sign']));
		}

		$awardList = [];
		foreach ($list as $key => $value) {
			$codelist = $value['codeList'];
			$allKey = array_keys($codelist);
			/**适用于只有一个开奖标识的 */
			foreach ($allKey as $val) {
				$money = $codelist[$val];
				$result = $val == $str ? 1 : 0;
				if($result){
					$awardList[] =  [
						'money' => $money,
						'id' => $value['id'],
						'userid' => $value['userid'],
					];
				}
			}
		}
		return $awardList;
	}
}