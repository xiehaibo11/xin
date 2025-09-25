<?php
namespace app\web\controller;

use think\Controller;
use app\web\model\User;
use app\index\model\ExtShowList;

class Game extends UserBase
{

    public function index($ext)
    {
        $ext = ltrim($ext, '/');
        $isExists = (new ExtShowList)->field('title')->where('name', 'IN', [$ext, '/'.$ext, "/game/".$ext])->where('status', 0)->find();
        if(!$isExists){
            $this->error('游戏错误', './index');
        }   
        if(session('?uid')){
            $extname = checkExt('Game_'.$ext, $this->user['extname']);
            $this->user['extname'] = (new User)->setExtName('Game_'.$ext,$extname, $this->user['extname']);
        }
        return $this->fetch($ext . '@index/web',['title' => $isExists->title]);
    }

}




