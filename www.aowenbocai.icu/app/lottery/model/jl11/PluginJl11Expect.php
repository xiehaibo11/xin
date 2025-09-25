<?php
namespace app\lottery\model\jl11;

use app\lottery\model\common\BaseExpect;

class PluginJl11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jl11';
    }
}