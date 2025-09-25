<?php
namespace app\admin\controller;

use app\admin\model\Banner as Abanner;
use think\Validate;

class Banner extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new Abanner();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        $list = $this->getList();
        return $this->fetch('index',['title'=>'图片管理', 'list' => $list]);
    }

    /**
     * 分类列表
     */
    public function getList()
    {
        $post  = $this->param;
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
        $list = $this->baseModel->order('msort', 'desc')->paginate($pageSize);
        return $list;
    }

    public function add()
    {
        if(request()->isPost()){
            $data = input('post.');
            $res =  $this->baseModel->add($data);
            if ($res['code']) {
                $this->success('添加成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $class = $this->baseModel->getClass();
        return $this->fetch('add', ['title' => '添加图片', 'class' => $class]);

    }

    public function edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $res =  $this->baseModel->add($data);
            if ($res['code']) {
                $this->success('更新成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $info = $this->baseModel->find($this->id);
        $status_option = $this->baseModel->statusOption($info->getData('status'));
        $class = $this->baseModel->getClass($info->getData('class_id'));
        $this->assign('info', $info);
        $this->assign('status_option', $status_option);
        $this->assign('class', $class);
        return $this->fetch('edit', ['title' => '添加商品']);

    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/image/') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    public function upload()
    {
        $file = request()->file('file');
        $return_data = [
            'code' => 1,
            'msg' => '上传成功'
        ];
        $info = $file->rule('uniqid')->validate(['size'=>5242880,'ext'=>'gif,jpg,jpeg,png'])->move(ROOT_PATH . 'public/static/images/shop/shop_phto','a');
        if($info){
            $return_data['save_type'] =  $info->getExtension();
        }else{
            $return_data['code'] = 0;
            $return_data['msg'] = $file->getError();
        }
        return json($return_data);
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
