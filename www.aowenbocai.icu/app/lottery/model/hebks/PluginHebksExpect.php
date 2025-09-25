<?php
namespace app\lottery\model\hebks;

use app\lottery\model\common\BaseExpect;

class PluginHebksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hebks';
    }
}