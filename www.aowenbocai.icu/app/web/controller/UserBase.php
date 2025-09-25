<?php
namespace app\web\controller;

use think\Controller;
use app\index\controller\Base as ABase;
use app\web\controller\Common;

class UserBase extends ABase
{
    public function __construct()
    {
        parent::__construct();
        if (empty($this->user) && !session("?agent_user")) {
            return  $this->redirect(url("./Login"));
        }
        (new Common)->getInit($this->user);
        if(session("?agent_user")){
            $actionList= ['detail', 'gamerecord', 'lotteryrecord', 'diamonds', 'drecharge', 'dexchange'];
            $controllerList= ['User', 'Details'];
            $action = request()->action();
            $controller = request()->controller();
            $getData = request()->get();
            $agent_user_isRight = !isset($getData['userid']) || empty($getData['userid']);
            $moudle_right = $controller == 'User' && in_array($action, $actionList) || $controller == 'Details';
            if(($agent_user_isRight || !$moudle_right) &&empty($this->user)){
                echo '404，你没有该权限';
                die;
            }
        }
     }
}