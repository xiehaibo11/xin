<?php
namespace app\home\common;

use app\common\controller\Count;
use app\admin\model\User;
use think\Controller;
use core\Setting;

use app\news\model\PluginNewsNav;

class Common extends Controller
{

    public $user;

    public function __construct()
    {
        parent::__construct();
        $setting = Setting::get(['site_name','company','logo_url','company_person','company_qq','company_email','company_wx', 'company_img','company_img_qq','company_tel']);
        $this->assign('setting', $setting);
        header('Access-Control-Allow-Origin: *');

        $navClass = new PluginNewsNav;
        $newsId = $navClass->where('title', '新闻资讯')->column('id');
        $noticeId = $navClass->where('title', '网站公告')->column('id');
        $helpId = $navClass->where('title', '帮助中心')->column('id');
        $this->assign(['newsId' => implode($newsId) , 'noticeId' => implode($noticeId), 'helpId' => implode($helpId)]);
    }
}
