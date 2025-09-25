<?php
namespace app\lottery\model\Bj11;

use app\lottery\model\common\BaseExpect;

class PluginBj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bj11';
    }
}