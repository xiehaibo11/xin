<?php
namespace app\pay\controller;


class Weinotify extends \think\Controller
{
    public function index()
    {
        $notify = new \wxpay\Notify();
        $notify->Handle();
    }
}