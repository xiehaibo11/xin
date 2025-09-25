<?php
namespace app\lottery\model\mnlssc;

use app\lottery\model\common\BaseExpect;

class PluginMnlsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'mnlssc';
    }
}