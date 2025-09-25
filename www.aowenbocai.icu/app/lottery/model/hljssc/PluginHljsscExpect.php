<?php
namespace app\lottery\model\hljssc;

use app\lottery\model\common\BaseExpect;

class PluginHljsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hljssc';
    }
}