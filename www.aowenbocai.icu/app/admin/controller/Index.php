<?php
namespace app\admin\controller;

use think\Config;
use think\Db;
use think\Controller;
use app\admin\model\User;
use app\common\model\MoneyHistory;
use app\admin\model\ShopOrder as AshopOrder;
use core\Setting;

class Index extends Base
{
    public function index()
    {
        $setting = Setting::get(['menu_tree','openCj']);
        $menu_tree = json_decode($setting['menu_tree'], true);
        if(!isset($setting['openCj'])){
            Setting::set(['openCj', 0]);
            $setting['openCj'] = 0;
        }
        $shopNum = (new ShopOrder())->getNum(0);
        $rechargeNum = (new Recharge())->getNum(0);
        return $this->fetch('index',['title' => '后台管理', 'admin' => $this->admin, 'menu_tree' => $menu_tree, 'isCj' => $setting['openCj'], 'shopNum' =>$shopNum['num'], 'rechargeNum' =>$rechargeNum['num']]);
    }

    public function main()
    {
        $version = Setting::get(['version']);
        return $this->fetch('', [
            'title' =>'后台首页',
            'now' => date('H:i:s'),
            'regCount' => $this->regCount(),
            'onlineCount' => $this->onlineCount(),
            'newUser' => $this->getNewTop10User(),
            'moneyHistroy' => $this->getMoneyHistory(),
            'actionUser' => $this->getActionTop10User(),
            'statis' => $this->getStatis(),
            'version' => $version['version']
        ]);
    }

    public function getOrderChange()
    {
        $shop_order_res = (new ShopOrder())->getNum(0);
        $recharge_order_res = (new Recharge())->getNum(0);
        return json(['shop_order' => $shop_order_res, 'recharge_order' => $recharge_order_res]);
    }

    private function regCount()
    {
        return Db::name('user')->count();
    }

    private function onlineCount()
    {
        return Db::name('user')->where('action_time', '>', date('Y-m-d H:i:s', strtotime('-10 minute')))->count();
    }

    public function index_main()
    {
        return $this->fetch('index_main',['title' => '后台管理']);
    }


    public function getOnlineCount()
    {
        return Db::name('chart_data')->where('type', 0)->order('id desc')->limit('20')->select();
    }

    public function getRegCount()
    {
        return Db::name('chart_data')->where('type', 1)->order('id desc')->limit('20')->select();
    }

    public function getNewTop10User()
    {
        $User = new User;
        return $User->order('id desc')->limit(5)->select();
    }

    public function getActionTop10User()
    {
        $User = new User;
        return $User->order('action_time desc')->limit(5)->select();
    }

    public function getMoneyHistory()
    {
        $MoneyHistory = new MoneyHistory;
        return $MoneyHistory->getList('', '', '', 10);
    } 
    public function getStatis()
    {
        $MoneyHistory = new MoneyHistory;
        return $MoneyHistory->getStaisc('',[">=", date("Y-m-d")]);
    }
}
