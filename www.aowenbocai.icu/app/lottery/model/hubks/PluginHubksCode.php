<?php
namespace app\lottery\model\hubks;

use app\lottery\model\common\BaseCode;

class PluginHubksCode extends BaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hubks';
    }
}