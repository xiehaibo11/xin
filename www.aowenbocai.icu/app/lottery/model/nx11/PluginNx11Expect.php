<?php
namespace app\lottery\model\nx11;

use app\lottery\model\common\BaseExpect;

class PluginNx11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nx11';
    }
}