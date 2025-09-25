<?php
namespace app\lottery\model\heb11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginHeb11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'heb11';
    }

}