<?php
namespace app\lottery\model\djssc;

use app\lottery\model\common\BaseExpect;

class PluginDjsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'djssc';
    }
}