<?php
namespace app\lottery\model\gs11;

use app\lottery\model\common\BaseExpect;

class PluginGs11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gs11';
    }
}