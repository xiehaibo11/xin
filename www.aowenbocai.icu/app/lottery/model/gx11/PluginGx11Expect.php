<?php
namespace app\lottery\model\gx11;

use app\lottery\model\common\BaseExpect;

class PluginGx11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gx11';
    }
}