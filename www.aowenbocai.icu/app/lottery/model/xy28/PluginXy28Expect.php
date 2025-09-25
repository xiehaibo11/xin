<?php
namespace app\lottery\model\xy28;

use app\lottery\model\common\BaseExpect;

class PluginXy28Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xy28';
    }
}