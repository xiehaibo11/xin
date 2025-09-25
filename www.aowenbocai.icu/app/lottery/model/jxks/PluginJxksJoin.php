<?php
namespace app\lottery\model\jxks;

use app\lottery\model\common\BaseJoin;

class PluginJxksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jxks';
    }
}