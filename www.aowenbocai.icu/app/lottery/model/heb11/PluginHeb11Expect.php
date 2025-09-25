<?php
namespace app\lottery\model\heb11;

use app\lottery\model\common\BaseExpect;

class PluginHeb11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'heb11';
    }
}