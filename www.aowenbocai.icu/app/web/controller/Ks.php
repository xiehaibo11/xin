<?php
namespace app\web\controller;
use core\Setting;

class Ks extends \app\common\controller\Ks
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}