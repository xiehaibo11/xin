<?php
namespace app\lottery\model\qhks;

use app\lottery\model\common\BaseCode;

class PluginQhksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qhks';
    }
}