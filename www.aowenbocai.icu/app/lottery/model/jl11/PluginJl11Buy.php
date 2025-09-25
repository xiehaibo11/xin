<?php
namespace app\lottery\model\jl11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginJl11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jl11';
    }

}