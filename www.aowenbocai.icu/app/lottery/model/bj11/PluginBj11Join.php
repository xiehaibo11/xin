<?php
namespace app\lottery\model\Bj11;

use app\lottery\model\common\BaseJoin;

class PluginBj11Join extends BaseJoin
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bj11';
    }
}