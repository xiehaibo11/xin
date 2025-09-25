<?php
namespace app\zhuawawa\controller;

use app\common\model\GameMoneyHistory;
use app\common\model\UserBag;
use app\index\controller\Base;
use app\zhuawawa\model\PluginZhuawawa as PZ;
use app\zhuawawa\model\PluginZhuawawaSetting as PZS;

class Index extends Base
{

    public function index()
    { 
        return $this->fetch('index', ['title' => '娃娃机']);
    }

    /**进入获取初始化信息 */
    public function getInfo()
    {
        $setting = \core\Setting::get(['free_coupon_coin']);
        $coupon = (new UserBag())->where('param_name', 'zhuawawa_coupon')->where('userid', $this->user['id'])->find();
        $data = [
            'code' => 1,
            'data' => [
                'id' => $this->user['id'],
                'money' => $this->user['money'],
                'nickname' => $this->user['nickname'],
                'photo' => $this->user['photo'],
                'coupon' => empty($coupon) ? 0 : $coupon['num'],
                'coupon_coin' => $setting['free_coupon_coin']
            ],
        ];
        return json($data);
    }

    /**
     * 获取奖品列表
     * @param init $level 投入金币数
     */
    public function getList($level = 200)
    {
        $list = [];
        $PZS = new PZS;
        $list[200]   = $PZS->getGiftList(200);
        $list[500]   = $PZS->getGiftList(500);
        $list[1000]  = $PZS->getGiftList(1000);
        $list[2000]  = $PZS->getGiftList(2000);
        $list[5000]  = $PZS->getGiftList(5000);
        $list[10000] = $PZS->getGiftList(10000);
        
        return json(['err' => 0, 'data' => $list]);
    }

    /**
     * 抓娃娃判断中奖否
     * @param int $id 奖品id
     */
    public function zhua($id)
    {
        $info = PZS::get($id);
        if (!$info) {
            return json(['err' => 1]);
        }
        $GameMoneyHistory = new GameMoneyHistory;
        $zhuawawa_coupon = (new UserBag())->where('userid', $this->user['id'])->where('param_name', 'zhuawawa_coupon')->find();
        if (!empty($zhuawawa_coupon) and $zhuawawa_coupon['num']) {
            if ($info['bet_money'] != 200) {
                return json(['err' => 1, 'msg' => '参数不对']);
            }
            $setting = \core\Setting::get(['free_coupon_coin']);
            $res = (new UserBag())->where('userid', $this->user['id'])->where('param_name', 'zhuawawa_coupon')->setDec('num');
            if (!$res) {
                return json(['err' => 1, 'msg' => '数据操作失败']);
            }
            $mark = '使用免费券抓娃娃';
            $user_money = $this->user['money'];
        } else {
            $money_res = $GameMoneyHistory->write([
                'money' => -$info['bet_money'],
                'userid' => $this->user['id'],
                'ext_name' => 'zhuawawa',
                'remark' => '抓娃娃'
            ]);
            if ($money_res['code'] == 0) {
                return json(['err' => 1, 'msg' => $money_res['msg']]);
            }
            $mark = '抓娃娃_中奖';
            $user_money = $money_res['afterMoney'];
        }

        $his = new PZ;
        $his->userid = $this->user['id'];
        $his->gift_id = $id;        
        if ($this->isHit($info['hit'])) {
            $his->is_get = 1;
            $gift = json_decode($info['gift'], true);
            if ($gift['type'] == 'coin') {
                $money_award = $GameMoneyHistory->write([
                    'money' => $gift['value'],
                    'type' => 1,
                    'userid' => $this->user['id'],
                    'ext_name' => 'zhuawawa',
                    'remark' => $mark
                ]);
                $user_money = $money_award['afterMoney'];
            }
            $his->save();            
            return json(['err' => 0, 'isHit' => 1, 'msg' => "恭喜获得[{$info['title']}]！", 'afterMoney' => $user_money]);
        }
        $his->save();
        return json(['err' => 0, 'isHit' => 0, 'afterMoney' => $user_money]);
    }

    // 中奖难度 越高越难
    private $maxRand = 1000;

    /**
     * 中间判断
     * @param int $hit 命中率
     */
    private function isHit($hit)
    {
        $randNum = mt_rand(1, $this->maxRand);
        if ($randNum <= $hit) {
            return true;
        }
        return;
    }

    /**
     * 获取历史抓取记录
     */
    public function getHisList()
    {
        $PZ = new PZ;
        $res = $PZ->hisList($this->user['id'])->toArray();
        $res['err'] = 0;
        return json($res);
    }
}