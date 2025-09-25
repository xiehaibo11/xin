<?php
namespace app\news\controller;

use app\admin\controller\Base;
use app\news\model\PluginNewsList;
use app\news\model\PluginNewsNav;

class Admin extends Base
{
    public function index($words = '', $nav_id = 0)
    {
        $nav_title = '全部栏目';
        $pageSize = 10;
        $News = new PluginNewsList;
        if (!empty($nav_id)) {
            $News->where('nav_id', $nav_id);
            $nav = PluginNewsNav::get($nav_id);
            if ($nav) {
                $nav_title = $nav->title;
            }
        }
        $data = request()->get();
        if(!empty($data)){
            if(!empty($data['starttime'])){
                $News->where('create_time', '>=', $data['starttime']);
            }
            if(!empty($data['endtime'])){
                $News->where('create_time', '<=', $data['endtime']);
            }
            if(!empty($data['words'])){
                $News->where('content|title', 'like', "%".$data['words']."%");
            }
        }


        $list = $News->order('id desc')->paginate($pageSize);
        $list->append(['navName']);
        $nav = new PluginNewsNav;
        $nav_list = $nav->select();
        return $this->fetch('index',['title' => '设置', 'list' => $list, 'query' => ['words' => $words], 'nav_list' => $nav_list, 'nav_title' => $nav_title]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $News = new PluginNewsList;
            $res = $News->dataAdd(request()->post());
            if (!$res['code']) {
                $this->error($res['msg']);
            }
            $this->success('新增成功', '/news/admin');
        }
        
        $nav = new PluginNewsNav;
        $nav_list = $nav->select();
        return $this->fetch('add', ['title' => '添加文章', 'nav_list' => $nav_list]);
    }

    public function nav()
    {
        
        $pageSize = 10;
        $News = new PluginNewsNav;
        $list = $News->paginate($pageSize);
        $list->append(['pidName']);

        return $this->fetch('nav',['title' => '栏目管理', 'list' => $list]);
    }

    public function addNav()
    {
        if (request()->isPost()) {
            $News = new PluginNewsNav;
            $res = $News->dataAdd(request()->post());
            if (!$res['code']) {
                $this->error($res['msg']);
            }
            $this->success('新增成功', '/news/admin/nav');
        }
        $list = (new PluginNewsNav)->where('pid', 0)->select();
        return $this->fetch('addnav', ['title' => '添加栏目', 'list' => $list]);
    }

    public function changeList($id)
    {
        $news = new PluginNewsList;
        if(request()->IsPost()){
            $data = request()->post();
            unset($data['files']);
            $res = $news->save($data,['id' => $id]);
            if(!$res){
                $this->error('修改失败');
            }
            $this->success('修改成功');
        }
        $nav = new PluginNewsNav;
        $nav_list = $nav->select();
        $list = $news->get($id);
        if(!$list){
            $this->error('信息错误');
        }
        $list->append(['navname']);
        $list = $list->toArray();
        $this->assign('info', $list);
        return $this->fetch('changeList',['title' => '修改消息', 'nav_list' => $nav_list]);
    }

    public function deleteData($id){
        $News = new PluginNewsList;
        if(is_array($id)){
            $res = $News->destroy($id);
        }else{
            $res = $News->destroy($id);
        }
        $err = $res ? 0 : 1;
        return json(['err' => $err]);
    }

    public function changeNav($id)
    {
        $nav = new PluginNewsNav;
        if(request()->IsPost()){
            $data = request()->post();
            $res = $nav->save($data,['id' => $id]);
            if(!$res){
                $this->error('修改失败');
            }
            $this->success('修改成功');
        }
        $list = $nav->get($id);
        if(!$list){
            $this->error('信息错误');
        }
        $list->append(['pidName']);
        $list = $list->toArray();
        $this->assign('info', $list);
        $navList = (new PluginNewsNav)->where('pid', 0)->select();
        return $this->fetch('changeNav',['title' => '修改栏目', 'list' => $navList]);
    }

    public function deleteNav($id)
    {
        $Nav = new PluginNewsNav;

        (new PluginNewsList)->where('nav_id' , $id)->delete();
        
        if(is_array($id)){
            $res = $Nav->where('id', 'in', $id)->whereOr('pid', 'in', $id)->delete();
        }else{
            $res = $Nav->where('id', $id)->whereOr('pid', $id)->delete();
        }
        $err = $res ? 0 : 1;
        return json(['err' => $err]);
    }

    /**
     * 编辑器上传图片
     */
    public function editor_upload($dir = '')
    {
        $files = request()->file("file");
        $getfile = uploadFile($files, "image_text", $dir);//编辑富文本 上传图片

        if ($getfile['code']) {
            return $getfile['dir'];
        } else {
            return $getfile['msg'];
        }
    }
}
