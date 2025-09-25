<?php
namespace app\lottery\model\sd11;

use app\lottery\model\common\BaseJoin;

class PluginSd11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sd11';
    }
}