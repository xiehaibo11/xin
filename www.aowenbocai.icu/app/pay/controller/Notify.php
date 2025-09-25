<?php

namespace app\pay\controller;

use app\common\controller\NotifyHandler;
use app\common\model\Recharge;

/**
* 通知处理控制器
*
* 完善getOrder, 获取订单信息, 注意必须数组必须包含out_trade_no与total_amount
* 完善checkOrderStatus, 返回订单状态, 按要求返回布尔值
* 完善handle, 进行业务处理, 按要求返回布尔值
*
* 请求地址为index, NotifyHandler会自动调用以上三个方法
*/
class Notify extends NotifyHandler
{
    protected $params; // 订单信息

    public function index()
    {
        parent::init();
    }

    /**
     * 获取订单信息, 必须包含订单号和订单金额
     *
     * @return string $params['out_trade_no'] 商户订单
     * @return float  $params['total_amount'] 订单金额
     */
    public function getOrder()
    {
        $out_trade_no = $_POST['out_trade_no'];
        $order = (new Recharge) -> get(['order' => $out_trade_no]);
        $params = [];
        if($order) {
            $order = $order->toArray();
            $params = [
                'out_trade_no' => $order['order'],
                'total_amount' => $order['money'],
                'status'       => $order['statuss'],
                'id'           => $order['id'],
                'userid'           => $order['userid'],
                'type' => $order['type']
            ];
        }
        
        $this->params = $params;
    }

    /**
     * 检查订单状态
     *
     * @return Boolean true表示已经处理过 false表示未处理过
     */
    public function checkOrderStatus()
    {
        if($this->params['status'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 业务处理
     * @return Boolean true表示业务处理成功 false表示处理失败
     */
    public function handle()
    {
        $result = (new Recharge)->save(['statuss'=>1], ['id' => $this->params['id']]);
        if($result) {
            (new Recharge())->diamonds($this->params['total_amount'], $this->params['userid'], $this->params['type']);
            return true;
        } else {
            return false;
        }
    }

}