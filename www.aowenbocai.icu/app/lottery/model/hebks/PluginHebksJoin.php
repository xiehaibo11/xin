<?php
namespace app\lottery\model\hebks;

use app\lottery\model\common\BaseJoin;

class PluginHebksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hebks';
    }
}