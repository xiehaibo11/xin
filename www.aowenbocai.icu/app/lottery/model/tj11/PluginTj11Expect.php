<?php
namespace app\lottery\model\tj11;

use app\lottery\model\common\BaseExpect;

class PluginTj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tj11';
    }
}