<?php
namespace app\lottery\model\ah11;

use app\lottery\model\common\BaseExpect;

class PluginAh11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ah11';
    }
}