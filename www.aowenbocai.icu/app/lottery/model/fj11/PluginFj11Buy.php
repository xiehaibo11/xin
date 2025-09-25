<?php
namespace app\lottery\model\fj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginFj11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fj11';
    }

}