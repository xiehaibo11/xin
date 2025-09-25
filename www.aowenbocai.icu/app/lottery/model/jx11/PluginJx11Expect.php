<?php
namespace app\lottery\model\jx11;

use app\lottery\model\common\BaseExpect;

class PluginJx11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jx11';
    }
}