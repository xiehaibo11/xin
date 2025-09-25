<?php
namespace app\web\controller;

use think\Controller;
use app\index\controller\Base as ABase;

class Base extends ABase
{
    public function __construct()
    {
        parent::__construct();
        $userInfo = $this->user ? $this->user : '';
        (new Common)->getInit($userInfo);
    }
}