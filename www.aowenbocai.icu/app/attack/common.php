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
    $strPol = "0123456789";
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
    if(!empty($param['sortName'])){
        $sort=!empty($param['sortOrder'])?$param['sortOrder']:'asc';
        $order = [
            $param['sortName'] => $sort
        ];
    } else {
        $order = [
            "id" => 'desc'
        ];
    }
    return $order;
}
