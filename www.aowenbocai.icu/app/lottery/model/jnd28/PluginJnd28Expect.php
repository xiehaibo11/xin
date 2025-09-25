<?php
namespace app\lottery\model\jnd28;

use app\lottery\model\common\BaseExpect;

class PluginJnd28Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnd28';
    }
}