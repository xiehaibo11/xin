<?php
namespace app\common\model;

use core\Setting;
use think\Model;
use app\index\model\User;
use app\admin\model\ExtShowList;

class GameMoneyHistory extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';


    /**
     * 获取购买信息
     */
    public function getShopData()
    {
        $setting = Setting::get(['game_money_recharge']);
        if($setting['game_money_recharge']){
            $game_money_recharge = json_decode($setting['game_money_recharge'],true);
        }
        return $game_money_recharge;
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['username'];
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['nickname'];
    }
    
    /**
     *获取器--获取模块 
     */
    public function getExtNameAttr($value, $data)
    {
        $ext = ltrim($data['ext_name'], '/');
        $ext = [$ext,'/'.$ext];
        $res = (new ExtShowList)->where('name', 'in', $ext)->column('title');
        $res = empty($res) ? $data['ext_name'] : $res[0];
        return $res;
    }


    /**
     * 获取器 - username
     * @return json
     */
    public function getPhotoAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return $user['photo'];
    }

    /**
     * 获取资金明细中type对应的类型
     */
    public function getTypeNameAttr($value,$data)
    {
        $type = ['消费', '中奖', '充值', '兑换', '赠送'];
        return $type[$data['type']];
    }
    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
    }

    /**
     * 获取充值金额减去1%的手续费
     */
     public function getRechargeAttr($value, $data)
     {
        $money = $data['total'] * 0.99;
        return $money;
     }


    /**
     * 明细生成
     * @param  array  $data
     * @return array
     */
    public function write($data = []) {

        $userid = $data['userid'];
        $user = User::get($userid);
        if (!$user) {
            return ['code' => 0, 'msg' => '用户不存在'];
        }

        //获取操作前后金额
        $data['before'] = $user['game_money'];
        $data['after'] = $data['before'] + $data['money'];

        if ($data['after'] < 0) {
            return ['code' => 0, 'msg' => '用户余额不足'];
        }
        //设置用户金额
        $user->game_money = $data['after'];
        $user->save();
        $data['create_ip'] = request()->ip();
        $data['ext_name'] =  Request()->module();
        $result = $this->validate([
            'userid|用户'  => 'require',
            'money|钻石' => 'number',
            'ext_name|模块名' => 'alphaDash',
            'before' => 'number',
            'after' => 'number',
        ])->insert($data);

        if (false === $result) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '记录添加成功','afterMoney' => $data['after'], 'beforeMoney' => $data['before']];
    }

    public function getList($words = '', $starttime = '', $endtime = '', $pagesize = 14, $where = '')
    {
        $data = request()->get();
        if ($words || !empty($data['words'])) {
            $words = !empty($data['words']) ? $data['words'] : !$words;
            $userid =(new User)->where('username|nickname', 'like', "%$words%")->column('id');

            $this->where(function($query) use ($words, $userid){
                $query->whereOr('userid', 'in', $userid)
                ->whereOr('create_ip|remark', 'like', "%{$words}%");
            });
        }
        if ($starttime|| !empty($data['starttime'])) {
            $starttime = !empty($data['starttime']) ? $data['starttime'] : !$starttime;
            $this->where('create_time', '>=', $starttime);
        }
        if ($endtime || !empty($data['endtime'])) {
            $endtime = !empty($data['endtime']) ? $data['endtime'] : !$endtime;
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->where('create_time', '<', $endtime);
        }
        if(!empty($data['ext'])){
            $ext = ltrim($data['ext'],'/');
            $ext = [$ext, '/'.$ext];
            $this->where('ext_name', 'in' ,$ext);
        }
        if($where){
            $this->where($where);
        }

        $list = $this->order("id desc")->paginate($pagesize, false, ['query' => request()->get()]);
        return $list;
    }

}
