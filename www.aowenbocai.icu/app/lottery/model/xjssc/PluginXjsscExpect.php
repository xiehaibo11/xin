<?php
namespace app\lottery\model\xjssc;

use app\lottery\model\common\BaseExpect;

class PluginXjsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xjssc';
    }
}