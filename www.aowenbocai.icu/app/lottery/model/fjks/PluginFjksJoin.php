<?php
namespace app\lottery\model\fjks;

use app\lottery\model\common\BaseJoin;

class PluginFjksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fjks';
    }
}