<?php
namespace app\lottery\model\nx11;

use app\lottery\model\common\BaseJoin;

class PluginNx11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nx11';
    }
}