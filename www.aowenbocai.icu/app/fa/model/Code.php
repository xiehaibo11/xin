<?php

namespace app\fa\model;

use app\common\model\GameMoneyHistory;
use app\common\model\MoneyHistory;
use app\common\model\UserAction;
use app\index\model\User;
use think\Db;
use think\Model;
use think\Validate;

class Code extends Model
{
    protected $name = 'plugin_fa_code';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 获取最新一期
     */
    public function getLastCode()
    {
        $id = $this->max('id');
        $res = $this->find($id);
        return $res->toArray();

    }

    /**
     * 获取历史期数
     */
    public function getHistoryCode()
    {
        $res = $this->order('id desc')->limit(0, 14)->select();
        return $res;
    }

    /**
     * 编辑开奖号
     */
    public function edit($data)
    {
        $validate = new Validate([
            'expect|期数' => 'require',
            'code|开奖号码' => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $res = $this->update($data);//记录开奖号码

        if (!$res) {
            return ["code" => 0];
        }
        return ["code" => 1];
    }

    /**
     * 开奖号码入库
     * $no_send  存在时  只添加开奖号码  不派奖
     */
    public function add($data, $no_send = '')
    {
        $validate = new Validate([
            'expect|期数' => 'require',
            'code|开奖号码' => 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }

        $res = $this->insert($data);//记录开奖号码
        if (!$res) return ['code' => 0, 'msg' => '开奖号码保存失败'];
        if ($no_send) return ['code' => 1];
        return $this->sendBonus($data['expect']);
    }

    /**
     * 派奖
     */

    public function sendBonus($expect)
    {
        $code = $this->where('expect', $expect)->find();
        $buy = (new Buy())->where('expect', $expect)->select();
        if ($expect and !empty($buy) and !empty($code)) {
            foreach ($buy as $v) {
                if($v['status'] != 0) {
                    return ['code' => 0];
                }
                if ($code['code'] > 6) {
                    $win_code = 1;
                    $rate = 1.4;
                } elseif ($code['code'] < 6) {
                    $win_code = 2;
                    $rate = 1.4;
                } elseif ($code['code'] == 6) {
                    $win_code = 6;
                    $rate = 6.5;
                }
                $v['code'] = json_decode($v['code'], true);
                if (isset($v['code'][$win_code]) and $v['code'][$win_code]) {//判断是否中奖
                    $winning_money = $rate * $v['code'][$win_code];
                    (new Buy())->where('id', $v['id'])->update([
                        'bonus' => $winning_money,
                        'status' => 1
                    ]);
                    (new GameMoneyHistory())->write([
                        'userid' => $v['userid'],
                        'money' => $winning_money,
                        'type' => 1,
                        'ext_name' => Request()->module(),
                        'remark' => '中奖编号为CDX-' . $v['id'] . ''
                    ]);
                    (new UserAction())->write([
                        'userid' => $v['userid'],
                        'content' => '投注猜大小，中奖' . $winning_money . '金豆',
                        'ext_name' => Request()->module()
                    ]);//添加会员活动
                } else {
                    (new Buy())->where('id', $v['id'])->update([
                        'status' => 2
                    ]);
                }
            }
            return ['code' => 1];
        }
    }


    /**
     * 撤单
     * $admin_do 管理员操作
     */
    public function backMoney($id='')
    {
        $buy = (new Buy)->find($id);
        (new GameMoneyHistory())->write([
            'userid' => $buy['userid'],
            'money' => $buy['money'],
            'ext_name' => Request()->module(),
            'remark' => '系统撤单，编号为CDX-' . $buy['id'] . ''
        ]);
        $res = (new Buy())->where('id', $buy['id'])->update([
            'status' => 7
        ]);
        if ($res) {
            return ['code' => 1];
        }
        return ['code' => 0];

    }

    /**
     * 开奖验证是否中奖
     * code  0 未参与  1中奖 2未中奖
     * @return array
     */
    public function isWinning($expect, $userid)
    {
        $buy = (new Buy)->where('expect', $expect)->where('userid', $userid)->select();
        if ($buy->isEmpty()) {
            return ['code' => 0];
        }
        $bonus = 0;
        foreach ($buy as $v) {
            if ($v['status'] == 1) {//判断是否中奖
                $bonus += $v['bonus'];
            }
        }
        if ($bonus) {
            return ['code' => 1, 'bonus' => $bonus];
        } else {
            return ['code' => 2];
        }

    }


}
