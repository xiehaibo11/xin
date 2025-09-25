<?php
namespace app\lottery\model\qhks;

use app\lottery\model\common\BaseExpect;

class PluginQhksExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'qhks';
    }
}