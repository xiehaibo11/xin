<?php
namespace app\lottery\model\ff11;

use app\lottery\model\common\BaseExpect;

class PluginFf11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff11';
    }
}