<?php
namespace app\lottery\model\js28;

use app\lottery\model\common\BaseExpect;

class PluginJs28Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js28';
    }
}