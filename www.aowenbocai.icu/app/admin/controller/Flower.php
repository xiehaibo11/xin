<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\FlowerGift;
use app\common\model\FlowerHistory;


class Flower extends Base
{
    /**
     * 鲜花资金明细
     */
    public function index()
    {
        $data = request()->get();

        $list = (new FlowerHistory)->getList($data);
        $list->append(['userinfo']);
        $page = $list->render();
        $list = $list->toArray();
        if(empty($list['data'])){
            $this->assign('_empty', 1);
        }

        $this->assign('list', $list['data']);
        $this->assign('page', $page);
        $this->assign('query', $data);
        return $this->fetch('', ['title' => '鲜花明细']);
    }

    /**
     * 删除赠送记录
     */
    public function flowerDelete()
    {
        $res = (new FlowerHistory)->deleteData(request()->get());
        if($res){
            return json(['err' => 0, 'msg' => '删除成功']);
        }
        return json(['err' => 1, 'msg' => '删除失败']);
    }

    /**
     * 获取赠送记录
     */
    public function giftList()
    {
        $data = request()->get();
        $list = (new FlowerGift)->getList($data);
        $lis = $list->append(['sendperson', 'getperson']);
        $page = $list->render();
        $list = $list->toArray();
        if(empty($list['data'])){
            $this->assign('_empty', 1);
        }

        $this->assign('list', $list['data']);
        $this->assign('page', $page);
        $this->assign('query', $data);
        return $this->fetch('',['title' => '鲜花赠送记录']);
    }

    /**
     * 删除赠送记录
     */
    public function giftDelete()
    {
        $res = (new FlowerGift)->deleteData(request()->get());
        if($res){
            return json(['err' => 0, 'msg' => '删除成功']);
        }
        return json(['err' => 1, 'msg' => '删除失败']);
    }
}
