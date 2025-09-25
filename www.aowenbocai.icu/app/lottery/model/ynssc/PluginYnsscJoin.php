<?php
namespace app\lottery\model\ynssc;

use app\lottery\model\common\BaseJoin;

class PluginYnsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynssc';
    }
}