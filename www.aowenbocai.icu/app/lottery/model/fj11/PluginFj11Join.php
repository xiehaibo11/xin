<?php
namespace app\lottery\model\fj11;

use app\lottery\model\common\BaseJoin;

class PluginFj11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fj11';
    }
}