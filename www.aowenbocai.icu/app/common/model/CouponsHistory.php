<?php
namespace app\common\model;

use think\Model;
use app\index\model\User;
use app\admin\model\ExtShowList;

class CouponsHistory extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

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
        $data['before'] = $user['coupons'];
        $data['after'] = $data['before'] + $data['money'];

        if ($data['after'] < 0) {
            return ['code' => 0, 'msg' => '用户余额不足'];
        }
        //设置用户金额
        $user->coupons = $data['after'];
        $user->save();
        $data['create_ip'] = request()->ip();
        $result = $this->validate([
            'userid|用户'  => 'require',
            'money|点券' => 'number',
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

        if($where){
            $this->where($where);
        }

        $list = $this->order("id desc")->paginate($pagesize, false, ['query' => request()->get()]);
        return $list;
    }

    /**
     * 资金明细变化总数
     * @param array $where 内包含所有用户的资金明细变化总数
     */
    public function getSum($where){
        return  $this->field('Sum(money) as total')->where($where)->find();
     }
      /**
     * 资金明细
     * @param array $where 内包含所有用户的资金明细变化总数
     */
    public function agentList($where){
        return  $this->field('userid,money,remark,create_time,type')->where($where)->order("id desc")->paginate(14);
    }

    /**
     * 获取战绩排行数据 
     *@param array $where 查询条件
     *@param int $pagesize 每页显示数量
     *@retrun  
     */
    public function listSort($where = [], $pagesize = 10)
    {
        return $this->field("userid, SUM(money) as money")->where($where)->where('type = 1 or type =4')->having('money > 0')->group("userid")->order('money desc, userid asc')->paginate($pagesize,100);
    }

    /**获取自己的排行数 */
    public function getSelfRanking($userid)
    {
        $info =  $this->field("SUM(money) as money")->where(['create_time' => ['>=' ,date("Y-m-d")], 'userid' => $userid])->where('type = 1 or type =4')->find()->toarray();
        $money = 0;
        if($info['money']){
            $money = $info['money'];
        }
        $count = $this->field("SUM(money) as money")->where(['create_time' => ['>=' ,date("Y-m-d")]])->where('type = 1 or type =4')->having('money > ' .$money)->group("userid")->count();
        return ['sort' => $count + 1, 'money'=> $money];
    }

    /**获取总的统计数据 */
    public function getStaisc($userid = '', $timeway = '')
    {
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'give'];
        $data = [];
        foreach ($typeToStr as $key => $value) {
            $data[$value] = $this->getMoneySumByType($key, $userid, $timeway) ?? 0;
        }
        return $data;
    }

    public function getMoneySumByType($type = '', $userid = '',$timeway)
    {
        $data = request()->param();
        if($timeway){
            $this->where(['create_time' => $timeway]);
        }
        if (!empty($data['starttime'])) {
            $this->where('create_time', '>=', $data['starttime']);
        }

        if (!empty($data['endtime'])) {
            $this->where('create_time', '<=', $data['endtime']." 23:59:59");
        }

        if (!empty($userid)) {
            if (is_array($userid)) {
                $this->where('userid', 'in', $userid);
            } else {
                $this->where('userid', $userid);
            }
        }
        $res = $this->where('type', $type)->sum('money');
        if($type == 0 || $type == 3){
            $res = -$res;
        }
        return $res;
    }

    
    /**获取统计数据 */
    public function getNewStaisc()
    {
        // $userid = (new User)->field('id')->select()->toArray();
        $userid = (new User)->column("id");
        foreach($userid as $value){
            $res= $this->getMoneySumByUserid($value);
        }
        return ['err' => 0];
    }

    public function getMoneySumByUserid($userid)
    {   
        $time = date("Y-m-d");
        $typeToStr = ['spend', 'award', 'recharge', 'change', 'send'];

        $data = [];
        $data['userid'] = $userid;
        $data['statis_time'] = $time;

        $statis = new Statis;
        $last = $statis->where("userid", $userid)->order("id DESC")->find();
        /**查询统计表中数据是否有 */
        if(!empty($last)){
            $last = $last->toArray();
            /**判断统计时间是否是当天 */
            if($last['statis_time'] == $time.' 00:00:00'){
                return false;
            }
        }
        $after = $this->where('userid', $userid)->where('create_time', "<", date("Y-m-d"))->order("create_time DESC")->limit(1)->column('after');
        $after = empty($after) ? 0 : $after[0];
        $data['money'] = $after;
        foreach ($typeToStr as $key => $value) {
            $this->where('userid', $userid);
            $this->where('create_time', '<', $time);
            if(!empty($last)){
                $this->where('create_time', '>=', $last['statis_time']);
            }
            $res = $this->where('type', $key)->sum('money');
            if($key == 0 || $key == 3){
                $res = -$res;
            }
            if(!empty($last)){
                $res += intval($last[$value]);
            }
            $data[$value] = $res ?? 0;
        }
        /**计算盈利 */
        $data['gain'] = $data['recharge'] * 0.99 - $after - $data['change'];
        $data['gain'] = round($data['gain'], 2);
        $statis->allowField(true)->save($data);
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $type
     * @param string $ext
     * @return float|int
     */
    public function getSumMoney($start = '', $end = '', $type = '', $ext = '')
    {
        if(!empty($start)){
            $this->where('create_time', '>=', $start);
        } 
        if(!empty($end)){
            $this->where('create_time', '<=', $end." 23:59:59");
        }
        if(!empty($type)){
            $type = $type == 6 ? 0 : $type;
            $this->where('type',$type);
        }
        return  $this->sum("money");
        
    }
}
