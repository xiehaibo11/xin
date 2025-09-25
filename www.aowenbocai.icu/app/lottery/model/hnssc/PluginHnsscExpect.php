<?php
namespace app\lottery\model\hnssc;

use app\lottery\model\common\BaseExpect;

class PluginHnsscExpect extends BaseExpect
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnssc';
    }

}