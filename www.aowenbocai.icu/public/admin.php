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


define('APP_PATH', __DIR__ . '/../app/');
define('BIND_MODULE','admin');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/base.php';

// 执行应用
\think\App::run()->send();
