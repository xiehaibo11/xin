<?php
namespace app\lottery\model\js11;

use app\lottery\model\common\BaseExpect;

class PluginJs11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js11';
    }
}