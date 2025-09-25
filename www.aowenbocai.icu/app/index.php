<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/base.php';

//屏蔽admin模块
// \think\Route::rule('admin' , function(){
//     return '404 Not Found';
// });
// $route_path = \think\Request::instance()->pathinfo();
// if ($route_path){
//     $route_url = explode('/', $route_path);
//     if (isset($route_url[1]) and $route_url[1] == 'admin') {
//         echo '404 Not Found';
//         return;
//     }
// }

// 执行应用
\think\App::run()->send();