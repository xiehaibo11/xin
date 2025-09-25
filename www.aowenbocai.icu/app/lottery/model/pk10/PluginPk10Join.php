<?php
namespace app\lottery\model\pk10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginPk10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'pk10';
    }
}
