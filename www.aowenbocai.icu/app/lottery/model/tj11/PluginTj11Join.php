<?php
namespace app\lottery\model\tj11;

use app\lottery\model\common\BaseJoin;

class PluginTj11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tj11';
    }
}