<?php
namespace app\index\controller;

use app\index\model\Address as Aaddress;

class Address extends Base
{
    public function __construct()
    {
        $this->baseModel  = new Aaddress();//当前模型
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch('index',['title'=>'收货地址']);
    }

    /**
     * 添加收货地址
     * @return array
     */
    public function add()
    {
        if (request()->isAjax()) {
            $data = $this->post;
            $data['userid'] = $this->user['id'];
            return $this->baseModel->add($data);
        }
        return $this->fetch('add',['title' => '新增收货地址']);
    }

    /**
     * 修改地址
     * @return array
     */
    public function edit()
    {
        $id = $this->id;
        $data['userid'] = $this->user['id'];
        $info = $this->baseModel->find($id);
        if (request()->isAjax()) {
            $data = $this->post;
            return $this->baseModel->add($data);
        }
        return $this->fetch('edit',['title' => '编辑收货地址', 'info' => $info]);
    }

    /**
     * 改变默认地址
     * @return array
     */
    public function changeDefault()
    {
        if (request()->isAjax()) {
            $data=[];
            $data['id'] = $this->id;
            $data['userid'] = $this->user['id'];
            return $this->baseModel->changeStatus($data);
        }
    }

    /**
     * 获取默认地址
     * @return array
     */
    public function getDefaultAddress()
    {
        if (request()->isAjax()) {
            return $this->baseModel->getDefault($this->user['id']);
        }
    }

    /**
     * 获取列表
     * @return array
     */
    public function getList()
    {
        $pageSize = 10;
        $list = $this->baseModel->where('userid', $this->user['id'])->order(['id' => 'desc'])->paginate($pageSize);
        return ['code'=>1,'data'=>['list' => $list]];

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
