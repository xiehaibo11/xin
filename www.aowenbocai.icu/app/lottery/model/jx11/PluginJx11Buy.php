<?php
namespace app\lottery\model\jx11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginJx11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jx11';
    }

}