<?php
namespace app\web\controller;
use core\Setting;

class Pc28 extends \app\common\controller\Pc28
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}