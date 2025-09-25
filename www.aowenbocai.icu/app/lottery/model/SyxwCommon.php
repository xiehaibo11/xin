<?php
namespace app\lottery\model;

use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use core\Setting;
use think\Model;
use app\index\model\User;
use think\Validate;

class SyxwCommon extends Model
{
    /**
     *  算注数 -- 验证号码
     * @param  array $data 表单提交的值
     * @return array $codeNum 投注数  $play 投注号码
     */
    public function playNum($data, $playType, $is_chase, $name)
    {
        $key_num = ['一' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8];
        $sign = array_column($playType, 'sign');
        $play_array = $data['play_array'];
        $codeArray = [];
        $error = true;
        $name_info = json_decode((new Syxw)->getValue($name . '_config'), true);
        $user = User::get(['sid' => session('sid')]);
        $user_rebate = isset($user['rebate']['syxw']) ? $user['rebate']['syxw'] : 0;
        foreach ($play_array as $value) {
            $codeNum=0;
            if(!in_array($value['type'], $sign)){
                $error = false;
                break;//直接返回错误或者返回注数为0
            }
            $play_model_res = (new LotteryCom())->playModeInit($value, $is_chase, $name_info['mode'], $user_rebate);
            if ($play_model_res['err']) {
                $error = false;
                break;//直接返回错误或者返回注数为0
            }

            $key = array_search($value['type'], $sign);
            $name = explode('-',$playType[$key]['name']);
            $zx = mb_substr($name[0], -2, 2, 'utf-8') == '直选' || $name[0] == '前一' ? 1 : 0;
            if(!$zx){
                $rx = mb_substr($name[0], 0, 2, 'utf-8') == '任选' ? 1 : 0;
                $countNum = $rx ? $key_num[mb_substr($name[0], -1, 1, 'utf-8')] : $key_num[mb_substr($name[0], 1, 1, 'utf-8')];
                $typenum = explode('.', $value['type']);
                if(count($typenum) == 2 && $typenum[1] == 2){//胆拖
                    $code = explode("#", $value['num']);
                    $danNum = count(explode(',', $code[0]));
                    $tuoNum = count(explode(',', $code[1]));
                    $need = $countNum - $danNum;
                    $codeNum  += (new LotteryCom)->countNum($tuoNum, $need);
                } else {
                    $code = count(explode(",", $value['num']));
                    $codeNum  +=(new LotteryCom)->countNum($code, $countNum);
                }
            } else {
                $code = explode('|',$value['num']);
                foreach ($code as $codekey => $codevalue) {
                    $zxNum[$codekey] = count(explode(',', $codevalue));
                }
               $codeNum += array_product($zxNum);
            }
            $play_model_res['data']['note_num'] = $codeNum;
            $codeArray[] = $play_model_res['data'];
        }
        if(!$error){
            return ['err' => 1];
        }
        return [ 'play' => $codeArray];
    }

/*************************************************************** */
/*************************************************************** */
/*************************************************************** */

    /**派奖处理  */
    public function prizeCom($code, $plan, $bouns, $setting_config)
    {
        $code = explode(',', $code);
        $sign = array_column($bouns, 'sign');
        $returnData = [];
        $sys_bonus_base = (isset($setting_config['bonus_base']) and $setting_config['bonus_base'] > 0 and $setting_config['bonus_base'] < 100) ? $setting_config['bonus_base'] : 100;
        $sys_bonus_base = 100 - $sys_bonus_base;
        $user_model = new User();
        foreach ($plan as $key => $value) {
            $money = 0;
            $playNum = $value['buy'];
            if (!$playNum) continue;
            $bonus_base = $sys_bonus_base;
            $user_info = $user_model->find($playNum['userid']);
            $my_rebate = isset($user_info['rebate']['syxw']) ? $user_info['rebate']['syxw'] : 0;
            if ($user_info['top_agents']) {
                $top_agents = $user_model->find($user_info['top_agents']);
                $top_rebate = isset($top_agents['rebate']['syxw']) ? $top_agents['rebate']['syxw'] : 0;
                $bonus_base += ($top_rebate - $my_rebate);
            }
            foreach ($playNum['play_num'] as $val) {
                $prizeNum = 0;//中奖注数
                $val['name'] = explode('-', $val['name'])[0];
                $type = explode('.',$val['type']);
                $dt = (count($type) == 1 || $type[1] != 2) ? 0 : 1;
                /**任选派奖 前几组选*/
                $rexuan = mb_substr($val['name'], 0,2, 'utf-8') == '任选' ? 1 : 0;
                if($rexuan || mb_substr($val['name'], -2,2, 'utf-8') == '组选'){
                    $prizeNum = $dt ? ($this->rxDtPrize($code, $val, $rexuan)) : ($this->rxPrize($code, $val, $rexuan));
                }
                /**前几直选 */
                if(mb_substr($val['name'], -2,2, 'utf-8') == '直选' || mb_substr($val['name'], -2,2, 'utf-8') == '前一'){
                    $prizeNum = $this->wzPrize($code, $val);
                }

                $bounskey = array_search($val['type'], $sign);
                if (isset($val['rebate'])) {//存在返点时
                    if ($val['rebate']) $bonus_base += $val['rebate'];
                }
                $bounsNum = $bouns[$bounskey]['gain'] * (100 - $bonus_base)/100;//获取最高奖金

                $unit_zh = [1 => 1, 2 => 10, 3 => 100, 4 => 1000];
                $multiple = 1;//投注的倍数 投注的金额除2
                if (isset($val['money'])) {
                    $multiple = $val['money'] / 2;
                }
                if (isset($val['unit'])) {
                    $multiple = 1 / $unit_zh[$val['unit']];
                }
                if (isset($val['multiple'])) {
                    $multiple = $multiple * $val['multiple'];
                }

                $money += $bounsNum * $prizeNum * $multiple;
            }
            $returnData[$key] = [
                'id' => $value['id'],
                'buy_id' => $playNum['id'],
                'expect' => $value['expect'],
                'is_join' => $playNum['is_join'],
                'total' => $playNum['total_money'],
                'total_share' => $playNum['total_share'],
                'lottery_id' => $playNum['lottery_id'],
                'userid' => $playNum['userid'],
                'assure_money' => $playNum['assure_money'],
                'gain' => $playNum['gain'],
                'is_stop' => $playNum['is_stop'],
                'bouns' => $money * floatval($value['multiple']),
                'multiple' => $value['multiple'],
                'play_num' => $playNum['play_num']
            ];
        }
        return $returnData;
    }

    /**任选普通处理 */
    public function rxPrize($code, $plan, $renxuan)
    {
        $start = $renxuan ? -1 : 1;
        $countNum = $this->prizeCount($plan,$start);
        $openNum = $countNum > 5 ? 5 : $countNum;
        $code = $renxuan ? $code : array_slice($code, 0, $countNum);//任选获取开奖全部号码  前几就取几
        $planCode = array_unique(explode(',', $plan['num']));

        $rightNum = 0;
        foreach ($planCode as $value) {
            $rightNum = in_array($value, $code) ? ($rightNum + 1) : $rightNum;
        }
        if ($countNum > 5 ) {
            if ($rightNum != 5) return 0;
            $num = (new LotteryCom)->countNum(count($planCode) - 5, $countNum - 5);
        } else {
            if ($rightNum < $openNum) return 0;
            $num = (new LotteryCom)->countNum($rightNum, $openNum);
        }
        return $num;
    }
    
     /**任选胆拖处理 */
     public function rxDtPrize($code, $plan, $renxuan)
     { 
         $start = $renxuan ? -1 : 1;
         $countNum = $this->prizeCount($plan, $start);
         $openNum = $countNum > 5 ? 5 : $countNum;
         $code = $renxuan ? $code : array_slice($code, 0, $countNum);
         $planCode = explode('#', $plan['num']);
         /**判断胆码是否全中 */
         $dan = array_unique(explode(',', $planCode[0])); 
         $danRight = 0;
         $danRightNum = count($dan) > 5 ? 5 : count($dan);
         foreach ($dan as $value) {
            $danRight = in_array($value, $code) ? ($danRight + 1) : $danRight;
         }
         if($countNum <= 5 and $danRight != $danRightNum){
             return 0;
         }
         /**判断拖码中的个数 */
         $tuo = array_unique(explode(',', $planCode[1])); 
         $tuoRight = 0;
         $ureCode = array_diff($code,$dan);
         foreach ($tuo as $value) {
             $tuoRight = in_array($value, $ureCode) ? ($tuoRight + 1) : $tuoRight;
         }
         if ($tuoRight + $danRight < $openNum) {
             return 0;
         }
         if ($countNum <= 5) {
             $num = (new LotteryCom)->countNum($tuoRight, $countNum - count($dan));
         } else {
             $num = (new LotteryCom)->countNum((count($tuo) - $tuoRight), ($countNum - count($dan)) - $tuoRight);
         }
         return $num;
     }
   
     /**前几直选处理 */
     public function wzPrize($code, $plan)
     {
        $countNum = $this->prizeCount($plan, 1);
        $planCode = explode('|', $plan['num']);
        $count = count($planCode);
        if($countNum != $count){
            return 0;
        }
        $code = array_slice($code, 0, $count);
        $rightNum = 0;
        foreach($code as $key => $value){
            $codeList = explode(',', $planCode[$key]);
            $rightNum = in_array($value, $codeList) ? ($rightNum + 1) : $rightNum;
        }
        return $rightNum == $count ? 1 : 0;
     }

     /**派奖通用取个数 */
     public function prizeCount($plan, $start = -1)
     {
        $key_num = ['一' => 1,'二' => 2, '三' => 3, '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8];
        $name = explode('-', $plan['name']);
        $countNum = $key_num[mb_substr($name[0], $start, 1, 'utf-8')];
        return $countNum;
     }


}