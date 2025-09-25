<?php
namespace app\lottery\model\js11;

use app\lottery\model\common\BaseJoin;

class PluginJs11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js11';
    }
}