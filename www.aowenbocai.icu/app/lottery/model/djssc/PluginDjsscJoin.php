<?php
namespace app\lottery\model\djssc;

use app\lottery\model\common\BaseJoin;

class PluginDjsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'djssc';
    }
}