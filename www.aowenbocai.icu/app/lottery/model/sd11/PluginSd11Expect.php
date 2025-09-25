<?php
namespace app\lottery\model\sd11;

use app\lottery\model\common\BaseExpect;

class PluginSd11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sd11';
    }
}