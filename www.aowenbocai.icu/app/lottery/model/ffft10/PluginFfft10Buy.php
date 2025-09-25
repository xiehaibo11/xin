<?php
namespace app\lottery\model\ffft10;

use app\lottery\model\common\BaseBuy;
use app\lottery\model\common\pk10\Pk10BaseBuy;
use app\lottery\model\LotteryCom;

class PluginFfft10Buy extends Pk10BaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'ffft10';
    }

    
}
