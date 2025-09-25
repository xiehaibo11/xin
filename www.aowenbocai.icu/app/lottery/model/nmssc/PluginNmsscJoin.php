<?php
namespace app\lottery\model\nmssc;

use app\lottery\model\common\BaseJoin;

class PluginNmsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmssc';
    }
}