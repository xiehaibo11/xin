<?php
namespace app\lottery\model\xj11;

use app\lottery\model\common\BaseExpect;

class PluginXj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xj11';
    }
}