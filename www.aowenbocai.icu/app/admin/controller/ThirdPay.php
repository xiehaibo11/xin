<?php
namespace app\admin\controller;


class ThirdPay extends Base
{

    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new \app\admin\model\ThirdPay();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index($words = '')
    {
        $pageSize = empty($param['pageSize']) ? 15 : $param['pageSize'];//每页记录数
        if ($words) {
            $this->baseModel->where('name', 'like', "%{$words}%");
        }
        $list = $this->baseModel->order('id', 'desc')->paginate($pageSize);
        return $this->fetch('index',['title' => '支付管理', 'list' => $list, 'query' => ['words' => $words]]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $data = $this->post;
            if ($data['type_param']) {
                $rule = json_decode($data['type_param'], true);
                if (!$rule) return $this->error('规则格式错误');
            }
            $res = $this->baseModel->add($data);
            if (!$res['code']) {
                return $this->error($res['msg']);
            }
            return $this->success('添加成功', 'index');
        }
        return $this->fetch('add', ['title' => '添加']);
    }

    public function edit($id = '')
    {
        $info = $this->baseModel->find($id);
        if (!$info) return $this->error('参数错误');
        if (request()->isPost()) {
            $data = $this->post;
            if ($data['type_param']) {
                $rule = json_decode($data['type_param'], true);
                if (!$rule) return $this->error('规则格式错误');
            }
            $res = $this->baseModel->add($data);
            if (!$res['code']) {
                return $this->error($res['msg']);
            }
            return $this->success('修改成功', 'index');
        }
        return $this->fetch('edit', ['title' => '修改', 'info' => $info]);
    }


}
