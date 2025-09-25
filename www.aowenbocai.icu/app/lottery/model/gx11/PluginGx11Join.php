<?php
namespace app\lottery\model\gx11;

use app\lottery\model\common\BaseJoin;

class PluginGx11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gx11';
    }
}