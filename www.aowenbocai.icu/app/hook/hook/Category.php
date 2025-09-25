<?php
/**
 * Created by PhpStorm.
 * User: JnToo
 * Date: 2016/11/12
 * Time: 1:11
 */
namespace app\hook\hook;
class Category
{
    function index()
    {
        echo '我是Category类型钩子中的index方法<br>';
    }
    function index_5()
    {
        echo '我是Category类型钩子中的index方法，我的权重比较低<br>';
    }
}
