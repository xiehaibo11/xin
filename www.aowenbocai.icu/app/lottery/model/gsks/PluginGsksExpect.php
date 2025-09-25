<?php
namespace app\lottery\model\gsks;

use app\lottery\model\common\BaseExpect;

class PluginGsksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'gsks';
    }
}