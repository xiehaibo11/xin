<?php
namespace app\web\controller;

use think\Controller;
use app\news\model\PluginNewsList;
use app\news\model\PluginNewsNav;
use app\news\News as NewsCore;
use app\index\model\Banner;
use app\index\model\BannerClass;
use app\index\model\ExtShowList;

class News extends Base
{
    /**
     * 文章列表
     * @param int $navid 栏目ID
     * @return json
     */
    public function game($navid)
    {
        $bannid = (new BannerClass)->where('name', 'pc版新闻版头图')->column('id');
        $banner = [];
        if(!empty($bannid)){
            $banner = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }

        $list = (new NewsCore)->getNewAList($navid,15);
        $nav = (new PluginNewsNav)->get($navid);
        if(!$nav){
            $nav['title'] = '该栏目不存在';
        }
        return $this->fetch('',['newsList' => $list,'banner'=>$banner,'nav' => $nav,'title' => $nav['title']]);
    }
    public function activity($navid)
    {
        $bannid = (new BannerClass)->where('name', 'pc版新闻版头图')->column('id');
        $banner = [];
        if(!empty($bannid)){
            $banner = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }

        $list = (new NewsCore)->getNewAList($navid,15);
        $nav = (new PluginNewsNav)->get($navid);
        if(!$nav){
            $nav['title'] = '该栏目不存在';
        }
        return $this->fetch('',['newsList' => $list,'banner'=>$banner,'nav' => $nav,'title' => $nav['title']]);
    }
    public function view($id, $navid)
    {
        $res = (new PluginNewsList)->get($id);
        $list = (new NewsCore)->getNewList($navid,15);
        $nav = (new PluginNewsNav)->get($navid);
        return $this->fetch('', ['view' => $res, 'list' => $list,'nav' => $nav]);
    }

    public function help($id = '', $navid = '')
    {
        $navClass = new PluginNewsNav;
        $pid = $navClass->where('title', '帮助中心')->column('id');
        if(!$id && !$navid){
            $helpPid = $navClass->where('pid', $pid[0])->column(['id']);
            $res = (new PluginNewsList)->where('nav_id', 'in', implode(',', $helpPid))->order('id ASC')->find();
            $id = $res->id;
            $navid = $res->nav_id;
        }else{
            $res = (new PluginNewsList)->where('id', $id)->where('nav_id', $navid)->find();
        }
        $showPid = $navClass->where('id', $navid)->column('pid');
        $showPid = empty($showPid) ? -1 : $showPid[0];
        $nav = $navClass->where('pid', $pid[0])->select();
        $nav->append(['article','column']);
        return $this->fetch('', ['view' => $res, 'nav' => $nav, 'id' => $id ,'navid' => $navid, 'showPid' => $showPid ,'pid' => collection($pid)]);
    }

    public function getLotteryNew($name)
    {
        $res = (new ExtShowList)->field('title')->where('name', 'in', [$name, '/'.$name])->find();
        if(!$res){
            return json(['err' => 1, 'msg' => '彩种不存在']);
        }
        
        $info = (new PluginNewsList)->where('title', $res->title)->find();
        if(!$info){
            return json(['err' => 1, 'msg' => '文章不存在']);
        }
        return json(['err' => 0, 'url' => url("help",['id' => $info->id, 'navid' => $info->nav_id])]);
    }
    /**
     * 移动端获取文章相关
     * @param int $navid 栏目ID
     * @return json
     */
   //获取文章列表
    public function getGameNews($navid)
    {

        $list = (new NewsCore)->getNewAList($navid,15);
        $nav = (new PluginNewsNav)->get($navid);
        if(!$nav){
            $nav['title'] = '该栏目不存在';
        }
        return json($list);
    }
     //获取文章详情
    public function getView($id, $navid)
    {
        $res = (new PluginNewsList)->get($id);
        return json($res);
    }
   //获取帮助中心相关
    public function getHelpNews($id = '', $navid = '')
    {
        $navClass = new PluginNewsNav;
        $pid = $navClass->where('title', '帮助中心')->column('id');
        if (empty($pid)) return json(['nav' => []]);
        if(!$id && !$navid){
            $helpPid = $navClass->where('pid', $pid[0])->column(['id']);
            $res = (new PluginNewsList)->where('nav_id', 'in', implode(',', $helpPid))->order('id ASC')->find();
            $id = $res->id;
            $navid = $res->nav_id;
        }else{
            $res = (new PluginNewsList)->where('id', $id)->where('nav_id', $navid)->find();
        }
        $showPid = $navClass->where('id', $navid)->column('pid');
        $showPid = empty($showPid) ? -1 : $showPid[0];
        $nav = $navClass->where('pid', $pid[0])->select();
        $nav->append(['article','column']);
        return json(['nav' => $nav]);
    }
     //获取对应彩票玩法介绍
    public function getAppBetNew($name)
    {
        $res = (new ExtShowList)->field('title')->where('name', 'in', [$name, '/'.$name])->find();
        if(!$res){
            return json(['err' => 1, 'msg' => '彩种不存在']);
        }

        $info = (new PluginNewsList)->where('title', $res->title)->find();
        if(!$info){
            return json(['err' => 1, 'msg' => '文章不存在']);
        }
        return json(['err' => 0, 'data' => $info]);
    }
}
