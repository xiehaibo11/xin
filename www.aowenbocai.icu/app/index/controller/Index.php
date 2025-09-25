<?php
namespace app\index\controller;

use app\index\model\Banner;
use core\Curl;
use think\Controller;
use app\index\model\ExtShowList;
use core\Setting;
use core\Share;

class Index extends Common
{
    public function index()
    {
        if(!isMobile()){
            $this->redirect(url('/web'));
        }

//        $setting = Setting::get(['share_title','share_desc','share_link','share_img','is_share', 'company_qq', 'company_img', 'company_wx', 'reg_award']);
//        $extshowlist = new ExtShowList;
//        $res = $extshowlist ->getList(5);
//        $count = count($res);
//        if($count == 0) $this->assign('count',0);
//        $info = $res->toArray();
//        $this->assign('info',$info);
//        $this->assign('login_status', empty($this->user) ? 0 : 1);
//        $this->assign('setting',$setting);
//        $this->assign('logo_url', Setting::get('logo_url'));
//        $banner = (new Banner)->where('class_id', 5)->where('status', 1)->order('msort', 'desc')->select();
//        $this->assign('banner',$banner);

        return $this->redirect('/wap');
    }

    public function getConfig($url = '')
    {
        $setting = Setting::get(['weiappid','weiappsecret']);
        $share = new Share($setting['weiappid'], $setting['weiappsecret']);
        $res = $share->getSignPackage($url);
        $res['isWei'] = isWeixin() ? 1 : 0;
        return $res;
    }
}
