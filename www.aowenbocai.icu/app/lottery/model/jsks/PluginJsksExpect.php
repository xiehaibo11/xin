<?php
namespace app\lottery\model\jsks;

use app\lottery\model\common\BaseExpect;

class PluginJsksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsks';
    }
}