<?php
namespace app\lottery\model\tj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginTj11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tj11';
    }

}