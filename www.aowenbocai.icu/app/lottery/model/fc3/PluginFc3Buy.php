<?php
namespace app\lottery\model\Fc3;

use app\lottery\model\common\fc3\Fc3BaseBuy;

class PluginFc3Buy extends Fc3BaseBuy
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'fc3';
    }
    

}