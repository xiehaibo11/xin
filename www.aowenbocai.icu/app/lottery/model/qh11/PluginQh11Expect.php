<?php
namespace app\lottery\model\qh11;

use app\lottery\model\common\BaseExpect;

class PluginQh11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qh11';
    }
}