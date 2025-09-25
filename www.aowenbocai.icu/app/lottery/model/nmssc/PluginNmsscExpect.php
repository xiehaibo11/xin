<?php
namespace app\lottery\model\nmssc;

use app\lottery\model\common\BaseExpect;

class PluginNmsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmssc';
    }
}