<?php
namespace app\dishu\controller;

use app\common\model\MoneyHistory;
use app\index\controller\Base;
use app\dishu\model\GameDishu as PZ;

class Index extends Base
{

    public function index()
    { 
        return $this->fetch('index', ['title' => '疯狂打地鼠']);
    }

    /**
     * 获取历史记录
     */
    public function getHisList()
    {
        $PZ = new PZ;
        $res = $PZ->hisList($this->user['id'])->toArray();
        $res['err'] = 0;
        return json($res);
    }
}