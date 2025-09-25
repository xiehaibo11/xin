<?php
namespace app\lottery\model\common\syxw;

use app\lottery\model\common\BaseBuy;
use app\lottery\model\Ssc;
use app\lottery\model\LotteryCom;

class SyxwBaseBuy extends BaseBuy
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
            $name = explode('-', $value['name']);
            $rx = mb_substr($name[0], 0, 2, 'utf-8') == '任选';
            $rxz = $rx || mb_substr($name[0], -2, 2, 'utf-8') == '组选' ? 1 : 0;
            $html[$key] = '['.$value['name'].']';
            /**直选 */
            if(!$rxz){
                $html[$key] .= $this->redZx($dq_code, $value['num']);
                $html[$key] .= '</font>';
                $html[$key] .= $model->planContent($value);
                continue;
            }
            $dt = count($name) == 2 && mb_substr($name[1], 0, 2, 'utf-8') == '胆拖' ? 1 : 0;
            if(!$rx){
                $codeNum = mb_substr($name[0], 1, 1, 'utf-8') == '二' ? 2 : 3;
                $dq_code = array_slice($dq_code, 0, $codeNum);
            }

            if($dt){
                $html[$key] .= $this->redDt($dq_code, $value['num']);
            }else{
                $codeList = explode(',', $value['num']);

                $rxhtml = '';
                foreach ($codeList as $rxcode) {
                    $rxhtml .= in_array($rxcode, $dq_code) ? "<span class='red'>".$rxcode."</span>," : $rxcode.',';
                }
                $html[$key] .= trim($rxhtml, ',');
            }
            $html[$key] .= '</font>';
            $html[$key] .= $model->planContent($value);
        }
        return $html;
    }

    /**标记胆拖 */
    public function redDt($code, $plan)
    {
        $html = '';
        $planCode = explode('#', $plan);

        $dan = explode(',', $planCode[0]);
        foreach ($dan as $key => $value) {
            $html .= in_array($value, $code) ? "<span class='red'>".$value."</span>," : $value.',';
        }
        $html =  trim($html, ',').'#';

        $tuo = explode(',', $planCode[1]);
        foreach ($tuo as $key => $value) {
            $html .= in_array($value, $code) ? "<span class='red'>".$value."</span>," : $value.',';
        }
        return trim($html, ',');
    }
    /**标记直选 */
    public function redZx($code, $plan)
    {
        $html = '';
        $plan = explode('|', $plan);
        $count = count($plan);
        $code = array_slice($code, 0, $count);
        foreach ($plan as $key => $value) {
            if($key != 0){
                $html = trim($html, ',').' | ';
            }
            $codelist = explode(',', $value);
            foreach ($codelist as $val) {
                $html .= intval($val) == intval($code[$key]) ? "<span class='red'>".$val."</span>," : $val.',';
            }
        }
        return trim($html, ',');
    }

}