<?php
namespace app\common\controller;

use think\Controller;
use app\admin\model\ExtShowList;

class Extend extends Controller
{
    /**
     * 获取拓展相关信息
     * @param  array  $data
     * @return array
     */
    public function getInfo($ext_name)
    {
        $Ext = new ExtShowList;
        $ext = $Ext->getInfoByName($ext_name);
        $main_path = '../plugin/' . $ext_name . '/main.php';
        return $ext ? ['logo' => $ext['image'], 'title' => $ext['title']] : [];
    }
}
