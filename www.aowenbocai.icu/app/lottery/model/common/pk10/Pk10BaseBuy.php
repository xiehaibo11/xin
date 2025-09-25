<?php
namespace app\lottery\model\common\pk10;

use app\common\controller\LotteryCommon;
use app\lottery\model\LotteryCom;
use app\lottery\model\common\BaseBuy;

class Pk10BaseBuy extends BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
    }
    public function getBuyNumAttr($value, $data)
    {
        if($data['is_join'] != 1){
            return $data['total_money'];
        }
        $count = LotteryCommon::getModel($data['ext_name'], 'join')->where('buy_id', $data['id'])->where('userid', $data['userid'])->sum('money');
        return $count;
    }

    /**
     * 获取器 - end_status
     * @return int  0进行中， 1已截止
     */
    public function getEndStatusAttr($value, $data)
    {
        return strtotime($data['end_time']) > time() ? 0 : 1;
    }


    public function getBettingAttr($value, $data)
    {
        $plan = json_decode($data['play_num'], true);
        return $this->transCode($plan);
    }

    public function transCode($plan, $code = '')
    {
        $play = ['WZ' => '猜位置', 'GJ' => '冠军', 'YJ' => '冠亚','YJ.2' => '冠亚-精准', 'JJ' => '前三', 'JJ.2' => '前三-精准', 'QS' =>'前四', 'QS.2' =>'前四-精准', 'QW' =>'前五', 'QL' =>'前六', 'QQ' =>'前七', 'QB' =>'前八', 'QJ' =>'前九', 'QSHI' =>'前十', 'DW' =>'定位', 'DXDS'
        => '大小单双', 'DXDS.2' => '后五大小单双', 'LH' => '龙虎'];
        $wz_play = ['冠军','亚军','季军','第四','第五','第六','第七','第八','第九','第十'];
        $dx = ['A' => '大','B' => '小','C' => '单','D' => '双'];
        $ww = ['冠','亚','季','四','五','六','七','八','九','十'];
        $game = [];
        foreach ($plan as $value) {
            $num = explode('|', $value['num']);
            if ($value['type'] == 'DXDS') {
                if (!isset($num[1])) {
                    $betting = "<i>[冠亚和大小单双]</i><font>";
                } else {
                    $betting = "<i>[前五大小单双]</i><font>";
                }
            } else {
                $betting = isset($play[$value['type']])?"<i>[".$play[$value['type']]."]</i><font>":"<i>[暂无]</i><font>";
            }
            if (empty($code)) {
                $betting .=   $value['num'];
            } else {
                $type = explode('.', $value['type']);
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
                    case 'DW':
                        foreach ($num as $key1 => &$value1) {
                            $weiCode = empty($code) ? '' : $code[$key1];
                            $value1 = explode(',', $value1);
                            if (in_array($weiCode, $value1)) {
                                $_key = array_search($weiCode, $value1);
                                $value1[$_key] = "<span class='red'>" . $weiCode . "</span>";
                            }
                            $betting .= ' ' . $ww[$key1] . ":" . implode(',', $value1);
                        }
                        break;
                    case 'DXDS':
                        if (!isset($num[1])) {//冠亚和
                            $bonus = json_decode((LotteryCommon::getSetting($this->ext_name))->getValue(LotteryCommon::getSettingValue($this->ext_name, 'bouns')), true);
                            $my_bonus = explode(',', $bonus[$type[0]][1]);
                            $no_11 = count(array_unique($my_bonus)) == 1 ? 0 : 1;
                            $he = intval($code[0]) + intval($code[1]);
                            $is_11 = 0;
                            if ($he == 11 and !$no_11) {
                                $is_11 = 1;
                            }
                            $code_array = [];
                            if ($he > 11 and !$is_11) {
                                array_push($code_array, '大');
                            }
                            if ($he < 12 and !$is_11) {
                                array_push($code_array, '小');
                            }
                            if ($he % 2 == 0 and !$is_11) {
                                array_push($code_array, '双');
                            }
                            if ($he % 2 == 1 and !$is_11) {
                                array_push($code_array, '单');
                            }
                            $my_plan = explode(',', $num[0]);
                            foreach ($my_plan as &$row) {
                                if (in_array($row, $code_array)) {
                                    $row = "<span class='red'>" . $row . "</span>";
                                }
                            }
                            $betting .=  implode(',', $my_plan);
                            break;
                        }
                        foreach ($num as $key => $v) {
                            if ($v != '-') {
                                if (isset($type[1]) and $type[1] == 2) {//后五大小单双
                                    $my_code = $code[$key+5];
                                } else {
                                    $my_code = $code[$key];
                                }
                                $code_array = [];
                                if ($my_code > 5) array_push($code_array, '大');
                                if ($my_code < 6) array_push($code_array, '小');
                                if ($my_code % 2 == 0) array_push($code_array, '双');
                                if ($my_code % 2 == 1) array_push($code_array, '单');
                                $my_plan = explode(',', $v);
                                foreach ($my_plan as $row) {
                                    if (in_array($row, $code_array)) {
                                        $row = "<span class='red'>" . $row . "</span>";
                                    }
                                }
                                $betting .=  ($key != 0 ? '|' : '') . implode(',', $my_plan);
                            }
                        }
                        break;
                    case 'LH':
                        foreach ($num as $key => $v) {
                            if ($v != '-') {
                                $code_array = [];
                                if ($code[$key] > $code[9 - $key]) array_push($code_array, '龙');
                                if ($code[$key] < $code[9 - $key]) array_push($code_array, '虎');
                                $my_plan = explode(',', $v);
                                foreach ($my_plan as $row) {
                                    if (in_array($row, $code_array)) {
                                        $row = "<span class='red'>" . $row . "</span>";
                                    }
                                }
                                $betting .=  ($key != 0 ? '|' : '') . implode(',', $my_plan) ;
                            }
                        }
                        break;
                }
            }
            $betting .= '</font>';
            //公共属性
            $betting .= (new LotteryCom())->planContent($value);
            array_push($game, $betting);
        }

        return $game;
    }


    public function setCode($code, $plan)
    {
        $res = $this->transCode($plan, $code);
        return $res;
    }
    

}