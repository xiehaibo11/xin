<?php
namespace app\lottery\model\qqssc;

use app\lottery\model\common\BaseJoin;

class PluginQqsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qqssc';
    }
}