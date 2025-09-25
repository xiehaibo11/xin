<?php
namespace app\index\controller;

use app\index\controller\Base;
use app\common\model\Recharge;
use core\Setting;
use think\Session;
use think\Request;

class Pay extends Base
{
    /**
     * 订单确认页面
     * @param num int 金额
     * @param type int 类型  1 为充值鲜花  2位充值点券
     */
    public function index()
    {
        /**判断金额是否在后台配置金额中 */
        $setting = Setting::get(['diamonds_recharge','other_pay', 'sao_pay', 'zfb_recharge_ewm', 'wx_recharge_ewm','flower_money','recharge_award','recharge_info', 'recharge_send_times']);
        $diamonds_recharge = json_decode($setting['diamonds_recharge'], true);
        $award = [];
        foreach ($diamonds_recharge as $key => $value) {
            $award[$value['money']] = round($value['award']/$value['coin'], 4);
        }

        if($setting['other_pay']){
            $other_pay = json_decode($setting['other_pay'],true);
            foreach ($other_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];
            }
        }
        if($setting['sao_pay']){
            $sao_pay = json_decode($setting['sao_pay'],true);
            foreach ($sao_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];

                $setting[$value['name']."_url"] = $value['name'] == 'pay_wx' ? (isset($setting['wx_recharge_ewm']) ? json_decode($setting['wx_recharge_ewm'],true) : '') : (isset($setting['zfb_recharge_ewm']) ? json_decode($setting['zfb_recharge_ewm'],true) : '');
            }
        }
        $user_recharge_times = (new Recharge())->whereTime('create_time', '>=', date('Y-m-d', time()))->count('id');
        $has_recharge_times = ($setting['recharge_send_times'] - $user_recharge_times) > 0 ? ($setting['recharge_send_times'] - $user_recharge_times) : 0;
        return $this->fetch('index',['paysetting' => collection($setting), 'award' => collection($award),'rechargeList' => $setting['diamonds_recharge'],
        'payAlipayUrl' => collection($setting['pay_alipay_url']), 'recharge_send_times' => $setting['recharge_send_times'], 'has_recharge_times' => $has_recharge_times,
        'payWeiUrl' => collection($setting['pay_wx_url'])]);
    }

    //获取充值信息
     public function getPayInfo()
    {
        /**判断金额是否在后台配置金额中 */
        $setting = Setting::get(['diamonds_recharge','other_pay', 'sao_pay', 'zfb_recharge_ewm', 'wx_recharge_ewm','flower_money','recharge_award','recharge_info', 'recharge_send_times']);
        $diamonds_recharge = json_decode($setting['diamonds_recharge'], true);
        $award = [];
        foreach ($diamonds_recharge as $key => $value) {
            $award[$value['money']] = round($value['award']/$value['coin'], 4);
        }

        if($setting['other_pay']){
            $other_pay = json_decode($setting['other_pay'],true);
            foreach ($other_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];
            }
        }
        if($setting['sao_pay']){
            $sao_pay = json_decode($setting['sao_pay'],true);
            foreach ($sao_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];

                $setting[$value['name']."_url"] = $value['name'] == 'pay_wx' ? (isset($setting['wx_recharge_ewm']) ? json_decode($setting['wx_recharge_ewm'],true) : '') : (isset($setting['zfb_recharge_ewm']) ? json_decode($setting['zfb_recharge_ewm'],true) : '');
            }
        }
        $user_recharge_times = (new Recharge())->whereTime('create_time', '>=', date('Y-m-d', time()))->count('id');
        $has_recharge_times = ($setting['recharge_send_times'] - $user_recharge_times) > 0 ? ($setting['recharge_send_times'] - $user_recharge_times) : 0;
        return ['paysetting' => collection($setting), 'award' => collection($award),'rechargeList' => $diamonds_recharge,
        'payAlipayUrl' => collection($setting['pay_alipay_url']), 'recharge_send_times' => $setting['recharge_send_times'], 'has_recharge_times' => $has_recharge_times,
        'payWeiUrl' => collection($setting['pay_wx_url'])];
    }

}
