<?php
namespace app\lottery\model\azssc;

use app\lottery\model\common\BaseJoin;

class PluginAzsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'azssc';
    }
}