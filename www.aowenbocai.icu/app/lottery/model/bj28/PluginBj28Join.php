<?php
namespace app\lottery\model\bj28;

use app\lottery\model\common\BaseJoin;

class PluginBj28Join extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bj28';
    }
}