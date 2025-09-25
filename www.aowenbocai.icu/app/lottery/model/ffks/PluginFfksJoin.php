<?php
namespace app\lottery\model\ffks;

use app\lottery\model\common\BaseJoin;

class PluginFfksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffks';
    }
}