<?php
namespace app\lottery\model\fjks;

use app\lottery\model\common\BaseExpect;

class PluginFjksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fjks';
    }
}