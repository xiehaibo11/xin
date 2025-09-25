<?php
namespace app\lottery\model\tjssc;

use app\lottery\model\common\BaseExpect;

class PluginTjsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'tjssc';
    }
}