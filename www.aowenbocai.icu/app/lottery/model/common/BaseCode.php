<?php
namespace app\lottery\model\common;

use app\common\controller\LotteryCommon;
use think\Model;
use app\common\model\MoneyHistory;
use app\lottery\model\LotteryCom;
use core\Setting;

class BaseCode extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $ext_name = '';
    protected $name = 'lottery_code';//表名
    protected $insert = ['ext_name'];

    // 定义全局的查询范围
    protected function base($query)
    {
        if ($this->ext_name) {
            $query->where('ext_name', $this->ext_name);
        }
    }

    public function setExtNameAttr($value)
    {
        return $this->ext_name;
    }

      /**
     * 获取器 - 分拆开奖号码
     */
    public function getCodeArrayAttr($value, $data)
    {
        $code_array = explode(',', $data['code']);
        return $code_array;
    }
    /**
     *  获取当前期数和开奖号码
     */
    public function getNowCode()
    {
        $code = $this->order('expect DESC')->find();
        return $code;
    }

    /**派奖 */
    public function setPrize($data = [])
    {
        if(empty($data)){
            return '没有开奖号码';
        }
        $data = $this->array_sort($data,'expect','asc','yes');
        $expectModel = (LotteryCommon::getModel($this->ext_name, 'expect'));

        $join = (LotteryCommon::getModel($this->ext_name, 'join'));
        $common = LotteryCommon::getCommon($this->ext_name);
        $setting = json_decode((LotteryCommon::getSetting($this->ext_name))->getValue(LotteryCommon::getSettingValue($this->ext_name, 'bouns')), true);
        $setting_config = json_decode((LotteryCommon::getSetting($this->ext_name))->getValue(LotteryCommon::getSettingValue($this->ext_name, 'config')), true);
        $allBouns = 0;
        $allMoney = 0;
        $buyModel = (LotteryCommon::getModel($this->ext_name, 'buy'));
        $moneyHis = new MoneyHistory;
        $settingCoin = 1;//彩金及金豆之间的转化比例
        foreach ($data as $top_value) {
            $expectList = (new LotteryCom())->getPrizeList($this->ext_name, $top_value['expect']);
            if(empty($expectList)){
                continue;
            }
            $valueRes = $common->prizeCom($top_value['code'], $expectList, $setting, $setting_config);//处理某一期所有投注的派奖奖金数据
            foreach ($valueRes as $key => $value) {
                /**判断是不是追号 */
                $expectBuyid = $expectModel->where('buy_id', $value['buy_id'])->select()->append(['buy'])->toArray();
                $is_chase = count($expectBuyid) > 1 ? 1 : 0;
                if($is_chase){
                    $allMultiple = array_sum(array_column($expectBuyid, 'multiple'));
                    $buyMoneyT = intval($value['total'] / $allMultiple) * $value['multiple'];
                }
                $now_total = isset($buyMoneyT) ? $buyMoneyT : $value['total'];
                $allMoney += $now_total;
                /**合买派奖 */
                if($value['is_join'] == 1){
                    $buy_id[] = $value['buy_id'];
                    $joinRes = $this->setJoinPrize($value, $is_chase, $expectBuyid, $settingCoin, $value['multiple']);
                    /**判断合买派奖是否成功 */
                    if(!$joinRes){
                        continue;
                    }
                }
                /**不合买 中奖停止 */
                if(!$value['is_join'] && $is_chase && $value['is_stop'] == 1 && $value['bouns'] > 0){
                    $chase = (new LotteryCom)->setStopChasePrize($value, $expectBuyid, $settingCoin);
                    if(!empty($chase)){
                        $expectModel->saveAll($chase);
                    }
                }
                //不合买 更新奖金
                if(!$value['is_join']){
                    $join_info = $join->where(['userid' => $value['userid'], 'buy_id' => $value['buy_id']])->find();
                    (new LotteryCom)->changeUserRechargeMoney($value, $expectBuyid, $value['multiple']);
                    if ($value['bouns'] > 0) {
                        $join->where(['userid' => $value['userid'], 'buy_id' => $value['buy_id']])->update([
                            'bonus' =>  $join_info['bonus'] + $value['bouns'],
                            'status' => 1
                        ]);
                    } else {
                        if ($join_info['status'] == 0) {
                            $join->where(['userid' => $value['userid'], 'buy_id' => $value['buy_id']])->update([
                                'status' => 2
                            ]);
                        }
                    }
                }
                $allBouns += $value['bouns'];
                $buyStatus =  $value['bouns'] > 0 ? 1 : 2;
                $expectSave[$key] = ['id' => $value['id'], 'status' => 2, 'bonus' => $value['bouns']];
                $buyRes = $buyModel->field('status')->find($value['buy_id'])['status'];
                if($buyRes != $buyStatus && $buyRes != 1){
                    $buySave[$key] = ['id' => $value['buy_id'], 'status' => $buyStatus];
                }
                if($value['bouns'] && !$value['is_join']){
                    $moneyHis->write(['userid' => $value['userid'], 'money' => $value['bouns'] * $settingCoin, 'ext_name' => $this->ext_name, 'type' => 1, 'remark' => '中奖:'.$value['lottery_id']]);
                }
                if (!$value['is_join']) {//返点
                    (new LotteryCom)->rebate($value['play_num'], $value['userid'], 1, $value['multiple'], LotteryCommon::getCpType($this->ext_name), $value['lottery_id']);
                }
            }
            isset($expectSave) && !empty($expectSave) && $expectModel->saveAll($expectSave);
            isset($buySave) && !empty($buySave) && $buyModel->saveAll($buySave);
        }
        $allExpect = array_column($data, 'expect');
        $expectAll = "<span  style='word-wrap:break-word;'>[<b>".implode('</b>],[<b>',$allExpect)."</b>]</span>";
        if(!isset($valueRes)){
            return '<br>派奖期号：<br>'.$expectAll.'<br>未有可进行派奖投注记录<br>';
        }
        if(isset($buy_id) && !empty($buy_id)){//系统保底奖金
            $systemMoney = $join->where('buy_id', 'in', array_unique($buy_id))->where('userid', 1)->sum('money');
        }
        $systemMoney = isset($systemMoney) ? $systemMoney : 0;
        return '<br>派奖期号：<br>'.$expectAll.'<br> <b style="color:red">共中奖'.$allBouns."彩金</br><b>总投注金额:".$allMoney."彩金</b></br>系统保底金额：$systemMoney 彩金";
    }

    /**合买派奖 */
    public function setJoinPrize($data, $is_chase, $expectBuyid, $settingCoin, $dq_multiple)
    {
        $setting = (new Setting)->get(['openBd', 'Bdpercent']);
        $setting['settingCoin'] = $settingCoin;
        $join = (LotteryCommon::getModel($this->ext_name, 'join'));
        $list = $join ->where('buy_id', $data['buy_id'])->select()->toArray();
        $lotteryCom = new LotteryCom;
        $res = $lotteryCom->joinPrize($data, $list, $setting, $is_chase ,$expectBuyid, LotteryCommon::getCpType($this->ext_name));
        $expect = (LotteryCommon::getModel($this->ext_name, 'expect'));
        if(isset($res['err'])){
            $expect->save(['status' => 0],['buy_id' => $data['buy_id']]);
            return false;
        }
        $has_bonus = 0;
        foreach ($res['update'] as $value) {
            $join_info = $join->find($value['id']);
            if ($join_info['userid'] != 1) {
                $lotteryCom->joinChangeUserRechargeMoney($data, $list, $expectBuyid, $dq_multiple);
            }
            if ($value['bonus'] > 0) {
                $has_bonus = $value['bonus'];
                $join->where('id', $value['id'])->update([
                    'bonus' =>  $value['bonus'],
                    'status' => 1
                ]);
            } else {
                if ($join_info['status'] == 0) {
                    $join->where('id', $value['id'])->update([
                        'status' => 2
                    ]);
                }
            }
        }
        if(!empty($res['bd'])){//系统保底
            $has_info = $join->where('buy_id', $res['bd']['buy_id'])->where('userid', $res['bd']['userid'])->find();
            if (!$has_info) {
                $join->insert($res['bd']);
            } else {
                $has_info->save(['bonus' => $res['bd']['bonus']]);
            }
        }
        if($is_chase && $data['is_stop'] && $has_bonus){//判断中奖停止
            $stopRetrun = $lotteryCom->setStopHmChasePrize($data, $list, $expectBuyid, $settingCoin, $this->ext_name);
            $expect->saveAll($stopRetrun);
        }
        return true;

    }

    private function array_sort($arr,$keys,$orderby='asc',$key='no'){
        $keysvalue = $new_array = array();
        foreach($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($orderby=='asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach($keysvalue as $k=>$v){
            if($key=='yes'){
                $new_array[$k] = $arr[$k];
            }else{
                $new_array[] = $arr[$k];
            }
        }
        return $new_array;
    }
}