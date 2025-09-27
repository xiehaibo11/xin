<?php
namespace app\admin\model;

use think\Model;
use think\Validate;
use think\db\Query;
use app\admin\model\Log as ALog;
use app\admin\model\UserIdbank;
use app\common\model\Statis;
use app\common\model\MoneyHistory;

class User extends BaseModel
{
    protected $insert = ['reg_ip'];
    protected $readonly = ['username',"reg_ip"];
    protected $resultSetType = 'collection';
    public $param = [
        'start_time' => '',
        'end_time' => '',
    ];

    protected function initialize()
    {
        parent::initialize();
        $this->status_array = [0 => '正常', 1 => '冻结'];
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value,$data)
    {
        $html  = $value == 1 ?  '<font style="color: red;">' .$this->status_array[$value]. '</font>':'<font style="color: #5cb85c;" >' .$this->status_array[$value]. '</font>';
        return $html;
    }

    /**
     * 修改器 - reg_ip
     * @return json
     */
    protected function setRegIpAttr($value)
    {
        return request()->ip();
    }

    /**
     * 获取器 - Agents
     * @return json
     */
    protected function getAgentsNameAttr($value, $data)
    {
        if (!$data['agents']) return '';
        $user_info = $this->find($data['agents']);
        return $user_info['nickname'] ? $user_info['nickname'] : ($user_info['username'] ? $user_info['username'] : 'id：' . $value);
    }

    /**
     * 获取器 - rebate
     * @return json
     */
    protected function getRebateAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 获取器--获取会员类型
     */
    public function getTypenameAttr($value, $data)
    {
        $type = [0 => '测试会员', 1 => '普通会员', 2 => '代理会员',6 => '发单会员',7 => '跟单会员'];
        return $type[$data['type']];
    }

    /**用户绑定银行卡 */
    public function getUserBankAttr($value, $data)
    {
        $res = (new UserIdbank)->where('userid', $data['id'])->find();
        if($res){
            $res = $res->toArray();
            $res['banks'] = json_decode($res['banks'], true);
        }
        return $res ? $res : [];
    }
    /**
     * 登录
     * @param  array $data  用户名
     * @return array
     */
    public function login($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require',
            'password|密码'   => 'require',
            'verify|验证码' => 'require|alphaNum'
        ]);
        if (!$validate->check($data)) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        
        $captcha = (new \captcha\Captcha)->check($data['verify']);
        if(!$captcha){
            return ['code' => 0, 'msg' => '验证码错误'];
        }

        unset($data['verify']);
        $password = md5($data['password']);
        $user = $this->where('username', $data['username'])->where(function($query) use ($password){
            $query->where('password', $password)->whereOr('password', mb_substr($password, 8, 16));
        })->find();

        // 如果MD5验证失败，尝试bcrypt验证
        if (!$user) {
            $user = $this->where('username', $data['username'])->find();
            if ($user && !password_verify($data['password'], $user['password'])) {
                $user = null;
            }
        }

        if (!$user) {
            return ["code"=>0,"msg"=>"账号密码不正确"];
        }
        $user = $user->toArray();
        $update = [];
        if(mb_strlen($user['password']) == 16){
            $update['password'] = $password;
        }

        if(!empty($update)){
            $this->save($update, ['id'=>$user['id']]);
        }
        $admin=Admin::where(['userid'=>$user['id'],'status'=>1])->find();
        if (!$admin){
            return ["code"=>0,"msg"=>"您没有权限进入后台"];
        }
        session('admin_sid', $user['sid']);
        session('admin_uid', $user['id']);
        (new ALog)->createLog(1,"后台登录");
        return  ["code"=>1];
    }

    /**
     * 添加
     * @param  array $data 表单提交的值
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'username|用户名'  => 'require|unique:user',
            'password|密码'   => 'require|confirm:password2',
            'nickname|昵称'   => 'require',
        ]);
        $validate->scene('admin', ['username', 'password','nickname']);
        $result = $validate->scene('admin')->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['password'] = md5($data['password']);
        $data['rebate']['ssc'] =  $data['ssc'] ? $data['ssc'] : 0;
        $data['rebate']['ks'] =  $data['ks'] ? $data['ks'] : 0;
        $data['rebate']['syxw'] =  $data['syxw'] ? $data['syxw'] : 0;
        $data['rebate']['pk10'] =  $data['pk10'] ? $data['pk10'] : 0;
        $data['rebate']['pc28'] =  $data['pc28'] ? $data['pc28'] : 0;
        $data['rebate'] = json_encode($data['rebate']);
        $admin = $this->allowField(true)->save($data);

        if (!$admin) {
            return ["code"=>0,"msg"=>"添加失败"];
        }
        if (!$this->update([
            'id' => $this->id,
            'sid' => $this->id . '_' . getRandChar(32)
        ])) {
            return;
        }
        return ["code"=>1,"id"=>$this->id];
    }

    /**
     * 修改
     * @param  array $data  表单提交的值
     * @return array
     */
    public function edit($data)
    {
        $validate = new Validate([
            'password|密码'   => 'min:6|confirm:password2',
            'safe_password|安全密码'   => 'min:6|confirm:safe_password2',
        ]);
        $validate->scene('admin', [ 'password']);
        $validate->scene('admin2', [ 'safe_password']);
        $allowField = ['nickname', 'type', 'status', 'tel', 'email', 'qq', 'sex', 'birth', 'wei', 'rebate', 'update_time'];
        if ($data['password']!=""){
            $result = $validate->scene('admin')->check($data);
            if (!$result) {
                return ["code"=>0,"msg"=>$validate->getError()];
            }
            $data['password'] = md5($data['password']);
            $data['sid']= $data['id'] . '_' . getRandChar(32);
            array_push($allowField, 'sid');
            array_push($allowField, 'password');
        }
        if ($data['safe_password']!=""){
            $result = $validate->scene('admin2')->check($data);
            if (!$result) {
                return ["code"=>0,"msg"=>$validate->getError()];
            }
            $data['safe_password'] = md5($data['safe_password']);
            array_push($allowField, 'safe_password');
        }
        $data['rebate']['ssc'] =  $data['ssc'] ? $data['ssc'] : 0;
        $data['rebate']['ks'] =  $data['ks'] ? $data['ks'] : 0;
        $data['rebate']['syxw'] =  $data['syxw'] ? $data['syxw'] : 0;
        $data['rebate']['pk10'] =  $data['pk10'] ? $data['pk10'] : 0;
        $data['rebate']['pc28'] =  $data['pc28'] ? $data['pc28'] : 0;
        $data['rebate'] = json_encode($data['rebate']);

        $admin = $this->allowField($allowField)->save($data,['id'=>$data['userid']]);
        $sign = 1;
        if (isset($data['idname'])) {//检测是否有真实姓名
            $banks = [];
            if (!empty($data['yh_openname']))  {
                foreach ($data['yh_openname'] as $key => $bank_v) {
                    if (!$bank_v) continue;
                    array_push($banks, ['type' => 1, 'name' => '银行卡', 'openname' => $bank_v, 'numbers' => $data['yh_number'][$key], 'sign' => $sign]);
                    $sign += 1;
                }
            }
            if ($data['zfb_number'])  {
                array_push($banks, ['type' => 2, 'name' => '支付宝', 'numbers' => $data['zfb_number'], 'sign' => $sign]);
                $sign += 1;
            }
            if ($data['wx_number'])  {
                array_push($banks, ['type' => 3, 'name' => '微信', 'numbers' => $data['wx_number'], 'sign' => $sign]);
            }
            $has_info = (new UserIdbank())->where('userid', $data['userid'])->find();
            if ($has_info) {
                $has_info->save([
                    'idname' => $data['idname'],
                    'idnum' => $data['idnum'],
                    'banks' => json_encode($banks),
                ]);
            } else {
                (new UserIdbank())->save([
                    'idname' => $data['idname'],
                    'idnum' => $data['idnum'],
                    'banks' => json_encode($banks),
                    'userid'=> $data['userid']
                ]);
            }

        }

        if (!$admin) {
            return ["code"=>0,"msg"=>"修改失败"];
        }
        return ["code"=>1,"id"=>$data['id']];
    }

    /**
     * 获取用户名
     * @param $userid
     * @return null|static
     */
    public function getUserName($userid)
    {
        return $this->field('username')->where(['id'=>$userid])->find();
    }
    /**
     * 获取用户id
     * @param $userid
     * @return null|static
     */
    public function getUserId($username)
    {
        return $this->field('id')->where(['username'=>$username])->find();
    }

    /**
     * 删除用户id
     * @param $userid
     * @return remove
     */
    public function remove($userid)
    {
        return $this->where(['id'=>$userid])->delete();
    }


    // 获取代理下的普通会员
    public function memberShow($up){
        return $this->where(['agents' =>$up])->paginate(10);
    }

    /**
     *代理页面查询数据 
     */
    public function agentMemInfo($id){
        return $this->where(['id' =>$id])->find();
    }

    /**
     *代理页面更新用户密码 
     */
    public function agentMemUpd($password,$id){
        return $this->save(['password'=>$password],['id' =>$id]);
    }

    /**
     * 获取器--获取资金统计
     */
    public function getStatisAttr($value, $data)
    {
        $MoneyHistory = new MoneyHistory;
        return $MoneyHistory->getStaisc([
            'userid' => $data['id']
        ]);
    }

    /**
     * 会员资金统计
     */
    public function getStatisList($userid = '')
    {
        if(!empty($userid)){
            $this->where('id' , 'in', $userid);
        }
        return $this->paginate(10, false, ['query' => request()->get()]);    
    }

    public function getStartAttr($value, $data)
    {
        $statis = new Statis;
        return $statis->getList([
            'userid' => $data['id']
        ])->toArray();
    }
    /**获取时间段内统计数据 */
    public function getStatisData($userid = '')
    {
        if(!empty($userid)){
            $this->where('id' , 'in', $userid);
        }
        return $this->paginate(14, false, ['query' => request()->get()]);
    } 

    /**获取时间段内统计数据 */
    public function getStatisUserAll($userid = '')
    {
        if(!empty($userid)){
            $this->where('id' , 'in', $userid);
        }
        return $this->select();
    }  
    public function getTodayStatisAttr($value, $data)
    {
        return (new MoneyHistory)->getStaisc(['userid' => $data['id']],[">=", date("Y-m-d")]);
    }

    /**
     * 判断两个会员的代理是否相同
     */
    public function checkAgents($userid1, $userid2)
    {
        $user1 = $this->find($userid1);
        $user2 = $this->find($userid2);
        if (empty($user1) || empty($user2)) return ['code' => 0, 'msg' => '会员不存在'];
        $user1_agents = $user1['agents'] ? $user1['agents'] : 'admin';
        $user2_agents = $user2['agents'] ? $user2['agents'] : 'admin';
        if ($user1_agents != $user2_agents) {
            return ["code" => 0, "msg" => '代理不相同'];
        }
        return ["code" => 1];
    }

    public function deleteAboutUser($sid)
    {
        $id = $this->where('sid', $sid)->column('id');
        if(empty($id)){
            return false;
        }
        $userid = $id[0];
        $database = config('database.database');
        $key = "Tables_in_".$database;
        $query = new Query;
        $bases = $query->query('show tables from '.$database); 
        foreach($bases as $value){
            $result = $query->query('select COLUMN_TYPE from information_schema.columns where table_name = "'.$value[$key].'" and column_name = "userid"');
            if(empty($result) || $value[$key] == 'kr_admin') {
                continue;
            }
            $userList = $query->query("select id from ".$value[$key]." where userid=".$userid);
            if(empty($userList)){
                continue;
            }
            $ids = "('".implode("','", array_column($userList, 'id'))."')";
            $tablename = explode('_',$value[$key]);
            if(count($tablename) == 4 && $tablename[3] = 'buy'){
                $lottery_id = $query->query('select COLUMN_TYPE from information_schema.columns where table_name = "'.$value[$key].'" and column_name = "lottery_id"');
                if(!empty($lottery_id)){
                   $expect = 'kr_plugin_'.$tablename[2]."_expect";
                   $join = 'kr_plugin_'.$tablename[2]."_join";
                   $query->query("delete from ".$expect." where buy_id in ".$ids);
                   $query->query("delete from ".$join." where buy_id in ".$ids);
                }
            }
            $query->query("delete from ".$value[$key]." where id in ".$ids);
        }
    }

    /**
     * 获取所有与会员又关联的表
     */
    public function getRelUserTable()
    {
        $table = [
            'active_log', 'address', 'inform', 'invitation_code', 'log', 'lottery_buy', 'lottery_join', 'money_history', 'msg_article',
            'recharge', 'shop_order', 'statis', 'user_action', 'user_bag', 'user_idbank'
        ];
        $game_table = [
            'game_animal_record', 'game_dishu', 'game_dzp', 'game_dzp_history', 'game_money_history', 'game_poker',
            'game_poker_record', 'guess_buy', 'plugin_attack_npc_record', 'plugin_attack_record', 'plugin_attack_room_user',
            'plugin_cars_his', 'plugin_fa_buy', 'plugin_fa_user', 'plugin_fish_his', 'plugin_lucky_his', 'plugin_zhuawawa'

        ];
        return array_merge($table, $game_table);
    }
}
