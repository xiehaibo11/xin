<?php
namespace app\lottery\model\hn11;

use app\lottery\model\common\BaseJoin;

class PluginHn11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hn11';
    }
}