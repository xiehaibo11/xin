<?php
namespace app\lottery\model\hlj11;

use app\lottery\model\common\BaseExpect;

class PluginHlj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hlj11';
    }
}