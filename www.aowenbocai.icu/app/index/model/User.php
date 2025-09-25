<?php

namespace app\index\model;

use core\Setting;
use think\Db;
use think\Model;
use think\Validate;
use app\admin\model\Log as ALog;
use app\common\model\MoneyHistory;

class User extends Model
{


    protected $resultSetType = 'collection';

    /**
     * 获取器 - user
     * @return string
     */
    public function getLevelAttr ($value,$data)
    {
     //   $system = (new Setting)->get(['user_level']);
      //  $user_level = json_decode($system['user_level'], true);
      //  $lv = array(
      //      'level' => $user_level[0]['level'],
      //      'level_name' => $user_level[0]['level_name'],
      //  );
      //  foreach ($user_level as $v) {
      //      if ($data['exp'] >= $v['explan']) {
       //         $lv['level'] = $v['level'];
       //         $lv['level_name'] = $v['level_name'];
       //     }
      //  }
      //  return $lv;
        return '';
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
     * 修改器 - reg_ip
     * @return json
     */
    protected function setRegIpAttr($value)
    {
        return request()->ip();
    }

    /**
     * 登录
     * @param  array $data 用户名
     * @return array
     */
    public function login_before($data)
    {
        $validate = new Validate([
            'username|用户名' => 'require',
            'password|密码' => 'require'
        ]);
        if (!$validate->check($data)) {
            return ["err" => 2, "msg" => $validate->getError()];
        }
        $data['password'] = md5($data['password']);
        $admin = $this->where($data)->find();
        if (!$admin) {
            return ["err" => 1, "msg" => "账号密码不正确"];
        }
        $res = $admin->toArray();
        session('sid', $res['sid']);
        session('uid', $res['id']);

        (new ALog)->createLog(1, "会员登录");
        $ip = request()->ip();
        $this->save(['last_ip'=> $ip],['id' => $res['id']]);
        return ["err" => 0, 'data' => $res];
    }

    /**
     * 微信登录
     */
    public function wxLogin($data)
    {
       $res = $this->where($data)->find();
       if(!$res) return ['code' =>0, 'msg'=>'信息错误'];
       $res = $res->toarray();
       
       session('sid', $res['sid']);
       session('uid', $res['id']);
       cookie('bAuth', true);
       
       (new ALog)->createLog(1, "会员登录");
       $ip = request()->ip();
       $this->save(['last_ip'=> $ip],['id' => $res['id']]);
       return ["code" => 1,'msg' =>$res];
    }
    /**
     * 注册--手机号、微信注册
     */
    public function register($data,$type = 1){
        $data['reg_ip'] = request()->ip();
        $admin = $this->allowField(['tel', 'reg_ip','username','photo','nickname','qq','wei'])->save($data);
        if (!$admin) {
            return ["code" => 0, "msg" => "注册失败"];
        }
        $sid = $type == 1 ? ($this->id . '_' . getRandChar(32)) : ($this->id . '_' . $data['openid']);
        if ($this->update([
            'id' => $this->id,
            'sid' => $sid
        ])
        ) {
            session('sid', $sid);
            session('uid', $this->id);
            (new ALog)->createLog(1, "会员注册");
            $this->save(['last_ip'=> $data['reg_ip'], 'action_time'=> date("Y-m-d H:i:s")],['id' => session('uid')]);
        }
        return ["code" => 1, "id" => session('uid')];
    }

    /**
     * 注册--之前
     * @param  array $data 用户名
     * @return array
     */
    public function register_before($data)
    {
        $validate = new Validate([
            'username|用户名' => 'require|unique:user|chsDash',
            'password|密码' => 'require|confirm:password2'
        ]);
        $result = $validate->check($data);

        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $data['password'] = md5($data['password']);
        $data['reg_ip'] = request()->ip();
        $admin = $this->allowField(['username', 'password', 'reg_ip', 'agents'])->save($data);
        if (!$admin) {
            return ["code" => 0, "msg" => "注册失败"];
        }
        $sid = $this->id . '_' . getRandChar(32);
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
        return ["code" => 1, "id" => session('uid')];
    }

    /**
     * 修改
     * @param  array $data 表单提交的值
     * @param  string $Field $Field= nickname   $Field= photo
     * @return [code,msg]
     */
    public function edit($data, $Field = '')
    {
        if (!$Field) {
            $allowField = ['tel', 'email', 'action_time', 'update_time'];
        } else {
            $allowField = [$Field, 'update_time'];
        }
        $data['photo'] = base64_upload($data['photo'], 'uploads/personal/', $data['id']) . '?t=' . time();
        if ($Field == 'photo' and $data['photo'] == '') {
            return ["code" => 0, "msg" => "请点击选择头像"];
        }
        if ($Field == 'nickname' and $data['nickname'] == '') {
            return ["code" => 0, "msg" => "请填写您的昵称"];
        }
        $admin = $this->allowField($allowField)->save($data, ['id' => $data['id']]);
        if (!$admin) {
            return ["code" => 0, "msg" => "修改失败"];
        }
        return ["code" => 1, "id" => $data['id']];
    }

    /**
     * 密码修改
     * @param  array $data
     * @return array
     */
    public function editPassword($data)
    {
        $validate = new Validate([
            'password|旧密码' => 'require',
            'new_password|密码' => 'require|confirm:' . $data['new_password2']
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $password = md5($data['password']);
        $user = $this->where(['id' => $data['userid'], 'password' => $password])->find();
        if (!$user) {
            return ["code" => 0, "msg" => "密码不对"];
        }
        $new_password = md5($data['new_password']);
        $sid = $data['userid'] . '_' . getRandChar(32);
        $admin = $this->update([
            'password' => $new_password,
            'id' => $data['userid'],
            'sid' => $sid
        ]);
        if (!$admin) {
            return ["code" => 0, "msg" => "修改失败"];
        }
        session('sid', $sid);
        session('uid', $data['userid']);
        (new ALog)->createLog(2, "修改密码");
        return ["code" => 1, "msg" => "修改成功"];
    }

    /**
     * 获取查看用户信息
     * @param  array $userid 用户id
     * @param  string $type 当存在$type时  获取用户详细信息
     * @return array
     */
    public function getInfo($userid, $type = "")
    {
        $info = $this->find($userid);
        if (empty($info)) {
            return ['code' => 0];
        }
        $times = json_decode($info['sign_times'],true);
        $times = $times['add'] ? ($times['add'] > date("Y-m-d") ? 1 : 0) : 0;
        $re = [
            'nickname' => $info['nickname'],
            'userid' => $userid,
            'photo' => $info['photo'],
            'explan' => $info['explan'],
            'sign_times' => $times,
            'photo' => $info['photo'],
            //'flower' => $info['flower'] ? $info['flower'] : 0,
            //'charm' => $info['charm'] ? $info['charm'] : 0,
        ];
        if ($type) {
            $re['money'] = $info['money'];
        }
        $dq_user = $this->where(['sid' => session('sid')])->find();
        if ($dq_user['id'] != $userid) {//取得关注状态
            $has_gz = (new Fans)->where('userid', $dq_user['id'])->where('to_userid', $userid)->find();
            $re['care_status'] = empty($has_gz) ? 0 : 1;
        }
        //获取会员等级 [{"level":1,"explan":0,"level_name":"游戏新秀"},{"level":2, "explan":100,"level_name":"游戏小生"},{"level":3, "explan":200,"level_name":"资深熟手"},{"level":4,"explan":400,"level_name":"游戏精英"},{"level":5,"explan":800,"level_name":"游戏大师"},{"level":6,"explan":1600,"level_name":"游戏宗师"}]
        $system = (new Setting)->get(['user_level']);
        $user_level = json_decode($system['user_level'], true);
        $lv = array(
            'level' => $user_level[0]['level'],
            'level_name' => $user_level[0]['level_name'],
        );
        //foreach ($user_level as $v) {
         //   if ($info['exp'] >= $v['explan']) {
          //      $lv['level'] = $v['level'];
         //       $lv['level_name'] = $v['level_name'];
         //   }
       // }
        //$re['lv'] = $lv;
        $re['lv'] = '';
        return ['code' => 1, 'data' => $re];
    }


    /**
     * 获取用户信息
     * @param array $where 获取个人信息的条件
     */
    public function getInfoWhere($where)
    {
        return $this->where($where)->find();
    }
    /**
     * 设置签名
     * @param str $explan 签名
     */
    public function setExplan($id, $explan = '')
    {
        $result = $this->validate(['explan|签名' => 'require|max:100',])->save(['explan' => $explan], ['id' => $id]);
        if (false === $result) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '设置成功'];

    }

    /**
     * 获取战绩排行榜
     * @return array
     */
    public function getRanking()
    {
        return $this->field(['nickname', 'id', 'photo', 'exp', 'explan'])->order('exp', 'desc')->limit(20)->select();
    }

    /**
     * 设置游戏浏览记录
     */

    public function setExtName($name, $result)
    {
        $extname = $result == 2 ? $name : $this->user['extname'] . "|" . $name;
        $res = $this->update(['extname' => $extname, 'id' => session('uid')]);
        if ($res) $result == 2 ? ($this->user['extname'] = $name) : ($this->user['extname'] .= "|" . $name);
    }

    /*
    获取浏览过的游戏
     */
    public function getExt($userid)
    {
        $extname = $this->field(['extname'])->where(['id' => $userid])->find()['extname'];
        if ($extname == '') return false;
        $extname = explode('|', $extname);
        return $extname;
    }

    /**
     * 修改资料
     * @param array $data 需更新的更新数据
     * @param array $where 条件
     * @return updateInfo
     */
    public function updateInfo($data,$where){
        $rule = [
            'username|用户名' => 'chsDash|length:2,8',
            'nickname' => 'chsDash|length:2,8',
            'tel|电话号码' => 'number|length:11',
            'email|邮箱' => 'email',
            'password|密码' => "confirm:password2|length:6,25"
        ];
        
        $validate = new Validate($rule);
        if(!$validate->check($data)){
            return ['msg' => $validate->getError()];
        }
        if(isset($data['password'])){
            $data['password'] = md5($data['password']);
        }
        return $this->allowField(true)->save($data,$where);
    }

    public function updateInc($field,$num,$where){
        return $this->where($where)->save($field, $num);
    }

    /*
    *获取代理的下级会员
     */
    public function memberShow($agents)
    {
        return $this->field('username,nickname,id')->where(['agents' => $agents])->paginate(14);
    }

    /*
    *更新会员代理
     */
    public function updateAgent($agents, $newagents)
    {
        return $this->save(['agents' => $newagents], ['agents' => $agents]);
    }

    /*
    查询代理下会员信息
     */
    public function getMember($id)
    {
        return $this->get($id);
    }

    /*
     删除会员
    */

    public function deleteMember($id)
    {
        return $this->where(['id' => $id])->delete();
    }
}
