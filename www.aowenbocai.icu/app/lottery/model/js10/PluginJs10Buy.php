<?php
namespace app\lottery\model\js10;

use app\lottery\model\common\pk10\Pk10BaseBuy;

class PluginJs10Buy extends Pk10BaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'js10';
    }

    
}
