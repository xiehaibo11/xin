<?php
namespace app\lottery\model\ff10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginFf10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff10';
    }
}
