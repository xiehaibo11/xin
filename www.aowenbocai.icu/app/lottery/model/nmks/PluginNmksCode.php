<?php
namespace app\lottery\model\nmks;

use app\lottery\model\common\BaseCode;

class PluginNmksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nmks';
    }
}