<?php
namespace app\lottery\model\jsft10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginJsft10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsft10';
    }

}
