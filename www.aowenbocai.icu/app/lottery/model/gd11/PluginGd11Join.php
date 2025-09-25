<?php
namespace app\lottery\model\gd11;

use app\lottery\model\common\BaseJoin;

class PluginGd11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gd11';
    }
}