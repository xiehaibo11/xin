<?php
namespace app\lottery\model\xjssc;

use app\lottery\model\common\BaseJoin;

class PluginXjsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xjssc';
    }
}