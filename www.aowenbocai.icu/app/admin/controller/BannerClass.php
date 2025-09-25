<?php

namespace app\admin\controller;


use app\admin\model\BannerClass as AbannerClass;
use think\Config;
use think\Db;
use think\Request;


class BannerClass extends Base
{

    public function __construct(\think\Request $request)
    {
        $this->param = $request->param();
        $this->post = $request->post();
        $this->baseModel = new AbannerClass();
        $this->id = isset($this->param['id']) ? intval($this->param['id']) : '';
        parent::__construct();
    }

    /**
     * 列表视图
     */
    public function index()
    {
        return $this->fetch('index',['title'=>'分类管理']);
    }

    /**
     * 分类列表
     */
    public function getList()
    {
        if ($this->request->isAjax()) {
            $post  = $this->post;
            if ($post['words']) {
                $this->baseModel->where('name', 'like', "%{$post['words']}%");
            }
            if ($post['starttime']) {
                $this->baseModel->where('create_time', '>=', $post['starttime']);
            }
            if ($post['endtime']) {
                $endtime = date('Y-m-d', strtotime($post['endtime'] . ' +1 day'));
                $this->baseModel->where('create_time', '<', $endtime);
            }
            $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
            $list = $this->baseModel->order('id', 'desc')->paginate($pageSize);
            $return = [
                'list' => $list,
                'render' => $list->render()
            ];
            return $return;
        }
    }

    /**
     * 分类增加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->post;
            $res = $this->baseModel->add($post);
            if ($res['code']) {
                return $this->success('增加成功',url('index'));
            } else {
                return $this->error($res['msg']);
            }
        }
        return $this->fetch('add',['title'=>'添加分类']);
    }

    /**
     * 修改
     */
    public function edit()
    {
        $id = $this->id;
        $info = $this->baseModel->get($id);
        if ($this->request->isPost()) {
            $post = $this->post;
            $res = $this->baseModel->add($post);
            if ($res['code']) {
                return $this->success('更新成功',url('index'));
            } else {
                return $this->error($res['msg']);
            }
        }
        $this->assign("info", $info);
        return $this->fetch('edit',['title'=>'编辑分类']);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id=$this->id;
        $info=$this->baseModel->get($id);
        if($info->delete()){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }



}

?>