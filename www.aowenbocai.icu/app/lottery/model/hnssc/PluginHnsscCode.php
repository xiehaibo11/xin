<?php
namespace app\lottery\model\hnssc;

use app\lottery\model\common\ssc\SscBaseCode;

class PluginHnsscCode extends SscBaseCode
{
    protected function initialize()
    {
        parent::initialize();
        $this->ext_name  = 'hnssc';
    }


}