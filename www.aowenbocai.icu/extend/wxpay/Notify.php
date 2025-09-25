<?php

namespace wxpay;

use app\common\model\CouponsHistory;
use think\Loader;
use core\Setting;
use app\common\model\Recharge;
use app\common\model\MoneyHistory;
use app\common\model\FlowerHistory;
use app\agents\model\User;

require_once 'lib/WxPayException.php';
Loader::import('wxpay.lib.WxPayApi');
Loader::import('wxpay.lib.WxPayNotify');

/**
 * 异步通知处理类
*
*
* ----------------- 求职 ------------------
* 姓名: zhangchaojie      邮箱: zhangchaojie_php@qq.com  应届生
* 期望职位: PHP初级工程师   地点: 深圳(其他城市亦可)
* 能力:
*     1.熟悉小程序开发, 前后端皆可
*     2.后端, PHP基础知识扎实, 熟悉ThinkPHP5框架, 用TP5做过CMS, 商城, API接口
*     3.MySQL, Linux都在进行进一步学习
*/
class Notify extends \WxPayNotify
{
    /**
     * 此为主函数, 即处理自己业务的函数, 重写后, 框架会自动调用
     *
     * @param array $data 微信传递过来的参数数组
     * @param string $msg 错误信息, 用于记录日志
     */
    public function NotifyProcess($data, &$msg)
    {
        // 一下均为实例代码
        // 1.校检参数
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }

        // 2.微信服务器查询订单，判断订单真实性(可不需要)
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }

        // 3.去本地服务器检查订单状态(强烈建议需要)
        $order = $this->getOrder($data);
        if(empty($order)) {
            $msg = '本地订单已改变或者不存在';
            return false;
        }

        // 4.检查订单状态
        // if($this->checkOrderStatus($order)) {
        //     // 如果订单处理过, 则直接返回true
        //     return true;
        // }

        // 订单状态未修改情况下, 进行处理业务
        $result = $this->processOrder($order, $data);
        if(!$result) {
            $msg = '订单处理失败';
            return false;
        }

        return true;
    }

    /**
     * 处理核心业务
     * @param  array $order 订单信息
     * @param  array $data  通知数组
     * @return Bollean
     */
    public function processOrder($order, $data)
    {
        // 进行核心业务处理, 如更新状态, 发送通知等等
        // 处理成功, 返回true, 处理失败, 返回false
        $result = (new Recharge)->save(['statuss'=>1], ['id' => $order['id']]);
        if($result){
            $this->setMoneyHistory($order);
        }
        return $result;
    }


    // 去微信服务器查询是否有此订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    // 去本地服务器查询订单信息
    public function getOrder($data)
    {
        // 可根据商户订单号进行查询
        // 例如:
        $order = (new Recharge)->get(['order' => $data['out_trade_no'], 'statuss' => 0]);
        if($order){
            $order = $order->toArray();
        }
        return $order;
    }

    /**
     * 检查order状态, 是否已经做过修改, 避免重复修改
     * 原因: 可能由于业务处理较慢, 还未等回复微信服务器, 同一订单的另一个通知已到达,
     *      为了避免重复修改订单, 需要对状态进行检查
     *
     * @return Bollean
     */
    public function checkOrderStatus($order)
    {
        // 检查还未修改, 则返回true, 检查已经修改过了, 则返回false
        return $order['statuss'] == 1 ? false : true;
    }


    public function setMoneyHistory($order)
    {
        (new Recharge())->diamonds($order['money'], $order['userid'], $order['type']);
//        if ($order['type'] == 1) {
//            $this->flower($order);
//        } elseif ($order['type'] == 2) {
//            $this->coupons($order);
//        }
        if(session('?weipay')){
            session('weipay', 'used');
        }
    }

    /**
     * 充值鲜花
     * @return Boolean true表示业务处理成功 false表示处理失败
     */
    public function flower($order)
    {
        /**获取系统设置充值比例及对应的额外奖励 */
        $setting = Setting::get(['recharge_award', 'other_recharge_award', 'flower_money']);
        $other = json_decode($setting['other_recharge_award'], true);
        $total_money = $order['money'];
        /**充值数额对应的鲜花写入明细 */
        $flower_money = $setting['flower_money'] ? $setting['flower_money'] : 1;
        $money = $total_money * $flower_money;
        $data = [
            'type' => 1,
            'userid' => $order['userid'],
            'money' => $money,
        ];
        (new FlowerHistory)->write($data);
        (new User)->rechargeRebate($order['userid'], $money);
        /**============结束===========*/

        /**充值数额对应的额外奖励金币写入资金明细。 有则写，无则不写 */
        $other_money = 0;
        foreach($other as $value){
            if($value['money'] == $order['money']){
                $other_money = $order['money'] * $value['award'];
                break;
            }
        }
        if($other_money){
            $data = [
                'type' => 4,
                'userid' => $order['userid'],
                'money' => $other_money,
                'ext_name' => 'pay',
                'remark' => '充值赠送'
            ];
            (new MoneyHistory)->write($data);
        }
        /**============结束===========*/
        return true;
    }

    /**
     * 充值点券
     * @return Boolean true表示业务处理成功 false表示处理失败
     */
    public function coupons($order)
    {
        /**获取系统设置充值比例及对应的额外奖励 */
        $setting = Setting::get(['coupons_recharge']);
        $coupons_list = json_decode($setting['coupons_recharge'], true);
        $coupons = 0;
        foreach ($coupons_list as $value) {
            if($value['money'] == $order['money']){
                $coupons = $value['coupons'];
                break;
            }
        }
        /**充值数额对应的鲜花写入明细 */
        $data = [
            'type' => 1,
            'userid' => $order['userid'],
            'money' => $coupons,
        ];
        (new CouponsHistory())->write($data);
        (new User)->rechargeRebate($order['userid'], $order['money']);
        /**============结束===========*/

        return true;
    }

    
}

