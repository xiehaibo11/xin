<?php
namespace app\lottery\model\jsks;

use app\lottery\model\common\BaseJoin;

class PluginJsksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsks';
    }
}