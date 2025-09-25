<?php
namespace app\lottery\model\ssc;

use app\lottery\model\common\BaseExpect;

class PluginSscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ssc';
    }
}