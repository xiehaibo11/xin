<?php
namespace app\lottery\model\hljssc;

use app\lottery\model\common\BaseJoin;

class PluginHljsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hljssc';
    }
}