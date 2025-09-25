<?php
namespace app\web\controller;

use app\admin\model\ThirdPay;
use app\index\controller\Base;
use app\common\model\Recharge;
use core\Setting;
use think\Session;
use think\Request;

class Pay extends UserBase
{
    /**
     * 订单确认页面
     * @param num int 金额
     * @param type int 类型  1 为充值鲜花  2位充值点券
     */
    public function index()
    {
        /**判断金额是否在后台配置金额中 */
        $pay_info = $this->getPayInfo();
        $pay_info['rechargeList'] = collection($pay_info['rechargeList']);
        $pay_info['payAlipayUrl'] = collection($pay_info['payAlipayUrl']);
        $pay_info['payWeiUrl'] = collection($pay_info['payWeiUrl']);
        $pay_info['paysetting'] = collection($pay_info['paysetting']);
        $pay_info['award'] = collection($pay_info['award']);
        $pay_info['bank_config'] = collection($pay_info['bank_config']);
        return $this->fetch('index', $pay_info);
    }

    //获取充值信息
     public function getPayInfo()
    {
        /**判断金额是否在后台配置金额中 */
        $setting = Setting::get(['diamonds_recharge','other_pay', 'sao_pay', 'zfb_recharge_ewm', 'wx_recharge_ewm','recharge_award','recharge_info', 'recharge_send_times', 'game_unit', 'lottery_unit', 'bank_open', 'bank_config']);
        $diamonds_recharge = $setting['diamonds_recharge'] ? json_decode($setting['diamonds_recharge'], true) : [];
        $bank_config = $setting['bank_config'] ? json_decode($setting['bank_config'], true) : [];
        $award = [];
        foreach ($diamonds_recharge as $key => $value) {
            $award[$value['money']] = round($value['award']/$value['lottery_money'], 4);
        }

        $select_other_pay = 0;
        if($setting['other_pay']){
            $third_pay_model = new ThirdPay();
            $other_pay = json_decode($setting['other_pay'],true);
            foreach ($other_pay as $key => $value) {
                if ($value['name'] == 'third_pay') {
                    foreach ($value['value'] as &$row) {
                        if ($key == 0) $select_other_pay = $row['id'];
                        $info = $third_pay_model->find($row['id']);
                        if ($info and $info['type_param']) {
                            $type_data = json_decode($info['type_param'], true);
                            $type_param = [];
                            foreach ($type_data as $key2 => $val) {
                                array_push($type_param, [
                                    'id' => $key2,
                                    'title' => $val,
                                ]);
                            }
                            $row['children'] = $type_param;
                        }
                    }
                }
                $setting[$value['name']] = $value['value'];
            }
        }
        if (isset($setting['other_wx']) and $setting['other_wx']) $select_other_pay = 'wx';
        if (isset($setting['other_alipay']) and $setting['other_alipay']) $select_other_pay = 'alipay';
        $setting['third_pay'] = (isset($setting['third_pay']) and $setting['third_pay']) ? collection($setting['third_pay']) : collection([]);
        if($setting['sao_pay']){
            $sao_pay = json_decode($setting['sao_pay'],true);
            foreach ($sao_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];
                $setting[$value['name']."_url"] = $value['name'] == 'pay_wx' ? (isset($setting['wx_recharge_ewm']) ? json_decode($setting['wx_recharge_ewm'],true) : '') : (isset($setting['zfb_recharge_ewm']) ? json_decode($setting['zfb_recharge_ewm'],true) : '');
            }
        }
        $user_recharge_times = (new Recharge())->where('userid', $this->user['id'])->where('statuss', 1)->whereTime('create_time', '>=', date('Y-m-d', time()))->count('id');
        $has_recharge_times = ($setting['recharge_send_times'] - $user_recharge_times) > 0 ? ($setting['recharge_send_times'] - $user_recharge_times) : 0;
        return ['paysetting' => $setting, 'award' => $award,'rechargeList' => $diamonds_recharge,
        'payAlipayUrl' => json_decode($setting['zfb_recharge_ewm'],true), 'recharge_send_times' => $setting['recharge_send_times'], 'has_recharge_times' => $has_recharge_times,
        'payWeiUrl' => json_decode($setting['wx_recharge_ewm'],true), 'select_other_pay' => $select_other_pay, 'third_pay' => $setting['third_pay'], 'bank_open' => $setting['bank_open'], 'bank_config' => $bank_config];
    }

    /**
     * 获取三方支付 - 支付类型
    */
    public function get_third_pay_type($id)
    {
        $info = (new ThirdPay())->find($id);
        if (!$info) return json(['err' => 1]);
        if (!$info['type_param']) return json(['err' => 1]);
        $type_param = json_decode($info['type_param'], true);
        return json(['err' => 0, 'data' => $type_param]);
    }

}
