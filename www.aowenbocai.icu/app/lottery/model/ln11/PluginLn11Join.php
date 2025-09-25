<?php
namespace app\lottery\model\ln11;

use app\lottery\model\common\BaseJoin;

class PluginLn11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ln11';
    }
}