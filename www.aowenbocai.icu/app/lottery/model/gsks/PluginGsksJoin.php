<?php
namespace app\lottery\model\gsks;

use app\lottery\model\common\BaseJoin;

class PluginGsksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gsks';
    }
}