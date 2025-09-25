<?php
namespace app\lottery\model\sx11;

use app\lottery\model\common\BaseJoin;

class PluginSx11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sx11';
    }
}