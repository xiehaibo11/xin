<?php
namespace app\lottery\model\yn11;

use app\lottery\model\common\BaseCode;

class PluginYn11Code extends BaseCode
{

    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'yn11';
    }
}