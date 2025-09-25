<?php
namespace app\lottery\model\jis11;

use app\lottery\model\common\BaseCode;

class PluginJis11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jis11';
    }
}