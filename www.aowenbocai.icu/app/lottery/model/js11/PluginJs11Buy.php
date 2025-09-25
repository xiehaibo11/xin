<?php
namespace app\lottery\model\js11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginJs11Buy extends SyxwBaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js11';
    }

}