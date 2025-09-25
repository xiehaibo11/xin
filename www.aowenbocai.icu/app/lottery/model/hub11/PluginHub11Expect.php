<?php
namespace app\lottery\model\hub11;

use app\lottery\model\common\BaseExpect;

class PluginHub11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hub11';
    }
}