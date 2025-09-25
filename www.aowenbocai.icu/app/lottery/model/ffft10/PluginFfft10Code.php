<?php
namespace app\lottery\model\ffft10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginFfft10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffft10';
    }

}
