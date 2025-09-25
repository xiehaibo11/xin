<?php
namespace app\index\controller;

use app\index\model\Banner;
use core\Curl;
use think\Controller;
use app\index\model\ExtShowList;
use core\Setting;
use core\Share;

class WebStop extends Controller
{

    public function index()
    {
        echo '网站升级中，请稍后再试';
    }

}
