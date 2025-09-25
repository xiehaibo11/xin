<?php
namespace app\lottery\model\jx11;

use app\lottery\model\common\BaseJoin;

class PluginJx11Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jx11';
    }
}