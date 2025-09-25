<?php
namespace app\lottery\model\Heb11;

use app\lottery\model\common\BaseJoin;

class PluginHeb11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Heb11';
    }
}