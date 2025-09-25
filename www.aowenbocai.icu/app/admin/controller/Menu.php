<?php
namespace app\admin\controller;

use core\Setting;

class Menu extends Base
{
    public function index()
    {
        $menu_tree = json_decode(Setting::get('menu_tree'), true);
        return $this->fetch('index', ['title' => '后台菜单管理', 'menu_tree' => $menu_tree]);
    }
}
