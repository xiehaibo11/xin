<?php
namespace app\index\controller;

use think\Controller;
use core\Active as AActive;
use core\model\ActiveLog;

class Active extends Common
{
    public function index($type = 1)
    {  
        $active = new AActive($this->user);
        if($type = 1){
           $type  = ['ext' => ['neq', "brisk"]];
        }
        $list = $active->task($type, $this->user);
        $activeList = $active->getActive();
       
        return json(['err' => 0, 'active' => $activeList, 'other' => $list]);
    }
    public function getAward($id){
        $active = new AActive($this->user);
        return json($active->getAward($id));
    }
    public function getAwardNum()
    {
        $num = (new ActiveLog)->where(['getaward'=>1, 'create_time' => ['>=', date("Y-m-d")], 'userid' => $this->user['id']])->count();
        return json(['err' => 0, 'num' => $num]);
    }
    public function share()
    {
        $active = new AActive($this->user);
        $active->addInterface(35, 1);
    }
}