<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as user;
use app\index\model\Banner;
use app\index\model\BannerClass;
use core\Setting;
use app\index\model\ExtShowList;

use app\admin\model\Shop;
use app\admin\model\ShopNav;
use app\admin\model\ShopOrder;
use think\Db;

class Lottery extends Controller
{

    /**
     *  开奖公告
     */
    public function get_kaijiang()
    {
        return (new \app\common\controller\Lottery())->get_kaijiang();
    }

    /**获取游戏记录 ---先写彩票类，测试合并显示*/
    public function game($gameid = '')
    {
        return (new \app\common\controller\Lottery())->game($gameid);
    }

}