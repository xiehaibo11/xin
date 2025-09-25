<?php
namespace app\lottery\model\nm11;

use app\lottery\model\common\BaseExpect;

class PluginNm11Expect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'nm11';
    }
}