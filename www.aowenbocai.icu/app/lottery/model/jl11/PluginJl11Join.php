<?php
namespace app\lottery\model\jl11;

use app\lottery\model\common\BaseJoin;

class PluginJl11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jl11';
    }
}