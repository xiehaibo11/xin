<?php
namespace app\lottery\model\qqssc;

use app\lottery\model\common\BaseExpect;

class PluginQqsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qqssc';
    }
}