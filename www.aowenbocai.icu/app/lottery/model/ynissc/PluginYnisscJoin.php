<?php
namespace app\lottery\model\ynissc;

use app\lottery\model\common\BaseJoin;

class PluginYnisscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynissc';
    }
}