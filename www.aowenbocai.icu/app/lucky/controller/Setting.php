<?php
namespace app\lucky\controller;

use app\admin\controller\Base;
// use app\lucky\model\PluginFishData as FD;

class Setting extends base
{
    public function index()
    {
        // $Fd = new FD;
        // $list = $Fd->fishList();
        // return $this->fetch('index', ['title' => '设置', 'list' => $list]);
    }

    public function edit($id = '', $hit = '', $sp = '')
    {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $info = FD::get($id);
        if (request()->isPost()) {
            if (empty($hit) || empty($sp)) {
                return $this->error('错误， 必填项不能为空');                
            }
            $info->hit = $hit;
            $info->sp = $sp;
            $info->save();
            return $this->success('修改成功', 'index');            
        }

        return $this->fetch('edit', ['title' => '修改', 'info' => $info]);
    }

    public function setStatus($id = '')
    {
        if (empty($id)) {
            return $this->error('错误');
        }
        
        $info = FD::get($id);
        $info->status = $info->status == 0 ? 1 : 0;
        $info->save();
        return $this->success('修改成功');
    }
}
