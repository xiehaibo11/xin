<?php
namespace app\lottery\model\sxi11;

use app\lottery\model\common\BaseExpect;

class PluginSxi11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'sxi11';
    }
}