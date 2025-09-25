<?php
namespace app\lottery\model\hn11;

use app\lottery\model\common\BaseExpect;

class PluginHn11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hn11';
    }
}