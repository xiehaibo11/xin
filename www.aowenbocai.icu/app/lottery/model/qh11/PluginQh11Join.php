<?php
namespace app\lottery\model\qh11;

use app\lottery\model\common\BaseJoin;

class PluginQh11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qh11';
    }
}