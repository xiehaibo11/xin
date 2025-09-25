<?php
namespace app\lottery\model;

use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use think\Model;
use app\index\model\User;
use think\Validate;

class SscCommon extends Model
{
     /**
     *  算注数 -- 验证号码
     * @param  array $data 表单提交的值
     * @return array $codeNum 投注数  $play 投注号码
     */
    public function playNum($data, $playType, $is_chase, $name)
    {
        $sign = array_column($playType, 'sign');
        $codeArray = [];
        $error = true;
        $name_info = json_decode((new Ssc())->getValue($name . '_config'), true);
        $user = User::get(['sid' => session('sid')]);
        $user_rebate = isset($user['rebate']['ssc']) ? $user['rebate']['ssc'] : 0;
        foreach ($data['play_array'] as $key => $value) {
            $codeNum = 0;
            $key = array_search($value['type'], $sign) + 1;
            if(!$key){
                $error = false;
                break;
            }
            $key = $key - 1;
            $play_model_res = (new LotteryCom())->playModeInit($value, $is_chase, $name_info['mode'], $user_rebate);
            if ($play_model_res['err']) {
                $error = false;
                break;//直接返回错误或者返回注数为0
            }
            $name = explode('-',$playType[$key]['name']);
            $firstLast = mb_substr($name[0], -2, 2, 'utf-8');
            $zx =  $firstLast== '直选' || $firstLast== '通选' || $name[0] == '大小单双' ? 1 : 0;//选择号码位数相乘
            $single_x = $name[0] == '定位胆' || $name[0]== '龙虎'|| $name[0]== '一星' ? 1 : 0;//选择号码个数
            $hz = count($name) > 1 && mb_substr($name[1], 0, 2, 'utf-8') == '和值' ? 1 : 0;
            $dt = count($name) > 1 && mb_substr($name[1], 0, 2, 'utf-8') == '胆拖' ? 1 : 0;
            if($hz){
                $codeNum += $this->zxHz($value['num'], $name);
                $play_model_res['data']['note_num'] = $codeNum;
                $codeArray[] = $play_model_res['data'];
                continue;
            }
            if($dt){
                $codeNum += $this->dtNum($value['num'], $name);
                $play_model_res['data']['note_num'] = $codeNum;
                $codeArray[] = $play_model_res['data'];
                continue;
            }
            if($single_x){
                $single_num = explode('|',$value['num']);
                foreach ($single_num as $v) {
                    $my_num = explode(',', $v);
                    foreach ($my_num as $row) {
                        if ($row != '-') $codeNum++;
                    }
                }
                $play_model_res['data']['note_num'] = $codeNum;
                $codeArray[] = $play_model_res['data'];
                continue;
            }
            if($zx){
                $code = explode('|',$value['num']);
                $zxNum = [];
                foreach ($code as $codekey => $codevalue) {
                    $zxNum[$codekey] = count(explode(',', $codevalue));
                }
                $codeNum += array_product($zxNum);
            }else{
                $codeNum += $this->zxNum($value['num'], $name);
            }
            $play_model_res['data']['note_num'] = $codeNum;
            $codeArray[] = $play_model_res['data'];

        }
        if(!$error){
            return ['err' => 1];
        }
        return ['play' => $codeArray];
    }
    
    /**组选计算注数  */
    public function zxNum($code, $name)
    {
        $fristChar = mb_substr($name[0], 1, 1, 'utf-8');
        $lastChar = mb_substr($name[0], -1, 1, 'utf-8');
        $need = $fristChar == '二' || $lastChar == '三' ? 2 : 3;
        $num = count(explode(',', $code));
        $codeNum = (new LotteryCom)->countNum($num, $need);
        $codeNum = $lastChar == '三' ? ($codeNum * 2) : $codeNum;
        return $codeNum;
    }

    /**胆拖计算注数 */
    public function dtNum($code, $name)
    {
        $code = explode("#", $code);
        $dan = count(explode(',', $code[0]));
        $tuo = count(explode(',', $code[1]));
        $is_zu_3 = mb_substr($name[0], -1, 1, 'utf-8') == '三' ? 1 : 0;
        if ($is_zu_3) {
            $codeNum = $tuo * 2;
        } else {
            $need = 3 - $dan;
            $codeNum = (new LotteryCom)->countNum($tuo, $need);
        }
        return $codeNum;
    }

    /**和值算注数 */
    public function zxhz($code, $name)
    {
        $arr = $this->hzCount($name);
        $code = explode(',', $code);
        $num = 0;
        foreach ($code as $value) {
            $num += $arr[$value];
        }
        return $num;
    }
    public function hzCount($type)
     {
        $name = mb_substr(implode('', $type), 0, 6, 'utf-8');
        switch($name){
            case '前二组选和值':
            case '后二组选和值':
                return [1,1,2,2,3,3,4,4,5,5,5,4,4,3,3,2,2,1,1];
            case '前二直选和值':
            case '后二直选和值':
                return [1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1];
            case '后三直选和值':
            case '前三直选和值':
            case '中三直选和值':
                return [1,3,6,10,15,21,28,36,45,55,63,69,73,75,75,73,69,63,55,45,36,28,21,15,10,6,3,1];
            case '后三组三和值':
            case '前三组三和值':
            case '中三组三和值':
                return [0,1,2,1,3,3,3,4,5,4,5,5,4,5,5,4,5,5,4,5,4,3,3,3,1,2,1,0];
            case '前三组六和值':
            case '中三组六和值':
            case '后三组六和值':
                return [0,0,0,1,1,2,3,4,5,7,8,9,10,10,10,10,9,8,7,5,4,3,2,1,1,0,0,0];
        }
     }

/******************************************************************** */
/******************************派奖*********************************** */
/******************************************************************** */

     /**派奖处理 */
     public function prizeCom($code, $plan, $bouns, $setting_config)
     {
         $code = explode(',', $code);
         $sys_bonus_base = (isset($setting_config['bonus_base']) and $setting_config['bonus_base'] > 0 and $setting_config['bonus_base'] < 100) ? $setting_config['bonus_base'] : 100;
         $sys_bonus_base = 100 - $sys_bonus_base;
         $user_model = new User();
         $signname = array_column($bouns, 'name');
         $returnData = [];
         foreach ($plan as $key => $value) {
             $money = 0;
             $playNum = $value['buy'];
             $bonus_base = $sys_bonus_base;
             $user_info = $user_model->find($playNum['userid']);
             $my_rebate = isset($user_info['rebate']['ssc']) ? $user_info['rebate']['ssc'] : 0;
             if ($user_info['top_agents']) {
                 $top_agents = $user_model->find($user_info['top_agents']);
                 $top_rebate = isset($top_agents['rebate']['ssc']) ? $top_agents['rebate']['ssc'] : 0;
                 $bonus_base += ($top_rebate - $my_rebate);
             }
             if (!$playNum) continue;
             foreach ($playNum['play_num'] as $val) {
                 $type = explode('-',$val['type']);
                 $typeCount = count($type);
                 if ($typeCount < 2) continue;
                 $bounskey = array_search($val['type'], $signname);
                 $gain_array = explode(',', $bouns[$bounskey]['gain']);
                 $gain = $gain_array[0];//每一注的奖金
                 $words = mb_substr($type[1], 0, 2, 'utf-8');
                 if($words == '胆拖'){
                     /**胆拖派奖 */
                    $prizeNum = $this->dtPrize($code, $val['num'], $type);
                 } elseif($words == '和值'){
                   /**和值派奖 */
                   $prizeNum = $this->hzPrize($code, $val['num'], $type);
                 } else {
                     $firstLast = mb_substr($type[0], -2, 2, 'utf-8');
                     $zx =  $firstLast== '直选' || $firstLast== '通选' || $type[0] == '一星' ? 1 : 0;

                     if($type[0] == '大小单双'){
                         $prizeNum = $this->dxdsPrize($code, $val['num']);
                     }
                     if($type[0] == '定位胆'){
                         $prizeNum = $this->dwdPrize($code, $val['num']);
                     }
                     if($type[0] == '龙虎'){
                         $lhPrize = $this->lhPrize($code, $val['num'], $type[1]);
                         $prizeNum = $lhPrize['num'];
                     }
                     if($zx){
                         $right = $this->zhixPrize($code, $val['num'], $type);
                         $prizeNum = $right['num'];
                     }
                     if(!$zx && $type[0] != '大小单双' && $type[0] != '龙虎' && $type[0] != '定位胆'){
                         $prizeNum = $this->zxPrize($code, $val['num'], $type);
                     }
                 }

                 if (isset($val['rebate'])) {//存在返点时
                     if ($val['rebate']) $bonus_base += $val['rebate'];
                 }
                 $bounsNum = $gain * (100 - $bonus_base)/100;//获取最高奖金

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

                 if(isset($firstLast) && $firstLast == '通选'){
                    $bounsArr = explode(',', $bouns[$bounskey]['gain']);
                     $money += $this->txCountMoney($bounsArr, $right) * (100 - $bonus_base)/100 * $multiple;
                 } elseif ($type[0] == '龙虎'){
                     $bounsArr = explode(',', $bouns[$bounskey]['gain']);
                     $money += $this->lhCountMoney($bounsArr, $lhPrize['res']) * (100 - $bonus_base)/100 * $multiple;
                 } else {
                     $money += $bounsNum * $prizeNum * $multiple;
                 }
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
        //  print_r($returnData);die;
         return $returnData;
     }

     /**五星通选计算奖金 */
     public function txCountMoney($bounsArr, $right)
     {
        if($right['num'] == 1){
            return $bounsArr[5];
         }
         if ($right['arr'][2] && $right['arr'][3] && $right['arr'][4]) {
             return $bounsArr[4];
         }
         if ($right['arr'][0] && $right['arr'][1] && $right['arr'][2]) {
             return $bounsArr[3];
         }
         if ($right['arr'][0] && $right['arr'][1] && $right['arr'][3] && $right['arr'][4]) {
             return $bounsArr[0];
         }
         if ($right['arr'][0] && $right['arr'][1]) {
             return $bounsArr[1];
         }
         if ($right['arr'][3] && $right['arr'][4]) {
             return $bounsArr[2];
         }
     }

     /**三星胆拖派奖 */
     public function dtPrize($code, $plan, $name)
     {
         $fristChar = mb_substr(trim($name[0]), 0, 1, 'utf-8');
         if ($fristChar == '前') {
             $code = array_slice($code, 0, 3);
         } elseif  ($fristChar == '中') {
             $code = array_slice($code, 1, 3);
         } else {
             $code = array_slice($code, 2, 3);
         }

        $codeNum = count(array_unique($code));
        $dtType = mb_substr(trim($name[0]), -1, 1, 'utf-8') == '三' ? 1 : 0;
        if(($dtType && $codeNum != 2) || (!$dtType && $codeNum != 3)){
            return 0;
        }
        $plan = explode('#', $plan);
        $dan = explode(',', $plan[0]);
        
        $danRight = 0;
        foreach ($dan as $value) {
            $danRight = in_array($value, $code) ? ($danRight + 1) : $danRight;
        }
        if($danRight != count($dan)){
            return 0;
        }
        
        $tuo = explode(',', $plan[1]);
        $tuoRight = 0;
        $ureCode = array_diff($code,$dan);
        foreach ($tuo as $value) {
            $tuoRight = in_array($value, $ureCode) ? ($tuoRight + 1) : $tuoRight;
        }

        return $dtType ? 1 : ((3 - count($dan)) == $tuoRight ? 1 : 0);
     }
     /**直选（通选）派奖 */
     public function zhixPrize($code, $plan, $type)
     {
        $numKey = ['一' => 1,'二' => 2, '三' => 3, '四' => 4, '五' => 5];
        $fristChar = mb_substr($type[0], 0, 1, 'utf-8');
        $qh_num = mb_substr(trim($type[0]), 1, 1, 'utf-8') == '三' ? 3 : 2;
        if ($fristChar == '前') {
            $code = array_slice($code, 0, $qh_num);
        } elseif  ($fristChar == '中') {
            $code = array_slice($code, 1, 3);
        } elseif  ($fristChar == '后') {
            $code = array_slice($code, 5 - $qh_num, $qh_num);
        } else {
            $code = array_slice($code, 5 - $numKey[$fristChar], $numKey[$fristChar]);
        }
        $plan = explode('|', $plan);
        foreach ($plan as $key => $value) {
            $planCode = explode(',', $value);
            $right[$key] = in_array($code[$key], $planCode) ? 1 : 0;
        }
        $uniqueCount = count(array_unique($right));
        $num = $uniqueCount == 1 && $right[0] == 1 ? 1 : 0;
        return ['num' => $num, 'arr' => $right];
     }

    /**龙虎派奖 */
    public function lhPrize($code, $plan, $type)
    {
        $plan = explode(',', $plan);
        switch ($type) {
            case '万千';
                return $this->lhResult($code[0], $code[1], $plan);
                break;
            case '万百';
                return $this->lhResult($code[0], $code[2], $plan);
                break;
            case '万十';
                return $this->lhResult($code[0], $code[3], $plan);
                break;
            case '万个';
                return $this->lhResult($code[0], $code[4], $plan);
                break;
            case '千百';
                return $this->lhResult($code[1], $code[2], $plan);
                break;
            case '千十';
                return $this->lhResult($code[1], $code[3], $plan);
                break;
            case '千个';
                return $this->lhResult($code[1], $code[4], $plan);
                break;
            case '百十';
                return $this->lhResult($code[2], $code[3], $plan);
                break;
            case '百个';
                return $this->lhResult($code[2], $code[4], $plan);
                break;
            case '十个';
                return $this->lhResult($code[3], $code[4], $plan);
                break;
        }
    }

    /**龙虎大小单双结果 */
    public function lhResult($code1, $code2, $plan)
    {
        $res = [];
        $sum = ($code1 + $code2) % 10;
        if ($code1 > $code2) array_push($res, '龙');
        if ($code1 < $code2) array_push($res, '虎');
        if ($code1 == $code2) array_push($res, '和');
        if ($sum % 2 == 1) array_push($res, '单');
        if ($sum % 2 == 0) array_push($res, '双');
        if ($sum < 5) array_push($res, '小');
        if ($sum > 4) array_push($res, '大');
        $zj_res = array_intersect($res, $plan);
        return ['num' => count($zj_res), 'res' => $zj_res];
    }

    /**龙虎大小单双计算奖金 */
    public function lhCountMoney($bounsArr, $award)
    {
        $award_array = ['龙', '虎', '和', '大', '小', '单', '双'];
        $money = 0;
        if (empty($award)) return 0;
        foreach ($award as $v) {
            foreach ($award_array as $key => $row) {
                if ($row == $v) {
                    $money += $bounsArr[$key];
                }
            }
        }
        return $money;
    }

    /**定位胆派奖 */
    public function dwdPrize($code, $plan)
    {
        $num = 0;
        $plan = explode('|', $plan);
        foreach ($code as $key => $value) {
            $my_pan = explode(',', $plan[$key]);
           if (in_array($value, $my_pan)) {
               $num++;
           }
        }
        return $num;
    }

     /**大小单双派奖 */
     public function dxdsPrize($code, $plan)
     {
        $code = array_slice($code, 3, 2);
        foreach ($code as $key => &$value) {
            $dx = $value > 4 ? '大' : '小';
            $ds = $value % 2 == 0 ? '双' : '单'; 
            $newCode[$key] = [$dx, $ds];
        }
        $plan = explode('|', $plan);
        foreach ($plan as $key => $value) {
            $num = 0;
            $planCode = explode(',', $value);
            foreach ($planCode as $val) {
               $num = in_array($val, $newCode[$key] ) ? ($num + 1) : $num;
            }
            $zxNum[$key] = $num;
        }
      return array_product($zxNum);
     }

     /**组选派奖 */
     public function zxPrize($code, $plan, $type)
     {
        $fristChar = mb_substr($type[0], 0, 1, 'utf-8');
         $qh_num = mb_substr(trim($type[0]), 1, 1, 'utf-8') == '三' ? 3 : 2;
         $num = 2;
         if ($fristChar == '前') {
             $code = array_unique(array_slice($code, 0, $qh_num));
         } elseif  ($fristChar == '中') {
             $code = array_unique(array_slice($code, 1, 3));
         } elseif  ($fristChar == '后') {
             $code = array_unique(array_slice($code, 5 - $qh_num, $qh_num));
         } else {
             $code = array_unique(array_slice($code, 5 - 2, 2));
         }
        $codeNew = count($code);
        if($qh_num == 3) {
            $is_zu_3 = mb_substr($type[0], -1, 1, 'utf-8') == '三' ? 1 : 0;
            $num = $is_zu_3 ? 2 : 3;
            if(($is_zu_3 && $codeNew != 2) || (!$is_zu_3 && $codeNew != 3)){
                return 0;
            }
        }
        $plan = explode(',',$plan);
        $rightNum = 0;
        foreach ($code as $value) {
            $rightNum = in_array($value, $plan) ? ($rightNum + 1) : $rightNum;
        }
        return $num == $rightNum ? 1 : 0;
     }
     /**和值派奖 */
     public function hzPrize($code, $plan, $type)
     {
       $fristChar = mb_substr($type[0], 0, 1, 'utf-8');
       $lastChar = mb_substr($type[0], -2, 2, 'utf-8');
         $qh_num = mb_substr(trim($type[0]), 1, 1, 'utf-8') == '三' ? 3 : 2;
         if ($fristChar == '前') {
             $code = (array_slice($code, 0, $qh_num));
         } elseif  ($fristChar == '中') {
             $code = (array_slice($code, 1, 3));
         } elseif  ($fristChar == '后') {
             $code = (array_slice($code, 5 - $qh_num, $qh_num));
         } else {
             $code = array_slice($code, 5 - 2, 2);
         }

       $countCode = count(array_unique($code));
       $returnCon = ($lastChar == '组六' && $countCode != 3) || ($lastChar == '组三' && $countCode != 2);
       if($fristChar == 3 && $returnCon){
           return 0;
       }
       $sum = array_sum($code);
       $plan = explode(',', $plan);
       return in_array($sum , $plan) ? 1 : 0;
     }

    /**获取一个玩法多个奖金的赔率表  */
    public function getGainShow($gain, $data)
    {
        $res = [];
        if ($data['sign'] == '1.1') {
            array_push($res, [
                'name' => '五星通选-前二后二',
                'gain' => $gain[0]
            ]);
            array_push($res, [
                'name' => '五星通选-前二或后二',
                'gain' => $gain[1]
            ]);
            array_push($res, [
                'name' => '五星通选-前三或后三',
                'gain' => $gain[3]
            ]);
            array_push($res, [
                'name' => '五星通选-全中',
                'gain' => $gain[5]
            ]);
        }
        $type = explode('.', $data['sign']);
        if ($type[0] == 9) {
            array_push($res, [
                'name' => $data['name'] . '-龙虎',
                'gain' => $gain[0]
            ]);
            array_push($res, [
                'name' => $data['name'] . '-和',
                'gain' => $gain[2]
            ]);
            array_push($res, [
                'name' => $data['name'] . '-大小单双',
                'gain' => $gain[3]
            ]);
        }
        return $res;
    }
}