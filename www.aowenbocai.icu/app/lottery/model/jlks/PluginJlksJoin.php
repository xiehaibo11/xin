<?php
namespace app\lottery\model\jlks;

use app\lottery\model\common\BaseJoin;

class PluginJlksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlks';
    }
}