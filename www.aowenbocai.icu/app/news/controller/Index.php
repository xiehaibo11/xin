<?php
namespace app\news\controller;

use think\Controller;
use app\news\model\PluginNewsList;
use app\index\model\User;
use app\common;
use app\news\News;
use core\Setting;

class Index extends Controller
{
    public function index($navid)
    {
        if(session('uid')){
            if($ext = checkExt('news', $this->user['extname'])){
                $User = new User;
                $User->setExtName('news',$ext);
            }
        }
        // $NewList = new PluginNewsList;
        // $list = $NewList->all();
        // $list = collection($list)->toArray();
        $list = News::getNewList($navid,15);
        return $this->fetch('index',['title' => '新闻资讯', 'list' => $list]);
    }

    public function list()
    {
        $pageSize = 10;
        $NewList = new PluginNewsList;
        $list = $NewList->order(['id' => 'DESC'])->paginate($pageSize);
        $list->append(['pic','content_index']);
        $data = $list->toArray();
        return ['code' => 0, 'data' => $data['data']];
    }

    public function view($id = '')
    {
        if (request()->isAjax()) {
            $NewList = new PluginNewsList;
            $res = $NewList->get($id);
            if (!$res) {
                return json(['err' => 0, 'msg' => '内容加载失败！']);
            }
            return json(['err' => 0, 'data' => $res]);
        }
        return $this->fetch('view', ['title' => '查看']);
    }

    /**
     * 根据id获取正文
     */
    public function getView($id = '')
    {
        $NewList = new PluginNewsList;
        $res = $NewList->get($id);
        if (!$res) {
            return json(['err' => 0, 'msg' => '内容加载失败！']);
        }
        return json(['err' => 0, 'data' => $res]);
    }

    /**
     * 获取某个栏目最新发布的一篇文章
     */
    public function getNewOne($nav_id = 0)
    {
        return json(['err' => 0, 'data' => News::getNewOne($nav_id)]);
    }

    /**
     * 获取某个栏目最新发布的一篇文章
     */
    public function getNewList($nav_id = 0, $rows = 10)
    {
        return json(['err' => 0, 'data' => News::getNewList($nav_id, $rows,'',true)]);
    }

    /**获取网站协议 */
    public function getService($id)
    {
        $list = (new PluginNewsList)->where('id', $id)->column('content');
        if(empty($list)){
            return json(['err' => 1, 'msg' =>'参数错误']);
        }
        $setting = Setting::get(['site_name']);
        $content = str_replace('**company**', $setting['site_name'], $list[0]);
        return json(['err' => 0,'content' => $content]);
    }
}
