<?php
namespace app\lottery\model\ahks;

use app\lottery\model\common\BaseExpect;

class PluginAhksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ahks';
    }
}