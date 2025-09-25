<?php
namespace app\admin\controller;

use app\admin\model\Prop as AProp;
use think\Validate;

class Prop extends Base
{

    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new AProp();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        $data = $this->getList();
        $data['title'] = '道具管理';
        $data['param'] = $this->param;
        return $this->fetch('index', $data);
    }

    /**
     * 分类列表
     */
    public function getList()
    {
        $param  = $this->param;
        if ($param['words']) {
            $this->baseModel->where('name', 'like', "%{$param['words']}%");
        }
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate($pageSize, false, ['query' => $param])->each(function($item, $key){
            $item['type_txt'] = $item['type_txt'];
            $item['ext_info'] = $item['ext_info'];
            return $item;
        });
        $return = [
            'list' => $list,
            'render' => $list->render()
        ];
        return $return;
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
        $type_option = $this->baseModel->typeOption();
        $ext_option = $this->baseModel->extOption();
        return $this->fetch('add', ['title' => '添加道具', 'type_option' => $type_option, 'ext_option' => $ext_option]);

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
        $type_option = $this->baseModel->typeOption($info->getData('type'));
        $ext_option = $this->baseModel->extOption($info->getData('ext_name'));
        $this->assign('info', $info);
        $this->assign('type_option', $type_option);
        $this->assign('ext_option', $ext_option);
        return $this->fetch('edit', ['title' => '编辑道具']);
    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/image/') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
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
