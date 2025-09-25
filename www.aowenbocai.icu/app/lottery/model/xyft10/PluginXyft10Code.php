<?php
namespace app\lottery\model\xyft10;

use app\lottery\model\common\pk10\Pk10BaseCode;

class PluginXyft10Code extends Pk10BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'xyft10';
    }


}
