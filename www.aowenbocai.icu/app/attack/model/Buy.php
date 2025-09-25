<?php

namespace app\attack\model;

use app\index\model\User;
use think\Db;
use think\Model;
use think\Validate;
use app\common\model\GameGameMoneyHistory;
use app\common\model\UserAction;

class Buy extends Model
{
    protected $name = 'plugin_attack_record';//表名
    protected $resultSetType = 'collection';
    protected $betting_type;
    protected $betting_type_txt;

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
    }

    /**
     * 获取器 - 友好时间
     */
    public function getCreateTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }

    public function getPlayInfoAttr($value, $data)
    {
        $user = new User;
        $enemy = $user->where('id', $data['enemy_userid'])->column('nickname');
        $_data['nickname'] = empty($enemy) ? '该用户不存在' : $enemy[0];
        return ($_data);
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
     *  获取投注剩余时间
     * @return json
     */
    public function getSurplusTime($create_time)
    {
        $now_timestamp = time();//当前日期和时间时间戳
        $my_time = strtotime($create_time);//当前日期时间戳
        return 30 - ($now_timestamp - $my_time);
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
     * 获取器 - 对手username
     * @return json
     */
    public function getEnemyUsernameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['enemy_userid']);
        return $user_info['username'];
    }

    /**
     * 获取器 - status_text
     */
    public function getStatusTextAttr($value, $data)
    {
        $res = $this->award($data['betting'], $data['enemy_betting']);
        $status = [0 => '败北', 1 => '胜利', 2 => '打平'];
        return $status[$res['win_status']];
    }

    /**
     * 获取器 - win_status
     */
    public function getWinStatus($data)
    {
        $status = [0 => '败北', 1 => '胜利', 2 => '打平'];
        $user = User::where('sid',session('sid'))->find();
        if ($user['id'] == $data['userid']) {
            $res = $this->award($data['betting'], $data['enemy_betting']);
            return $status[$res['win_status']];
        } else  if ($user['id'] == $data['enemy_userid']) {
            $res = $this->award($data['enemy_betting'], $data['betting']);
            return $status[$res['win_status']];
        }
        return '';
    }

    /**
     * 双方准备后 判断最小投注  生成对局记录
     */
    public function addReady($room_id, $user_id)
    {
        $money = 0;
        $room_info = Room::get($room_id);
        $room_all_user =  (new RoomUser())->where('room_id', $room_id)->select();
        foreach ($room_all_user as $v) {
            if ($v['ready'] != 1) {
                return ["code" => 1, "msg" => '会员已准备或已生成对局'];
            }
            if (!$money) {
                $money = $v['ready_money'];
            } elseif ($money > $v['ready_money'] and $money > 0) {
                $money = $v['ready_money'];
            }
            if ($room_info['create_user'] == $v['userid']) {
                $data['userid'] = $v['userid'];
            } else {
                $data['enemy_userid'] = $v['userid'];
            }
        }
        $checkAgents = (new \app\admin\model\User())->checkAgents($data['userid'], $data['enemy_userid']);
        if (!$checkAgents['code']) {
            return ["code" => 0, "msg" => '您没有权限进行投注'];
        }
        foreach ($room_all_user as $v) {
            $money_mx = [
                'userid' => $v['userid'],
                'money' => -$money,
                'ext_name' => Request()->module()
            ];
            $res_mx = (new GameMoneyHistory)->write($money_mx);//添加资金明细
            if (!$res_mx['code']) {
                return ["code" => 0, "msg" => $res_mx['msg']];
            }
        }
        $data['status'] = 0;
        $data['money'] = $money;
        $res = $this->allowField(['userid', 'money', 'status', 'enemy_userid', 'create_time', 'update_time'])->save($data);
        (new RoomUser())->where('room_id', $room_id)->update(['ready' => 2, 'record_id' => $this->id]);
        if (!$res) {
            return ['code' => 0, 'msg' => '生成对局失败'];
        }
        return ['code' => 1, 'msg' => '双方已准备,请出兵', 'money' => $money];

    }

    /**
     * 添加投注号码
     */
    public function add($data, $userid)
    {
        $validate = new Validate([
            'betting|投注号码' => 'require|number'
        ]);
        $add_data = [];
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        //验证投注号码
        if (!in_array($data['betting'], $this->betting_type)) {
            return ["code" => 0, "msg" => '投注参数错误'];
        }
        $room_user_info = RoomUser::where('userid', $userid)->find();
        if (empty($room_user_info)) {
            return ["code" => 0, "msg" => '非法操作'];
        }
        $betting_info = $this->find($room_user_info['record_id']);
        if (empty($betting_info)) return ["code" => 0, "msg" => '对局不存在'];
        if ($betting_info['userid'] == $userid) {
            if ($betting_info['betting']) {
                return ["code" => 0, "msg" => '已经出兵'];
            }
            $add_data['betting'] = $data['betting'];
        } else {
            if ($betting_info['enemy_betting']) {
                return ["code" => 0, "msg" => '已经出兵'];
            }
            $add_data['enemy_betting'] = $data['betting'];
        }

        $res = $this->allowField(['betting','enemy_betting', 'update_time'])->save($add_data, ['id' => $betting_info['id']]);
        if (!$res) {
            return ['code' => 0, 'msg' => '数据执行失败'];
        }

        return ['code' => 1];
    }

    /**
     * 判断胜负
     */
    public function award($betting, $enemy_betting)
    {
        $code = $betting - $enemy_betting;
        if ( $code == -1 || $code == 2 ) {//胜利
            $status = 1;
        } else if ( $code == 1 || $code == -2 ) {//败北
            $status = 0;
        } else {//平
            $status = 2;
        }
        return ['win_status' => $status, 'enemy_betting' => $enemy_betting];
    }

    /**
     * 开奖
     */
    public function openAward($betting_info)
    {
        if ($betting_info['status'] == 1) {
            return ['code' => 1, 'bonus' => $betting_info['bonus']];
        } elseif ($betting_info['status'] == 2) {
            return ['code' => 2];
        }
        $bonus = $betting_info['money'] * 2;
        $betting = $betting_info->getData('betting');
        $enemy_betting = $betting_info->getData('enemy_betting');
        $surplusTime = $this->getSurplusTime($betting_info->getData('create_time'));
        $win_userid = 0;

        if (!$betting and $enemy_betting) {
            if ($surplusTime > 0) {
                return ['code' => 0];
            }
            $win_userid = $betting_info['enemy_userid'];
        } elseif ($betting and !$enemy_betting) {
            if ($surplusTime > 0) {
                return ['code' => 0];
            }
            $win_userid = $betting_info['userid'];
        } elseif (!$betting and !$enemy_betting) {//双方未出兵 返回资金
            $this->back_money($betting_info);
            return ['code' => 2];
        } else{
            $award = $this->award($betting_info->getData('betting'), $betting_info->getData('enemy_betting'));
            switch ($award['win_status']) {
                case 0:
                    $win_userid = $betting_info['enemy_userid'];
                    break;
                case 1:
                    $win_userid = $betting_info['userid'];
                    break;
                case 2://打平 返回资金
                    $this->back_money($betting_info);
                    break;
            }
        }
        if ($win_userid) {
            $money_mx = [
                'userid' => $win_userid,
                'money' => $bonus,
                'type' => 1,
                'ext_name' => Request()->module()
            ];
            $res_mx = (new GameMoneyHistory)->write($money_mx);//添加资金明细
            if (!$res_mx['code']) {
                return ['code' => 0, 'msg' => '派奖未成功'];
            }
        }
        $res = $this->save(['status' => 1, 'bonus' => $bonus], ['id' => $betting_info['id']]);
        if ($res) {
            return ['code' => 1, 'bonus' => $bonus];
        }

        return ['code' => 0];
    }

    /**
     * 返还
     */
    public function back_money($betting_info)
    {
        $user_array = [
            $betting_info['userid'],
            $betting_info['enemy_userid']
        ];
        foreach ($user_array as $v) {
            $money_mx = [
                'userid' => $v,
                'money' => $betting_info['money'],
                'ext_name' => Request()->module(),
                'remark' => '欢乐攻城返还'
            ];
            (new GameMoneyHistory)->write($money_mx);//添加资金明细
        }
        $this->save(['status' => 2], ['id' => $betting_info['id']]);
    }

    /**
     * 检测是否有未完成的对局
     */
    public function checkBuy($userid)
    {
        $betting_list = $this
            ->where('(status = 0 and userid = '. $userid . ') or  (status = 0 and enemy_userid = ' . $userid . ')')
            ->select();
        if (!empty($betting_list)) {
            foreach ($betting_list as $v) {
                $this->openAward($v);
            }
        }
    }

}
