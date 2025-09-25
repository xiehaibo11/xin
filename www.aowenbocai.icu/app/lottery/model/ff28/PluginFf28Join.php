<?php
namespace app\lottery\model\ff28;

use app\lottery\model\common\BaseJoin;

class PluginFf28Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ff28';
    }
}