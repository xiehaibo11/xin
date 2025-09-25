<?php
namespace app\common\model;

use core\Setting;
use think\Model;
use app\index\model\User;

class FlowerHistory extends Model
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
     * 获取器--获取用户信息
     */
    public function getUserInfoAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['username' => $user['username'], 'nickname' => $user['nickname']];
        }else{
            return ['username' => '用户信息出错', 'nickname' => '或会员不存在'];
        }
    }


    /**
     * 获取器 - 友好时间
     */
    public function getFriendTimeAttr($value,$data)
    {
        return (new \org\FriendlyDate())->getmyDate(strtotime($data['create_time']));
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

        //获取操作前后鲜花数量
        $data['before'] = $user['flower'];
        $data['after'] = $data['before'] + $data['money'];

        if ($data['after'] < 0) {
            return ['code' => 0, 'msg' => '用户鲜花数量不足'];
        }
        //设置用户鲜花数量
        $user->flower = $data['after'];
        $user->save();
        if ($data['type'] == 1) {//充值鲜花时   奖励余额
            $system = (new Setting)->get(['recharge_award']);
            $money_data = [
                'userid' => $data['userid'],
                'money' => $data['money'] * $system['recharge_award'],
                'ext_name' => Request()->module(),
                'remark' => '充值鲜花奖励金豆',
                'type' => 2
            ];
            $res_mx = (new MoneyHistory)->write($money_data);//添加资金明细
        }

        $result = $this->validate([
            'userid|用户'  => 'require',
            'money|鲜花' => 'number',
            'before' => 'number',
            'after' => 'number'
        ])->insert($data);

        if (false === $result) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '记录添加成功'];
    }

    /**
     * 获取列表信息
     */
    public function getList($data = [], $where = [], $pagesize = 14)
    {
        $MoneyHistory = new MoneyHistory;

        if (!empty($data['words'])) {
            $words = $data['words'];
            $userid = (new User)->where('username|nickname', 'like', "%$words%")->column('id');
            $this->where('userid', 'in', $userid);
        }

        if (!empty($data['starttime'])) {
            $this->where('create_time', '>=', $data['starttime']);
        }
        if (!empty($data['endtime'])) {
            $this->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        if (!empty($data['field'])) {
            $this->field($data['field']);
        }

        if(!empty($where)){
            $this->where($where);
        }

        $list = $this->order("id desc")->paginate($pagesize, false, ['query' => request()->get()]);
        return $list;
    }

    /**
     * 资金明细变化总数
     * $where 内包含所有用户的资金明细变化总数
     */
    public function getSum($where){
        return  $this->field('Sum(money) as total')->where($where)->find();
     }

     
     /**
     * 删除数据 
     *  @param int data['id'] 根据数据id删除
     *  @param string data['ids'] id字符串, 删除有id的数据
     *  @param int data['day'] 删除多少天以前的数据 1删除所有
     */
    public function deleteData($data, $userid = '')
    {
        $res = false;
        if($userid != ''){
            $this->where('userid', $userid);
        }
        
        if(isset($data['id'])){
            $res = $this->destroy($data['id']);
        }
        
        if(isset($data['ids'])){
            $res = $this ->where(['id' => ['in', $data['ids']]])->delete();
        }
        
        if(isset($data['day'])){
            $today = strtotime(date("Y-m-d"));
            $start = $data['day'] == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$data['day']." day", $today));
            $res = $this ->where(['create_time' => ['<' , $start]])->delete();
        }
        return $res;
    }

}
