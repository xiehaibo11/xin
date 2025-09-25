<?php
namespace app\lottery\model\ln11;

use app\lottery\model\common\BaseExpect;

class PluginLn11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ln11';
    }
}