<?php
namespace app\lottery\model\xyft10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginXyft10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xyft10';
    }
}
