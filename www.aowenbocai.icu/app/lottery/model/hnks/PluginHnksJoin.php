<?php
namespace app\lottery\model\hnks;

use app\lottery\model\common\BaseJoin;

class PluginHnksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnks';
    }
}