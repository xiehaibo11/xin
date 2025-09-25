<?php
namespace app\lottery\model\pk10;

use app\lottery\model\common\BaseExpect;

class PluginPk10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'pk10';
    }
}
