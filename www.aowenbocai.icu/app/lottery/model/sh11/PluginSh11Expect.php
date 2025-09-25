<?php
namespace app\lottery\model\sh11;

use app\lottery\model\common\BaseExpect;

class PluginSh11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sh11';
    }
}