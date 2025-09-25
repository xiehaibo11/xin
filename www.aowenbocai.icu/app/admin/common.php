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

error_reporting(E_ERROR | E_WARNING | E_PARSE);//规避非必要错误
/**
 * 用户登入
 * @access private static
 * @param  int      $uid 用户ID
 * @param  string      $username 用户名
 * @param  string   $sid 用户标示
 * @return array
 */
function login_sign ($uid, $username, $sid)
{
    if(empty($uid) && empty($username) && empty($sid)){
        return false;
    }
    $data = [
        'uid'       => $uid,
        'sid'  => $sid
    ];
    $user = [
        'uid' => $uid,
        'username' => $username
    ];
    session('user',$user);
    session('sid',data_auth_sign($data));

    return true;
}
/**
 * 获取随机字符串
 * @return string
 */
function getRandChar($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

/**
 * 时间搜索条件
 * @return array
 */
function getWhereDate($param)
{
    if (!empty($param['startDate']) and empty($param['endDate'])){

        return ['>=',strtotime($param['startDate'])];

    } elseif (!empty($param['endDate']) and empty($param['startDate'])){

        return ['<=',strtotime($param['endDate']."+1day")];

    }elseif (!empty($param['endDate']) and !empty($param['startDate'])){
        return array(array('>=',strtotime($param['startDate'])),array('<=',strtotime($param['endDate']."+1day")),'and');
    }

}

/**
 * 排序条件
 * $param  浏览器接收的变量参数
 * @return array
 */
function getOrder($param)
{
    if(!empty($param['sort_field'])){
        $sort = !$param['sort_asc'] ? 'desc' : 'asc';
        $order = [
            $param['sort_field'] => $sort
        ];
    } else {
        $order = [
            "id" => 'desc'
        ];
    }
    return $order;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 艺品网络  <twothink.cn>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

function deldir($dir, $bl = false) {
    //先删除目录下的文件：
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath, true);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹
    if ($bl) {
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}
