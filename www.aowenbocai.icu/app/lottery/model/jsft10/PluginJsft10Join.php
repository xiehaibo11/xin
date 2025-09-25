<?php
namespace app\lottery\model\jsft10;

use app\lottery\model\common\pk10\Pk10BaseJoin;

class PluginJsft10Join extends Pk10BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsft10';
    }
}
