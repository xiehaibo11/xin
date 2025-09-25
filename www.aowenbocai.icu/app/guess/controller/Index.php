<?php
namespace app\guess\controller;

use app\admin\model\User;
use app\index\controller\Base;
use \app\guess\model\Guess as AGuess;
use \app\guess\model\GuessBuy;
use think\Db;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('index', ['title' => '趣味竞猜']);
    }
    public function order()
    {
        return $this->fetch('order', ['title' => '我的竞猜']);
    }

    /**
     * 获取竞猜
     */
    public function getGuess()
    {
        $pageSize = 10;
        $Model = (new AGuess);
        $list = $Model->field('id, logo as img, title, open_time as end_time, basis , options ')->where('status', 1)->whereTime('end_time','>',date('Y-m-d H:i:s'))->order(['start_time' => 'desc'])->paginate($pageSize);
        $data_list = [];
        if (!$list->isEmpty()) $data_list = $list->append(['plan'])->hidden(['options'])->toArray();
        if (empty($data_list)) {
            return json(['err' => 1, 'data' => []]);
        } else {
            return json(['err' => 0, 'data' => $data_list, 'money' => $this->user['money']]);
        }
    }

    /**
     * 投注
     */
    public function addBetting()
    {
        if ($this->request->isPost()) {
            $data = request()->post();
            $Model = (new GuessBuy);
            $data['guess_id'] = $data['id'];
            $data['code'] = $data['cont'];
            unset($data['id']);
            unset($data['cont']);
            $data['userid'] = $this->user['id'];
            $res = $Model->addData($data);
            if ($res['err']) {
                return json(['err' => 1, 'msg' => $res['msg']]);
            }
            return json(['err' => 0, 'msg' => '投注成功']);
        }
    }

    /**
     * 获取记录
     */
    public function getRecord()
    {
        $Model = (new GuessBuy);
        $list = $Model->getRecordList($this->user['id']);
        if (empty($list)) {
            $list['err'] = 1;
            return $list;
        } else {
            $list['err'] = 0;
            return $list;
        }
    }

}
