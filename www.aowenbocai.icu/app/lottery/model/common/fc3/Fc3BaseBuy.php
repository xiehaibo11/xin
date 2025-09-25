<?php
namespace app\lottery\model\common\fc3;

use app\common\controller\LotteryCommon;
use app\lottery\model\LotteryCom;
use app\lottery\model\common\BaseBuy;

class Fc3BaseBuy extends BaseBuy
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取器 - 投注号码
     */
    public function getPlayNumAttr($value, $data)
    {
        $setting = json_decode(LotteryCommon::getSetting($data['ext_name'])->getValue(LotteryCommon::getSettingValue($data['ext_name'], 'bouns')), true);
        $sign = array_column($setting, 'sign');
        $play_num = json_decode($value, true);
        foreach ($play_num as &$value){
            $signExist = array_search($value['type'], $sign);
            $value['type'] = is_numeric($signExist) ? $setting[$signExist]['name'] : '该玩法不存在';
        }
        return $play_num;
    }

    /**标记中奖号码 */
    public function setCode($code, $plan)
    {
        $model = new LotteryCom();
        $html = []; // 初始化为数组

        foreach ($plan as $key => $value) {
            $html[$key] = '<i>[' . $value['type'] . ']</i>';
            $html[$key] .= '<font>';

            $name = explode('-', $value['type']);
            $name_h2 = mb_substr($name[0], -2, 2, 'utf-8');
            $is_zhi = $name_h2 == '直选' || $name_h2 == '一星' || $name_h2 == '通选' || $name[0] == '定位胆';
            $is_zu = $name_h2 == '组三' || $name_h2 == '组六' || $name_h2 == '组选';

            /** 直选 */
            if ($is_zhi && trim($name[1]) != '和值') {
                if (isset($value['num'])) {
                    $html[$key] .= $this->redZx($code, $value['num']);
                }
            }

            /** 和值 */
            if ($is_zhi && trim($name[1]) == '和值') {
                if (isset($value['num'])) {
                    $html[$key] .= $this->redZxHz($code, $value['num'], 1, $name);
                }
            }

            if (!$is_zhi && trim($name[1]) == '和值') { // 组选和值
                if (isset($value['num'])) {
                    $html[$key] .= $this->redZxHz($code, $value['num'], 0, $name);
                }
            }

            if (trim($name[1]) == '胆拖') {
                if (isset($value['num'])) {
                    $html[$key] .= $this->redDt($code, $value['num'], $name[0]);
                }
            }

            if ($is_zu && trim($name[1]) != '和值' && trim($name[1]) != '胆拖') { // 组选
                if (isset($value['num'])) {
                    $html[$key] .= $this->redZu($code, $value['num'], $name[0]);
                }
            }

            if ($name[0] == '大小单双') {
                if (isset($value['num'])) {
                    $html[$key] .= $this->redDxds($code, $value['num']);
                }
            }

            if ($name[0] == '龙虎') {
                if (isset($value['num'])) {
                    $html[$key] .= $this->redLh($code, $value['num'], $name[1]);
                }
            }

            $html[$key] .= '</font>';
            $html[$key] .= $model->planContent($value);
        }
        return $html;
    }

    /**标记大小单双 */
    public function redDxds($code, $plan)
    {
        $plan = explode('|', $plan);
        $code = array_slice($code, 3, 2);
        foreach ($code as $key => $value) {
            $dx = $value < 5 ? '小' : '大';
            $ds = $value % 2 == 0 ? '双' : '单';
            $res[$key] = [$dx, $ds];
        }

        $html = '';
        foreach ($plan as $key => $value) {
            $arr = explode(',', $value);
            if ($key != 0) {
                $html = trim($html, ',') . ' | ';
            }
            foreach ($arr as $val) {
                $html .= in_array($val, $res[$key]) ? "<span class='red'>" . $val . "</span>," : $val . ',';
            }
        }
        return trim($html, ',');
    }

    /**标记直选 */
    public function redZx($code, $plan)
    {
        $plan = explode('|', $plan);
        $count = count($plan);
        $code = array_slice($code, 5 - $count, $count);

        $html = '';
        foreach ($plan as $key => $value) {
            if ($key != 0) {
                $html = trim($html, ',') . ' | ';
            }
            $newCode = explode(',', $value);
            foreach ($newCode as $val) {
                $html .= intval($val) == intval($code[$key]) ? "<span class='red'>" . $val . "</span>," : $val . ',';
            }
        }
        return trim($html, ',');
    }

    /**标记和值 */
    public function redZxHz($code, $plan, $zhix, $type, $zx = 0)
    {
        $fristChar = mb_substr($type[0], 0, 1, 'utf-8');
        $qh_num = mb_substr(trim($type[0]), 1, 1, 'utf-8') == '三' ? 3 : 2;
        if ($fristChar == '前') {
            $code = (array_slice($code, 0, $qh_num));
        } elseif ($fristChar == '中') {
            $code = (array_slice($code, 1, 3));
        } elseif ($fristChar == '后') {
            $code = (array_slice($code, 5 - $qh_num, $qh_num));
        } else {
            $code = array_slice($code, 5 - 2, 2);
        }
        $name = $type[0];
        $count = mb_substr($name, 0, 1, 'utf-8') == '三' ? 3 : 2;
        $code = array_slice($code, 5 - $count, $count);
        if (!$zhix && $count == 3) {
            $last = mb_substr($name, -1, 1, 'utf-8') == '三' ? 2 : 3;
            $codeNum = count(array_unique($code));
            if ($codeNum == 3 || $codeNum != $last) {
                return $plan;
            }
        }
        $plan = explode(',', $plan);
        $html = '';

        $sum = array_sum($code);
        foreach ($plan as $key => $value) {
            if ($zx) {
                $html .= in_array($value, $code) ? "<span class='red'>" . $value . "</span>," : $value . ',';
            } else {
                $html .= intval($value) == $sum ? "<span class='red'>" . $value . "</span>," : $value . ',';
            }
        }
        return trim($html, ',');
    }

    /**标记和值 */
    public function redZu($code, $plan, $type)
    {
        $fristChar = mb_substr($type, 0, 1, 'utf-8');
        $qh_num = mb_substr(trim($type[0]), 1, 1, 'utf-8') == '三' ? 3 : 2;
        if ($fristChar == '前') {
            $code = (array_slice($code, 0, $qh_num));
        } elseif ($fristChar == '中') {
            $code = (array_slice($code, 1, 3));
        } elseif ($fristChar == '后') {
            $code = (array_slice($code, 5 - $qh_num, $qh_num));
        } else {
            $code = array_slice($code, 5 - 2, 2);
        }
        $html = '';
        $plan = explode(',', $plan);
        foreach ($plan as $key => $value) {
            $html .= in_array($value, $code) ? "<span class='red'>" . $value . "</span>," : $value . ',';
        }
        return trim($html, ',');
    }

    /**标记胆拖 */
    public function redDt($code, $plan, $name)
    {
        $fristChar = mb_substr($name, 0, 1, 'utf-8');
        $qh_num = mb_substr(trim($name), 1, 1, 'utf-8') == '三' ? 3 : 2;
        if ($fristChar == '前') {
            $code = (array_slice($code, 0, $qh_num));
        } elseif ($fristChar == '中') {
            $code = (array_slice($code, 1, 3));
        } elseif ($fristChar == '后') {
            $code = (array_slice($code, 5 - $qh_num, $qh_num));
        } else {
            $code = array_slice($code, 5 - 2, 2);
        }

        $last = mb_substr($name, -1, 1, 'utf-8') == '三' ? 2 : 3;
        $code = array_slice($code, 2, 3);
        $codeNum = count(array_unique($code));
        if ($codeNum == 3 || $codeNum != $last) {
            return $plan;
        }
        $html = '';
        $planCode = explode('#', $plan);

        $dan = explode(',', $planCode[0]);
        foreach ($dan as $key => $value) {
            $html .= in_array($value, $code) ? "<span class='red'>" . $value . "</span>," : $value . ',';
        }
        $html = trim($html, ',') . '#';

        $tuo = explode(',', $planCode[1]);
        foreach ($tuo as $key => $value) {
            $html .= in_array($value, $code) ? "<span class='red'>" . $value . "</span>," : $value . ',';
        }
        return trim($html, ',');
    }

    /**龙虎 */
    public function redLh($code, $plan, $type)
    {
        switch ($type) {
            case '万千':
                return $this->lhResult($code[0], $code[1], $plan);
            case '万百':
                return $this->lhResult($code[0], $code[2], $plan);
            case '万十':
                return $this->lhResult($code[0], $code[3], $plan);
            case '万个':
                return $this->lhResult($code[0], $code[4], $plan);
            case '千百':
                return $this->lhResult($code[1], $code[2], $plan);
            case '千十':
                return $this->lhResult($code[1], $code[3], $plan);
            case '千个':
                return $this->lhResult($code[1], $code[4], $plan);
            case '百十':
                return $this->lhResult($code[2], $code[3], $plan);
            case '百个':
                return $this->lhResult($code[2], $code[4], $plan);
            case '十个':
                return $this->lhResult($code[3], $code[4], $plan);
        }
    }

    /**龙虎大小单双结果 */
    public function lhResult($code1, $code2, $plan)
    {
        $res = [];
        $sum = $code1 + $code2;
        if ($code1 > $code2) array_push($res, '龙');
        if ($code1 < $code2) array_push($res, '虎');
        if ($code1 == $code2) array_push($res, '和');
        if ($sum % 2 == 1) array_push($res, '单');
        if ($sum % 2 == 0) array_push($res, '双');
        if ($sum < 5) array_push($res, '小');
        if ($sum > 4) array_push($res, '大');
        $html = '';
        $plan = explode(',', $plan);
        foreach ($plan as $key => $value) {
            $html .= in_array($value, $res) ? "<span class='red'>" . $value . "</span>," : $value . ',';
        }
        return trim($html, ',');
    }
}
