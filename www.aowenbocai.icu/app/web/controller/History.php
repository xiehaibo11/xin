<?php
namespace app\web\controller;

use app\common\controller\LotteryHistory;

class History extends LotteryHistory
{
    public function __construct()
    {
        parent::__construct();
        new Base();
    }
}




