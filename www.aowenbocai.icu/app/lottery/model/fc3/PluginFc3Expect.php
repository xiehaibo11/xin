<?php
namespace app\lottery\model\Fc3;

use app\lottery\model\common\BaseExpect;

class PluginFc3Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fc3';
    }
}