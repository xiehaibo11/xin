<?php
namespace app\lottery\model\ynssc;

use app\lottery\model\common\BaseExpect;

class PluginYnsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ynssc';
    }
}