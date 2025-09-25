<?php
namespace app\index\controller;

use think\Controller;

class Result extends Controller
{
    public function successInfo($info, $url)
    {
        echo $info;
        die;
    }
    
    public function errorInfo($info, $url)
    {
        echo $info;
        die;
    }
}