<?php
namespace app\lottery\model\common\ks;

use app\lottery\model\common\BaseBuy;
use app\lottery\model\Ssc;
use app\lottery\model\LotteryCom;

class KsBaseBuy extends BaseBuy
{

    protected function initialize()
    {
        parent::initialize();
    }


    /**标记中奖号码 */
    public function setCode($code,$plan)
    {
        $model = new LotteryCom();
        foreach ($plan as $key => $value) {
            $dq_code = $code;
            $html[$key] = '<i>['.$value['name'].']</i>';
            $html[$key] .= '<font>';
            switch ($value['type']) {
                case 1://和值
                    $sum = array_sum($dq_code);
                    $planCode = explode(',', $value['num']);
                    foreach ($planCode as $v) {
                        if ($sum >= 11 and $v == '大') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if ($sum < 11 and $v == '小') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if ($sum%2 == 1 and $v == '单') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if ($sum%2 == 0 and $v == '双') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if ($sum == floatval($v)) {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        $html[$key] .= $v . ",";
                    }
                    $html[$key] = trim($html[$key], ',');
                    break;
                case 2://三同号通选
                    if (count(array_unique($dq_code)) == 1) {
                        $html[$key] .= "<span class='red'>".$value['num']."</span>";
                    }  else {
                        $html[$key] .= $value['num'];
                    }
                    break;
                case 3://三同号单选
                    $planCode = explode(',', $value['num']);
                    if (count(array_unique($dq_code)) == 1) {
                        $bj_code = '';
                        foreach ($dq_code as $v) {
                            $bj_code .= $v;
                        }
                        if (in_array($bj_code, $planCode)) {
                            $html[$key] .= "<span class='red'>".$value['num']."</span>";
                        } else {
                            $html[$key] .= $value['num'];
                        };
                    } else {
                        $html[$key] .= $value['num'];
                    };
                    break;
                case 4://三不同号
                    $planCode = explode(',', $value['num']);
                    if (count(array_unique($dq_code)) == 3) {
                        if (empty(array_diff($dq_code, $planCode))) {
                            $html[$key] .= "<span class='red'>".$value['num']."</span>";
                        } else {
                            $html[$key] .= $value['num'];
                        }
                    } else {
                        $html[$key] .= $value['num'];
                    }
                    break;
                case 5://三连号通选
                    sort($dq_code);
                    $bj_num = -1;
                    $bool = true;
                    foreach ($dq_code as $v) {
                        if ($bj_num == -1) {
                            $bj_num = $v;
                        } else {
                            if (($v-1) != $bj_num) {
                                $bool = false;
                            }
                            $bj_num = $v;
                        }
                    }
                    if ($bool) {
                        $html[$key] .= "<span class='red'>".$value['num']."</span>";
                    } else {
                        $html[$key] .= $value['num'];
                    }
                    break;
                case 6://二同号复选
                    $bool = 0;
                    $planCode = explode(',', $value['num']);
                    if (count(array_unique($dq_code)) == 2) {
                        foreach ($planCode as $v) {
                            $one_num = mb_substr($v, 0,1, 'utf-8');
                            $i = 0;
                            foreach ($dq_code as $row) {
                                if ($one_num == $row) $i++;
                            }
                            if ($i == 2)  $bool = 1;
                        }
                    }
                    if ($bool) {
                        $html[$key] .= "<span class='red'>".$value['num']."</span>";
                    } else {
                        $html[$key] .= $value['num'];
                    }
                    break;
                case 7://二同号单选
                    $bool = 0;
                    $code_data = explode('|', $value['num']);
                    if (count(array_unique($dq_code)) == 2) {
                        $code_one = explode(',', $code_data[0]);
                        foreach ($code_one as $v) {
                            $one_num = mb_substr($v, 0,1, 'utf-8');
                            $two_num = explode(',', $code_data[1]);
                            foreach ($two_num as $row) {
                                $play = [floatval($one_num), floatval($one_num), floatval($row)];
                                if ($play == $dq_code) $bool = 1;
                            }
                        }
                    }
                    if ($bool) {
                        $html[$key] .= "<span class='red'>".$value['num']."</span>";
                    } else {
                        $html[$key] .= $value['num'];
                    }
                    break;
                case 8://二不同号
                    $planCode = explode(',', $value['num']);
                    foreach ($planCode as $v) {
                        if (in_array($v, $dq_code)) {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                        } else {
                            $html[$key] .= $v . ",";
                        }
                    }
                    $html[$key] = trim($html[$key], ',');
                    break;
            }
            $html[$key] .= '</font>';
            $html[$key] .= $model->planContent($value);

        }
        return $html;
    }
    

}