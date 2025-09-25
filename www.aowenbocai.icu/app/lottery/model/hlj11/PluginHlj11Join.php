<?php
namespace app\lottery\model\hlj11;

use app\lottery\model\common\BaseJoin;

class PluginHlj11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hlj11';
    }
}