<?php
namespace app\lottery\model\jis11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginJis11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jis11';
    }

}