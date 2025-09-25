<?php
namespace app\lottery\model\sh11;

use app\lottery\model\common\BaseJoin;

class PluginSh11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sh11';
    }
}