<?php
namespace app\lottery\model\jsft10;

use app\lottery\model\common\BaseBuy;
use app\lottery\model\common\pk10\Pk10BaseBuy;
use app\lottery\model\LotteryCom;

class PluginJsft10Buy extends Pk10BaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jsft10';
    }
    
}
