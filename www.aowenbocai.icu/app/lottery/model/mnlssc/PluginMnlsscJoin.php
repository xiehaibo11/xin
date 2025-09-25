<?php
namespace app\lottery\model\mnlssc;

use app\lottery\model\common\BaseJoin;

class PluginMnlsscJoin extends BaseJoin
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'mnlssc';
    }
}