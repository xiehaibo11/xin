<?php
namespace app\pay\controller;

use app\admin\model\ThirdPay;
use app\index\controller\Base;
use app\common\model\Recharge;
use core\Setting;
use think\Config;
use think\Controller;


class PayNotify extends Controller
{
    /**
     * 订单确认页面
     * @param $out_trade_no //订单id
     * @param $token   //验证
     */
    public function index($out_trade_no, $signature)
    {
        $token = Config::get('baseConfig.authorization_token');
        $check_signature = md5($token . substr($out_trade_no, 2, 4));
        if ($check_signature != $signature) return ['err' => 1, 'msg' => 'TOKEN不正确'];
        $order = (new Recharge)->where('order', $out_trade_no)->find();
        if($order and $order['statuss'] == 0) {
            $result = $order->save(['statuss' => 1]);
            if ($result) {
                (new Recharge())->diamonds($order['money'], $order['userid'], $order['type']);
                return ['err' => 0];
            } else {
                return ['err' => 1, 'msg' => '数据库执行失败'];
            }
        } else if($order and $order['statuss'] == 1) {
            return ['err' => 0];
        } else {
            return ['err' => 1, 'msg' => '订单不存在'];
        }
    }

    /**
     * 订单查询状态
     * @param $out_trade_no //订单id
     */
    public function get_status($out_trade_no)
    {
        $order = (new Recharge)->where('order', $out_trade_no)->find();
        if($order and $order['statuss'] == 0) {
            return ['err' => 2, 'msg' => '订单还未完成'];
        } else if($order and $order['statuss'] == 1) {
            return ['err' => 0];
        } else {
            return ['err' => 1, 'msg' => '订单不存在'];
        }
    }
}
