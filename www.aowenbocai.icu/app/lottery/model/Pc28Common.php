<?php
namespace app\lottery\model;

use think\Model;
use app\index\model\User;

class Pc28Common extends Model
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
        $name_info = json_decode((new Pc28())->getValue($name . '_config'), true);
        $user = User::get(['sid' => session('sid')]);
        $user_rebate = isset($user['rebate']['pc28']) ? $user['rebate']['pc28'] : 0;
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

            $single_x = in_array($value['type'], [1, 2, 3]) ? 1 : 0;
            if($single_x){//选择号码个数
                $single_num = explode(',',$value['num']);
                $codeNum += count($single_num);
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
            $my_rebate = isset($user_info['rebate']['pc28']) ? $user_info['rebate']['pc28'] : 0;
            if ($user_info['top_agents']) {
                $top_agents = $user_model->find($user_info['top_agents']);
                $top_rebate = isset($top_agents['rebate']['pc28']) ? $top_agents['rebate']['pc28'] : 0;
                $bonus_base += ($top_rebate - $my_rebate);
            }
            $sum = array_sum($code);
            //判断特码13，14返本金
            if ($sum == 13 || $sum == 14) {
                $planCode_array = [];
                foreach ($playNum['play_num'] as $val) {
                    if ($val['type'] != 1) continue;
                    $planCode = explode(',', $val['num']);
                    $planCode_array = array_merge($planCode_array, $planCode);
                }
                if (in_array('小单', $planCode_array) || in_array('大双', $planCode_array)
                ) {
                    $ben_bonus = 1;
                }
            }
            foreach ($playNum['play_num'] as $val) {
                $prizeNum = 0;//中奖注数
                $bounskey = array_search($val['type'], $sign);
                $gain_array = explode(',', $bouns[$bounskey]['gain']);
                $gain = $gain_array[0];//每一注的奖金

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
                switch ($val['type']) {
                    case 1://混合 和值
                            $planCode = explode(',', $val['num']);
                            foreach ($planCode as $v) {
                                if ($sum >= 14 and $v == '大') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[0];
                                }
                                if ($sum < 14 and $v == '小') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[1];
                                }
                                if ($sum%2 == 1 and $v == '单') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[2];
                                }
                                if ($sum%2 == 0 and $v == '双') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[3];
                                }
                                if ($sum >= 14 and $sum%2 == 1 and $v == '大单') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[4];
                                }
                                if ($sum < 14 and $sum%2 == 1 and $v == '小单') {
                                    $prizeNum += 1;
                                    if (isset($ben_bonus)) {
                                        $gain = 2;
                                        $no_bonus_base = 1;
                                    } else {
                                        $gain =  $gain_array[5];
                                    }
                                }
                                if ($sum >= 14 and $sum%2 == 0 and $v == '大双') {
                                    $prizeNum += 1;
                                    if (isset($ben_bonus)) {
                                        $gain = 2;
                                        $no_bonus_base = 1;
                                    } else {
                                        $gain =  $gain_array[6];
                                    }
                                }
                                if ($sum < 14 and $sum%2 == 0 and $v == '小双') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[7];
                                }
                                if ($sum >= 22 and $v == '极大') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[8];
                                }
                                if ($sum < 6 and $v == '极小') {
                                    $prizeNum += 1;
                                    $gain = $gain_array[9];
                                }
                            }
                        break;
                    case 2://色波
                        $planCode = explode(',', $val['num']);
                        foreach ($planCode as $v) {
                            if (in_array($sum, [1,4,7,10,16,19,22,25]) and $v == '绿波') {
                                $prizeNum += 1;
                                $gain = $gain_array[1];
                            }
                            if (in_array($sum, [2,5,8,11,17,20,23,26]) and $v == '蓝波') {
                                $prizeNum += 1;
                                $gain = $gain_array[2];
                            }
                            if (in_array($sum, [3,6,9,12,15,18,21,24]) and $v == '红波') {
                                $prizeNum += 1;
                                $gain = $gain_array[0];
                            }
                        }
                        break;
                    case 3://豹子
                        if (count(array_unique($code)) == 1) $prizeNum = 1;
                        break;
                }

                if (isset($val['rebate'])) {//存在返点时
                    if ($val['rebate']) $bonus_base += $val['rebate'];
                }
                $bounsNum = !isset($no_bonus_base) ? $gain * (100 - $bonus_base)/100 : $gain;//获取最高奖金
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
            case 1://混合
                array_push($res, [
                    'name' => $data['name'] . '-大小单双',
                    'gain' => $gain[0]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-大单',
                    'gain' => $gain[4]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-小单',
                    'gain' => $gain[4]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-大双',
                    'gain' => $gain[4]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-小双',
                    'gain' => $gain[4]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-极大',
                    'gain' => $gain[8]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-极小',
                    'gain' => $gain[9]
                ]);
                break;
            case 2://色波
                array_push($res, [
                    'name' => $data['name'] . '-红波',
                    'gain' => $gain[0]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-绿波',
                    'gain' => $gain[1]
                ]);
                array_push($res, [
                    'name' => $data['name'] . '-蓝波',
                    'gain' => $gain[2]
                ]);
                break;
        }
        return $res;
    }


}