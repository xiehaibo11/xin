<?php
namespace app\lottery\model\ffft10;

use app\lottery\model\common\BaseExpect;

class PluginFfft10Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffft10';
    }
}
