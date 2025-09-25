<?php
namespace app\lottery\model\ah11;

use app\lottery\model\common\BaseJoin;

class PluginAh11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ah11';
    }
}