<?php
namespace app\lottery\model;

use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use core\Setting;
use think\Model;
use app\common\model\MoneyHistory;
use app\lottery\controller\Lottery;
use app\index\model\User;
use think\Validate;

class KsCommon extends Model
{
    /**
     *  算注数 -- 验证号码
     * @param  array $data 表单提交的值
     * @return array $codeNum 投注数  $play 投注号码
     */
    public function playNum($data, $playType, $is_chase, $name)
    {
        $sign = array_column($playType, 'sign');
        $play_array = $data['play_array'];
        $codeArray = [];
        $error = true;
        $name_info = json_decode((new Ks)->getValue($name . '_config'), true);
        $user = User::get(['sid' => session('sid')]);
        $user_rebate = isset($user['rebate']['ks']) ? $user['rebate']['ks'] : 0;
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

            $single_x = in_array($value['type'], [1,2,3,5,6]) ? 1 : 0;
            if($single_x){//选择号码个数
                $single_num = explode(',',$value['num']);
                $codeNum += count($single_num);
            }
            if ($value['type'] == 4) {//三不同号
                $code = count(explode(",", $value['num']));
                $codeNum  +=(new LotteryCom)->countNum($code, 3);
            }
            if ($value['type'] == 8) {//二不同号
                $code = count(explode(",", $value['num']));
                $codeNum  +=(new LotteryCom)->countNum($code, 2);
            }
            if ($value['type'] == 7) {//二同号单选
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
            $my_rebate = isset($user_info['rebate']['ks']) ? $user_info['rebate']['ks'] : 0;
            if ($user_info['top_agents']) {
                $top_agents = $user_model->find($user_info['top_agents']);
                $top_rebate = isset($top_agents['rebate']['ks']) ? $top_agents['rebate']['ks'] : 0;
                $bonus_base += ($top_rebate - $my_rebate);
            }
            foreach ($playNum['play_num'] as $val) {
                $prizeNum = 0;//中奖注数
                $bounskey = array_search($val['type'], $sign);
                $gain_array = explode(',', $bouns[$bounskey]['gain']);
                $gain = $gain_array[0];//每一注的奖金
                switch ($val['type']) {
                    case 1://和值
                            $sum = array_sum($code);
                            $planCode = explode(',', $val['num']);
                            foreach ($planCode as $v) {
                                if ($sum >= 11 and $v == '大') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[16];
                                }
                                if ($sum < 11 and $v == '小') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[17];
                                }
                                if ($sum%2 == 1 and $v == '单') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[18];
                                }
                                if ($sum%2 == 0 and $v == '双') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[19];
                                }
                                if ($sum == floatval($v)) {
                                    $prizeNum += 1;
                                    $gain = $gain_array[$sum-3];
                                }
                            }
                        break;
                    case 2://三同号通选
                        if (count(array_unique($code)) == 1) $prizeNum = 1;
                        break;
                    case 3://三同号单选
                        $planCode = explode(',', $val['num']);
                        if (count(array_unique($code)) == 1) {
                            $bj_code = '';
                            foreach ($code as $v) {
                                $bj_code .= $v;
                            }
                            if (in_array($bj_code, $planCode)) $prizeNum = 1;
                        }
                        break;
                    case 4://三不同号
                        $planCode = explode(',', $val['num']);
                        if (count(array_unique($code)) == 3) {
                            if (empty(array_diff($code, $planCode))) {
                                $prizeNum = 1;
                            }
                        }
                        break;
                    case 5://三连号通选
                        sort($code);
                        $bj_num = -1;
                        $bool = true;
                        foreach ($code as $v) {
                            if ($bj_num == -1) {
                                $bj_num = $v;
                            } else {
                                if (($v-1) != $bj_num) {
                                    $bool = false;
                                }
                                $bj_num = $v;
                            }
                        }
                        if ($bool) $prizeNum = 1;
                        break;
                    case 6://二同号复选
                        $planCode = explode(',', $val['num']);
                        if (count(array_unique($code)) == 2) {
                            foreach ($planCode as $v) {
                                $one_num = mb_substr($v, 0,1, 'utf-8');
                                $i = 0;
                                foreach ($code as $row) {
                                    if ($one_num == $row) $i++;
                                }
                                if ($i == 2)  $prizeNum = 1;
                            }
                        }
                        break;
                    case 7://二同号单选
                        $code_data = explode('|', $val['num']);
                        if (count(array_unique($code)) == 2) {
                            $code_one = explode(',', $code_data[0]);
                            $two_num = explode(',', $code_data[1]);
                            foreach ($code_one as $v) {
                                $one_num = mb_substr($v, 0,1, 'utf-8');
                                foreach ($two_num as $row) {
                                    $one_check_num = 0;
                                    $two_check_num = 0;
                                    foreach ($code as $row2) {
                                        if (floatval($one_num) == $row2) {
                                            $one_check_num++;
                                        }
                                        if (floatval($row) == $row2) {
                                            $two_check_num++;
                                        }
                                    }
                                    if ($one_check_num == 2 and $two_check_num == 1) {
                                        $prizeNum = 1;
                                    }
                                }
                            }
                        }
                        break;
                    case 8://二不同号
                        $planCode = explode(',', $val['num']);
                        $win_num = 0;
                        foreach ($planCode as $v) {
                            if (in_array($v, $code)) {
                                $win_num++;
                            }
                        }
                        if ($win_num == 2) $prizeNum = 1;
                        if ($win_num == 3) $prizeNum = 3;
                        break;
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

    /**获取一个玩法多个奖金的赔率表  */
    public function getGainShow($gain, $data)
    {
        $res = [];
        switch ($data['type']) {
            case 1://和值
                array_push($res, [
                    'name' => '和值3,18',
                    'gain' => $gain[0]
                ]);
                array_push($res, [
                    'name' => '和值4,17',
                    'gain' => $gain[1]
                ]);
                array_push($res, [
                    'name' => '和值5,16',
                    'gain' => $gain[2]
                ]);
                array_push($res, [
                    'name' => '和值6,15',
                    'gain' => $gain[3]
                ]);
                array_push($res, [
                    'name' => '和值7,14',
                    'gain' => $gain[4]
                ]);
                array_push($res, [
                    'name' => '和值8,13',
                    'gain' => $gain[5]
                ]);
                array_push($res, [
                    'name' => '和值9,12',
                    'gain' => $gain[6]
                ]);
                array_push($res, [
                    'name' => '和值10,11',
                    'gain' => $gain[7]
                ]);
                array_push($res, [
                    'name' => '和值-大小单双',
                    'gain' => $gain[16]
                ]);
                break;
        }
        return $res;
    }

}