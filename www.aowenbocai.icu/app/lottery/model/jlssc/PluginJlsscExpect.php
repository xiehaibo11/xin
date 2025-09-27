<?php
namespace app\lottery\model\jlssc;

use app\lottery\model\common\BaseExpect;

class PluginJlsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jlssc';
    }
}