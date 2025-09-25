<?php
namespace app\lottery\model\common\pc28;

use app\lottery\model\common\BaseBuy;
use app\lottery\model\Ssc;
use app\lottery\model\LotteryCom;

class Pc28BaseBuy extends BaseBuy
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
            $html[$key] = '<i>['.$value['name'].']</i>';
            $html[$key] .= '<font>';
            switch ($value['type']) {
                case 1://混合
                    $sum = array_sum($code);
                    $planCode = explode(',', $value['num']);
                    foreach ($planCode as $v) {
                        $da = ($sum >= 14 and $v == '大');
                        $xiao = ($sum < 14 and $v == '小');
                        $dan = ($sum%2 == 1 and $v == '单');
                        $shuang = ($sum%2 == 0 and $v == '双');
                        if ($da || $xiao || $dan || $shuang) {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        $html[$key] .= $v . ",";
                    }
                    $html[$key] = trim($html[$key], ',');
                    break;
                case 2://色波
                    $sum = array_sum($code);
                    $planCode = explode(',', $value['num']);
                    foreach ($planCode as $v) {
                        if (in_array($sum, [1,4,7,10,16,19,22,25]) and $v == '绿波') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if (in_array($sum, [2,5,8,11,17,20,23,26]) and $v == '蓝波') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        if (in_array($sum, [3,6,9,12,15,18,21,24]) and $v == '红波') {
                            $html[$key] .= "<span class='red'>".$v."</span>,";
                            continue;
                        }
                        $html[$key] .= $v . ",";
                    }
                    break;
                case 3://豹子
                    if (count(array_unique($code)) == 1)
                        $html[$key] .= "<span class='red'>" . $value['num'] . "</span>,";
                    else
                        $html[$key] .= $value['num'] . ",";
                    break;
            }
            $html[$key] .= '</font>';
            $html[$key] .= $model->planContent($value);

        }
        return $html;
    }
    

}