<?php
namespace app\lottery\model\js10;

use app\lottery\model\common\BaseExpect;

class PluginJs10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js10';
    }
}
