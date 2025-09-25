<?php
namespace app\lottery\model\hnssc;

use app\lottery\model\common\BaseJoin;

class PluginHnsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnssc';
    }

}