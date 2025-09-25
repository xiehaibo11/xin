<?php

namespace app\index\controller;

use app\common\model\GameMoneyHistory;
use app\index\model\Shop as Ashop;

class Shop extends Base
{

    public function __construct()
    {
        $this->baseModel = new Ashop();//当前模型
        $this->param = request()->param();
        $this->post = request()->post();
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch('index', ['title' => '兑换商城']);
    }

    /**
     * 获取列表
     * @return array
     */
    public function getList()
    {
        $pageSize = 10;
        $list = $this->baseModel->where('status', 1)->order(['create_time' => 'desc'])->paginate($pageSize)->each(function($item, $key){
            $item['has_num'] = $item['has_num'];
            return $item;
        });
        $data_list = $list->toArray();
        $data_list['code'] = 1;
        return $data_list;
    }

    /**
     * 兑换处理
     * @return array
     */
    public function convert()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $res = $this->baseModel->checkNum($this->user, $data['id'], $data['num']);
            if (!$res['code']) {
                return ['code' => 0, 'msg' => $res['msg']];
            }
            return ['code' => 1, 'msg' => '兑换成功'];
        }
    }

    /**
     * 确认订单
     * @return array
     */
    public function orderDo()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $user = $this->user;
            $res = $this->baseModel->doConvert($user, $data);
            if (!$res['code']) {
                return ['code' => 0, 'msg' => $res['msg']];
            }
            return ['code' => 1, 'msg' => '兑换成功'];
        }
        $shop_info = $this->baseModel->find($this->id);
        $num = isset($this->param['num']) ? intval($this->param['num']) : 0;
        $check_num = $this->baseModel->checkNum($this->user, $this->id, $num);
        if (!$check_num['code']) return $this->error($check_num['msg'], url('index'));
        $this->assign('shop_info',$shop_info);
        $this->assign('num',$num);
        return $this->fetch('order', ['title' => '编辑兑换信息']);
    }

    /**
     * 撤销订单
     * @return array
     */
    public function backOrder()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $res = (new \app\index\model\ShopOrder())->backOrder($this->user, $data['id']);
            if (!$res['code']) {
                return ['code' => 0, 'msg' => $res['msg']];
            }
            return ['code' => 1, 'msg' => '撤单成功'];
        }
    }

    /**
     * 选择收获地址
     * @return array
     */
    public function selectAddress()
    {
        return $this->fetch('select_adr', ['title' => '选择收货地址']);
    }

    /**
     * 点券购买钻石
     * @return array
     */
    public function payDiamonds($coupons)
    {
        $coupons = intval($coupons);
        $data = (new GameMoneyHistory())->couponsChange($this->user, $coupons);
        if ($data['code']) {
            return json(['err' => 0, 'coupons' => $data['coupons'], 'diamonds' => $data['diamonds']]);
        }
        return json(['err' => 1, 'msg' => $data['msg']]);
    }

    /**
     * 钻石购买
     * @return array
     */
    public function getDiamondsBuy()
    {
        $data = (new GameMoneyHistory())->getShopData();
        return json(['err' => 0, 'data' => $data]);
    }
}
