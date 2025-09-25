<?php
namespace app\lottery\model\azssc;

use app\lottery\model\common\BaseExpect;

class PluginAzsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'azssc';
    }
}