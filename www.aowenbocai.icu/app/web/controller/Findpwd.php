<?php
namespace app\web\controller;


class Findpwd extends \app\common\controller\Findpwd
{

    public function __construct()
    {
        parent::__construct();
        (new Common)->getInit(0);
    }

    /**
     * 找回密码
     */
    public function index()
    {
        return $this->fetch('login/find_pwd', ['title' => '找回密码']);
    }
}
