<?php
namespace app\lottery\model\ff11;

use app\lottery\model\common\BaseJoin;

class PluginFf11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff11';
    }
}