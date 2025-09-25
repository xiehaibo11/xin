<?php
namespace app\lottery\model\jisks;

use app\lottery\model\common\BaseCode;

class PluginJisksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'jisks';
    }
}