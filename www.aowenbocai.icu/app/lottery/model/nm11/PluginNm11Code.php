<?php
namespace app\lottery\model\nm11;

use app\lottery\model\common\BaseCode;

class PluginNm11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nm11';
    }
}