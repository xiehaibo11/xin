<?php
namespace app\pay\controller;

use app\index\controller\Base;
use app\common\model\Recharge;
use app\admin\model\User as AUser;

class api extends Base
{
    /**
     * 充值页面
     */
    public function index($order_id = '')
    {
        $order = (new Recharge)->where(['order' => $order_id])->find();
        if(!$order) return false;
        if($order['statuss'] != 0) return false;
        $result = $order->save(['statuss'=>1]);
        if($result) {
            $res = (new Recharge())->diamonds($order['money'], $order['userid'], $order['type']);
            if ($res) {
                (new AUser)->rechargeRebate($order['userid'], $order['money']);
            }
        } else {
            return false;
        }
    }

}
