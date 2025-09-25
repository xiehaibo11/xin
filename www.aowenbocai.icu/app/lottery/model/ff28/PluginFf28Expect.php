<?php
namespace app\lottery\model\ff28;

use app\lottery\model\common\BaseExpect;

class PluginFf28Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff28';
    }
}