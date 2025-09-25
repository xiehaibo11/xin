<?php
namespace app\lottery\model\bjks;

use app\lottery\model\common\BaseJoin;

class PluginBjksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bjks';
    }
}