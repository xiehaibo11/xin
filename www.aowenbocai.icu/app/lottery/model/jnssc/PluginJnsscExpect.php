<?php
namespace app\lottery\model\jnssc;

use app\lottery\model\common\BaseExpect;

class PluginJnsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jnssc';
    }
}