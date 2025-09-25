<?php
namespace app\lottery\model\xj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginXj11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xj11';
    }

}