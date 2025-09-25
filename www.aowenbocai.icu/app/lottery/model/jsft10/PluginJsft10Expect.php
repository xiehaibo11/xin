<?php
namespace app\lottery\model\jsft10;

use app\lottery\model\common\BaseExpect;

class PluginJsft10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsft10';
    }
}
