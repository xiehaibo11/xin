<?php

namespace app\index\controller;

use app\index\model\ShopOrder as AshopOrder;

class ShopOrder extends Base
{

    public function __construct()
    {
        $this->baseModel = new AshopOrder();//当前模型
        $this->param = request()->param();
        $this->post = request()->post();
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch('index', ['title' => '我的兑换']);
    }

    /**
     * 获取列表
     * @return array
     */
    public function getList()
    {
        $pageSize = 10;
        $list = $this->baseModel->where('userid', $this->user['id'])->order(['create_time' => 'desc'])->paginate($pageSize)->each(function($item, $key){
            $item['shop_info'] = $item['shop_info'];
            return $item;
        });
        $data_list = $list->toArray();
        $data_list['code'] = 1;
        return $data_list;
    }

}
