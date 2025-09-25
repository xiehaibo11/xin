<?php
namespace app\web\controller;


class Orders extends \app\common\controller\Orders
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}




