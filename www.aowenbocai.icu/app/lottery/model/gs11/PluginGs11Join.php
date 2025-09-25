<?php
namespace app\lottery\model\gs11;

use app\lottery\model\common\BaseJoin;

class PluginGs11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gs11';
    }
}