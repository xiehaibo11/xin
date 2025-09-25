<?php
namespace app\lottery\model\shks;

use app\lottery\model\common\BaseJoin;

class PluginShksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'shks';
    }
}