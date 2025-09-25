<?php
namespace app\lottery\model\sx11;

use app\lottery\model\common\BaseExpect;

class PluginSx11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sx11';
    }
}