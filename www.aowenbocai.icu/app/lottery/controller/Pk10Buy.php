<?php
namespace app\lottery\controller;

use app\index\controller\Base;
use app\lottery\model\pk10\PluginPk10Code;
use app\lottery\model\pk10\PluginPk10Buy;
use app\lottery\model\pk10\PluginPk10Join;
use app\lottery\controller\Lottery;
use core\Setting;

class Pk10Buy extends Base
{
    /**PK10发单,添加数据 */
    public function add($data, $user)
    {
        $lottery = new Lottery;
        $_issue = $lottery->getExpect('pk10')['data']['expect'];
        if (!is_array($data['play_num'])) {
            $data['play_num'] = json_decode($data['play_num'], true);//投注号码
        }
        if (!is_array($data['expect'])) {
            $data['expect'] = json_decode($data['expect'], true);//期数
        }
        /**验证最小期号是否正确 */
        $expectList = array_column($data['expect'], 'expect');
        $min_issue = min($expectList);
        if ($_issue != $min_issue) {
            return [ 'err' => 2, 'msg' => '下注失败，期号错误，或者本期已经截止!'];
        }

        /**验证投注内容 */
        if(empty($data['play_num'])){
            return ['err' => 3, 'msg' => '投注内容不能为空，请确认后再提交'];
        }
        // 计算注数 - start
        $count = 0;
        $plan = [];
        foreach ($data['play_num'] as $value) {
            if($value['type'] != 'WX' ){
                $value['num']= explode('|', $value['num']);
                foreach ($value['num'] as $key => &$val) {
                    $val = explode(',', $val);
                }
            }else{
                $value['num'] = explode(',', $value['num']);
            }
            switch ($value['type']) {
                case 'WX':
                    $count += $this->WZCount($value['num']);
                    break;
                case 'GJ':
                    $count += count($value['num'][0]);
                    break;
                case 'YJ' :
                    $count += $this->betCount(1,$value['num']);
                    break;
                case 'YJ.2':
                    $count += $this->betCount(1,$value['num']);
                    break;
                case 'JJ':
                    $count += $this->betCount(2,$value['num']);
                    break;
                case 'JJ.2':
                    $count += $this->betCount(2,$value['num']);
                    break;
                case 'QS':
                    $count += $this->betCount(3,$value['num']);
                    break;
                case 'QS.2':
                    $count += $this->betCount(3,$value['num']);
                    break;
                case 'QW':
                    $count += $this->betCount(4,$value['num']);
                    break;
                case 'QL':
                    $count += $this->betCount(5,$value['num']);
                    break;
                case 'QQ':
                    $count += $this->betCount(6,$value['num']);
                    break;
                case 'QB':
                    $count += $this->betCount(7,$value['num']);
                    break;
                case 'QJ':
                    $count += $this->betCount(8,$value['num']);
                    break;
                case 'QSHI':
                    $count += $this->betCount(9,$value['num']);
                    break;
            }
            $plan[$value['type']] = $value['num'];
        }
        /**期号倍数，计算总金额*/
        $allMul = array_sum(array_column($data['expect'], 'multiple'));
        $total = $count * $allMul * 2; //总金币数
        if ($total <= 0) {
            return [ 'err' => 4, 'msg' => '下注失败，投注错误！'];
        }
        $data_db = [
            'play_num' => json_encode($plan),
            'userid' => $user['id'],
            'total_money' => $total,
            'is_join' => $data['is_hemai'],
            'is_stop' => $data['is_stop'],
            'declaration' => $data['declaration'],
            'show' => $data['show'],
            'end_time' => date('Y-m-d H:i:s', $lottery->issueToTime('pk10', $min_issue)),
            'expect' => $data['expect'],
            'min_expect' => $min_issue,
            'lottery_id' => 'PK10|'.$user['id'].str_replace('.','',sprintf("%.4f",microtime(true)))
        ];
        // 判断是否合买
        $join = $total;
        if (!empty($data['is_hemai'])) {
            $data_db['is_join'] = 1;
            $data_db['assure_money'] = empty($data['bd_money']) ? 0 : $data['bd_money']; // 保底
            $data_db['gain'] = empty($data['gain']) ? 0 : $data['gain']; // 提成
            $join = $data['money'];
            
            if ($data_db['gain'] > 10) {
                return ['err' => 5, 'msg' => '下注失败，提成(%)错误！'];
            }

            if ($join < ceil($total * 0.1) || $join > $total) {
                return ['err' => 5,'msg' => '下注失败，参与份数错误！'];
            }
                
            if ($data_db['assure_money'] > $total - $join || $data_db['assure_money'] < 0) {
                return ['err' => 5,'msg' => '下注失败，保底份数错误！'];
            }
            $buyMoney = $data_db['assure_money'] + $join;
        }
        $data_db['buyMoney'] = isset($buyMoney) ? $buyMoney : $total;
        $moneyHis = $data_db['buyMoney'];
        if($moneyHis > $user['money']){
            return ['err' => 6, 'msg' => '余额不足，请先充值'];
        }
        $data_db['money'] =  isset($join) ? $join : $total;
        return (new PluginPk10Buy)->buy($data_db, $moneyHis, count($expectList));
    }
/*
     * JS移植
     **/
    public function betCount($n, $data)
    { // 组数计算
        $this->n = $n;
        $this->data = $data;//combination($data, $n);

        $count = $this->bc();        
        return $count;
    }

    /*
     * JS移植
     **/
    private function bt($l, $arr)
    {
        $count = 0;
        $n = $this->n;
        $data = $this->data;
        for ($i = 0; $i < count($data[$l]); $i++) {
            $code = $data[$l][$i];
            if (in_array($code, $arr)) continue;
            $_arr = array_merge($arr, [$code]);
            if ($l < $n) {
                $count += $this->bt($l + 1, $_arr);
            } else {
                $count ++;
            }
        }
        return $count;
    }

    /*
     * JS移植
     **/
    private function bc()
    {
        if (!empty($this->data)) return $this->bt(0, []);    
        
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
}