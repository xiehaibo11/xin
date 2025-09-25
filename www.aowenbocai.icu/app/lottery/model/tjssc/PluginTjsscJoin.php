<?php
namespace app\lottery\model\tjssc;

use app\lottery\model\common\BaseJoin;

class PluginTjsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tjssc';
    }
}