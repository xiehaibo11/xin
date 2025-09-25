<?php
namespace app\index\controller;

use think\Controller;
use app\news\model\PluginNewsList;
use app\news\model\PluginNewsNav;
use app\news\News as NewsCore;
use app\index\model\ExtShowList;

class News extends Controller
{
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
    //获取代理说明文章
    public function getAgentExplain(){
        $navClass = new PluginNewsList;
        $info = $navClass->where('title', '代理说明')->find();
        if(!$info){
            return json(['err' => 1, 'msg' => '文章不存在']);
        }
        return json(['err' => 0, 'data' => $info]);
    }

    /**获取新闻相关对应栏目id */
     public function getNavClassId()
    {
        $navClass = new PluginNewsNav;
        $newsId = $navClass->where('title', '新闻资讯')->column('id');
        $noticeId = $navClass->where('title', '网站公告')->column('id');
        $activityId = $navClass->where('title', '优惠活动')->column('id');
        return json([ 'news_id' => implode($newsId),'notice_id' => implode($noticeId),'activity_id' => implode($activityId)]);
    }
}
