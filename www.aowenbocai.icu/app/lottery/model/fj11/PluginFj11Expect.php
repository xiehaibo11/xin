<?php
namespace app\lottery\model\fj11;

use app\lottery\model\common\BaseExpect;

class PluginFj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fj11';
    }
}