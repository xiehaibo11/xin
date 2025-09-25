<?php
namespace app\lottery\model\gxks;

use app\lottery\model\common\BaseJoin;

class PluginGxksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gxks';
    }
}