<?php
namespace app\web\controller;


class Pk10 extends \app\common\controller\Pk10
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}