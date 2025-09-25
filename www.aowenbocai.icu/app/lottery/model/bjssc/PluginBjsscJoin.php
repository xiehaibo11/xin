<?php
namespace app\lottery\model\Bjssc;

use app\lottery\model\common\BaseJoin;

class PluginBjsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bjssc';
    }
}