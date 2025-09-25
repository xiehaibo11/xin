<?php
namespace app\lottery\model\Fc3;

use app\lottery\model\common\BaseJoin;

class PluginFc3Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fc3';
    }
}