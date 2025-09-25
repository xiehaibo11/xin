<?php
namespace app\lottery\model\jis11;

use app\lottery\model\common\BaseExpect;

class PluginJis11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jis11';
    }
}