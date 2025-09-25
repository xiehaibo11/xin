<?php
namespace app\lottery\model\ff10;

use app\lottery\model\common\BaseExpect;

class PluginFf10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff10';
    }
}
