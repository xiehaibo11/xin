<?php
namespace app\lottery\model\ahks;

use app\lottery\model\common\BaseJoin;

class PluginAhksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ahks';
    }
}