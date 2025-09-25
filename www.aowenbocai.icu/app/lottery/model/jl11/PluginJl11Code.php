<?php
namespace app\lottery\model\jl11;

use app\lottery\model\common\BaseCode;

class PluginJl11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jl11';
    }
}