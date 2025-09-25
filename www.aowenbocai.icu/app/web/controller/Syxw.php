<?php
namespace app\web\controller;


class Syxw extends \app\common\controller\Syxw
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}