<?php
namespace app\web\controller;

use app\common\controller\LotteryCommon;
use app\index\model\Banner;
use app\index\model\BannerClass;
use app\index\model\ExtShowList;
use app\common\model\MoneyHistory;
use app\index\model\User;
use think\Controller;
use think\Cookie;
use think\Session;
use app\news\News;
use think\Db;
use core\Page;
use core\Setting;
use core\Smtp;

class Index extends Base
{
    public function index()
    {
        $article = ['news' => News::getNewList('',10,'新闻资讯'), 'notice'=>News::getNewList('',10,'网站公告'),'noticeList' => News::getNewList('','','网站公告',true)];
        $this->assign(['article' => $article]);
        $this->assign(['noticeList' => collection($article['noticeList'])]);
        $bannid = (new BannerClass)->where('name', 'pc版首页banner')->column('id');
        $banner = [];
        if(!empty($bannid)){
            $banner = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }

        $bannidA = (new BannerClass)->where('name', 'pc版首页广告图')->column('id');
        $bannerA = [];
        if(!empty($bannidA)){
            $bannerA = (new Banner)->where('class_id', $bannidA[0])->where('status', 1)->order('msort', 'desc')->select();
        }
        $this->assign('banner',$banner);
        $this->assign('bannerA',$bannerA);

        $extShow = new ExtShowList;
        $rec = $extShow->where('reco', 1)->where('status',0)->limit(0,6)->order('sort ASC')->select();
        $rec = $rec->toArray();
        foreach ($rec as &$value) {
            $value['link'] = $value['name'];
            $value['name'] = LotteryCommon::setUrl($value['name'])[0];
        }
        $this->assign('rec', $rec);

        $gamenav =  $extShow->field('id,name,title')->where('type', 1)->where('status',0)->order('sort ASC')->select();

        $this->assign('gameInfo', $gamenav);
        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['isGain','lottery_unit','coin_lottery']);

        /**中奖累积前十排行 */
        $list = (new User)->field('nickname, award')->order('award DESC')->limit(0,10)->select();
        /**中奖资讯 */
        $awardList = (new MoneyHistory)->field('userid,money,remark')->where('type', 1)->where('money > 0')->order('id desc')->limit(0,20)->select();
        $awardList->append(['nickname', 'lotteryName']);
        $this->assign('system', collection($system));
        $dzq_open = (new ExtShowList())->where('name', '/dzp')->find();
        if (!$dzq_open || $dzq_open['status'] == 1 || $dzq_open['pause'] == 1)  {
            $dzq_open = 0;
        } else {
            $dzq_open = 1;
        }
        return $this->fetch('', ['top10' => $list, 'awardList' => $awardList, 'dzq_open' => $dzq_open]);
    }
    /**
     * 游戏中心
    */
    public function gameCenter()
    {
        $bannid = (new BannerClass)->where('name', 'pc版游戏大厅banner')->column('id');
        $banner = [];
        if(!empty($bannid)){
            $banner = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }
        $this->assign('banner', $banner);

        $extShow = new ExtShowList;
        $rec = $extShow->where('reco', 1)->where('status',0)->limit(0,4)->order('sort ASC')->select();
        $rec = $rec->toArray();
        foreach ($rec as &$value) {
            $value['link'] = $value['name'];
            $value['name'] = LotteryCommon::setUrl($value['name'])[0];
        }
        $this->assign('rec', $rec);
        return $this->fetch('');
    }

    /**
     *  开奖公告
     */
    public function get_kaijiang()
    {
        return (new \app\common\controller\Lottery())->get_kaijiang();
    }

    /**
     *  开奖公告
    */
    public function kaijiang()
    {
        $open = (new \app\common\controller\Lottery())->get_kaijiang();;
        $this->assign('list', $open);
        return $this->fetch('');
    }

     /**获取游戏记录 ---先写彩票类，测试合并显示   */
     public function game($gameid = '')
     {
         return (new \app\common\controller\Lottery())->game($gameid);
     }


    /**家长监护*/
     public function custody()
     {
        return $this->fetch('');
     }
}




