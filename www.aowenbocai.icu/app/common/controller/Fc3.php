<?php
namespace app\common\controller;

use app\lottery\model\LotteryCom;
use app\web\controller\UserBase;
use think\Controller;
use app\lottery\model\Fc3 as ASetting;
use app\index\model\ExtShowList;
use app\lottery\controller\Lottery;
use app\web\model\User;
use core\Setting;

class Fc3 extends UserBase
{
    
  private  function get_url($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);  //设置访问的url地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间
    $result =  curl_exec($ch);
    curl_close ($ch);
    return $result;
}

    /**
     * 获取默认期号信息（当API调用失败时使用）
     * @param string $name 彩种名称
     * @return array
     */
    private function getDefaultIssue($name)
    {
        try {
            // 尝试从数据库获取最新期号
            $common = \app\common\controller\LotteryCommon::getCommon($name);
            if ($common) {
                $lastIssue = $common->getLastIssue($name);
                if ($lastIssue) {
                    return [
                        'expect' => $lastIssue + 1,
                        'lastIssue' => $lastIssue
                    ];
                }
            }
        } catch (\Exception $e) {
            \think\Log::error("获取默认期号失败: " . $e->getMessage());
        }

        // 如果数据库也获取失败，使用基于时间的默认值
        $today = date('Ymd');
        $defaultIssue = $today . '001'; // 默认为当天第一期

        return [
            'expect' => intval($defaultIssue),
            'lastIssue' => intval($defaultIssue) - 1
        ];
    }

    /***这里面所有有关彩种的引用均是可变的，后期统一后再做调整 */
    public function index($name)
    {

        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['isGain','lottery_unit','coin_lottery', 'rebate_isOpen']);
        $this->assign('system', collection($system));

        $lottery_info = $this->getLotteryCommon($name);
       
		$nameTitle = $lottery_info['title'].'-投注页面';
        $title = $lottery_info['lottery'];
        $play = $lottery_info['play'];
        $small = $lottery_info['small'];
        $newArr = $lottery_info['info'];
        $cold = $lottery_info['cold'];
        $hot = $lottery_info['hot'];
        $tenCode = $lottery_info['ten'];
        $getnewcode = $lottery_info['getnewcode'];
        $name = $lottery_info['name'];
        $miss = $lottery_info['miss'];
        $openTime = $lottery_info['openTime'];
        $open = $lottery_info['open'];
        $firstIssue = $lottery_info['firstIssue'];

        $bonus_base = $lottery_info['bonus_base']; //系统基本奖金
        if (!$system['rebate_isOpen']) {//返点关闭  奖金扣除系统盈利
        
            $user_base = $lottery_info['up_user_rebate'];
            $user_base = $user_base < 0 ? 0 : $user_base;
            $small = $small->toArray();
            foreach ($small as &$v) {
                $v = (new LotteryCom())->setGain($v, ($bonus_base + $user_base));
            }
            $small = collection($small);
            $play = $play->toArray();
            foreach ($play as &$v2) {
                $v2 = (new LotteryCom())->setGain($v2, ($bonus_base + $user_base));
            }
            $play = collection($play);
            if (isset($newArr['gain'])) {
                $newArr = (new LotteryCom())->setGain($newArr, ($bonus_base + $user_base));
            }
        }
       
  
         if($name=='fc3')
         {
             try {
                 $url='https://api.api68.com/QuanGuoCai/getLotteryInfoList.do?lotCode=10041';

                 // 获取API数据
                 $response = $this->get_url($url);
                 if (empty($response)) {
                     throw new \Exception('API响应为空');
                 }

                 // JSON解码
                 $data = json_decode($response, true);
                 if (json_last_error() !== JSON_ERROR_NONE) {
                     throw new \Exception('JSON解码失败: ' . json_last_error_msg());
                 }

                 // 验证数据结构
                 if (!isset($data['result']['data']) || !is_array($data['result']['data'])) {
                     throw new \Exception('API返回数据结构异常: result.data不存在或不是数组');
                 }

                 $result = $data['result']['data'];
                 if (empty($result) || !isset($result[0]['preDrawIssue'])) {
                     throw new \Exception('API返回数据为空或缺少preDrawIssue字段');
                 }

                 $preissue = $result[0]['preDrawIssue'];
                 if (!is_numeric($preissue)) {
                     throw new \Exception('preDrawIssue不是有效的数字');
                 }

                 $newArr['expect'] = intval($preissue) + 1;
                 $newArr['lastIssue'] = intval($preissue);

                 // 记录成功日志
                 \think\Log::info("fc3 API调用成功，期号: {$preissue}");

             } catch (\Exception $e) {
                 // 记录错误日志
                 \think\Log::error("fc3 API调用失败: " . $e->getMessage() . ", URL: {$url}");

                 // 提供默认值或从数据库获取备用数据
                 $defaultIssue = $this->getDefaultIssue($name);
                 $newArr['expect'] = $defaultIssue['expect'];
                 $newArr['lastIssue'] = $defaultIssue['lastIssue'];
             }
         }
         

        return $this->fetch('',['title' => $nameTitle,'lottery' => $title,'play' => $play,'small' => $small, 'info' => $newArr, 'cold' => $cold,
        'hot' => $hot, 'ten' => $tenCode, 'open' => $open,'getnewcode' => $getnewcode,'miss' => $miss,'openTime' => $openTime,'name'=>$name,'firstIssue' => $firstIssue, 'bonus_base' => $bonus_base,
            'pause' => $title['pause'],
            'user_rebate' => $lottery_info['user_rebate'],
            'up_user_rebate' => $lottery_info['up_user_rebate']
        , 'mode' => $lottery_info['mode']]);
    }

	/***获取彩种初始信息*/
	public function getLotteryInfo($name)
	{
        $lottery_info = $this->getLotteryCommon($name);
        $system = (new Setting)->get(['isGain','lottery_unit','coin_lottery', 'rebate_isOpen']);
        $small = $lottery_info['small'];
        $play = $lottery_info['play'];
        $bonus_base = $lottery_info['bonus_base']; //系统基本奖金
        $newArr = $lottery_info['info'];
        if (!$system['rebate_isOpen']) {//返点关闭  奖金扣除系统盈利
            $user_base = $lottery_info['up_user_rebate'];
            $user_base = $user_base < 0 ? 0 : $user_base;
            $small = $small->toArray();
            foreach ($small as &$v) {
                $v = (new LotteryCom())->setGain($v, ($bonus_base + $user_base));
            }
            $small = collection($small);
            $play = $play->toArray();
            foreach ($play as &$v2) {
                $v2 = (new LotteryCom())->setGain($v2, ($bonus_base + $user_base));
            }
            $play = collection($play);
            if (isset($newArr['gain'])) {
                $newArr = (new LotteryCom())->setGain($newArr, ($bonus_base + $user_base));
            }
        }

		return ['title' => $lottery_info['title'],'lottery' => $lottery_info['lottery'],'play' => $play,'small' => $small, 'info' => $newArr, 'ten' => $lottery_info['ten'],'getnewcode' => $lottery_info['getnewcode'],'miss' => $lottery_info['miss'],'openTime' => $lottery_info['openTime'],
		'name'=>$name,
        'firstIssue' => $lottery_info['firstIssue'],
        'bonus_base' => $lottery_info['bonus_base'],
        'mode' => $lottery_info['mode'],
        'user_rebate' => $lottery_info['user_rebate'],
        'up_user_rebate' => $lottery_info['up_user_rebate']
        ];
	}

    /***获取彩种初始信息*/
    public function getLotteryCommon($name)
    {
        /**判断该彩种是否存在并开启 */
        $res = (new ExtShowList)->field('name, title,image, pause')->where('name', 'in', [$name, '/'.$name])->where('status',0)->find();
        if(!$res){
            $this->error('该彩种暂未开启');
        }
        $setting = new ASetting;
        /**彩种配置 */
        $cp_config = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'config')),true);
       
        /**默认玩法type */
        $selected = $cp_config['selected'];
        $isSelect = 0;
        $play = [];
        /**奖金+玩法 */
        $bouns = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'bouns')),true);
   
        if (!empty($bouns)) {
            foreach ($bouns as $key => $value) {
                if(isset($value['type']) && $value['type'] != '' && $value['isOpen'] != 1){
                    $play[] = $value;
                    if($value['type'] == $selected){
                        $isSelect = 1;
                        $selectArr['type'] = $selected;
                        $selectArr['gain'] = $value['gain'];
                    }
                }
                if(!isset($value['type']) || !$value['type']){
                    $sign = explode('.', $value['sign']);
                    if (!isset($small[$sign[0]][$sign[1]])) {
                        $small[$sign[0]][$sign[1]] = [];
                    }
                    if (isset($sign[2])) {
                        if (!isset($small[$sign[0]][$sign[1]][$sign[2]])) {
                            $small[$sign[0]][$sign[1]][$sign[2]] = [];
                        }
                        $small[$sign[0]][$sign[1]][$sign[2]]['isOpen'] = $value['isOpen'] ? 1 : 0;
                        $small[$sign[0]][$sign[1]][$sign[2]]['gain'] = $value['gain'] ? 1 : 0;
                    } else {
                        $small[$sign[0]][$sign[1]]['isOpen'] = $value['isOpen'] ? 1 : 0;
                        $small[$sign[0]][$sign[1]]['gain'] = $value['gain'];
                    }
                }
            }
        }
        $small = isset($small) ? $small : [];
        if(!empty($small)){
            foreach ($small as $key => &$value) {
                $value = collection($value);
            }
        }

        if($isSelect != 1){
            $selectArr['type'] = $play[0]['type'];
            $selectArr['gain'] = $play[0]['gain'];
        }

        /**获取期号 */
        $issue = (new Lottery)->getExpect($name)['data'];
        
        /**获取最后一期 */
        $codeClass = $this->getModel($name);
        $code = $codeClass->getNowCode();
        $code = $code ? ($code->toArray()) : ['expect' => 100000];
        session($name.'_getnewcode', null);
      
 
        if($issue['lastIssue'] != $code['expect']){
            $code = ['code' => $code['code'], 'expect' => $code['expect']];
            session($name.'_getnewcode', 1);
        }
        // $newArr = array_merge($issue, $info, $selectArr);
        $newArr = array_merge($issue, $selectArr);
        $newArr['awardNumber'] = collection($code);
        $newArr['today'] = date("Ymd");
        $newArr['todayTime'] = date("Y-m-d");
        $newArr['issue'] = $newArr['expect'] = $issue['expect'];
        /**获取遗落数据 */
        $miss = json_decode($setting->getValue($name.'_miss'),true);

        $miss = $miss ? collection($miss) : 0;
        /**获取冷热号 */
        $hot = $this->getHot($name);
        /**获取最新十期最新号码*/
        $tenCode = $codeClass->field('expect,code')->order('expect DESC')->limit(0,10)->select();
        $tenCode = $this->setCode($tenCode, 10);

        $title = $res->append(['expect_type'])->toArray();
        /**获取今日开奖数据 */
        /**获取今日第一期期号 */
        if ($title['expect_type']) {
            $newArr['sale'] = intval((time() - strtotime(date("Y-m-d").$newArr['startTime'])) / ($newArr['timelong'] * 60));
            $fristExp = $newArr['issue'] - $newArr['sale'];
            $open = $codeClass->where('expect', '>=', $fristExp)->order('expect ASC')->column(['expect','code']);
        } else {
            $fristExp = date('Ymd');
            if ($newArr['startIssue'] >= 1000) {
                $fristExp .= '0001';
            } elseif ($newArr['startIssue'] >= 100) {
                $fristExp .= '001';
            } else {
                $fristExp .= '01';
            }
            $open = $codeClass->where('LOCATE('.date("Ymd").',`expect`)>0')->order('expect ASC')->column('expect,code');
        }

        if(session('?'.$name.'_getnewcode')){
            array_unshift($tenCode,['expect' => $issue['lastIssue'], 'code' =>['开','奖','中','.','.'], 'shi' => '--', 'ge'=>'--','hs' => '--']);
            $open[(string)$issue['lastIssue']] = '开奖中..';
        };

        /**当前期开奖时间 */
        $openTime = (new Lottery())->issueToTime($name, $issue['expect']);
        $openTime = date("Y-m-d H:i:s", $openTime + 10);


        $lottery_common = (new \app\common\controller\Lottery())->getLotteryCommon($name, $this->user);

        return ['title' => $title['title'],'lottery' => $title,'play' => collection($play),'small' => collection($small), 'info' => $newArr, 'cold' => collection($hot['cold']),
            'hot' => collection($hot['hot']), 'ten' =>collection($tenCode), 'open' => collection($open),'getnewcode' => session('?'.$name.'_getnewcode'),'miss' => $miss,'openTime' => $openTime,
            'firstIssue' => $fristExp,
            'name'=>$name,
            'bonus_base' => 100 - $cp_config['bonus_base'],
            'mode' => $cp_config['mode'],
            'user_rebate' => $lottery_common['user_rebate'],
            'up_user_rebate' => $lottery_common['up_user_rebate']
        ];
    }

    /**获取期号 */
    public function getIssueInfo($name = 'fc3')
    {
        /**判断该彩种是否存在并开启 */
        $res = (new ExtShowList)->field('name')->where('name', 'in', [$name, '/'.$name])->where('status',0)->find();
        if(!$res){
            return json(['err' => 1, 'msg' => '该彩种暂未开启']);
        }
        $ext_info = $res->append(['expect_type'])->toArray();

        $issue = (new Lottery)->getExpect($name)['data'];
     
        $openTime = (new Lottery())->issueToTime($name, $issue['expect']);
        $issue['openTime'] = date("Y-m-d H:i:s", $openTime + 10);
        $issue['todayTime'] = date("Y-m-d");

        $codeClass = $this->getModel($name);
        $code = $codeClass->getNowCode();
        $code = $code ? ($code->toArray()) : ['expect' => 100000];
        session($name.'_getnewcode', null);
        if($issue['lastIssue'] != $code['expect']){
            $code = ['code' => '-1,-1,-1,-1,-1', 'expect' => $issue['lastIssue']];
            session($name.'_getnewcode', 1);
        }
        $issue['awardNumber'] = collection($code);
        $issue['getnewcode'] = session('?'.$name.'_getnewcode');

        $lotery_setting = json_decode((new ASetting)->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        if ($ext_info['expect_type']) {
            $sale = intval((time() - strtotime(date("Y-m-d").$lotery_setting['startTime'])) / ($lotery_setting['timelong'] * 60));
            $fristExp = $issue['expect'] - $sale;
        } else {
            $fristExp = date('Ymd');
            if ($lotery_setting['startIssue'] >= 1000) {
                $fristExp .= '0001';
            } elseif ($lotery_setting['startIssue'] >= 100) {
                $fristExp .= '001';
            } else {
                $fristExp .= '01';
            }
        }
        $issue['expect_type'] = $ext_info['expect_type'];
        $issue['firstIssue'] = $fristExp;

        $tenCode = $codeClass->field('expect,code')->order('expect DESC')->limit(0,10)->select();
        $tenCode = $this->setCode($tenCode, 10);
        if(session('?'.$name.'_getnewcode')){
            array_unshift($tenCode,['expect' => $issue['lastIssue'], 'code' =>['开','奖','中','.','.'], 'shi' => '--', 'ge'=>'--','hs' => '--']);
        };
        $issue['open'] = collection($tenCode);

        return json(['err' => 0, 'data' => $issue]);
    }
    /**ajax获取遗漏 */
    public function getMiss($name = 'fc3')
    {
        $miss = json_decode((new ASetting)->getValue($name.'_miss'),true);
        return json(['err' => 0, 'data' => $miss]);
    }

    /**获取冷热号 */
    public function getHot($name = 'fc3', $num = 100)
    {
        $codeClass = $this->getModel($name);
        $code = $codeClass->order('expect desc')->limit(0,100)->column('code');
        for ($i = 0; $i < 10 ; $i++) {
            $codeNum[$i] = 0;
        }
        foreach ($code as $key => $value) {
            $valCode = explode(',', $value);
            if(count($valCode) != 5){
                continue;
            }
            foreach ($valCode as $val) {
                $codeNum[intval($val)] +=1;
            }
        }
        asort($codeNum);
        $i = 0;
        foreach ($codeNum as $key => $value) {
            if($i < 5){
                $data['cold'][] = ['code' => $key, 'num' => $value];
                $i++; continue;
            }
            break;
        }

        arsort($codeNum);
        $i = 0;
        foreach ($codeNum as $key => $value) {
            if($i < 5){
                $data['hot'][] = ['code' => $key, 'num' => $value];
                $i++; continue;
            }
            break;
        }
        return $data;
    }

    /**ajax获取冷热号 */
    public function getColdHot($name)
    {
        $res = $this->getHot($name);
        return json(['err' => 0, 'data' => $res]);
    }

    /**ajax获取最新开奖号码 */
    public function getModel($name = 'fc3')
    {
        return LotteryCommon::getModel($name, 'code');
    }

    /**ajax获取最新开奖号码 */
    public function getNewCode($issue, $name = 'fc3')
    {
        $codeClass = $this->getModel($name);
        $res = $codeClass->field('code,expect')->where('expect', $issue)->find();
        if(!$res){
            return json(['err' => 1]);
        }else{
            $tenCode = $codeClass->field('expect,code')->order('expect DESC')->limit(0,10)->select();
            $tenCode = $this->setCode($tenCode, 10);
            $data['tenCode'] = $tenCode;
            $data['sort_exp'] = mb_substr($tenCode[0]['expect'], -2);
            $data['code'] = $tenCode[0]['code'];
            $data['expect'] = $tenCode[0]['expect'];
            $data['codeOpen'] = implode(",", $tenCode[0]['code']);
            return json(['err' => 0, 'data' => $data]);
        }
    }

    /**开奖号码设置形态 */
    public function setCode($code, $num)
    {
        if(!$code){
            return [];
        }

        $code = $code->toArray();
        if($num == 1){
            $code = $this->setCode2($code);
        }else{
            foreach ($code as &$value) {
                $value =$this->setCode2($value);
            }
        }
        return $code;
    }
    /**开奖号码设置形态 2*/
    public function setCode2($code)
    {
        $sum = 0;
        $da = 0;
        $xiao = 0;
        $dan = 0;
        $shuang = 0;

        $code['code'] = explode(',', $code['code']);
        if(count($code['code']) != 5){
			$code['hs'] = '--';
			$code['shi'] = '--';
			$code['ge'] = '--';
			return $code;
		}
        $last = array_slice($code['code'], 2);
        $hsCount = count(array_unique($last));
        $code['hs'] = $hsCount == 3 ? '组六' : ($hsCount == 1 ? '豹子' : '组三');
        $codeKey = ['shi', 'ge'];
        for ($i = 0; $i < 2 ; $i++) {
            $code[$codeKey[$i]] = $last[$i + 1] > 5 ? '大' : '小';
            $code[$codeKey[$i]] .= $last[$i + 1] % 2 == 0  ? '双' : '单';
        }
        return $code;
    }
    public function betting()
    {
        if(request()->isPost()){
            $user = User::get(['sid' => session('sid')]);
            if(!$user){
                return json(['err' => 1, 'msg' => '还未登录,请先登录']);
            }
            $data = request()->post();
            $url = LotteryCommon::getModel($data['lottery_name'], 'buy');
            unset($data['lottery_name']);
            return $url->add($data,$user);
        }
    }

}