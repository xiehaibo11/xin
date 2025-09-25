<?php
namespace app\lottery\model\zj11;

use app\lottery\model\common\BaseExpect;

class PluginZj11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'zj11';
    }
}