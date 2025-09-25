<?php
namespace app\lottery\model\yn11;

use app\lottery\model\common\BaseExpect;

class PluginYn11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'yn11';
    }
}