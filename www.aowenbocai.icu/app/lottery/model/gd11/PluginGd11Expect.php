<?php
namespace app\lottery\model\gd11;

use app\lottery\model\common\BaseExpect;

class PluginGd11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gd11';
    }
}