<?php
namespace app\index\controller;

use core\Setting;

class Recharge extends Base
{

    /** 获取支付方式*/
    public function getPayWay($money)
    {
        $setting = Setting::get(['other_pay', 'sao_pay', 'zfb_recharge_ewm', 'wx_recharge_ewm']);
        $setting['other_pay'] = json_decode($setting['other_pay'],true);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
        $setting['sao_pay'] = json_decode($setting['sao_pay'],true); 
        $setting['zfb_recharge_ewm'] = json_decode($setting['zfb_recharge_ewm'],true);
        $setting['wx_recharge_ewm'] = json_decode($setting['wx_recharge_ewm'],true);
        foreach ($setting['other_pay'] as $value) {
            if($value['value'] == 1){
                $way[] = $value['name'];
            }
        }   
        foreach ($setting['sao_pay'] as $value) {
            if($value['value'] == 1){
                $way[] = $value['name'];
                if ($value['name'] == 'pay_wx') {
                    $url[$value['name']] = $setting['wx_recharge_ewm'][$money];
                } else if ($value['name'] == 'pay_alipay') {
                    $url[$value['name']] = $setting['zfb_recharge_ewm'][$money];
                }
            }
        }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
        if(!isset($way)){
            return json(['err' => 1,'msg' => '暂未开启任何支付方式']);
        }
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $pay_wx = in_array('pay_wx', $way);
        $other_wx = in_array('other_wx', $way);
        if(strpos($user_agent,'MicroMessenger') !== false && ($pay_wx || $other_wx)){
            $default = $pay_wx ? 'pay_wx' : 'other_wx';
        }
        $default = isset($default) ? $default : $way[0];
        $isWei = strpos($user_agent,'MicroMessenger') !== false ? 1 : 0;
        return json(['err' => 0,'msg' => '', 'data' => ['ways' => $way,'default' => $default,'isWei' => $isWei, 'url' => $_SERVER['HTTP_HOST']], 'img_url' => $url]);
    }
    
     /** 获取支付方式 --- 新*/
    public function getPayWayNew()
    {
        $setting = Setting::get(['other_pay', 'sao_pay','recharge_info']);
        $pay_way = [];
        $pay_info = [];
        foreach($setting as &$value){
            $value = json_decode($value, true);
        }
        $info = $setting['recharge_info'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $isWei = strpos($user_agent,'MicroMessenger') !== false ? 1 : 0;
        foreach ($setting['other_pay'] as $value) {
            if($value['value'] == 1){
                $pay_way[] = $value['name'];
                if($isWei){
                    $default = 'wei';
                }
            }
        }

        foreach ($setting['sao_pay'] as $value) {
            if($value['value'] == 1){
                $pay_way[] = $value['name'];
                $pay_info[$value['name']] = $value['url'];
            }
        }
        $default = isset($default) ? $default : $pay_way[0];
        return json(['err' => 0,'msg' => '', 'data' => ['ways' => $pay_way,'default' => $default,'isWei' => $isWei, 'url' => $_SERVER['HTTP_HOST'],'sao' => $pay_info,'info' => $info]]);
    }

    /**获取兑换信息 */
    public function getMoneyConfig()
    {
        $setting = Setting::get(['getmoney_times', 'getmoney_low', 'getmoney_startime', 'getmoney_endtime', 'getmoney_info']);
        return json(['err' => 0, 'data' => $setting]);
    }
    /**获取钻石充值列表 */
    public function getRechargeList()
    {
        $setting = Setting::get(['diamonds_recharge', 'recharge_award']);
        $other = json_decode($setting['diamonds_recharge'], true);
        if(!$other){
         return json(['err' => 1,'data' => $other]);
        }
        array_multisort($other);
//        foreach($other as &$value){
//            $value['coin'] = $value['diamonds'] * $setting['recharge_award'];
//        }
        return json(['err' => 0,'data' => $other]);
    }

    /**获取点券充值列表 */
    public function getCouponsRechargeList()
    {
        $setting = Setting::get([ 'coupons_recharge']);
        $recharge_data = json_decode($setting['coupons_recharge'], true);
        return json(['err' => 0,'data' => $recharge_data]);
    }
}
