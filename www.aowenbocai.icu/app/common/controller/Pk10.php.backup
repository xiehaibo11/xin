<?php
namespace app\common\controller;

use app\lottery\model\LotteryCom;
use app\web\controller\UserBase;
use think\Controller;
use app\lottery\model\Pk10 as ASetting;
use app\index\model\ExtShowList;
use app\lottery\controller\Lottery;
use app\lottery\model\pk10\PluginPk10Code;
use app\lottery\controller\Pk10Buy;
use app\web\model\User;
use core\Setting;

class Pk10 extends UserBase
{
    protected $playWay = ['GJ' => '猜冠军','YJ' => '猜亚军','JJ' => '猜季军','QS' => '猜前四','QW' => '猜前五','QL' => '猜前六','QQ' => '猜前七','QB' => '猜前八','QJ' => '猜前九',
    'QSHI' => '猜前十','DW' => '定位胆','DXDS' => '大小单双','LH' => '龙虎斗'];
    protected $playType = ['GJ' => '1','YJ' => '2','JJ' => '3','QS' => '4','QW' => '5','QL' => '6','QQ' => '7','QB' => '8','QJ' => '9','QSHI' => '10','DW' => '11','DXDS' => '12',
    'LH' => '13'];

    public function index($name = 'pk10')
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
        $tenCode = $lottery_info['ten'];
        $getnewcode = $lottery_info['getnewcode'];
        $name = $lottery_info['name'];
        $miss = $lottery_info['miss'];
        $openTime = $lottery_info['openTime'];
        $open = $lottery_info['open'];
        $firstIssue = $lottery_info['firstIssue'];
        $bouns = $lottery_info['bouns'];

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
        return $this->fetch('',['title' => $nameTitle,'lottery' => $title,'play' => $play,'small' => $small, 'info' => $newArr,
		'ten' => $tenCode, 'open' => $open,'getnewcode' => $getnewcode,'miss' => $miss,'openTime' => $openTime,'name'=>$name,'firstIssue' => $firstIssue,'bouns' => $bouns,
            'bonus_base' => $bonus_base,
            'pause' => $title['pause'],
            'user_rebate' => $lottery_info['user_rebate'],
            'up_user_rebate' => $lottery_info['up_user_rebate']
            , 'mode' => $lottery_info['mode']]);
    }

	/***获取彩种初始信息*/
    public function getLotteryInfo($name = 'pk10')
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
                $v2 = (new LotteryCom())->setGain2($v2, ($bonus_base + $user_base));
            }
            $play = collection($play);
            if (isset($newArr['gain'])) {
                $newArr = (new LotteryCom())->setGain($newArr, ($bonus_base + $user_base));
            }
        }
        return json(['title' => $lottery_info['title'],'lottery' => $lottery_info['lottery'],'play' => $play,'small' => $small, 'info' => $newArr, 'ten' => $lottery_info['ten'],'getnewcode' => $lottery_info['getnewcode'],'miss' => $lottery_info['miss'],'openTime' => $lottery_info['openTime'],
            'bouns' => $lottery_info['bouns'],
            'name'=>'pk10',
            'firstIssue' => $lottery_info['firstIssue'],
            'bonus_base' => $lottery_info['bonus_base'],
            'mode' => $lottery_info['mode'],
            'user_rebate' => $lottery_info['user_rebate'],
            'up_user_rebate' => $lottery_info['up_user_rebate']
        ]);
	}

    /***获取彩种初始信息*/
    public function getLotteryCommon($name)
    {
        $res = (new ExtShowList)->field('name, title,image,pause')->where('name', 'in', [$name, '/'.$name])->where('status',0)->find();
        if(!$res){
            $this->error('该彩种暂未开启');
        }

        $setting = new ASetting;
        $isSelect = 0;
        $selectArr = [];
        /**奖金+玩法 */
        $bouns = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'bouns')),true);
        $play = [];
        $small = [];
        foreach ($bouns as $key => &$value) {
            if($key == 'WZ') continue;
            $resplay['sign'] = $key;
            $resplay['type'] = $this->playType[$key];
            $resplay['name'] = $this->playWay[$key];
            $resplay['isOpen'] = $value['isOpen'];
            unset($value['isOpen']);
            if(isset($value['wz'])){
                $newkey = $key.'.2';
                $newres['gain'] = $value['wz'];
                $newres['sign'] = $newkey;
                $newres['type'] = $this->playType[$key]."2";
                array_push($small, $newres);
                unset($value['wz']);
            }
            if ($resplay['isOpen']) {
                unset($bouns[$key]);
                continue;
            }
            if ($key == 'GJ') {
                $selectArr['gain'] = $value[1];
            }
            $resplay['gain'] = $key == 'GJ' || $key == 'DW' || $key == 'DXDS' || $key == 'LH' ? $value[1] : ($value[1]."-".$value[count($value)]);
            $value['name'] = $this->playWay[$key];
            array_push($play, $resplay);
        }
        /**彩种配置 */
        $cp_config = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'config')),true);

        /**获取期号 */
        $issue = (new Lottery)->getExpect($name)['data'];
        // /**获取最后一期 */
        $codeClass = LotteryCommon::getModel($name, 'code');
        $code = $codeClass->field('expect,code')->order('expect DESC')->find();
        session('getnewcode', null);
        $lastIssue = $issue['expect'] - 1;
        if(!$code || ($lastIssue != $code->expect)){
            $code = collection(['code' => '0,0,0,0,0,0,0,0,0,0', 'expect' => $lastIssue]);
            session('getnewcode', 1);
        }
        $newArr = array_merge($issue, $selectArr);
        $newArr['awardNumber'] = $code;
        $newArr['today'] = date("Ymd");
        $newArr['todayTime'] = date("Y-m-d");
        $newArr['issue'] = $newArr['expect'] = $issue['expect'];
        /**获取遗落数据 */
        $miss = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'miss')),true);
        $miss = $miss ? collection($miss) : 0;
        /**获取最新十期最新号码*/
        $tenCode = $codeClass->field('expect,code')->order('expect DESC')->limit(0,10)->select();
        $tenCode = $this->setCode($tenCode, 10);
        /**当前期开奖时间 */
        $openTime = (new Lottery())->issueToTime($name, $issue['expect']);
        $openTime = date("Y-m-d H:i:s", $openTime + 60);

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

        if(session('?getnewcode')){
            array_unshift($tenCode,['expect' => $lastIssue, 'code' =>['正','在','开','奖','中','.','.','.','.','.']]);
            $open[$lastIssue] = '开奖中..';
        }

        $lottery_common = (new \app\common\controller\Lottery())->getLotteryCommon($name, $this->user);
        return ['title' => $title['title'],'lottery' => $title,'play' => collection($play), 'info' => $newArr,'getnewcode' => session('?getnewcode'),
            'ten' =>collection($tenCode),'open' => collection($open),'firstIssue' => $fristExp,'openTime' => $openTime,'name' => $name,'small' => collection($small),'miss' => $miss,
            'bouns' => collection($bouns),
            'bonus_base' => 100 - $cp_config['bonus_base'],
            'mode' => $cp_config['mode'],
            'user_rebate' => $lottery_common['user_rebate'],
            'up_user_rebate' => $lottery_common['up_user_rebate']
        ];
    }

    /**获取期号 */
    public function getIssueInfo($name = 'pk10')
    {
        /**判断该彩种是否存在并开启 */
        $res = (new ExtShowList)->field('name')->where('name', 'in', [$name, '/'.$name])->where('status',0)->find();
        if(!$res){
            return json(['err' => 1, 'msg' => '该彩种暂未开启']);
        }
        $ext_info = $res->append(['expect_type'])->toArray();

        $issue = (new Lottery)->getExpect($name)['data'];
        $openTime = (new Lottery())->issueToTime($name, $issue['expect']);
        $issue['openTime'] = date("Y-m-d H:i:s", $openTime + 60);
        $issue['todayTime'] = date("Y-m-d");

        $codeClass = $this->getModel($name);
        $code = $codeClass->getNowCode();
        $code = $code ? ($code->toArray()) : ['expect' => 100000];
        session($name.'_getnewcode', null);
        if($issue['lastIssue'] != $code['expect']){
            $code = ['code' => '0,0,0,0,0,0,0,0,0,0', 'expect' => $issue['lastIssue']];
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
            array_unshift($tenCode,['expect' => $issue['lastIssue'], 'code' =>['正','在','开','奖','中','.','.','.','.','.']]);
        };
        $issue['open'] = collection($tenCode);
        return json(['err' => 0, 'data' => $issue]);
    }

    /**ajax
     * 获取最新开奖号码
     */
    public function getModel($name = 'pk10')
    {
        return LotteryCommon::getModel($name, 'code');
    }

    /**ajax获取最新开奖号码 */
    public function getNewCode($issue, $name = 'pk10')
    {   
        $codeClass = $this->getModel($name);
        $res = $codeClass->field('code,expect')->where('expect', $issue)->find();
        if(!$res){
            return json(['err' => 1]);
        }else{
            $tenCode = $codeClass->field('expect,code')->order('expect DESC')->limit(0,10)->select();
            $tenCode = $this->setCode($tenCode, 10);
            $data['codeOpen'] = implode(",", $tenCode[0]['code']);
            $data['tenCode'] = $tenCode;
            $data['expect'] = $tenCode[0]['expect'];
            $data['code'] = $tenCode[0]['code'];
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
        foreach ($code as &$v) {
            $v['code'] = explode(',', $v['code']);
        }
        return $code;
    }

    /**投注 */
    public function betting()
    {
        if(request()->isPost()){
            $user = User::get(['sid' => session('sid')]);
            if(!$user){
                return json(['err' => 1, 'msg' => '还未登录,请先登录']);
            }
            $data = request()->post();
            $model = LotteryCommon::getModel($data['lottery_name'], 'buy');
            unset($data['lottery_name']);
            return $model->add($data,$user);
        }
    }

    /**ajax获取遗漏 */
    public function getMiss($name = 'pk10')
    {
        $miss = json_decode((new ASetting)->getValue(LotteryCommon::getSettingValue($name, 'miss')),true);
        return json(['err' => 0, 'data' => $miss]);
    }

}