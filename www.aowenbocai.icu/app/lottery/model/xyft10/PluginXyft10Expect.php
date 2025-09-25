<?php
namespace app\lottery\model\xyft10;

use app\lottery\model\common\BaseExpect;

class PluginXyft10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xyft10';
    }
}
