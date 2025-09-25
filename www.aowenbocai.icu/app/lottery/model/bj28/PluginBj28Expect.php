<?php
namespace app\lottery\model\bj28;

use app\lottery\model\common\BaseExpect;

class PluginBj28Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'bj28';
    }
}