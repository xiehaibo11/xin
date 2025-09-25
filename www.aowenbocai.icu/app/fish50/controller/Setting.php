<?php
namespace app\fish50\controller;

use app\admin\controller\Base;
use app\fish50\model\Setting as ASetting;
use app\fish50\model\PluginFishData as FD;

class Setting extends Base
{
    public function index()
    {
        $setting = (new ASetting())->column('value','name');
        $setting['fish_screen'] = json_decode($setting['fish_screen'], true);
        $this->assign('setting', $setting);
        return $this->fetch('index', ['title' => '基本设置']);
    }

    public function fishSet()
    {
        $Fd = new FD;
        $list = $Fd->fishList();
        return $this->fetch('fish_set', ['title' => '设置', 'list' => $list]);
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

    /**
     * 保存设置项
    */
    public function add_setting()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = json_decode($data['data'], true);
            $res =   (new ASetting())->add($data);
            if ($res['code']) {
                return $this->success('设置成功');
            } else {
                return $this->error('设置失败');
            }
        }
    }
}
