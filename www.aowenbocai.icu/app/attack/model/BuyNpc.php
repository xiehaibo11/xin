<?php

namespace app\attack\model;

use app\common\model\UserBag;
use app\index\model\User;
use core\Setting;
use think\Db;
use think\Model;
use think\Validate;
use app\common\model\GameMoneyHistory;
use app\common\model\UserAction;

class BuyNpc extends Model
{
    protected $name = 'plugin_attack_npc_record';//表名
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $betting_type;
    protected $betting_type_txt;
    protected $odds;//赔率

    protected function initialize()
    {
        parent::initialize();
        $this->betting_type = ['0','1', '2', '3'];//投注方式   1矛  2盾  3弓
        $this->betting_type_txt = [
            0 => '未出兵',
            1 => '矛',
            2 => '盾',
            3 => '弓',
        ];
        $this->odds = [
            '0' => 0,
            '1' => 1.45,
            '2' => 1.25
        ];
    }
    /**
     * 获取器 - 友好时间
     */
    public function getCreateTimeAttr($value)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($value));
    }

    /**
     * 获取器 - betting
     * @return json
     */
    public function getBettingAttr($value)
    {
        return $this->betting_type_txt[$value];
    }

    /**
     * 获取器 - betting
     * @return json
     */
    public function getEnemyBettingAttr($value)
    {
        return $this->betting_type_txt[$value];
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['nickname'];
    }


    /**
     * 获取器 - status_text
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '败北', 1 => '胜利', 2 => '打平'];
        return $status[$data['status']];
    }

    /**
     * 添加投注号码
     */
    public function add($data)
    {
        $validate = new Validate([
            'userid|会员ID' => 'require',
            'betting|投注号码' => 'require|number',
            'money|投注金额' => 'require|number|max:100'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        //验证投注号码
        if (!in_array($data['betting'], $this->betting_type)) {
            return ["code" => 0, "msg" => '投注参数错误'];
        }
        $attack_coupon = (new UserBag())->where('userid', $data['userid'])->where('param_name', 'attack_coupon')->find();
        if (!empty($attack_coupon) and $attack_coupon['num']) {
            $setting = Setting::get(['free_coupon_coin']);
            (new UserBag())->where('userid', $data['userid'])->where('param_name', 'attack_coupon')->setDec('num');
            $data['money'] = $setting['free_coupon_coin'];
            $data['pay_type'] = 2;
        } else {
            $data['pay_type'] = 1;
            $money_mx = [
                'userid' => $data['userid'],
                'money' => -$data['money'],
                'ext_name' => Request()->module()
            ];
            $res_mx = (new GameMoneyHistory)->write($money_mx);//添加资金明细
            if (!$res_mx['code']) {
                return ["code" => 0, "msg" => $res_mx['msg']];
            }
        }

        //随机结果
        $award = $this->award($data['betting']);
        $data['status'] = $award['status'];
        $data['enemy_betting'] = $award['enemy_betting'];
        $data['bonus'] = $this->odds[$data['status']] * $data['money'];

        $res = $this->allowField(['userid', 'betting', 'money', 'bonus', 'status', 'enemy_betting', 'create_time', 'pay_type'])->save($data);
        if (!$res) {
            return ['code' => 0, 'msg' => '数据执行失败'];
        }

        if ($data['status'] != 0 ) {//不为失败的时候  添加会员活动以及奖金明细
            $money_mx = [
                'userid' => $data['userid'],
                'money' => $data['bonus'],
                'type' => 1,
                'ext_name' => Request()->module()
            ];
            (new GameMoneyHistory)->write($money_mx);//添加资金明细
            (new UserAction)->write([
                'userid' => $data['userid'],
                'content' => '参与攻城，赢得了' . $data['bonus'] . '金豆',
                'ext_name' => Request()->module()
            ]);
        }
        $user = (new User())->find($data['userid']);
        return ['code' => 1, 'bonus' => $data['bonus'], 'enemy_betting' => $data['enemy_betting'], 'status' => $data['status'], 'afterMoney' => $user['money']];
    }

    /**
     * 开奖
     */
    public function award($betting)
    {
        $enemy_betting = rand(1,3);
        $code = $betting - $enemy_betting;

        if ( $code == -1 || $code == 2 ) {//胜利
            $status = 1;
        } else if ( $code == 1 || $code == -2 ) {//败北
            $status = 0;
        } else {//平
            $status = 2;
        }
        return ['status' => $status, 'enemy_betting' => $enemy_betting];
    }

}
