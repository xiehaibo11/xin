<?php
namespace app\lottery\model\ssc;

use app\lottery\model\common\BaseJoin;

class PluginSscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ssc';
    }
}