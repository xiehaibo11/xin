<?php
namespace app\lottery\model\js10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginJs10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js10';
    }
}
