<?php
namespace app\zhuawawa\controller;

use app\admin\controller\Base;
use app\zhuawawa\model\PluginZhuawawaSetting as PZS;

class Setting extends base
{
    public function index()
    {
        $PZS = new PZS;
        $list = $PZS->getList();
        return $this->fetch('index', ['title' => '设置', 'list' => $list]);
    }

    public function add($title = '', $hit = '', $bet_money = '', $gift_type = '', $gift_value = '', $icon = '')
    {
        if (request()->isPost()) {
            $gift = json_encode(['type' => $gift_type, 'value' => $gift_value]);
                PZS::insert([
                'icon' => $icon,
                'gift' => $gift,
                'title' => $title,
                'bet_money' => $bet_money,
                'hit' => $hit
            ]);
        }
        return $this->fetch('add', ['title' => '添加']);
    }

    public function edit($id = '', $hit = '', $gift_value = '')
    {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $info = new PZS;
        if (request()->isPost()) {
            if (empty($hit) || empty($gift_value)) {
                return $this->error('错误， 必填项不能为空');                
            }
            $data = request()->post();
            $gift = [
                'type' => $data['gift_type'],
                'value' => $data['gift_value'],
            ];
            $data['gift'] = json_encode($gift);
            $info->allowField(true)->save($data, ['id' => $id]);
            return $this->success('修改成功', 'index');            
        }
        $gameInfo = $info::get($id);
        $gf = json_decode($gameInfo['gift'], true);
        $gameInfo['gift_type'] = $gf['type'];
        $gameInfo['gift_value'] = $gf['value'];
        return $this->fetch('edit', ['title' => '修改', 'info' => $gameInfo]);
    }

    public function setStatus($id = '')
    {
        if (empty($id)) {
            return $this->error('错误');
        }
        
        $info = PZS::get($id);
        $info->status = $info->status == 0 ? 1 : 0;
        $info->save();
        return $this->success('修改成功');
    }

    public function base64Upload($base64_data, $userid)
    {
        $path = base64_upload($base64_data,'uploads/extimg/zhuanwawa', $userid) . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }
}
