<?php
namespace app\web\controller;


class Ssc extends \app\common\controller\Ssc
{

    public function __construct()
    {
        parent::__construct();
        new Base();
    }

}