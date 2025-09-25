<?php
namespace app\lottery\model\shks;

use app\lottery\model\common\BaseExpect;

class PluginShksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'shks';
    }
}