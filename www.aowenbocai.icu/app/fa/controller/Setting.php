<?php
namespace app\fa\controller;

use think\Controller;
use app\news\model\Setting as ASetting;


class Setting extends Controller
{
    public function index()
    {
        $Setting = new ASetting;
        // $Setting->save([
        //     'title' =>'2',
        //     'name' => 'ad',
        //     'value' => '你好'
        // ]);
        //
        $Setting->setValue('flys', '给你sss什么');
        print_r($Setting->getValue('flys'));
        return $this->fetch('index', ['title' => '设置']);
    }

    public function setValue()
    {}

    public function getValue()
    {}

    public function hasValue()
    {}

    public function allValue()
    {}
}
