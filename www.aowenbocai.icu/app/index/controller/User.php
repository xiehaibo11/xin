<?php
namespace app\index\controller;

use app\common\model\UserBag;
use app\index\model\MsgArticle;
use app\index\model\User as AUser;
use app\index\model\ExtShowList;
use core\model\ActiveLog;
use app\common\model\UserAction;
use app\common\controller\Extend;
use core\Active as BActive;
use core\Setting;
use core\Sms;
use core\model\Good;
use app\common\model\MoneyHistory;
use app\common\model\FlowerGift;
use app\common\model\FlowerHistory;
use app\common\model\Inform;
use app\phpqrcode\controller\QR;
use app\common\model\GameDzp;
use app\index\model\Fans;
use think\Session;
use app\common\controller\Uploadimage;
use app\index\model\UserIdbank;

class User extends Base
{
    public function __construct()
    {
        $this->baseModel  = new AUser();//当前模型
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    private $page_size = 10;

    /**
     * 设置昵称
     */
    public function setNickname()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $data['id'] = $this->user['id'];
            $res = $this->baseModel->edit($data, 'nickname');
            if (!$res['code']) {
                $this->error($res['msg']);
            }
            return $this->success('设置成功', url('admin/user/setPhoto'));
        }
        return $this->fetch('set_nickname', ['title' => '设置昵称']);
    }


    /**
     * 设置头像
     */
    public function setPhoto()
    {
        if (request()->isPost()) {
            $data = request()->post();
            $data['photo'] = $data['photo'] ? $data['photo'] : "/static/images/no-photo.png";
            $data['id'] = $this->user['id'];
            $res = $this->baseModel->edit($data, 'photo');
            if (!$res['code']) {
                $this->error($res['msg']);
            }
            return $this->success('设置成功', url('admin/user/index'));
        }
        return $this->fetch('set_photo', ['title' => '设置头像']);
    }

    public function setExplan($explan = '')
    {
        if (request()->isAjax()) {
            $User = new AUser;
            return $User->setExplan($this->user['id'], $explan);
        }
        return $this->fetch('set_explan', ['title' => '设置签名', 'explan' => $this->user['explan']]);
    }

    /**
     * 会员中心
     */
    public function index()
    {
        return $this->show();
    }

    public function set()
    {
        $data = [
            'data' => $this->baseModel->getInfo($this->user['id'], 1),
            'title' => '个人中心'
        ];
        if (empty($data['data']['data']['explan'])) {
            $data['data']['data']['explan'] = '您还没有设置签名，点击我设置！';
        }
        if (request()->isAjax()) {
            return $data['data'];
        }
        return $this->fetch('index', $data);
    }

    /**
     * 个人资料
     */
    public function info()
    {
        if (request()->isPost()) {
            $data=request()->post();

            if(!$this->user['tel']){
                return json(['err' => 2001, 'msg' => '请先绑定手机号']);
            }
            if($data['nickname'] == $this->user['nickname'] && $data['email'] == $this->user['email']) $this->error('没有修改项', url("admin/User/info"));
            $res = sms::getSmsCode($this->user['tel'], 'changeinfo', $data['yzm']); 

            if($res['err'] > 0){
                $this->error($res['msg'], url("admin/User/info"));
            }

            $user = new AUser;
            $sqlres = $user->updateInfo(['nickname' => $data['nickname'], 'email' =>$data['email']], ['id' =>$this->user['id'], 'tel' =>$data['tel']]);
            if (!$sqlres)  $this->error('修改失败', url("admin/User/info"));
            
            $this->user['nickname'] = $data['nickname'];
            $this->user['email'] = $data['email'];
            /**删除短信Session*/
            Session::delete($res['smskey']);
            return $this->success('修改成功');
        }
        return $this->fetch('info', ['title' => '个人资料修改']);
    }

    /**
     * 修改资料获取信息
     */
    public function getChangeInfo()
    {
        $user = (new AUser)->field('nickname,email,tel')->find($this->user['id']);
        $user = $user->toArray();
        $err = 1;
        if($user['tel']){
            if($user['tel'] != $this->user['tel']){
                $this->user['tel'] = $user['tel'];
            }
            $user['tel'] = substr_replace($this->user['tel'],'****',3,4);
            $err = 0;
        }
        return json(['err' => $err, 'info' => $user]);

    }

    public function sendSmsInfo()
    {
        if(request()->IsAjax()){
            if(!$this->user['tel']){
                return json(['err' => 2001, 'msg' => '请先绑定手机号']);
            }
            $info = (new AUser)->get(['tel' => $this->user['tel']], $this->user['id']);
            if(!$info) {
                return json(['err' =>1,'msg' =>'该手机号不是您所绑定的手机号']);
            }
            $res = Sms::setSmsCode($this->user['tel'] , 'changeinfo');
            return $res;
        }
    }

    /**
     * 获取资料
     */
    public function getInfo()
    {
        $tel = $this->user['tel'] ? 1: 0;
        $email = $this->user['email'];
         if($email){
             $emailArr = explode('@', $email);
             $len = mb_strlen($emailArr[0]);
             $email = substr_replace($emailArr[0], '****', 2, $len-4)."@".$emailArr[1];
         }
        $data = [
            'err' => 0,
            'data' => [
                'id' => $this->user['id'],
                'username' => $this->user['username'],
                'type' => $this->user['type'],
                'money' => $this->user['money'],
                'game_money' => $this->user['game_money'],
                //'coupons' => $this->user['coupons'],
                'explan' => $this->user['explan'],
                'email' => $email,
                'nickname' => $this->user['nickname'],
                'photo' => $this->user['photo'],
                'fristLogin' => $this->user['sign_times'] ? 0 : 1,
                'tel' => $this->user['tel'] ? substr_replace($this->user['tel'],"****",3,4) : '',
                'rebate' => $this->user['rebate'],
                'sex' => $this->user['sex'],
                'birth' => $this->user['birth'],
                'qq' => $this->user['qq'],
                'user_grade' => (new \app\web\model\User())->getGrade($this->user)['data'],
                'recharge_money' => $this->user['recharge_money'],
            ]
        ];
        return json($data);
    }

    /**
     * 修改密码 - 渲染压面
     * @param string $by 通过方式
     */
    public function editPassword($by = '')
    {
        $telorname = $this->user['tel'] ? 'tel' : ($this->user['username'] ? 'name' : 'nothing');
        $way = $by ? $by : $telorname;

        if($way == 'nothing'){
            $this->assign('isPassword', 0);
            return $this->fetch('edit_passwordbyname', ['title' => '用户名密码修改']);
        }

        $this->assign('isPassword', 1);
        if($way == 'tel'){
            $this->assign('tel',substr_replace($this->user['tel'],'****',3,4));
            return $this->fetch('edit_passwordbytel', ['title' => '手机号密码修改']);
        }
        if($way == 'name'){
            return $this->fetch('edit_passwordbyname', ['title' => '用户名密码修改']);
        }

    }

    
    /**
     * 修改密码--手机号发送短信
     */
    public function sendSmsPassword()
    {
        if(request()->IsAjax()){
            if(!$this->user['tel']) {
                return json(['err' =>1,'msg' =>'您暂时未绑定手机号']);
            }
            $res = Sms::setSmsCode($this->user['tel'] , 'passwordByTel');
            return $res;
        }
    }

    /**
     * 修改密码 - 通过手机号修改密码
     */
    public function editByTel()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(empty($data['yzm']) || empty($data['password']) || empty($data['password2'])){
                return json(['err' =>2, 'msg' => '填写资料出错，请认真填写资料']);
            }

            if(!$this->user['tel']) {
                return json(['err' =>1,'msg' =>'您暂时未绑定手机号']);
            }

            $res = Sms::getSmsCode($this->user['tel'], 'passwordByTel', $data['yzm']);
            if($res['err'] > 0){
                return json($res);
            }
            return json($this->resetPassword($data, $res['smskey']));
        }
    }


    /**
     * 修改密码
     */
    public function resetPassword($data, $smskey = ''){
        
        $check = [
            'password' => $data['password'],
            'password2' => $data['password2'],
        ];
        $res = (new AUser)->updateInfo($check, ['id' => $this->user['id']]);
        if(isset($res['msg'])){
            return ['err' => 3, 'msg' => $res['msg']];
        }
        if($res){
            if(!empty($smskey)){
                Session::delete($smskey);
            }
            return ['err' => 0, 'msg' => '密码修改成功'];
        }

        return ['err' => 1, 'msg' => '密码修改失败'];
    }
    /**
     * 修改密码 - 通过用户名修改密码
     */
    public function editByName()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(empty($data['oldpwd']) || empty($data['password']) || empty($data['password2'])){
                return json(['err' =>2, 'msg' => '填写资料出错，请认真填写资料']);
            }

            if($this->user['password'] != md5($data['oldpwd'])){
                return json(['err' =>1, 'msg' => '原密码错误']);
            }
            return json($this->resetPassword($data));
        }
    }

    /**
     * 修改密码获取相关数据
     */
    public function aboutPassword()
    {
        $user = (new AUser)->get($this->user['id']);
        if($user){
            $user = $user->toArray();
            $data = [
                'tel' => 0,
                'username' => 0
            ];

            if($user['tel']){
                $data['tel'] = 1;
            }

            if($user['username']){
                $data['username'] = 1;
            }
            return json($data);
        }
        
    }
    /**
     * 更换手机号--验证原手机号发送短信
     */
    public function sendSmsEdit()
    {
        if(request()->IsAjax()){
            $data = input('get.');
            $info = (new AUser)->get(['id' => $this->user['id']]);
            if(!$info || $info->tel != $this->user['tel']) {
                return json(['err' =>1,'msg' =>'该手机号不是您所绑定的手机号']);
            }
            $res = Sms::setSmsCode($this->user['tel'] , 'changetel');
            return $res;
        }
    }

    /**
     * 更换手机号--验证原来的手机号
     */
    public function changeTel()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $usertel = $this->user['tel'];
            if($usertel != $data['tel']) return json(['err' =>1, 'msg' => '手机号码错误']);
            $res = Sms::getSmsCode($data['tel'], 'changetel', $data['yzm']);
            if ($res['err'] > 0) {
                return json($res);
            }
            /**删除短信Session*/
            isset($res['smskey']) ? Session::delete($res['smskey']) : '';
            return json(['err' =>0]);
        }
        if($this->user['tel']){
            $this->assign('tel',substr_replace($this->user['tel'],'****',3,4));
            $this->assign("usertel", $this->user['tel']);
        }
        $isTel = $this->user['tel'] ? 1 : 0;
        $this->assign("isTel", $isTel);
        return $this->fetch('change_tel', ['title' => '手机号更换']);
    }
    /**
     * 更换手机号码--验证新手机号
     */
    public function writePhone()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $user = new AUser;
            $info = $user->get(['tel' => $data['tel']]);
            if($info) {
                return json(['err' =>2,'msg' =>'该手机号已绑定']);
            }
            $setting = Setting::get(['tel_checked']);
            if ($setting['tel_checked']) {
                $res = Sms::getSmsCode($data['tel'], 'change', $data['yzm']);
                if($res['err'] > 0){
                    return json($res);
                }
            }
            
            $sqlres = $user->updateInfo(['tel' =>$data['tel']],['id' =>$this->user['id']]);
            if($sqlres){
                $this->user['tel'] = $data['tel'];
                /**删除短信Session*/
                isset($res['smskey']) ? Session::delete($res['smskey']) : '';
                return json(['err' =>0, 'msg' => '修改成功']);
            }else{
                return json(['err' =>1, 'msg' => '修改失败']);
            }
        }            
    }

   /**
     *  更换手机号码--验证新手机号发送短信
     */
    public function sendSmsChange()
    {
        if(request()->IsAjax()){
            $data = input('get.');
            $info = (new AUser)->get(['tel' => $data['tel']]);
            if($info) {
                return json(['err' =>1,'msg' =>'该手机号已绑定']);
            }
            $res = Sms::setSmsCode($data['tel'] , 'change');
            return $res;
        }
    }


    /**
     * [会员消息收件模板]
     * string $msg_type  1为发件 2收件
     */
    public function message()
    {
        $data=$this->user;
        $this->assign('data', $data);
        return $this->fetch('message', ['title' => '消息中心']);
    }

    /**
     * 删除
     */
    public function messageDelete()
    {
        $id = $this->id;
        $info = (new MsgArticle)->get($id);
        if ($info->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * [会员获取消息]
     * @param  string $type  0为全部  1为已读
     * @return array
     */
    public function getMessage($msg_type = "1", $type = "")
    {
        $msg = new MsgArticle;
        $pageSize = 10;
        if ($msg_type == 2) {
            $msg->where("send_userid", $this->user['id']);
        } elseif ($msg_type == 1) {
            $msg->where("userid", $this->user['id']);
        }
        if (!$type) {
            $list = $msg->order(['status'=>' asc','create_time' => 'desc'])->paginate($pageSize);
        } elseif ($type == 1) {
            $list = $msg->where("status", 0)->order(['status'=>' asc','create_time' => 'desc'])->paginate($pageSize);
        }
        $list = $list->append(['username','send_username']);
        foreach ($list as &$value) {
            $value['read'] = $value['status'] == 1 ? 'read' : 'noread';
        }
        if (empty($list)) {
            return json(['err'=>0]);
        } else {
            return json(['err'=>0,'data' => $list]);
        }
    }
    /**
     * 消息状态设置
     */
    public function setStatus($id)
    {
        $res = (new MsgArticle)->changeRead($id);
    }
    /**
     * [显示标题]
     * @return array
     */
    public function show($userid = '')
    {
        if (empty($userid)) {
            $userid = $this->user['id'];
        } else {
            $info = $this->baseModel->find($userid);
            $userid = empty($info) ? $this->user['id'] : $userid;
        }
        if (request()->isAjax()) {
            return $this->baseModel->getInfo($userid);
        }
        
        return $this->fetch('show', ['title' => '用户信息']);
    }

    public function getAction($userid = '', $ext = '')
    {
        if ($userid == '') {
            $userid = $this->user['id'];
        }
        $Action = new UserAction;
        if ($userid != 0) {
            $Action->where(['userid' => $userid]);
        }
        if (!empty($ext)) {
            $Action->where(['ext_name' => $ext]);
        }
        $res = $Action->order('id', 'desc')->paginate($this->page_size, 100);
        if(!$res) return false;
        $res->append(['user_info', 'friend_time']);
		$uid = $this->user['id'];
        foreach ($res as &$item) {
			$item['good'] = (new Good)->rGood('action', $item['id'], $uid);
			$item['goodnum'] = (new Good)->goodnum('action', $item['id']);
            $ext_info = Extend::getInfo($item['ext_name']);
            $item['ext_info'] = [
                'logo' => $ext_info['logo'],
                'title' => $ext_info['title']
            ];
        }
        return $res;
    }
	
	/**
    * 点赞
	* @param int $id 动态ID
	* @return array
    */
    public function good($id = '', $type = 'action')
    {
        $userid = $this->user['id'];	
		$data = (new Good)->gooder($type, $userid,$id);
		return $data;
    }

    /*
    获取浏览过的游戏
     */
    public function getExt($userid = '')
    {
        if ($userid == '') {
            $userid = $this->user['id'];
        }
        
        $user = new AUser;
        $list = $user->getExt($userid);
        if (!$list) {
            return false;
        }
        $extshowlist = new ExtShowList;
        $res = $extshowlist->getUserExtList($list);
        return $res;
    }

    public function action()
    {
        return $this->fetch('action', ['title' => '动态展示大厅']);
    }
	
	

    public function ranking()
    {
        return $this->fetch('ranking', ['title' => '战绩']);
    }

    /***
     * 等级排行榜
     */
    public function getLevelList()
    {
        $res = (new AUser)->field(['nickname', 'id', 'photo', 'explan', 'exp'])->order('exp', 'desc')->paginate(20,100);
        $res = $res->append(['level']);
        $data['err'] = 0;
        $data['data'] = $res;
        return json($data);
    }

    /**
     * 获取自己的经验排行
     */
    public function getSelfLevel()
    {
        $user = new AUser;
        $res = $user->field(['nickname', 'id', 'photo', 'explan', 'exp'])->find($this->user['id']);
        $res = $res->append(['level']);
        $count = $user->where('exp', '>', $res['exp'])->count();
        $res['sort'] = $count + 1;
        return json(['err' => 0, 'data' => $res]);

    }

    /**
    *战绩排行榜
    *@param int $type 代表获取当日的排行 
    */
    public function getRankingList($type = 0)
    {
        $today = date("Y-m-d");
        $where = ['create_time' => ['>=' ,$today]];
        if($type){
            $time = date("Y-m-d ", strtotime("last day"));
            $where = ['create_time' => ['bettwen' ,$time.",".$today]];
        }
        $res = (new MoneyHistory)->listSort($where, $this->page_size);
        $res->append(['nickname','photo']);
        $res = $res->toArray();
        $res['err'] = 0;
        return json($res);
    }

    /**获取自己的当前排行 */
    public function getSelfRanking()
    {
        $data = (new MoneyHistory)->getSelfRanking($this->user['id']);
        return json(['err' => 0, 'data' => $data]);
    }


    public function ushow($userid = '')
    {
        return $this->fetch('ushow', ['title'=>'个人空间']);
    }
    
    /**
     * 查看是否有新消息
     */
    public function newMsgNum()
    {
        $count = (new MsgArticle) ->getList($this->user['id']);
        return json(['err' => 0, 'data' => $count]);
    }


   /**
   * 用户签到数据
   */
    public function signbefore()
    {
        $sign_times = $this->user['sign_times'];
        $nexttime = 0;
        if($sign_times){
            $sign = json_decode($sign_times, true);
            $nexttime = $sign['add'] ? ($sign['add'] > date("Y-m-d",strtotime("-1 day")) ? $sign['times'] : 0) : 0;
            $today = $sign['add'] ? ($sign['add'] > date("Y-m-d") ? 1 : 0) : 0;
           // $nexttime+= 1;
        }

        $setting = Setting::get(['sign_in_award']);
        if($setting['sign_in_award']){
            $signAward = explode("," , $setting['sign_in_award']);
        } else {
            $signAward = [];
        }
        return  json(['err' => 0, 'data' =>['times' => $nexttime,'intr' => $signAward,'today_sign' => $today]]);
    }

    
    /**
     * 用户签到
     */
    public function sign()
    {
        $user = new AUser;
        $info = $this->user;

        $nowday = date("Y-m-d 00:00:00");
        $before = date("Y-m-d H:i:s", strtotime(date('Y-m-d')) - 24 * 60 * 60);
        
        $setting = Setting::get(['sign_in_award']);
        $signAward = explode("," , $setting['sign_in_award']);
        if (!$info['sign_times']) {
            $signtime = 1;
            $data = json_encode(['times' => $signtime,'add' =>date("Y-m-d H:i:s")]);
        } else {
            
            /**判断今日是否已经签到 */
            $sign =  json_decode($info['sign_times'], true);
            if ($sign['add'] > $nowday) {
                return json(['err' => 1, 'data' => ['times' => $sign['times'], 'intr' =>$signAward]]);
            }
            $signtime = $sign['add'] >= $before ? ($sign['times']+1) : 1;

            $data = json_encode(['times' => $signtime,'add' =>date("Y-m-d H:i:s")]);
        }
       /**更新数据 */
        $res = $user->updateInfo(['sign_times' => $data], ['id' => $info['id']]);
        $this->user['sign_times'] = $data;
        if (!$res) {
            return json(['err' =>1, 'data' =>['intr' =>$signAward]]);
        }
       /**签到奖励更新 */
        $maxSign = count($signAward);
        $signtime = $signtime > $maxSign ? $maxSign : $signtime;
        if ($signAward[$signtime-1]) {
            $moneyhistory = new MoneyHistory;
            /**判断今日是否已经领取奖励 */
            $count = $moneyhistory->where(['create_time' => ['>=', date("Y-m-d")],'remark' => '签到赠送金币','userid' => $this->user['id']])->count();
            if(!$count){
                $regSend = [
                    'userid' => session('uid'),
                    'money' => $signAward[$signtime-1],
                    'ext_name' => 'index/user',
                    'remark' => '签到赠送金币',
                    'type' => 4
                ];
                $moneyhistory ->write($regSend);
            }
        }

        /**初始化大转盘 */
        $dzp = new GameDzp;
        $dzpinfo = $dzp->get(['userid' => $this->user['id']]);
        if($dzpinfo){
            $dzpinfo = $dzpinfo->toArray();
            if($dzpinfo['last_time'] < date("Y-m-d")){
                $dzp->save(['count' => 3], ['userid' => $this->user['id']]);
            }
        }else{
            $dzp->insert([
                'userid' => $this->user['id'],
                'count' => 3,
            ]);
        }
        $active = new BActive($this->user);
        $active->addInterface(7,1);
        return json(['err' =>0 ,'data' => ['times' =>$signtime,'intr' =>$signAward]]);
    }


    /**
     *每日任务 
     */
    public function task()
    {   
        $active = new BActive($this->user);
        $res = $active->task(['pk10','brisk']);
        $this->assign('award',$res['active']);
        $this->assign('list',$res['list']);
        return $this->fetch('',['title' => "每日任务"]);
    }

    /**
     * 领取奖励
     * @param object $res
    */
    public function getAward($id)
    {
        $active = new BActive($this->user);
        return $active->giveactive($id);
    }

    /**绑定账户登录信息 */
    public function lockInfo()
    {
        if(request()->IsAjax()){
            $user = (new AUser)->get($this->user['id'])->toArray();
            $award = Setting::get(['lockTel_award'])['lockTel_award'];
            $qq = $user['qq'] ? 0 : 1;
            $wei = $user['wei'] ? 0 : 1;
            $safe_pwd = $user['safe_password'] ? 0 : 1;
            $tel = $user['tel'] ? substr_replace($user['tel'],"****",3,4) : 1;
            $email = $user['email'] ? substr_replace($user['email'],"****",2,6) : 1;
            $bankClass = new UserIdbank;
            $res = $bankClass->get(['userid' => $this->user['id']]);
           // $res = $name_rel ? $name_rel->toArray() : '';
            if($res){
                $res = $res->toArray();
                $idname = $res['idname'] ? mb_substr($res['idname'], 0, 1)."**" : '';
                $idnum = $res['idnum'] ? substr_replace($res['idnum'], "******", 3,11) : '';
            }
            $isnum = (isset($idnum) and $idnum) ? $idnum : 0;
            $isname = (isset($idname) and $idname) ? $idname : 0;

            $banks = $res ? json_decode($res['banks'], true) : [];
            $wx_open = 0;$zfb_open = 0;$bank_open = 0;
            if (!empty($banks)) {
                foreach ($banks as $v) {
                    if ($v['type'] == 1) {
                        $bank_open = 1;
                    } elseif ($v['type'] == 2) {
                        $zfb_open = 1;
                    } elseif ($v['type'] == 3) {
                        $wx_open = 1;
                    }
                }
            }
            $info = [
                'username' => $user['username'] ? $user['username'] : 1,
                'qq' => $qq,
                'wei' => $wei,
                'tel' => $tel,
                'email' => $email,
                'lockTelAward' => $award,
                'wx_open' => $wx_open,
                'zfb_open' => $zfb_open,
                'bank_open' => $bank_open,
              //  'is_rel_name' => $res ? 1 : 0,
                'is_name' => $isname,
                'is_num' => $isnum,
                'safe_pwd' => $safe_pwd,
            ];
            return json(['err' => 0, 'data' => $info]);
        }
        return $this->fetch('', ['title' => '绑定信息']);
    }

       
    /**
     * 绑定手机号发送验证码
     */
    public function sendSms()
    {
        if(request()->IsAjax()){
            $data = input('get.');
            $user = new AUser;
            $info = $user->getInfoWhere(['tel' => $data['tel']]);
            if($info) return json(['err' =>1,'msg' =>'该手机号已绑定']);
            $res = Sms::setSmsCode($data['tel'] , 'lock_tel_info');
            return $res;
        }
    }

    /**绑定手机号 */
    public function lockTel()
    {
        if(request()->get()){
            $data = input('get.');
            $user = new AUser;
            $info = $user->getInfoWhere(['tel' =>$data['tel']]);
            if($info) {
                return json(['err' =>1,'msg' =>'该手机号已绑定']);
            }
            $checkSms = Sms::getSmsCode($data['tel'], 'lock_tel_info', $data['yzm']);
            if($checkSms['err'] > 0){
                return json($checkSms);
            }
            /**删除短信Session*/
            isset($checkSms['smskey']) ? Session::delete($checkSms['smskey']) : '';
            $res = $user->updateInfo(['tel'=>$data['tel']],['id' => $this->user['id']]);

            if(!$res) {
                return json(['err' =>2, 'msg' =>'绑定失败']);
            }

            /**获取绑定手机奖励*/
            $setting = Setting::get(['lockTel_award']);
            $lockTel_award = intval($setting['lockTel_award']);
            if($lockTel_award > 0){
                $MoneyHistory = new MoneyHistory;
                $data = [
                    'userid' =>$this->user['id'],
                    'money' =>$lockTel_award,
                    'remark' => '绑定手机赠送金币',
                    'type' => 4
                ];
                $MoneyHistory ->write($data);
            }
            $this->user['tel'] = $data['tel'];
            return json(['err' =>0, 'msg' =>'绑定成功']);
        }
    }
    
    /**绑定用户名 */
    public function lockName()
    {
        if(request()->get()){
            $user = new AUser;
            $data = input('get.');
            $info = $user->get(['username' => $data['name']]);
            
            if($info){
                return json(['err' => 1, '该用户名已绑定']);
            }
            $saveData = [
                'username' =>$data['name'],
                'password' =>$data['pwd'],
                'password2' =>$data['pwd2'],
            ];
            $res = $user->updateInfo($saveData,['id' => $this->user['id']]);
        
            if(isset($res['msg']) || !$res) {
                $msg = $res ? $res['msg'] : '绑定失败';
                return json(['err' =>2, 'msg' =>$msg]);
            }
            return json(['err' =>0, 'msg' =>'绑定成功']);
        }
    }
 

    /**微信生成二维码*/
    public function setQrcode($type = 0)
    {
        header("Content-type: image/png");
        $value = 'http://h5.onecai.cn/index/Weilogin/tocheck';
        if($type){
            $value.= '?sid='.$this->user['sid'];
        }
        $qr = new QR($value);
        $logo = isset($_GET['logo']) ? $_GET['logo'] : ROOT_PATH . '/public/uploads/logo_s.png';//必须绝对路径
        $q = $qr->Q($logo,4);
        die();
    }

    
    /**查看通知列表 */
    public function getInform()
    {
        if(request()->IsAjax()){
            $res = (new Inform) ->getAdminList(['userid' =>$this->user['id']]);
            if(!$res) return ['err' => 1];
            $res->append(['userinfo']);
            $res = $res->toArray();
            $this->setInformStatus(array_column($res['data'],'id'));
            $res['err'] = 0;
            return json($res);
        }
        return $this->fetch('inform',['title' => '系统通知']);
    }

    /**
     * 修改系统通知已读状态
     * @param array|int $id  通知id或者id组成的数组
     * @return  setStatus
     */

    public function setInformStatus($id)
    {
         $id  = is_array($id) ? ['in', $id] : $id;
         (new Inform)->save(['statuss' => 1], ['id' => $id]);
    }
    /** 删除通知*/
    public function deleteInform()
    {
        $data = request()->get();
        if(empty($data)){
            return json(['err' => 1, 'msg' => '操作错误']);
        }
        $res = (new Inform)->deleteData($data, $this->user['id']);
        if($res){
            return json(['err' => 1, 'msg' => '删除成功']);
        } 
        return json(['err' => 1, 'msg' => '删除失败']);
    }

    /**获取通知数 */
    public function getInformNum()
    {
        $res = (new Inform)->getCount(['userid' =>$this->user['id'],'statuss' =>0]);
        if(!$res) {
            return json(['err' => 1, 'data' =>0]);
        }
        return json(['err' => 0 , 'data' =>$res]);

    }

    /**
     * 赠送鲜花
     *   $data = [
     *       'getid' => 226,
     *      'flower' => 100
     *  ];
     */
    public function sendFlower()
    {
        $user = new AUser;
        $info = $user->get($this->user['id']);
        if(!$info){
            return json(['err' => 1, 'msg' => '用户信息错误']);
        }
        $flower = $info->toArray()['flower'];

        $data = request()->get();
        if($data['flower'] > $flower) {
            return json(['err' => 2001, 'msg' => '鲜花数量不足，请先充值']);
        }

        $getUser = $user->get($data['getid']);
        if(!$getUser){
            return json(['err' => 2, 'msg' => '赠送的用户不存在']);
        }
        $getUserId = $getUser->toArray()['id'];
        $gift = [
            'userid' => $this->user['id'],
            'gift_userid' => $getUserId,
            'num' => $data['flower']
        ];
        $res = (new  FlowerGift)->addGift($gift);
        return json($res);
    }

    public function getFlowerList()
    {
        $list = (new FlowerHistory)->getList(['field' => 'money,remark,create_time'],['userid' => $this->user['id']]);
        $list = $list->toarray();
        $list['err'] = 0;
        return json($list);
    }

    public function flowerList()
    {
        return $this->fetch('flower',['title'=>'鲜花赠送明细']);
    }

   /**实名认证 */
   public function trueName()
   {
       $data = request()->post();
       $bankClass = new UserIdbank;
       if($bankClass->get(['userid' => $this->user['id']])){
           return json(['err' => '2', 'msg' => '您已认证，无须重复认证']);
       }
       $data['idnum'] = trim($data['idnum']);
       $idRes = $this->checkIdCard($data['idnum']);
       if(!$idRes){
           return json(['err' => 1, 'msg' => '身份证号码不正确']);
       }
       $data['userid'] = $this->user['id'];
       $res = (new UserIdbank)->addTrue($data);
       return json($res);
   }
    public function realName()
    {
          return $this->fetch('real_name',['title'=>'实名认证']);
    }
    public function bank()
    {
          return $this->fetch('bank',['title'=>'银行卡管理']);
    }
    public function addBank()
    {
           $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
           if($res){
               $res = $res->toArray();
               $idname = mb_substr($res['idname'], 0, 1)."**";
           }
           $name = isset($idname) ? $idname : '';
           return $this->fetch('add_bank',['title'=>'添加银行卡', 'name' => $idname,'fullname' => $res['idname']]);
    }
    public function alipay()
    {
          $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
          if($res){
              $res = $res->toArray();
              $idname = mb_substr($res['idname'], 0, 1)."**";
          }
          $name = isset($idname) ? $idname : '';
          return $this->fetch('alipay',['title'=>'支付宝绑定', 'name' => $idname]);
    }

   /**获取实名认证信息 */
   public function getTrueInfo()
   {
       $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
       if($res and $res['idname'] and $res['idnum']){
           $res = $res->toArray();
           $data['idname'] = mb_substr($res['idname'], 0, 1)."**";
           $data['idnum'] = substr_replace($res['idnum'], "******", 3,13);
           return json(['err' => 0, 'data' => $data]);
       }
       return json(['err' => 1, 'msg' => '您还未实名认证']);
   }

   /**绑定银行卡 */
   public function lockBank()
   {
       $data = request()->post();
       $bankClass = new UserIdbank;
       $res = $bankClass->get(['userid' => $this->user['id']]);
       if(!$res){
        return json(['err' => 1, 'msg' => '您还未实名认证']);
       }

       $request = !empty($data['type']) && !empty($data['name']) && !empty($data['numbers']);
       $res = $res->toArray();
       /**银行卡判断卡号是否符合规则 */
       if($data['type'] == 1){
           $request = $request && !empty($data['openname']);
           $num = strlen($data['numbers']) - 1;
           if($num < 15 || $num > 20){
               return json(['err' => 2, 'msg' => '请输入正确的卡号']);
           }
           $sum = 0;
           $n = 0;
           for ($i = $num; $i >=0 ; $i--) { 
              if($n%2 == 0){
                  $sum += $data['number'][$i];
              }else{
                  $_sum = $data['number'][$i] * 2;
                  if($_sum > 9){
                      $tt = intval($_sum/10) + $_sum%10;
                  }else{
                      $tt = $_sum;
                  }
                  $sum += $tt;
              }
              $n++;
           }
           if($sum % 10 != 0){
               return json(['err' => 2, 'msg' => '请输入正确的卡号']);
            }
       }
       if(!$request){
           return json(['err' => 3, 'msg' => '参数错误']);
       }
       $before = json_decode($res['banks'], true);
       $before = $before ? $before : [];
       $numbers = array_column($before, 'numbers');
       if(in_array($data['numbers'], $numbers)){
           return json(['err' => 5, 'msg' => '该卡号已存在']);
       }
       $data['sign'] = count($before) + 1;
       array_push($before, $data);
       $result = $bankClass->where('id', $res['id'])->setField('banks', json_encode($before));
       if($result){
           return json(['err' => 0, 'msg' => '绑定成功']);
       }
       return json(['err' => 4, 'msg' => '绑定失败']);

   }

   /**获取银行卡*/
   public function getBanks($type = 1)
   {
       $bankClass = new UserIdbank;
       $res = $bankClass->get(['userid' => $this->user['id']]);
       if(!$res){
        return json(['err' => 1, 'msg' => '您还未实名认证']);
       }
       $res = $res->toArray();
       $banks = json_decode($res['banks'], true);
       if(empty($banks)){
           return json(['err' => 2, 'msg' => '您还未绑定银行卡']);
       }
       $need = [];
        foreach ($banks as &$value) {
            if($value['type'] == $type){
                $value['numbers'] = mb_substr($value['numbers'], 0, 3, 'utf-8')."*********".mb_substr($value['numbers'], -2, 2, 'utf-8');
                $need[] = $value;
            }
        }
       return json(['err' =>0, 'data' => $need]);

   }

   /**删除银行卡 */
   public function  deleteBank($sign)
   {
        $bankClass = new UserIdbank;
        $res = $bankClass->get(['userid' => $this->user['id']]);
        if(!$res){
        return json(['err' => 1, 'msg' => '您还未实名认证']);
        }
        $res = $res->toArray();
        $banks = json_decode($res['banks'], true);
        $signs = array_column($banks, 'sign');
        $keys = array_search($sign, $signs);
        array_splice($banks, $keys, 1);
        $result = $bankClass->where('id', $res['id'])->setField('banks', json_encode($banks));
        if($result){
            return json(['err' => 0, 'msg' => '删除成功']);
        }
        return json(['err' => 4, 'msg' => '删除失败']);


   }

    /**身份证号码验证 */
   public function checkIdCard($idcard)
   {  
       // 只能是18位  
       if(mb_strlen($idcard)!=18){  
           return false;  
       }  
     
       // 取出本体码  
       $idcard_base = substr($idcard, 0, 17);  
     
       // 取出校验码  
       $verify_code = substr($idcard, 17, 1);  
     
       // 加权因子  
       $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);  
     
       // 校验码对应值  
       $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');  
     
       // 根据前17位计算校验码  
       $total = 0;  
       for($i=0; $i<17; $i++){  
           $total += substr($idcard_base, $i, 1)*$factor[$i];  
       }  
     
       // 取模  
       $mod = $total % 11;  
     
       // 比较校验码  
       if($verify_code == $verify_code_list[$mod]){  
           return true;  
       }else{  
           return false;  
       }  
   }

    public function getBagList()
    {
        $list = (new UserBag())->where('userid', $this->user['id'])->where('num','neq',0)->select();
        $list = $list->append(['pic', 'name', 'desc', 'url'])->toarray();
        return json($list);
    }

    public function userBag()
    {
        return $this->fetch('user_bag',['title'=>'我的背包']);
    }
}
