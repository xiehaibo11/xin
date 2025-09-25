<?php
namespace app\lottery\model\Bjssc;

use app\lottery\model\common\BaseExpect;

class PluginBjsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'Bjssc';
    }
}