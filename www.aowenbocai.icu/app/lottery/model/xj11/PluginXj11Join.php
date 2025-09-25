<?php
namespace app\lottery\model\xj11;

use app\lottery\model\common\BaseJoin;

class PluginXj11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xj11';
    }
}