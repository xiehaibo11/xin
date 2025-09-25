<?php
namespace app\lottery\model\gzks;

use app\lottery\model\common\BaseJoin;

class PluginGzksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gzks';
    }
}