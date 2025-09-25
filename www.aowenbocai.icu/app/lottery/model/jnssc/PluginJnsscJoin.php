<?php
namespace app\lottery\model\jnssc;

use app\lottery\model\common\BaseJoin;

class PluginJnsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnssc';
    }
}