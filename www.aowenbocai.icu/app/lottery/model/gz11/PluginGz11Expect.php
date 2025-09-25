<?php
namespace app\lottery\model\gz11;

use app\lottery\model\common\BaseExpect;

class PluginGz11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gz11';
    }
}