<?php
namespace app\fish50\controller;

use app\index\controller\Base;
use think\Controller;
use app\fish50\model\PluginFishHis;
use app\fish50\model\Setting as ASetting;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('index',['title' => '欢乐捕鱼']);
    }

    public function getSid()
    {
        return json(['sid' => $this->user['sid']]);
    }

    /**
     * 获取会员信息
     */
    public function getInfo()
    {
        $data = [
            'id' => $this->user['id'],
            'money' => $this->user['money'],
            'diamonds' => $this->user['diamonds'],
            'nick_name' => $this->user['nickname'],
            'photo' => $this->user['photo'],
        ];
        return json($data);
    }

    public function getHisList()
    {
        $PZ = new PluginFishHis;
        $res = $PZ->hisList($this->user['id'])->toArray();
        $res['err'] = 0;
        return json($res);
    }

    public function getScreen()
    {
        $setting = (new ASetting())->column('value','name');
        $setting['fish_screen'] = json_decode($setting['fish_screen'], true);
        $fish_screen_array = [];
        foreach ($setting['fish_screen'] as $key => $v) {
            array_push($fish_screen_array, [
                'index' => $key+1,
                'multiple' => $v['screen']
            ]);
        }
        return json(['err' => 0, 'data' => $fish_screen_array]);
    }

}
