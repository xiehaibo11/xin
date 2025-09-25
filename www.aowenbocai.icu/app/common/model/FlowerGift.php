<?php
namespace app\common\model;

use think\Model;
use think\Validate;
use app\index\model\User;

class FlowerGift extends Model
{

    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器--获取赠送鲜花人信息
     */
    public function getSendPersonAttr($value, $data)
    {
        $user = (new User)->get($data['userid']);
        if($user){
            return ['username' => $user['username'], 'nickname' => $user['nickname']];
        }else{
            return ['username' => '用户信息出错', 'nickname' => '或会员不存在'];
        }
    }

    /**
     * 获取器--获取赠送鲜花人信息
     */
    public function getGetPersonAttr($value, $data)
    {
        $user = (new User)->get($data['gift_userid']);
        if($user){
            return ['username' => $user['username'], 'nickname' => $user['nickname']];
        }else{
            return ['username' => '用户信息出错', 'nickname' => '或会员不存在'];
        }
    }


    /**
     * 获取赠送记录
     */
    public function getList($data = [], $where = [], $pagesize = 14)
    {
        $paginate = [];
        $MoneyHistory = new MoneyHistory;

        if (!empty($data['words'])) {
            $words = $data['words'];
            $userid = (new User)->where('username|nickname', 'like', "%$words%")->column('id');
            $this->where('userid|gift_userid', 'in', $userid);
        }

        if (!empty($data['starttime'])) {
            $this->where('create_time', '>=', $data['starttime']);
        }
        if (!empty($data['endtime'])) {
            $this->where('create_time', '<=', $data['endtime']." 23:59:59");
        }

        if(!empty($where)){
            $this->where($where);
        }

        $list = $this->order("id desc")->paginate($pagesize, false, $paginate);
        return $list;
    }


    /**
     * 增加赠送记录
     * @param array $data 
     * $data = [
     *  'userid' => '赠送人id’,
     *  'gift_userid' => '被赠送人id’,
     *  'num' => '赠送数量,
     * ]
     */
    public function addGift($data)
    {
        $rules = [
            'userid' => 'require|number|gt:0',
            'gift_userid' => 'require|number|gt:0',
            'num' => 'require|number',
        ];
        $validate = new Validate($rules);

        $result = $validate->check($data);
        if (!$result) {
            return ["err" => 1, "msg" => $validate->getError()];
        }
        $res = $this->allowField(true)->insert($data);
        if($res){
            // 获取赠送与被赠送人信息
            $user = new User;
            $sendUser = $user->get($data['userid'])->toArray();
            $sendUserName = $sendUser['nickname'];

            $getUser = $user->get($data['gift_userid'])->toArray();
            $getUserName = $getUser['nickname'];

            $flower = new FlowerHistory;
            /**赠送人的资金明细 */
            $history = [
                'userid' => $data['userid'],
                'money' => -$data['num'],
                'remark' => '赠送鲜花给用户'.$getUserName,
            ];
            $result = $flower->write($history);
            if(!$result){
                return ['err' => '3', 'msg' => '鲜花明细写入失败'];
            }

            /**被赠送人的资金明细 */
            $history = [
                'userid' => $data['gift_userid'],
                'money' => $data['num'],
                'remark' => '用户'.$sendUserName.'赠送的鲜花',
            ];
            $result = $flower->write($history);
            if(!$result){
                return ['err' => '3', 'msg' => '鲜花明细写入失败'];
            }

            /**系统通知被赠送的用户赠送消息 */
            $sendInfo = [
                'content'  => '用户<a href = "/index/user/ushow?userid='.$data['userid'].'">【'.$sendUserName.'】</a>给您赠送了【'.$data['num'].'】支鲜花',
                'userid' => $data['gift_userid']
            ];
            (new Inform)->addData($sendInfo);

            return ['err' => 0, 'msg' => '赠送成功'];
        }
        return ['err' => 2, 'msg' => '赠送记录写入失败'];
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
