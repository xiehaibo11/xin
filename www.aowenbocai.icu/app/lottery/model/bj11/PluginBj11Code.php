<?php
namespace app\lottery\model\Bj11;

use app\lottery\model\common\BaseCode;

class PluginBj11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bj11';
    }
}