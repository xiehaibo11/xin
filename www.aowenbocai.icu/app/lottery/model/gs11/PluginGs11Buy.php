<?php
namespace app\lottery\model\gs11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginGs11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gs11';
    }

}