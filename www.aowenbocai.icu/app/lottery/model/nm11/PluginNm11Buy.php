<?php
namespace app\lottery\model\nm11;

use app\lottery\model\common\syxw\SyxwBaseBuy;

class PluginNm11Buy extends SyxwBaseBuy
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nm11';
    }

}