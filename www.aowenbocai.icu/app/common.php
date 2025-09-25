<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 加载安全类
require_once ROOT_PATH . 'core/PasswordHash.php';
require_once ROOT_PATH . 'core/SessionSecurity.php';
require_once ROOT_PATH . 'core/XSSProtection.php';
require_once ROOT_PATH . 'core/SecurityMonitor.php';
require_once ROOT_PATH . 'core/SecurityMiddleware.php';
/**
 * [hook description]
 * @param  [type] $name hook('index_user_index.create', function() {});
 * @return [type]       [description]
 */
function hook($name)
{

}

/**
 * 封装base64位图片上传
 */
function base64_upload($base64, $path='uploads/images/', $filename='')
{
    $admin = \app\web\model\User::get(['sid' => session('admin_sid')]);
    if ($admin['id'] != 1) return '';
    $filename = $filename ? $filename : uniqid();//文件名
    $base64_image = str_replace(' ', '+', $base64);
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
        if($result[2] == 'jpeg'){
            $image_name = $filename.'.jpg';
        }else{
            $image_name = $filename.'.'.$result[2];
        }
        $image_file = $path.$image_name;
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
            return '/'.$path.$image_name;
        }else{
            return '';
        }
    }else{
        return '';
    }
}

/**
 * 封装base64位图片上传（修改）
 */
function new_base64_upload($base64, $path='uploads/images/', $filename='')
{
    $admin = \app\web\model\User::get(['sid' => session('admin_sid')]);
    if ($admin['id'] != 1) return '';
    $filename = $filename ? $filename : uniqid();//文件名
    $base64_image = str_replace(' ', '+', $base64);
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
        if($result[2] == 'jpeg'){
            $image_name = $filename.'.jpg';
        }else{
            $image_name = $filename.'.'.$result[2];
        }
        if(!is_dir($path)){
            mkdirs($path);
        }
        $image_file = $path.$image_name;
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
            return '/'.$path.$image_name;
        }else{
            return '';
        }
    }else{
        return '';
    }
}

/**
 * 上传图片
 * @param  string $files 上传的图片
 * @param  string $type 上传类型
 * @param  string $filename 自定义保存文件名称富文本图片传id
 * @param  string $dirname 同一个页面存在多个富文本时候  区分文件夹
 * @param  string $verify 验证码
 * @return json
 */

function uploadFile($files, $type = "image", $filename = "")
{
    $ext_arr = array(//定义允许上传的文件扩展名
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'image_text' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
    );
    $max_size = 13072000;//最大文件大小300kb
    $upload_path = 'public/uploads';//附件存放目录
    switch ($type) {
        case "image"://图片
            $file_path = '/image';
            break;
        case "image_text"://富文本图片
            $file_path = '/image_text';
            break;
    }
    if ($filename) {
        $file_path .= '/' . $filename;
    }
    $rule = ["size" => $max_size, "ext" => $ext_arr[$type]];
    $targetFolder = $upload_path . $file_path;//文件保存路劲
    $savename = md5(microtime(true));
    $info = $files->validate($rule)->move(ROOT_PATH . $targetFolder, $savename);

    if ($info) {
        return ["code" => 1, "dir" => '/uploads' . $file_path . "/" . $info->getSaveName()];// 成功上传后 获取上传信息
    } else {
        return ["code" => 0, "msg" => $files->getError()];// 上传失败获取错误信息
    }
}

function mkdirs($path)
{
    $path = explode('/', $path);
    $new = '';
    foreach ($path as $value) {
        $new .= $value.'/'; 
        if(!is_dir($new)){
            mkdir($new);
        }
    }
}

/**
 * 判断该玩法有没加入个人浏览中
 * @param $name
 * @return int
 */
function checkExt($name, $extname){
    if (!$extname) return 2;

    $extname = explode('|', $extname);
    if(!in_array($name, $extname)){
        return 1;
    }
    return 0;
}

function setting($name, $value = '', $title = '')
{
    static $Setting;
    if (empty($Setting)) $Setting = new core\model\Setting;

    $set = [];
    //批量配置
    if (gettype($name) == 'array') {
        $set = $name;
    }
    //单个配置 允许标题
    if (isset($value)) {
        $set[$name] = $value;
    }

    $remove = [];
    if (is_null($value)) {
        //批量移除
        if (gettype($name) == 'array') {
            $remove = $name;
        }

        //单个移除
        if (gettype($name) == 'string') {
            $remove[] = $name;
        }
        $Setting->remove($name);
    }


    foreach ($remove as $v) {
        $Setting->remove($v);
    }

    foreach ($set as $k => $v) {
        $Setting->setValue($k, $v, $title);
    }
    
    return $Setting;
}

/**
 * 判断手机电脑
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 判断微信
 */
function isWeixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

//订单号生产  由微秒数生成
function buildOrderNo()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}

/**
 * 耗时异步操作
 * @param $url   模板      模块名称/控制器/方法?参数名=参数值
 * */
function  asyncronous($url, $param, $key = ''){
    if(empty($url)){
        return array("code"=>0,"msg"=>"参数不正确");
    }
    if(strpos($url, "/")==0){
        return array("code"=>0,"msg"=>"参数格式错误");
    }
    $server=$_SERVER['HTTP_HOST'];

    $data = http_build_query($param);

    try {
        $fp = fsockopen($server,80,$errno,$errstr,30);
    } catch(\Exception $e) {
        usleep(100);
        if (!$key) {
            $key = buildOrderNo();
            \think\Cache::set($key, 1);
        } else {
            $count = \think\Cache::get($key);
            if ($count >= 10) {
                return ["code" => 0,"msg"=>$errno + $errstr];
            }
            \think\Log::error('异步<' . $count . '>：' . $url);
            \think\Cache::set($key, \think\Cache::get($key) + 1);
        }
        return asyncronous($url, $param, $key);
     }

    $out = "POST /$url HTTP/1.1\r\n";
    $out .= "Host: $server\r\n";
    $out .= "Content-type:application/x-www-form-urlencoded\r\n";
    $out .= "Content-length:".strlen($data)."\r\n";
    $out .= "Connection:close\r\n\r\n";
    $out .= "$data";

    fwrite($fp, $out);
    //忽略执行结果
    fclose($fp);
    return ["code"=>1,"msg"=>"异步调用成功！"];
}

//ip地址归属地查询
function getIPCity($ip=''){
    $iplocation = new \app\admin\com\IpLocation(__DIR__ . '/admin/qqwry.dat');  //new IpLocation($ipfile) $ipfile ip对应地区信息文件
    $ipresult = $iplocation->getlocation($ip); //根据ip地址获得地区 getlocation("ip地区")
    return $ipresult['country'] . ' ' . $ipresult['area'];
}