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