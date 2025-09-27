<?php
namespace app\lottery\model;

use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\admin\model\User;
use app\common\controller\LotteryCommon;
use app\lottery\controller\Lottery;
use think\Model;
use app\common\model\MoneyHistory;
use think\Validate;
use core\Setting;

/**所有彩种通用的方法 */
class LotteryCom extends Model
{
    /**发单购买 */
    public function buyCom($post, $user, $playType, $name)
    {
        $setting = (new Setting())->get(['max_chase']);
        $model = LotteryCommon::getSetting($name);
        $name_setting = json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        $res = (new ExtShowList())->where('name', 'in', [$name, '/'.$name])->where('pause',0)->where('status',0)->find();
        if(!$res){
            return ["err"=>1,"msg"=>'该彩种暂未开启'];
        }
        $checkData = $this->checkPost($post, $user);//数据验证转换
     
        if($checkData['err']){
            return $checkData;
        }
        $data = $checkData['data'];
        //验证期数
        $expect = array();

        foreach ($data['expect'] as $v) {
            array_push($expect, $v['expect']);
        }
        $min_expect = min($expect); //获取期数最小值
        $dq_expect = (new Lottery())->getExpect($name)['data'];//要修改
      //  if (floatval($dq_expect['expect']) > floatval($min_expect)&&!strstr($name,'fc')){
         //   return ["err"=>6,"msg"=>'您投注的第'. $min_expect .'期已截止，请投注其他期数'];
      //  }
        $num_expect = count($expect);
        $is_chase = $num_expect > 1 ? 1 : 0;
        $allow_expect = $setting['max_chase'] > $name_setting['startIssue'] ? $name_setting['startIssue'] : $setting['max_chase'];
        if ($allow_expect < $num_expect) {
            return ["err" => 7, "msg"=>'您最多可以追' . $setting['max_chase'] . '期'];
        }
        //投注号码 格式化
        $playNum = (LotteryCommon::getCommon($name))->playNum($data, $playType, $is_chase, $name);
        if (isset($playNum['err'])) {
            return ["err"=>5,"msg"=>'投注号码有误<err:5>'];
        }
        $playss=json_decode($post['play_num'],true);
        $playexpectss=json_decode($post['expect'],true);
        $total_bs=0;
      foreach($playexpectss as $k)
      {
          
        $total_bs+=$k['multiple'];  
      }
        $tatol_money=0;
        foreach($playss as $vo)
        {
            
          $tatol_money+=$vo['money']*$total_bs;   
        }
        //$tatol_money =  $this->totalMoney($playNum, $data);

        /**获取截止时间 */
        $lottery = new Lottery();
        if($name=='ynssc'||$name=='jlssc')
        {
            $currentTime = date('H:i');
        $targetTime = '21:00';
        if(strtotime($currentTime)>strtotime($targetTime))
        $end_time = date('Y-m-d 21:00:00', time()+86400);
        else $end_time=date('Y-m-d 21:00:00',time());

        if(strtotime(date('H:i',time()))>=strtotime('21:00')&&strtotime(date('H:i',time()))<=strtotime('21:50')&&($name=='ynssc'||$name=='jlssc'))
        return ["err"=>6,"msg"=>'系统封盘中，暂停投注<err:6>'];
        }
        else
        $end_time = date('Y-m-d H:i:s', $lottery->issueToTime($name, $min_expect));

        $data['lottery_id'] = strtoupper($name)."|".$data['userid'].str_replace('.','',sprintf("%.4f",microtime(true)));
        $res = $this->checkPro($data, $user, $tatol_money, $name, count($expect));//写入资金明细 - 并转换资金相关的 data
        if($res['err']){
            return $res;
        }
        $data = $res['data'];
        $buyData = [
            'play_num' => json_encode($playNum['play']),
            'is_join' => $data['is_join'],
            'is_stop' => $data['is_stop'],
            'show' => $data['show'],
            'declaration' => $data['declaration'],
            'gain' => $data['gain'],
            'assure_money' => $data['assure_money'],
            'userid' => $data['userid'],
            'total_money' => $tatol_money,
            'total_share' => $data['total_share'],
            'end_time' => $end_time,
            'lottery_id' => $data['lottery_id'],
            'status' =>  0
        ];
        return ['err' => 0, 'data' => ['buy' => $buyData, 'data' => $data, 'nowIssue' => $dq_expect['expect']]];
    }

    /**投注验证提交信息 */
    public function checkPost($post, $user)
    {
        if (is_array($post['play_num'])) {
            $play_array = $post['play_num'];//投注号码
        } else {
            $play_array = json_decode($post['play_num'], true);//投注号码
        }
        foreach ($play_array as $row) {
            if ((isset($row['multiple']) and $row['multiple'] <= 0) and (isset($row['money']) and $row['money'] <= 0)){
                return ["err"=>3, "msg"=>'投注参数错误<01>'];
            }
        }
        if (is_array($post['expect'])) {
            $expect = $post['expect'];//期数
        } else {
            $expect = json_decode($post['expect'], true);//期数
        }
        foreach ($expect as $v) {
            if (!isset($v['expect']) || (isset($v['multiple']) and $v['multiple'] <= 0)){
                return ["err"=>3, "msg"=>'投注参数错误<02>'];
            }
        }
        if (!isset($expect[0]['expect'])) {
            return ["err"=>3, "msg"=>'期号不正确<01>'];
        }

        if (isset($post['money']) and $post['money'] <= 0 and $post['is_hemai']) {
            return ["err"=>3, "msg"=>'投注参数错误<03>'];
        }
        $is_hemai = $post['is_hemai'];//是否合买
        $is_stop = $post['is_stop'];//是否合买
        $show = isset($post['show']) ? $post['show'] : 1;//是否公开
        $declaration = isset($post['declaration']) ? $post['declaration'] : '';//合买宣言
        $gain = isset($post['gain']) ? $post['gain'] : 0;//合买提成
        $money = isset($post['buy_share']) ? intval($post['buy_share']) : 0;//购买金额取绝对值
        $assure_money = isset($post['bd_share']) ? intval($post['bd_share']) : 0;//购买保底金额
        $total_share = isset($post['total_share']) ? intval($post['total_share']) : 0;
        if ($is_hemai) {
            if (!isset($post['total_share']) || $total_share <= 0) {
                return ["err"=>4,"msg"=>'合买份数错误<err:5>'];
            }
            if ($money <= 0) {
                return ["err"=>4,"msg"=>'合买购买份数错误<err:6>'];
            }
            if ($assure_money < 0) {
                return ["err"=>4,"msg"=>'保底购买份数错误<err:6>'];
            }
        }
        if($money < 0){
            return ["err"=>4,"msg"=>'投注金额错误<err:4>'];
        }
        $data = [
            'play_array' => $play_array,
            'expect' => $expect,
            'is_join' => $is_hemai,
            'is_stop' => $is_stop,
            'show' => $show,
            'declaration' => $declaration,
            'gain' => $gain,
            'money' => $money,
            'assure_money' => $assure_money,
            'total_share' => $total_share,
            'userid' => $user['id']
        ];
        
        $validate = new Validate([
            'play_array|玩法'   => 'require',
            'expect|期数'   => 'require',
        ]);
        $result = $validate->check($data);
       
        if (!$result) {
            return ["err"=>1, "msg"=>$validate->getError()];
        }
        return ['err' => 0, 'data' => $data];
    }

    /**验证信息合法性，组合需要数据 */
    public function checkPro($data, $user, $tatol_money, $name, $expect_num)
    {
         if ($data['is_join']) {
             $money = (($data['money'] + $data['assure_money']) * $tatol_money)/$data['total_share'];
             $money = sprintf("%.2f",substr(sprintf("%.3f", $money), 0, -2));
         } else {
             $money = $tatol_money;
         }
         if (!$money) {
             return ["err"=>4, "msg"=>'投注金额有误<4>'];
         }
         $saveMoney = $money;//金豆转换  -- 现在无用
         if($saveMoney > $user['money']){
             return ["err"=>7,"msg"=>'余额不足'];
         }
         if ($data['is_join'] && $tatol_money < $money){
             return ["err"=>8, "msg"=>'保底金额加购买金额超过总金额'];
         }
         $action_content = $expect_num > 1 ? '购买:'.$data['lottery_id'].':追号合买' : '购买:'.$data['lottery_id'].':自购合买';
         if (!$data['is_join']) {
             $data['assure_money']=0.00;
             $data['money'] = $money;
             $action_content =  $expect_num > 1 ? '购买:'.$data['lottery_id'].":追号" : '购买:'.$data['lottery_id'].":自购";
         }
         //添加资金明细
         $money_mx = [
             'userid'  => $data['userid'],
             'money' => -$saveMoney,
             'ext_name' => $name,
             'remark' => $action_content
         ];
         $res_mx = (new MoneyHistory)->write($money_mx);
         if (!$res_mx['code']){
             return ["err"=>9,"msg"=>$res_mx['msg']];
         }
         return ['err' => 0, 'data' => $data];
    }
    
    /**参与合买 */
    public function joinCom($data, $info,$joinList, $coinLottery)
    {
        $data['money'] = intval($data['money']);
        if ($data['money'] <= 0 ){
            return ["code"=>0,"msg"=>'购买份数不能为0'];
        }
        $validate = new Validate([
            'money|份数'   => 'require',
            'userid|用户'   => 'require',
            'buy_id|合买方案'   => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0,"msg" => $validate->getError()];
        }
        $moneys = array_sum(array_column($joinList, 'money'));
        if ($info['total_share'] - $moneys < $data['money']){
            return ["code"=>0,"msg"=>'购买的份数不能大于剩余份数'];
        }
        $saveMoney = ($data['money'] * $info['total_money'])/$info['total_share'];
        $saveMoney = sprintf("%.2f",substr(sprintf("%.3f", $saveMoney), 0, -2));
        $money_mx = [
            'userid'  => $data['userid'],
            'money' => -$saveMoney,
            'ext_name' => $info['ext_name'],
            'remark' => '参与:'.$info['lottery_id']
        ];
        $res_mx = (new MoneyHistory)->write($money_mx);//添加资金明细
        if (!$res_mx['code']){
            return ["code"=>0,"msg"=>$res_mx['msg']];
        }

        //添加或更新参与会员信息
        $userids = array_column($joinList, 'userid');
        if(in_array($data['userid'], $userids)){
            $key = array_search($data['userid'], $userids) + 1;
        }
        $key = isset($key) ? $key : 0;
        return ['err' => 0, 'data' => $data, 'key' => $key, 'buyMoney' => $moneys + $data['money']];
    }

    /**
     *  计算总价格
     * @return string
     */
    public function totalMoney($playNum,$data)
    {
        $multiple = 0;
        $total_money = 0;
        $unit_zh = [1 => 1, 2 => 10, 3 => 100, 4 => 1000];
        foreach ($data['expect'] as $key => $expect){
            $multiple +=  $expect['multiple'];
        }
        foreach ($playNum['play'] as $v) {
            $money = 0;
            if (isset($v['money'])) {
                $money += $v['money'];
            }
            if (isset($v['unit'])) {
                $money += $v['multiple'] * 2 / $unit_zh[$v['unit']];
            }
            $money = $money ? $money : 2;
            $total_money += $v['note_num'] * $multiple * $money;
        }
        return $total_money;
    }

    /**
     *  根据投注模式转换投注的金额
     * @return array
     */
    public function playModeInit($data, $is_chase, $play_mode = 1, $user_rebate)
    {
        $codeArray = [
            'type' => $data['type'],
            'num' => $data['num']
        ];
        $setting = (new Setting())->get(['unit_isOpen', 'rebate_isOpen']);
        if ($play_mode == 1) {//模式1
            if ($setting['unit_isOpen']) {
                if (!isset($data['unit_value']) || !in_array(floatval($data['unit_value']), [1,2,3,4])) {
                    return ['err' => 1];
                }
                $codeArray['unit'] = $data['unit_value'];
                $codeArray['multiple'] = $is_chase ? 1 : $data['multiple'];
            }
        } elseif ($play_mode == 2) {//模式2
            if (floatval($data['money']) < 1 && is_numeric($data['money'])) {
                return ['err' => 1];
            }
            if ($is_chase) {
                return ['err' => 1];
            }
            $codeArray['money'] = $data['money'];
        }
        if ($setting['rebate_isOpen']) {//判断是否开启返点 并验证返点
            if ($user_rebate) {
                $data['rebate'] = isset($data['rebate']) ? $data['rebate'] : 0;
                if ($data['rebate'] > $user_rebate) {
                    return ['err' => 1];
                }
                $codeArray['rebate'] = $data['rebate'];
            } else {
                $codeArray['rebate'] = 0;
            }
        }
        return ['err' => 0, 'data' => $codeArray];
    }

    /**
     * 组合算法，计算注数
     * @param $m 备选个数
     * @param $n 需要选的个数
    */
    public function countNum($m, $n)
    {
        if($n > $m ){
            return 0;
        }
        $diff = $m - $n;
        $mm = 1;
        for($i = 1; $i <= $m; $i++){
            $mm *= $i; 
        }

        $diffm = 1;
        for($i = 1; $i <= $diff; $i++){
            $diffm *= $i; 
        }

        $nn = 1;
        for($i = 1; $i <= $n; $i++){
            $nn *= $i; 
        }
        return $mm / ($diffm * $nn); 
    }

    /**系统自动出票判断及期号数据 */
    public function expectData($data, $total_money, $buy_id, $nowExpect, $ext_name = '')
    {
         //添加期号--自动出票判断
         $setting = (new Setting)->get(['isAuto', 'openBd', 'Bdpercent']);
         $isAuto = !empty($setting) && isset($setting['isAuto']) ? $setting['isAuto'] : 0;
         /**合买判断出票 */
         if($data['is_join'] == 1 && $isAuto){
             $allMoney = $data['money'] + $data['assure_money'];
             $percent = 100;
             if($setting['openBd'] == 1){
                 $percent = floatval($setting['Bdpercent']) > 0 ? floatval($setting['Bdpercent']) : $percent;
             }
             $isAuto =  floatval($allMoney / $total_money * 100) < $percent ? 0 : 1;
         }
         foreach ($data['expect'] as $key => $v){
             $expect_array[$key]['expect'] = $v['expect'];
             $expect_array[$key]['multiple'] = floatval($v['multiple']) > 0 ? floatval($v['multiple']) : 1;
             $expect_array[$key]['buy_id'] = $buy_id;
             $expect_array[$key]['status'] = $nowExpect ==$v['expect'] &&  $isAuto ? 1 : 0;
             // 添加ext_name字段，修复投注失败<代码：02>问题
             if ($ext_name) {
                 $expect_array[$key]['ext_name'] = $ext_name;
             }
         }
         return $expect_array;
    }

    /**合买派奖，更新数据 */
    public function joinPrize($data, $list, $setting, $is_chase ,$expectBuyid, $cp_type)
    {
        $bouns = $data['bouns'];
        $moneyHistory = new MoneyHistory;
        /**发起人提成*/
        if($data['gain'] > 0){
            $gainBouns = ($bouns * $data['gain'] / 100) * floatval( $setting['settingCoin']);
            $moneyHistory->write(['userid'  => $data['userid'],'money' => $gainBouns,'ext_name' => Request()->module(),'remark' => '提成:'.$data['lottery_id'], 'type' => 5]);
            $bouns -= $gainBouns; 
        }
        $buyall = array_sum(array_column($list, 'money'));
        $total_share = $data['total_share'];
        $bdNum = $data['assure_money'];
        $bdJoin = [];
        $first = true;
        $useBd = false;
        if($is_chase){//判断是否有追号
            $min = min(array_column($expectBuyid, 'expect'));
            $first = $min == floatval($data['expect']) ? $first : false;
        }
        /**合买数据小于总份数 */
        if($buyall < $total_share){
            $buyall += $bdNum;
            $useBd = true;
			
			
	
			
			
			
			
            /**（合买数据+发起人保底数）与 总分数相比较*/
            if($buyall >= $total_share){
                /**计算总分数超过多少，多余的返回保底 */
                $bd_ure = $buyall - $total_share;
                $bdNum -= $bd_ure;
            }else{
                /**系统撤单 后台进行 == 系统保底操作 */
                if(!$setting['openBd']){
                    return ['err' => 2];
                }
                $bdpercent = floatval($setting['Bdpercent']) > 0 ? floatval($setting['Bdpercent']) : 100;
                $buyPercent = floatval($buyall / $total_share * 100);
                if($buyPercent < $bdpercent){
                    return ['err' => 2];
                }
                
                $systembuy = $total_share - $buyall;//系统保底的金额
                $bdJoin = ['userid' => 1, 'money' => $systembuy, 'buy_id' => $data['buy_id']];
                array_push($list, ['id' => 'system','userid' => 1,  'money' => $systembuy, 'bonus' => 0]);
            }
        }
        /**返回保底-- 未使用保底，或者已使用部分返回剩余保底 ==追号在第一期进行*/
        if((!$useBd || isset($bd_ure)) && $first){
           $bdReturn = !$useBd ? $bdNum : $bd_ure;
            if($bdReturn > 0){
               $moneyHistory->write(['userid' => $data['userid'], 'money' =>$bdReturn * $setting['settingCoin'], 'remark' => '返保:'.$data['lottery_id']]);
            }
        }
        if(isset($bd_ure) && !$first){
            $total_share -= $bdNum;
        }
        $joinUpdate = [];
        //更新合买表数据，奖金累计
        foreach ($list as $value) {
            $money = $useBd && $first && $value['userid'] == $data['userid'] ? ($value['money'] + $bdNum) : $value['money'];//发起人投注金额加上保底金额
            $onePer = $money / $total_share;
            $now_bonus = floatval($onePer * $bouns * 100) / 100;//当前期的奖金
            $bonus = $value['bonus'] + $now_bonus;//累加之前的奖金
            if($value['id'] == 'system'){//系统保底的投注金额
                $bdJoin['bonus'] = $bonus;
                continue;
            }
            $bonusArr = ['id' => $value['id'], 'bonus' => $bonus];
            $joinUpdate[] = $bonusArr;
            if(isset($value['userid']) && $value['userid'] != 1){
                //写奖金记录
                if ($now_bonus > 0) $moneyHistory->write(['userid' => $value['userid'], 'money' => $now_bonus * $setting['settingCoin'],'type' => 1,'hm' => 1,  'remark' => '中奖:'.$data['lottery_id']]);
                //返点
                $this->rebate($data['play_num'], $value['userid'], $onePer, $data['multiple'], $cp_type, $data['lottery_id']);
            }
        }
        return ['update' => $joinUpdate, 'bd' => $bdJoin];
     }

    /**返点 */
    public function rebate($plan, $userid, $buyPer, $multiple, $cp_type, $lottery_id)
    {
        $moneyHistory = new MoneyHistory;
        $unit_zh = [1 => 1, 2 => 10, 3 => 100, 4 => 1000];
        $user_info = User::get($userid);
        if (!$user_info || !$user_info['agents']) return;
        foreach ($plan as $val) {
            $money = 0;
            if (isset($val['money'])) {
                $money += $val['money'];
            }
            if (isset($val['unit'])) {
                $money += 2 / $unit_zh[$val['unit']];
            }
            if (isset($val['multiple'])) {
                $multiple = $val['multiple'];
            }
            $money = $money ? $money : 2.00;
            $money = $val['note_num'] * $multiple * $money * $buyPer;
            if (isset($val['rebate']) and $val['rebate'] > 0) $moneyHistory->write(['userid' => $userid, 'money' => $money * $val['rebate']/100,'type' => 6,  'remark' => '返点:'.$lottery_id]);
            $this->parent_rebate($userid, $cp_type, $money, $lottery_id);
        }
    }

    /**递归返点 */
    public function parent_rebate($userid, $cp_type, $money, $lottery_id)
    {
        $user_model = new \app\index\model\User();
        $moneyHistory = new MoneyHistory;
        $user_info = $user_model->find($userid);
        $my_rebate = isset($user_info['rebate'][$cp_type]) ? $user_info['rebate'][$cp_type] : 0;
        if ($user_info['agents']) {
            $p_agents = $user_model->find($user_info['agents']);
            $p_rebate = isset($p_agents['rebate'][$cp_type]) ? $p_agents['rebate'][$cp_type] : 0;
            $rebate = $p_rebate - $my_rebate;
            $rebate ? $moneyHistory->write(['userid' => $user_info['agents'], 'money' => $money * $rebate/100,'type' => 6,  'remark' => '返点:'.$lottery_id]) : '';
            $this->parent_rebate($user_info['agents'], $cp_type, $money, $lottery_id);
        }
    }

     /**中奖停止追号 */
     public function setStopChasePrize($data, $list, $settingCoin)
     {
         $multiple = 0;
         $chaseMult = 0;
         $chase = [];
         foreach ($list as $value) {
            $multiple += $value['multiple'];
            if($value['status'] < 2 and $value['expect'] > $data['expect']){
                $chase[] = [
                    'id' => $value['id'],
                    'status' => 3
                ];
                $chaseMult += $value['multiple'];
            }
         }
         $chaseMoney = floatval($data['total'] / $multiple * 100) * $chaseMult / 100; 
         $count = count($chase);
         if ($count) {
            (new MoneyHistory)->write(['userid'  => $data['userid'],'money' => $chaseMoney * $settingCoin,'ext_name' => Request()->module(),'remark' => '中奖停止:'.$data['lottery_id'].',共'.$count."期"]);
         }
         return $chase;
     }

     /**合买中奖停止追号 */
     public function setStopHmChasePrize($data, $hm, $list, $settingCoin, $ext_name)
     {
         /**计算停止的期号，组合更新expect中的状态 */
         $stopRetrunEpt = [];
         $expCount = 0;
         $chaseMult = 0;
        foreach ($list as $key => $value) {
            $expCount += $value['multiple'];
            if($value['status'] < 2 and floatval($value['expect']) > floatval($data['expect'])){
                $stopRetrunEpt[] = ['id' => $list[$key]['id'], 'status' => 3];
                $chaseMult += $value['multiple'];
            }
        }
        /**根据合买人数量计算返回资金数据 */
        $retCount = count($stopRetrunEpt);
        $returnMoney = floatval($data['total'] / $expCount * 100) * $chaseMult / 100;
         foreach ($hm as $key => $value) {
            if($value['userid'] != 1){
                $chaseMoney = floatval($value['money'] / $data['total_share'] * $returnMoney * 100) / 100;
                if ($retCount) {
                    (new MoneyHistory)->write(['userid' => $value['userid'],'money' => $chaseMoney * $settingCoin,'ext_name' => $ext_name, 'remark' => '中奖停止:'.$data['lottery_id'].',共'.$retCount."期"]);
                }
            }
        }
        return $stopRetrunEpt;
     }

    /**合买消费会员充值累计 */
    public function joinChangeUserRechargeMoney($data, $hm, $list, $chaseMult)
    {
        $expCount = 0;
        foreach ($list as $key => $value) {
            $expCount += $value['multiple'];
        }
        $returnMoney = floatval($data['total'] / $expCount * 100) * $chaseMult / 100;
        $user_model = new User();
        foreach ($hm as $key => $value) {
            if($value['userid'] != 1){
                $chaseMoney = floatval($value['money'] / $data['total_share'] * $returnMoney * 100) / 100;
                $user_info = $user_model->where('id', $value['userid'])->find();
                if ($user_info['recharge_money'] > 0 ) {
                    $rechage_money = $user_info['recharge_money'] - $chaseMoney;
                    $rechage_money = $rechage_money >= 0 ? $rechage_money : 0;
                    $user_model->where('id', $value['userid'])->update(['recharge_money' => $rechage_money]);
                }
            }
        }
    }

    /**直购消费会员充值累计 */
    public function changeUserRechargeMoney($data, $list, $chaseMult)
    {
        $multiple = 0;
        foreach ($list as $value) {
            $multiple += $value['multiple'];
        }
        $chaseMoney = floatval($data['total'] / $multiple * 100) * $chaseMult / 100;
        $user_model = new User();
        $user_info = $user_model->where('id', $data['userid'])->find();
        if ($user_info['recharge_money'] > 0 ) {
            $rechage_money = $user_info['recharge_money'] - $chaseMoney;
            $rechage_money = $rechage_money >= 0 ? $rechage_money : 0;
            $user_model->where('id', $data['userid'])->update(['recharge_money' => $rechage_money]);
        }

    }

    //合买撤单金额
    public function joinChaseStop($ext, $data, $returnMoney, $expectNum)
    {
        $join_model =  LotteryCommon::getModel($ext, 'join');
        $buy_model =   LotteryCommon::getModel($ext, 'Buy');

        $list = $join_model->where('buy_id', $data['id'])->column(['userid', 'money']);
        $buy = $buy_model->find($data['id']);
        $buy = $buy->append(['is_end'])->toArray();
        if (!$buy['is_end']) {
            return [];
        }
        $moneyHis = [];
        foreach ($list as $key => $value) {
            if ($buy['userid'] == $key) {
                $value += $buy['assure_money'];
            }
            $selfMoney = (($value * $returnMoney)/$buy['total_share']);
            $moneyHis[] = ['userid' => $key, 'money' => $selfMoney,'ext_name' => $buy['ext_name'], 'remark' => '停止追号:['.$data['lottery_id'].']中第'.$expectNum.'期'];
        }
        return $moneyHis;
    }


    /**获取符合条件的未派奖的单子 */
    public function getPrizeList($ext, $issue)
    {
        $join_model =  LotteryCommon::getModel($ext, 'join');
        $buy_model =   LotteryCommon::getModel($ext, 'Buy');
        $expect_model =  LotteryCommon::getModel($ext, 'Expect');

        $list = $expect_model->where('expect', $issue)->where('status', 'in', [0, 1])->select()->append(['buy'])->toArray();
        $setting = (new \core\Setting)->get(['openBd', 'Bdpercent']);
        $settingCoin = 1;//金豆转换比例
        $prize = [];
        $money_model = new MoneyHistory;
        foreach ($list as $key => $value) {
            if($value['status'] == 1){
                array_push($prize, $value);
                continue;
            }
            if($value['buy']['is_join'] == 1){
                $joinlist = $join_model->where('buy_id', $value['buy_id'])->column(['userid', 'money']);
                $total_share = array_sum($joinlist);
                $bdpercent = floatval($setting['Bdpercent']) > 0 && $setting['openBd'] ? floatval($setting['Bdpercent']) : 100;
                $buyPercent = floatval(($total_share + $value['buy']['assure_money']) / $value['buy']['total_share'] * 1000) / 10;
                if($buyPercent < $bdpercent){//完成度不够 撤单
				
				
				 $id= $value['id'];
                 
                   $joinbuynum=0;
                    foreach ($joinlist as $key2 => $v) {
                        $joinbuynum+=$v;
                       

                    }
                    
                   
                   $systemuser=(new User())->getUserId('系统保底');
                   $datas['userid']=$systemuser['id'];
                   $datas['money']=$value['buy']['total_share']-$joinbuynum;
                   $datas['join_status']=2;
                   $datas['create_time']=date('Y-m-d H:i:s');
                   $datas['ext_name']=$value['ext_name'];
                   $datas['buy_id']=$value['buy_id'];
                    $join_model->insert($datas);
                    
                }
            }
            array_push($prize, $value);
        }
        return $prize;
    }

    /***初始化奖金 去掉系统盈利*/
    public function setGain($data, $bonus_base)
    {
        // 确保bonus_base是数值类型
        $bonus_base = floatval($bonus_base);

        if (isset($data['gain'])) {
            $gain = explode(',', $data['gain']);
            foreach ($gain as &$row) {
                // 确保$row是数值类型，并进行数值验证
                $row = trim($row);
                if (is_numeric($row)) {
                    $row = floatval($row) * (100 - $bonus_base) / 100;
                }
            }
            $data['gain'] = implode(',', $gain);
        } else {
            foreach ($data as &$v) {
                $v = $this->setGain($v, $bonus_base);
            }
        }
        return $data;
    }

    /***初始化奖金 去掉系统盈利 区间奖金*/
    public function setGain2($data, $bonus_base)
    {
        if (isset($data['gain'])) {
            $gain = explode('-', $data['gain']);
            foreach ($gain as &$row) {
                $row *= (100 - $bonus_base)/100;
            }
            $data['gain'] = implode('-', $gain);
        } else {
            foreach ($data as &$v) {
                $v = $this->setGain2($v, $bonus_base);
            }
        }
        return $data;
    }

    /***订单投注内容公共属性*/
    public function planContent($value)
    {
        $betting = '';
        $betting .= '<em>注数：' . $value['note_num'] . '</em>';
        if (isset($value['multiple'])) {
            $betting .= '<em>倍数：' . $value['multiple'] . '</em>';
        }
        if (isset($value['rebate']) and $value['rebate']) {
            $betting .= '<em>返点：' . $value['rebate'] . '%</em>';
        }
        if (isset($value['rebate']) and $value['rebate']) {
            $betting .= '<em>返点：' . $value['rebate'] . '%</em>';
        }
        if (isset($value['money'])) {
            $betting .= '<em>投注金额：' . $value['money'] . '</em>';
        }
        if (isset($value['unit'])) {
            $unit_array = [1 => '元', 2 => '角', 3 => '分', 4 => '厘'];
            $betting .= '<em>模式：' . $unit_array[$value['unit']] . '</em>';
        }
        return $betting;
    }

}