<?php
namespace app\index\controller;

use app\common\model\MoneyHistory;
use app\common\controller\Extend;

class Detail extends Base
{
    public function index()
    {
        return $this->fetch('index',['title' => '账户明细']);
    }

    public function list()
    {
        $MoneyHistory = new MoneyHistory;
        $list = $MoneyHistory->where('userid', $this->user['id'])->order('id desc')->paginate(15);
        $list->append(['friend_time']);
        foreach ($list as &$item) {
            $ext_info = Extend::getInfo($item['ext_name']);
            $item['ext_info'] = [
                'logo' => $ext_info['logo'],
                'title' => $ext_info['title']
            ];
        }
        return $list;
    }
}
