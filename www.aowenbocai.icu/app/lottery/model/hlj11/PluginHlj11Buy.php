<?php
namespace app\lottery\model\hlj11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginHlj11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hlj11';
    }

}