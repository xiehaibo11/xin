<?php
namespace app\web\model;

use app\common\controller\LotteryCommon;
use app\lottery\model\common\BaseBuy;
use core\Setting;
use think\Env;
use think\Model;
use think\Validate;
use app\admin\model\Log as ALog;
use app\index\model\ExtShowList;

class User extends Model
{
    protected $update_time = false;
    protected $resultSetType = 'collection';

    public function getUserInfo($uid)
    {
        return $this->where('id', $uid)->find();
    }
    
    public function setupdate($data)
    {
    if(isset($data['id']))	
    $res=$this->where('id', $data['id'])->update($data);	
    else $res=$this->save($data);
    return $res;
    }
    public function getAgentsNumAttr($value, $data)
    {
        return $this->where('agents', $data['id'])->count('id');
    }

    public function getAgentsMoneyAttr($value, $data)
    {
        $money = $this->where('agents', $data['id'])->sum('money');
        return $money ? $money + $data['money'] : $data['money'];
    }

    /**获取器--获取战绩排行版上有没有未结结束的单子 */
    public function getPlayNumAttr($value, $data)
    {
        $list = (new BaseBuy())->field('id, total_money, ext_name')->where('userid', $data['id'])->where('is_join', 1)->where('end_time', '>', date('Y-m-d H:i:s', time()))->count();
        if(!$list){
            return 0;
        }
        return $list;
    }
    
    /**
     * 设置游戏浏览记录
     */
    public function setExtName($name, $result, $userExtlist)
    {
        switch ($result) {
            case '0':
                $extArr = explode('|', $userExtlist);
                $nowKey = array_flip($extArr);
                if(in_array($name, $nowKey)){
                    $nowKey1 = $nowKey[$name];
                    unset($extArr[$nowKey1]);
                }
                $extArr[] = $name;
                $new = array_unique($extArr);
                $extname = $new ? implode('|', $new) : $name;
                break;
            case '1':
                $extname = $userExtlist . "|" . $name;
                break;
            case '2':
                $extname = $name;
                break;
        }
        $res = $this->update(['extname' => $extname, 'id' => session('uid')]);
        if ($res){
           return $extname;
        }
    }

     /**
     * 用户名注册
     * @param  array $data 用户名
     * @return array
     */
    public function register($data)
    {
        $validate = new Validate([
            'username|用户名' => 'require|unique:user|chsDash',
            'nickname|昵称' => 'require|chsDash',
            'password|密码' => 'require|confirm:password2',
        ]);
        $result = $validate->check($data);

        if (!$result) {
            return ["err" => 1, "msg" => $validate->getError()];
        }
        $data['password'] = \PasswordHash::hash($data['password']);
        $data['reg_ip'] = request()->ip();
        $data['type'] = 1;
        $admin = $this->isUpdate(false)->allowField(['username', 'nickname', 'tel', 'password', 'reg_ip', 'type', 'qq'])->save($data);
        if (!$admin) {
            return ["err" => 2, "msg" => "注册失败"];
        }
        $sid = $this->id . '_' .($this->getRandChar(32));
        if ($this->update([
            'id' => $this->id,
            'sid' => $sid
        ])
        ) {
            session('sid', $sid);
            session('uid', $this->id);
            (new ALog)->createLog(1, "会员登录");
            $this->save(['last_ip'=> $data['reg_ip'], 'action_time'=> date("Y-m-d H:i:s")],['id' => session('uid')]);
        }
        return ["err" => 0, "id" => session('uid')];
    }

    /**
     * 获取随机字符串
     * @return string
     */
    public function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    /**用户登录 -- 用户名*/
    public function loginName($data)
    {
        $validate = new Validate([
            'username|用户名' => 'require',
            'password|密码' => 'require',
        ]);

        $result = $validate->check($data);
        if (!$result) {
            return ["err" => 1, "msg" => $validate->getError()];
        }

        $username = $data['username'];
        $password = $data['password']; // 保持明文用于验证
        $res = $this->where('username|tel|email', $username)->find();
        if(!$res){
            return ['err' => 1, 'msg' => '账号不存在'];
        }
        if ($res->getData('status')) {
            return ['err' => 1, 'msg' => '账号已冻结，请联系在线客服'];
        }

        // 使用新的密码验证方法
        if(!\PasswordHash::verifyLegacy($password, $res['password'])){
            $login_time = cookie('login_time') ? cookie('login_time') : 0;
            if ($login_time >= 5) {
                $this->where('username|tel|email', $username)->update(['status' => 1]);
                cookie('login_time', null);
                return ['err' => 1, 'msg' => '您连续登陆失败超过6次，账号已被冻结，请联系在线客服'];
            }
            cookie('login_time', $login_time + 1);
            return ['err' => 1, 'msg' => '密码不正确，6次错误账号将被冻结，剩余' . (6 - $login_time + 1) . '次'];
        }
        $res = $res->toArray();
        $update = [];
        if(!$res['sid']){
            $update['sid'] = $res['id']."_".\PasswordHash::generateRandomString(32);
            $res['sid'] = $update['sid'];
        }

        // 如果是旧密码，迁移到新哈希算法
        if(strlen($res['password']) <= 32) {
            $update['password'] = \PasswordHash::hash($password);
        }

        if(!empty($update)){
            $this->save($update, ['id'=>$res['id']]);
        }
        session('uid', $res['id']);
        session('sid', $res['sid']);
        cookie('bAuth', true);
        cookie('login_time', null);
        //判断是否记住密码
        if (isset($data['remember']) and $data['remember']) {
            $safe_key = Env::get('authorization_token');
            $str = md5($res['sid'].$safe_key);
            cookie('has_login', $str.$res['sid'], 60*60*24*7);
        }
        (new ALog)->createLog(1, "会员登录");
        $ip = request()->ip();
        $this->save(['last_ip'=> $ip],['id' => $res['id']]);
        return ["err" => 0, 'data' => $res];
    }

    /**检测会员是否是某个会员的下级*/
    public function checkAgents($userid, $agents_id, $type = 0)
    {
        $user_info = $this->find($userid);
        if (!$user_info) return ['code' => 0, 'msg' => '会员不存在'];
        if ($user_info['type'] != 2 and $type) return ['code' => 0, 'msg' => '会员类型不为代理'];
        if (!$user_info['agents']) return ['code' => 0, 'msg' => '会员不是您的下级'];
        if ($user_info['agents'] != $agents_id) return $this->checkAgents($user_info['agents'], $agents_id, $type);
        return ['code' => 1];
    }

    /**获取会员的所有下级*/
    public function getAllAgents($userid, $is_son = 1)
    {
        $user_list = $this->field('id, type')->where('agents', $userid)->select()->toArray();
        $res = [];
        if (!empty($user_list)) {
            foreach ($user_list as $v) {
                if ($v['type'] == 2) {
                    $res = array_merge($this->getAllAgents($v['id'], 1), $res);
                } else {
                    $res = array_merge([$v['id']], $res);
                }
            }
            $res = array_merge($res, [$userid]);
            return $res;
        } else {
            return $is_son ? [$userid] : [];
        }
    }

    /**
     * 获取会员等级
     */
    public function getGrade($user)
    {
        $spent = ($user['spent'] ?? 0) ? $user['spent'] : 0;
        $setting = Setting::get(['user_level']);
        $user_level = json_decode($setting['user_level'], true);
        krsort($user_level);
        $data = [];
        foreach ($user_level as $key => $v) {
            if ($spent >= $v['explan']) {
                $data['userGrade'] = 'VIP' . $v['level'];
                $data['gradeGrow'] = $spent;
                $data['userTitle'] = $v['level_name'];
                if ($key == count($user_level) -1 ) {
                    $data['nextGrade'] = '';
                    $data['nextGrow'] = 0;
                    $data['perNum'] = 100;
                    $data['isVipMax'] = true;
                } else {
                    $data['nextGrade'] = 'VIP' . (intval($v['level'])+1);
                    $data['nextGrow'] = intval($user_level[$key+1]['explan']) - $spent;
                    $data['perNum'] = round($spent * 100/intval($user_level[$key+1]['explan']), 2);
                    $data['isVipMax'] = false;
                }
                break;
            }
        }
        return ['code' => 1, 'data' => $data];
    }

    /**获取器--获取战绩排行版上有没有未结结束的单子 */
    public function changeSafePwd($data, $user)
    {
        if ($data['password'] != $data['password2']) {
            return ['err' => 1, 'msg' => '两次密码不相同'];
        }
        $res = $this->where('id', $user['id'])->update(['safe_password' => md5($data['password'])]);
        if ($res) {
            return ['err' => 0, 'msg' => '修改成功'];
        }
        return ['err' => 1, 'msg' => '修改失败'];
    }
    
    /**获取会员类别**/
     public function gettypeusers($type)
    {
        $res = $this->where('type', $type)->orderRaw('rand()')->find();
        return $res;
    }

}