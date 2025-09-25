<?php
namespace app\home\controller;

use think\Controller;
use core\Setting;
use app\news\model\PluginNewsList;
use app\news\News as NewsCore;
use app\home\common\Common;
use app\news\model\PluginNewsNav;
use app\index\model\ExtShowList;

class News extends Common
{
    /**
     * 文章列表
     * @param int $navid 栏目ID
     * @return json
     */
    public function index($title='')
    {
        $navClass = new PluginNewsList;
        $info = $navClass->where('title', $title)->find();

        $extShow = new ExtShowList;
        $lotterynav =  $extShow->field('id,name,title')->where('type', 1)->order('sort ASC')->select();
        $gamenav =  $extShow->field('id,name,title')->where('type', 0)->order('sort ASC')->select();

        $this->assign(['info' => $info,'gamenav' => $gamenav,'lotterynav' => $lotterynav]);
        return $this->fetch();
    }

    /**
     * 文章列表
     * @param int $navid 栏目ID
     * @return json
     */
    public function help()
    {
        $navClass = new PluginNewsNav;
        $navid = $navClass->where('title', '帮助中心home')->column('id');
        $list = NewsCore::getNewAList(implode($navid),15);
        $this->assign(['help' => $list]);
        return $this->fetch();
    }
    public function newsList($navid)
    {
        $list = NewsCore::getNewAList($navid,15);
        $this->assign(['newsList' => $list]);
        return $this->fetch();
    }

    public function view($id, $navid)
    {
        $res = PluginNewsList::get($id);
        $this->assign('view', $res);
        $list = NewsCore::getNewList($navid,15);
        $this->assign(['list' => $list]);
        return $this->fetch();
    }
}
