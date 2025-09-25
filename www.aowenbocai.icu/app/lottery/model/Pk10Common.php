<?php
namespace app\lottery\model;

use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use core\Setting;
use think\Model;
use app\index\model\User;
use think\Validate;

class Pk10Common extends Model
{
    /**
     *  算注数 -- 验证号码
     * @param  array $data 表单提交的值
     * @return array $codeNum 投注数  $play 投注号码
     */
    public function playNum($data, $playType, $is_chase, $name)
    {
        $sign = array_keys($playType);
        $play_array = $data['play_array'];
        $codeArray = [];
        $error = true;
        $name_info = json_decode((new Pk10())->getValue(LotteryCommon::getSettingValue($name, 'config')), true);
        $user = User::get(['sid' => session('sid')]);
        $user_rebate = isset($user['rebate']['pk10']) ? $user['rebate']['pk10'] : 0;
        foreach ($play_array as $value) {
            $codeNum=0;
            $type_array = explode('.', $value['type']);
            if(!in_array($type_array[0], $sign)){
                $error = false;
                break;//直接返回错误或者返回注数为0
            }
            $play_model_res = (new LotteryCom())->playModeInit($value, $is_chase, $name_info['mode'], $user_rebate);

            if ($play_model_res['err']) {
                $error = false;
                break;//直接返回错误或者返回注数为0
            }

            if($value['type'] != 'WX' ){
                $value['num'] = explode('|', $value['num']);
                foreach ($value['num'] as $key => &$val) {
                    $val = explode(',', $val);
                }
            }else{
                $value['num'] = explode(',', $value['num']);
            }
            switch ($value['type']) {
                case 'WX':
                    $codeNum += $this->WZCount($value['num']);
                    break;
                case 'GJ':
                    $codeNum += count($value['num'][0]);
                    break;
                case 'YJ' :
                    $codeNum += $this->betCount(1,$value['num']);
                    break;
                case 'YJ.2':
                    $codeNum += $this->betCount(1,$value['num']);
                    break;
                case 'JJ':
                    $codeNum += $this->betCount(2,$value['num']);
                    break;
                case 'JJ.2':
                    $codeNum += $this->betCount(2,$value['num']);
                    break;
                case 'QS':
                    $codeNum += $this->betCount(3,$value['num']);
                    break;
                case 'QS.2':
                    $codeNum += $this->betCount(3,$value['num']);
                    break;
                case 'QW':
                    $codeNum += $this->betCount(4,$value['num']);
                    break;
                case 'QL':
                    $codeNum += $this->betCount(5,$value['num']);
                    break;
                case 'QQ':
                    $codeNum += $this->betCount(6,$value['num']);
                    break;
                case 'QB':
                    $codeNum += $this->betCount(7,$value['num']);
                    break;
                case 'QJ':
                    $codeNum += $this->betCount(8,$value['num']);
                    break;
                case 'QSHI':
                    $codeNum += $this->betCount(9,$value['num']);
                    break;
                case 'DW':
                case 'DXDS':
                case 'DXDS.2':
                case 'LH':
                    foreach ($value['num'] as $v) {
                        if ($v[0] != '-') {
                            $codeNum += count($v);
                        }
                    }
                    break;
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

    public function prizeCom($open, $expectList, $bonus, $setting_config)
    {
        $open = explode(',', $open);
        $returnData = [];
        $sys_bonus_base = (isset($setting_config['bonus_base']) and $setting_config['bonus_base'] > 0 and $setting_config['bonus_base'] < 100) ? $setting_config['bonus_base'] : 100;
        $sys_bonus_base = 100 - $sys_bonus_base;
        $user_model = new User();
        foreach ($expectList as $value) {
            $buy = $value['buy'];
            if (!$buy) continue;
            $playNum = $buy['play_num'];
            $money = 0;
            foreach ($playNum as $val) {
                /**猜位置，大小单双 */
                if($val['type'] == 'WZ'){
                    continue;
                }
                $money += $this->prizeCount($open, $val, $bonus, $user_model, $buy['userid'], $sys_bonus_base);
            }
            $returnData[] = [
                'id' => $value['id'],
                'buy_id' => $buy['id'],
                'expect' => $value['expect'],
                'multiple' => $value['multiple'],
                'is_join' => $buy['is_join'],
                'total' => $buy['total_money'],
                'total_share' => $buy['total_share'],
                'userid' => $buy['userid'],
                'lottery_id' => $buy['lottery_id'],
                'assure_money' => $buy['assure_money'],
                'gain' => $buy['gain'],
                'is_stop' => $buy['is_stop'],
                'bouns' => $money * intval($value['multiple']),
                'play_num' => $playNum
            ];
        }
        return $returnData;
    }

    /**计算奖金  */
    public function prizeCount($open, $plan, $bonus, $user_model, $userid, $bonus_base)
    {
        $rightNum = 0;
        $plan_num = explode('|', $plan['num']);
        $type = explode('.', $plan['type']);
        $is_jz = count($type);
        $gain = 0;
        if($is_jz == 2 and $plan['type'] != 'DXDS.2'){
            $gain = $rightNum == count(explode('|', $plan['num'])) ? $bonus[$plan['type']]['wz'] : 0;
        } else {
            switch ($type[0]) {
                case 'GJ':
                case 'YJ':
                case 'JJ':
                case 'QS':
                case 'QW':
                case 'QL':
                case 'QQ':
                case 'QB':
                case 'QJ':
                case 'QSHI':
                    $plan_num_array = $this->getPlan($plan);
                    foreach ($plan_num_array as $key => $v) {
                        if (count($v) != count(array_unique($v))) continue;
                        foreach ($v as $key2 => $row) {
                            $rightNum += $open[$key2] == $row ? 1 : 0;
                        }
                    }
                    $gain =  isset($bonus[$plan['type']][$rightNum]) ? $bonus[$plan['type']][$rightNum] : 0;
                    break;
                case 'DW':
                    foreach ($plan_num as $key => $v) {
                        if ($v != '-') {
                            $my_plan = explode(',', $v);
                            $gain += in_array($open[$key], $my_plan) ? $bonus[$plan['type']][1] : 0;
                        }
                    }
                    break;
                case 'DXDS':
                    $my_bonus = explode(',', $bonus[$type[0]][1]);
                    $no_11 = count(array_unique($my_bonus)) == 1 ? 0 : 1;
                    if (!isset($plan_num[1])) {//冠亚和
                        $he = intval($open[0]) + intval($open[1]);
                        $code_array = [];
                        $bonus_array = [];
                        if ($he == 11 and !$no_11) {
                            break;
                        }
                        if ($he > 11) {
                            array_push($code_array, '大');
                            $bonus_array['大'] = $my_bonus[2];
                        }
                        if ($he < (11 + $no_11)) {
                            array_push($code_array, '小');
                            $bonus_array['小'] = $my_bonus[3];
                        }
                        if ($he % 2 == 0) {
                            array_push($code_array, '双');
                            $bonus_array['双'] = $my_bonus[5];
                        }
                        if ($he % 2 == 1) {
                            array_push($code_array, '单');
                            $bonus_array['单'] = $my_bonus[4];
                        }
                        $my_plan = explode(',', $plan_num[0]);
                        foreach ($my_plan as $row) {
                            $gain += in_array($row, $code_array) ? $bonus_array[$row] : 0;
                        }
                        break;
                    }
                    foreach ($plan_num as $key => $v) {
                        if ($v != '-') {
                            if (isset($type[1]) and $type[1] == 2) {//后五大小单双
                                $code = $open[$key+5];
                            } else {
                                $code = $open[$key];
                            }
                            $code_array = [];
                            if ($code > 5) array_push($code_array, '大');
                            if ($code < 6) array_push($code_array, '小');
                            if ($code % 2 == 0) array_push($code_array, '双');
                            if ($code % 2 == 1) array_push($code_array, '单');
                            $my_plan = explode(',', $v);
                            foreach ($my_plan as $row) {
                                $gain += in_array($row, $code_array) ? $my_bonus[0] : 0;
                            }
                        }
                    }
                    break;
                case 'LH':
                    foreach ($plan_num as $key => $v) {
                        if ($v != '-') {
                            $code_array = [];
                            if ($open[$key] > $open[9 - $key]) array_push($code_array, '龙');
                            if ($open[$key] < $open[9 - $key]) array_push($code_array, '虎');
                            $my_plan = explode(',', $v);
                            foreach ($my_plan as $row) {
                                $gain += in_array($row, $code_array) ? $bonus[$type[0]][1] : 0;
                            }
                        }
                    }
                    break;
            }
        }
        $user_info = $user_model->find($userid);
        $my_rebate = isset($user_info['rebate']['pk10']) ? $user_info['rebate']['pk10'] : 0;
        if ($user_info['top_agents']) {
            $top_agents = $user_model->find($user_info['top_agents']);
            $top_rebate = isset($top_agents['rebate']['pk10']) ? $top_agents['rebate']['pk10'] : 0;
            $bonus_base += ($top_rebate - $my_rebate);
        }
        if (isset($val['rebate'])) {//存在返点时
            if ($plan['rebate']) $bonus_base += $plan['rebate'];
        }

        $bounsNum = $gain * (100 - $bonus_base)/100;//获取最高奖金

        $unit_zh = [1 => 1, 2 => 10, 3 => 100, 4 => 1000];
        $multiple = 1;//投注的倍数 投注的金额除2
        if (isset($plan['money'])) {
            $multiple = $plan['money'] / 2;
        }
        if (isset($plan['unit'])) {
            $multiple = 1 / $unit_zh[$plan['unit']];
        }
        if (isset($plan['multiple'])) {
            $multiple = $multiple * $plan['multiple'];
        }
        return $bounsNum * $multiple;
    }

    /**
     * 获取所有有效的投注组合
     **/
    private function getPlan($plan)
    {
        $plan_num = explode('|', $plan['num']);
        $res= [];
        foreach ($plan_num as $key => $v) {
            array_push($res, explode(',', $v));
        }
        return $this->combination($res);
    }

    private function combination(array $options)
    {
        $rows = [];
        foreach ($options as $option => $items) {
            if (count($rows) > 0) {
                // 2、将第一列作为模板
                $clone = $rows;

                // 3、置空当前列表，因为只有第一列的数据，组合是不完整的
                $rows = [];

                // 4、遍历当前列，追加到模板中，使模板中的组合变得完整
                foreach ($items as $item) {
                    $tmp = $clone;
                    foreach ($tmp as $index => $value) {
                        $value[$option] = $item;
                        $tmp[$index] = $value;
                    }

                    // 5、将完整的组合拼回原列表中
                    $rows = array_merge($rows, $tmp);
                }
            } else {
                // 1、先计算出第一列
                foreach ($items as $item) {
                    $rows[][$option] = $item;
                }
            }
        }
        return $rows;
    }

    /*
     * JS移植
     **/
    public function betCount($n, $data)
    {

        $count = $this->bc($n, $data);
        return $count;
    }

    /*
     * JS移植
     **/
    private function bt($n, $data, $l, $arr)
    {
        $count = 0;
        for ($i = 0; $i < count($data[$l]); $i++) {
            $code = $data[$l][$i];
            if (in_array($code, $arr)) continue;
            $_arr = array_merge($arr, [$code]);
            if ($l < $n) {
                $count += $this->bt($n, $data, $l + 1, $_arr);
            } else {
                $count ++;
            }
        }
        return $count;
    }

    /*
     * JS移植
     **/
    private function bc($n, $data)
    {
        if (!empty($data)) return $this->bt($n, $data,0, []);

        return 0;
    }

    /*
     * JS移植
     **/
    public function WZCount($data, $exp = false)
    {
        $carr = [];
        foreach ($data as $p => $item) {
            $count = 0;
            foreach ($item as $_item) {
                $count += $_item;
            }
            $carr[$p] = $count;
        }
        if ($exp) {
            return $carr;
        }

        return array_sum($carr);
    }

    /**获取一个玩法多个奖金的赔率表  */
    public function getGainShow($type, $data)
    {
        $res = [];
        $play = [ 'GJ' => '冠军', 'YJ' => '冠亚', 'JJ' => '前三', 'QS' =>'前四', 'QW' =>'前五', 'QL' =>'前六', 'QQ' =>'前七', 'QB' =>'前八', 'QJ' =>'前九', 'QSHI' =>'前十', 'DW' =>'定位', 'DXDS'
        => '大小单双', 'LH' => '龙虎'];
        foreach ($data as $key => $v) {
            if ($key == 'isOpen') continue;
            if (is_numeric($key)) {
                array_push($res, [
                    'name' => $play[$type] . '-中' . $key . '位',
                    'gain' => $v
                ]);
            }
            if ($key == 'wz') {
                array_push($res, [
                    'name' => $play[$type] . '-精准',
                    'gain' => $v
                ]);
            }
            if (in_array($type, ['DW', 'DXDS', 'LH'])) {
                array_push($res, [
                    'name' => $play[$type],
                    'gain' => $v
                ]);
            }

        }
        return $res;
    }



}