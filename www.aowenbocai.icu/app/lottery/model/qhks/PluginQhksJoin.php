<?php
namespace app\lottery\model\qhks;

use app\lottery\model\common\BaseJoin;

class PluginQhksJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qhks';
    }
}