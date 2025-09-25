<?php
namespace app\web\controller;

use app\common\model\Recharge;
use app\common\model\Statis;
use org\ValidateIdCard;
use think\Controller;
use core\Setting;
use core\Sms;
use think\Validate;
use app\index\model\ExtShowList;
use app\web\model\UserIdbank;
use app\web\model\User as AUser;
use app\common\model\Inform;
use app\common\model\MoneyHistory;
use app\common\model\GameMoneyHistory as Diamonds;
use app\index\model\MsgArticle;
use app\admin\model\Shop;
use app\admin\model\ShopNav;
use app\admin\model\ShopOrder;
use app\common\controller\Email;
use app\index\controller\Qqlogin;
use app\web\controller\Common;
use app\common\model\UserBag;
use think\Session;

class User extends UserBase
{
    /**
     * 会员中心首页
     */
    public function my()
    {
        $setting = (new Setting)->get(['user_level']);
        if ($setting['user_level']) {
            $new_user_level = [];
            $user_level = json_decode($setting['user_level'], true);
            foreach ($user_level as $key => $v) {
                $new_user_level[$key]['grade'] = 'VIP' . $v['level'];
                $new_user_level[$key]['gradeName'] = $v['level_name'];
                $new_user_level[$key]['gradeGrow'] = $v['explan'];
            }
            $setting['user_level'] = $new_user_level;
        }
        $data = $this->telEmail();

        $name = $this->getTrueStatus(1);
        $wei = $this->getTrueStatus(3);
        $alipay = $this->getTrueStatus(2);

        return $this->fetch('',['data' => $data, 'user_grade' => collection((new AUser())->getGrade($this->user)['data']),
         'gradeList' => collection($setting['user_level']), 'banks' => $name['banks'] ,'isname' => $name['name'],'wei_banks' => $wei['banks'],'alipay_banks' => $alipay['banks']]);
    }

    /**
     * 会员主页
     */
    public function main()
    {
        $extShow = new ExtShowList;
        $rec = $extShow->field('name,title,type,img')->where('reco', 1)->where('status',0)->limit(0,4)->order('sort ASC')->select();
        $rec = $rec->toArray();
        foreach ($rec as &$value) {
            $value['name'] = (new Common)->setUrlBase($value['name']);
        }
        $static = $this->getStatic(false);
        return $this->fetch('',['rec'=> $rec, 'extName'=> $this->getExtList()['data'], 'all'=> $static['coin'], 'diamond' => $static['diamond']]);
    }
     /**
     * 获取最近玩过的游戏列表
     */
    public function getExtList()
    {
        $extShow = new ExtShowList;
        $extList = [];
        if($this->user['extname'] ?? ''){
            $ext = explode('|', $this->user['extname']);
            krsort($ext);
            $n = 0;
            foreach ($ext as $vale) {
                if($n >= 6){
                    break;
                }
                $name = '';
                $val = explode('_', $vale);
                if(count($val) > 1){
                    if($val[1] == 'dishu' || $val[1] == 'guess' || $val[1] == 'poker'){
                        $name = 'game/'.$val[1];
                    }
                    $name = $name ? $name : $val[1];
                    $title = $extShow->field('name,title,type,image')->where('name', 'in', ['/'.$name, $name])->find();
                    if(!$title) continue;
                    $url = $val[0] == 'Pk10' ? url("./Pk10/index") : url("./".$val[0].'/index',['name'=>$val[1]]);
                    $data['url'] = $val[0] == 'Game' ?  url("./".$val[0].'/'.$val[1]) : $url;
                    $data['info'] = $title->toarray();
                    $extList[] = $data;
                    $n++;
                }
            }
        }
         return ['data'=> $extList];
    }
     /**
     * 获取消费统计数据
     */
    public function getStatic($is_json = true)
    {
        $param = request()->param();
        $where['create_time'] = ['>', date('Y-m-01 00:00:00', time())];
        if (!empty($param['starttime']) and empty($param['endtime'])) {

            $where['create_time'] = ['>=', date('Y-m-d H:i:s' , strtotime($param['starttime']))];

        } elseif (!empty($param['endtime']) and empty($param['starttime'])) {

            $where['create_time'] = ['<=', date('Y-m-d H:i:s' , strtotime($param['endtime'] . "+1day"))];

        } elseif (!empty($param['endtime']) and !empty($param['starttime'])) {
            $where['create_time'] = array(array('>=', date('Y-m-d H:i:s' , strtotime($param['starttime']))), array('<=', date('Y-m-d H:i:s' , strtotime($param['endtime'] . "+1day"))), 'and');
        }
        $moenyHistroy = new MoneyHistory;
        $data['allSpend'] = $moenyHistroy->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->where('type', 0)->sum("money") ?? '0.00';
        $data['allAward'] = $moenyHistroy->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->where('type', 1)->sum("money") ?? '0.00';
        $data['longSpend'] = $moenyHistroy->where('userid', $this->user['id'])->where($where)->where('type', 0)->sum("money") ?? '0.00';
        $diamond = new Diamonds;
        $diam['todayS'] = $diamond->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->where('type', 0)->sum("money") ?? '0.00';
        $diam['todayA'] = $diamond->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->where('type', 0)->sum("money") ?? '0.00';
        $diam['diamondsSpend'] = $diamond->where('userid', $this->user['id'])->where($where)->where('type', 0)->sum("money") ?? '0.00';
        if ($is_json) {
            return json(['coin'=> $data, 'diamond' => $diam]);
        }
        return ['coin'=> $data, 'diamond' => $diam];
    }

    public function setUrl($name)
    {
        $ssc = explode('ssc',$name);
        if(count($ssc) > 1){
            return  $name = 'Ssc/index/name/'.ltrim($name, '/');
        }
        $sy = explode('11',$name);
        if(count($sy) > 1){
            return  $name = 'Syxw/index/name/'.ltrim($name, '/');
        }
        $ks = explode('ks',$name);
        if(count($ks) > 1){
            return  $name = 'Ks/index/name/'.ltrim($name, '/');
        }
        return  $name = ltrim($name, '/');
    }
    /**
     * 游戏记录--休闲游戏
     */
    public function gameRecord()
    {
        $res =  (new ExtShowList)->getGamesList();
        $data = [];
        foreach($res as $key => $value){
            $data[] = [
                'value'=> $key,
                'label' => $value['title'],
                'name' => ltrim($value['name'], '/')
            ];
        }
        $this->assign('gameInfo', collection($data));
        return $this->fetch('game_record');
    }

    /**
     * 游戏记录--开奖游戏
     */
    public function lotteryRecord()
    {
        $res =  (new ExtShowList)->getGamesList(1);
        $data = [];
        foreach($res as $key => $value){
            $data[] = [
                'value'=> ltrim($value['name'], '/'),
                'label' => $value['title']
            ];
        }
        array_unshift($data, ['value' => '', 'label' => '所有彩种']);
        $this->assign('lotteryInfo', collection($data));
        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['lottery_unit','coin_lottery']);
        $this->assign('system', collection($system));
        return $this->fetch('lottery_record');
    }

    /**
     * 追号记录
     */
    public function chaseRecord()
    {
        $res =  (new ExtShowList)->getGamesList(1);
        $data = [];
        foreach($res as $key => $value){
            $data[] = [
                'value'=> ltrim($value['name'], '/'),
                'label' => $value['title']
            ];
        }
        array_unshift($data, ['value' => '', 'label' => '所有彩种']);
        $this->assign('lotteryInfo', collection($data));
        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['lottery_unit','coin_lottery']);
        $this->assign('system', collection($system));
        return $this->fetch('chase_record');
    }

    /**
     * 账户明细
     */
    public function detail()
    {
        /**游戏模块信息*/
        $result = $this->getExt();
        return $this->fetch('',['extList' => $result]);
    }
    /**
     * 钻石明细
     */
    public function diamonds()
    {
        /**游戏模块信息*/
        $result = $this->getExt();
        $this->assign('extList',$result);
        /**钻石方式 */
        $diamond = [['value' => '', 'label' =>'所有方式'],['value' => 1, 'label' =>'充值'],['value' => 2, 'label' =>'消费赠送'],['value' => 3, 'label' =>'系统奖励'],['value' => 4, 'label' =>'系统扣除']];
        $this->assign('diamond', collection($diamond));
        return $this->fetch('detail_diamonds');
    }

    /**
     * 转账明细
     */
    public function dtransform()
    {
        return $this->fetch('detail_transform');
    }

    public function getExt()
    {
        $res = (new ExtShowList)->field('name,title,type')->where('status', 0)->select();
        $res = $res->toArray();
        foreach($res as $key => $value){
            $name = ltrim($value['name'], '/');
            $name = $value['type'] == 1 ? 'lottery_'.$name : $name;
            $result[$key] = ['label' => $value['title'], 'value' => $name];
        }
        array_unshift($result, ['value' => '', 'label' => '所有交易来源']);
        return collection($result);
    }
    /**
     * 充值明细
     */
    public function drecharge()
    {
        /**充值方式 */
        $setting = Setting::get(['sao_pay','other_pay']);
        $pay_way = [];
        if($setting['other_pay']){
            $other_pay = json_decode($setting['other_pay'],true);
            foreach ($other_pay as $value) {
                if($value['value'] ==1){
                    $title = $value['name'] == 'other_wx' ? '微信充值' : '支付宝充值';
                    $value = $value['name'] == 'other_wx' ? 0 : 1;
                    array_push($pay_way,['value'=>$value, 'label' =>$title]);
                }
            }
        }
        if($setting['sao_pay']){
            $sao_pay = json_decode($setting['sao_pay'],true);
            foreach ($sao_pay as $value) {
                if($value['value'] == 1){
                    $title = $value['name'] == 'pay_wx' ? '微信扫码' : '支付宝扫码';
                    $value = $value['name'] == 'pay_wx' ? 2 : 3;
                    array_push($pay_way,['value'=>$value, 'label' =>$title]);
                }
            }
        }
        array_unshift($pay_way, ['value' => '', 'label' => '所有充值方式']);
        $this->assign('pay_way', collection($pay_way));
        return $this->fetch('detail_recharge');
    }
    /**
     * 兑换明细
     */
    public function dexchange()
    {
        return $this->fetch('detail_exchange');
    }


    /**
     * 系统消息
     */
    public function inform()
    {
        return $this->fetch();
    }

    /**
     * 实名认证
     */
    public function idCard()
    {
        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        if($res){
            $res = $res->toArray();
            $idname = $res['idname'] ? mb_substr($res['idname'], 0, 1)."**" : '';
            $idnum = $res['idnum'] ? substr_replace($res['idnum'], "******", 3,13) : '';
        }
        $isCard = (isset($idnum) and $idnum) ? 1 : 0;
        $isName = (isset($idname) and $idname) ? 1 : 0;
        $idname = (isset($idname) and $idname) ? $idname : '';
        $idnum = (isset($idnum) and $idnum) ? $idnum : '';

        $data = $this->telEmail();
        return $this->fetch('id_card',['isCard' => $isCard,'isName' => $isName,'idname' => $idname,'idnum' => $idnum,'data' => $data]);
    }

    /**
     * 绑定银行卡
     */
    public function bank()
    {
        $name = $this->getTrueStatus(1);
        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        $isname = (isset($res['idname']) and $res['idname']) ? 1 : 0;
        $isnum = (isset($res['idnum']) and $res['idnum']) ? 1 : 0;
        return $this->fetch('',['isname'=>$isname,'isnum'=>$isnum ? 1 : 0,'idname'=>$name['name'],'banks' => $name['banks']]);
    }

    /**
     * 添加银行卡
     */
    public function addBank()
    {
        $name = $this->getTrueStatus(2);
        return $this->fetch('add_bank',['isname'=>collection($name)]);
    }

    /**
     * 绑定支付宝账号
     */
    public function alipay()
    {
        $name = $this->getTrueStatus(2);
        return $this->fetch('',['isname'=>$name['name'],'banks' => $name['banks']]);
    }
    /**
     * 绑定微信账号
     */
    public function wei()
    {
        $name = $this->getTrueStatus(3);
        return $this->fetch('',['isname'=>$name['name'],'banks' => $name['banks']]);
    }

    public function getTrueStatus($type)
    {
        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        $need = [];
        if($res){
            $res = $res->toArray();
            $idname = $res['idname'] ? mb_substr($res['idname'], 0, 1)."**" : '';
            $banks = json_decode($res['banks'], true);
            if(!empty($banks)){
                foreach ($banks as &$value) {
                    if($value['type'] == $type){
                        $value['numbers'] = mb_substr($value['numbers'], 0, 3, 'utf-8')."*********".mb_substr($value['numbers'], -2, 2, 'utf-8');
                        $need[] = $value;
                    }
                }
            }
        }
        $name = isset($idname) ? $idname : '';
        return ['name' => $name, 'banks' => collection($need)];
    }
    /**
     * 收货地址
     */
    public function address()
    {
        return $this->fetch('');
    }

    /**
     * 个人资料
     */
    public function info()
    {
        $data = $this->telEmail();
        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        if($res){
            $res = $res->toArray();
            $idname = $res['idname'] ? mb_substr($res['idname'], 0, 1)."**" : '';
        }
        $idname = (isset($idname) and $idname) ? $idname : '';
        $isname = (isset($idname) and $idname) ? 1 : 0;
        return $this->fetch('',['data' => $data,'idname' => $idname,'isname' => $isname]);
    }

    /**
     * 安全中心
     */
    public function safe()
    {
        $setting = Setting::get(['weiopenappid','weiopenurl','wxsm_checked','qq_checked']);
        $csrf = md5(uniqid(rand(), TRUE))."_1_";
        session('login_csrf',$csrf);
        $setting['state'] = $csrf;
        $setting['weiopenurl'] = urlencode($setting['weiopenurl']);

        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        if($res){
            $res = $res->toArray();
            $idname = $res['idname'] ? mb_substr($res['idname'], 0, 1)."**" : '';
            $idnum = $res['idnum'] ? substr_replace($res['idnum'], "******", 3,13) : '';
        }
        $isCard = (isset($idnum) and $idnum) ? 1 : 0;
        $idname = (isset($idname) and $idname) ? $idname : '';
        $idnum = (isset($idnum) and $idnum) ? $idnum : '';

        $data = $this->telEmail();

        return $this->fetch('', ['data' => $setting,'isCard' => $isCard,'idname' => $idname,'idnum' => $idnum,'telEmail' => $data]);
    }

    /**
     * 兑换中心
     */
    public function exchange()
    {
        $nav = (new ShopNav)->field('id,name')->where('status', 1)->select();
        if($nav){
            $navList = $nav->toArray();
            $list = (new Shop)->where('type', $navList[0]['id'])->where('status', 1)->select();
        }
        $list = isset($list) ? $list : [];
        $data = $this->telEmail();

        /**判断取款（兑换时间）时间 */
        $setting = (new Setting)->get(['getmoney_startime', 'getmoney_endtime', 'getmoney_times','getmoney_low']);
        $setting['isTochange'] = 1;
        $day = date("Ymd");
        $nowtimestamp = time();
        $isnext = $setting['getmoney_startime'] > $setting['getmoney_endtime'] ? 1 : 0;
        $starttime = strtotime($day." ".$setting['getmoney_startime']);
        $endtime = strtotime($day." ".$setting['getmoney_endtime']);
        $endtime = $isnext ? ($endtime + 24 * 60 * 60) : $endtime;
        if($isnext && ($nowtimestamp <  $starttime && $nowtimestamp > ($endtime + 24 * 60 * 60))){
            $setting['isTochange'] = 0;
        }
        if(!$isnext && ($nowtimestamp <  $starttime || $nowtimestamp > $endtime)){
            $setting['isTochange'] = 0;
        }

        $todayTime = (new ShopOrder)->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->count();
        $setting['ure_times'] = $setting['getmoney_times'] - $todayTime;

        $user = (new AUser)->get($this->user['id'])->toArray();
        $isPwd = $user['safe_password'] ? 1 : 0;
        return $this->fetch('',['nav' => $nav, 'list' => $list , 'data' => $data, 'getMoneyInfo' => $setting, 'isPwd' => $isPwd]);
    }


    /**根据分类查询 */
    public function getExchangeType()
    {
        if(request()->isGet()){
            $data = request()->get();
            if(!isset($data['type']) || !$data['type']){
                return json(['err' => 1, 'msg' => '操作错误']);
            }
            $type = intval($data['type']);
            $list = (new Shop)->where('type', $type)->select();
            return json(['err' => 0, 'data' => $list]);
        }
    }
    /**
     * 密码修改
     */
    public function setPwd()
    {
        $isBind = $this->user['username'] ? 1 : 0 ;
        $this->assign('isBind', $isBind);

        $data = $this->telEmail();
        return $this->fetch('set_pwd',['title' => '登录密码管理','data' => $data]);
    }

    /**
     * 手机管理
     */
    public function setTel()
    {
        $data = $this->telEmail();
        return $this->fetch('set_tel',['title' => '手机管理','data' => $data]);
    }
    /**
     * 邮箱管理
     */
    public function setEmail()
    {
        $data = $this->telEmail();
        return $this->fetch('set_email',['title' => '邮箱管理','data' => $data]);
    }
    /**
     * 游戏道具
     */
    public function props()
    {
        $list = (new UserBag())->where('userid', $this->user['id'])->where('num','neq',0)->select();
        $list = $list->append(['pic', 'name', 'desc', 'url']);
        return $this->fetch('',['title' => '游戏道具','data'=>$list]);
    }

    /**实名认证 */
    public function trueName()
    {
        $data = request()->post();
        $bankClass = new UserIdbank;
        $rzsmqt=$bankClass->get(['userid' => $this->user['id']]);
        if($rzsmqt and $rzsmqt["idnum"]!=""){
            return json(['err' => '2', 'msg' => '您已认证，无须重复认证']);
        }

        $res = $this->checkMsgEmail($data, ['email' => 'id_card', 'tel' => 'true_info']);
        if(isset($res['err'])){
            return json($res);
        }

        $data['idnum'] = trim($data['idnum']);
        $idRes = $this->checkIdCard($data['idnum']);
        if(!$idRes){
            return json(['err' => 1, 'msg' => '身份证号码不正确']);
        }
        $data['userid'] = $this->user['id'];
        if ($rzsmqt['idname']) {
            $data['idname'] = $rzsmqt['idname'];
        }
        $res = (new UserIdbank)->addTrue($data);
        return json($res);
    }

    /**获取实名认证信息 */
    public function getTrueInfo()
    {
        $res = (new UserIdbank)->get(['userid' => $this->user['id']]);
        if($res){
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
                    $sum += $data['numbers'][$i];
                }else{
                    $_sum = $data['numbers'][$i] * 2;
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
    public function  deleteBank($sign = '')
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
    /**修改银行卡 */
    public function  editBank()
    {
        if(request()->isPost()){
            $data = request()->post();
            $bankClass = new UserIdbank;
            $res = $bankClass->get(['userid' => $this->user['id']]);
            if(!$res){
                return json(['err' => 1, 'msg' => '您还未实名认证']);
            }
            $res = $res->toArray();
            $banks = json_decode($res['banks'], true);
            foreach ($banks as &$value) {
                if($value['type'] == $data['type'] && $value['sign'] == $data['sign']){
                    $value['numbers'] = $data['numbers'];
                    break;
                }
            }
            $result = $bankClass->where('id', $res['id'])->setField('banks', json_encode($banks));
            if($result){
                return json(['err' => 0, 'msg' => '修改成功']);
            }
            return json(['err' => 4, 'msg' => '修改失败']);
        }
    }

    /**身份证号码验证 */
    public function checkIdCard($cid)
    {
        /*  // 只能是18位
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
         } */
        $cid = strtoupper($cid);
        return (new ValidateIdCard)->ValidateIdCard($cid);
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
     * 删除
     */
    public function messageDelete()
    {
        if(request()->isPost()){
            $data = request()->post();
            $msg = new MsgArticle;
            if(isset($data['id']) && $data['id'] != 'all'){
                $msg ->where('id', $data['id']);
            }
            if(isset($data['type'])){
                $status = $data['type'] == 1 ? 1 : 0;
                $msg ->where('status', $status);
            }
            $res = $msg->where('userid', $this->user['id'])->where('send_userid', 0)->delete();
            if($res){
                return json(['err' => 0, 'msg' => '删除成功']);
            }
            return json(['err' => 1, 'msg' => '删除失败']);
        }
    }

    /**
     * [会员获取消息]
     * @param  string $type  0为全部  12未读
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
            $list = $msg->where("status", 0)->where('send_userid', 0)->order(['status'=>' asc','create_time' => 'desc'])->paginate($pageSize);
        }
        $list = $list->append(['username','send_username']);
        foreach ($list as &$value) {
            $value['read'] = $value['status'] == 1 ? 'read' : 'noread';
        }
        if (empty($list)) {
            return json(['err'=>0]);
        } else {
            $list = $list->toArray();
            $list['err'] = 0;
            return json($list);
        }
    }

    /**
     * 消息状态设置
     */
    public function setStatus($id)
    {
        $msg = new MsgArticle;
        if($id == 'all'){
            $msg->where('status', 0);
        }else{
            $msg->where('id', $id);
        }
        $res = $msg->update(['status' => 1]);
        if($res){
            return json(['err' => 0, 'msg' => '设置成功']);
        }
        return json(['err' => 1, 'msg' => '设置失败']);
    }

    /**获取手机邮箱是否开启*/
    public function telEmail()
    {
        $setting = Setting::get(['tel_checked', 'email_checked']);
        $data['tel'] = [
            'isOpen' => $setting['tel_checked'] ?? 0,
            'islock' => ($this->user['tel'] ?? '') ? 1 : 0,
            'number' => ($this->user['tel'] ?? '') ? substr_replace($this->user['tel'],'****',3,4) : ''
        ];
        $email = $this->user['email'] ?? '';
        if($email){
            $emailArr = explode('@', $email);
            $len = mb_strlen($emailArr[0]);
            $email = substr_replace($emailArr[0], '****', 2, $len-4);
            $email = isset($emailArr[1]) ? $email ."@".$emailArr[1] : null;
        }
        $data['email'] = [
            'isOpen' => $setting['email_checked'] ?? 0,
            'islock' => $this->user['email'] ? 1 : 0,
            'number' => $email
        ];
        return $data;
    }

    /**邮箱发送验证码 --- 绑定邮箱*/
    public function sendLockEmail($email)
    {
        $res = (new Email)->send($email, 'lock', '绑定邮箱');
        return json($res);
    }

    /**绑定邮箱 */
    public function lockEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            $setting = Setting::get(['email_checked']);
            $data['yzm'] = $setting['email_checked'] ? $data['yzm'] : '';
            if(!isset($data['email']) || !isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $res = (new Email)->checkSend($data['email'], 'lock', $data['yzm']);
            if($res['err']){
                return json($res);
            }
            $res = (new AUser)->where('id', $this->user['id'])->setField('email', $data['email']);
            if($res){
                return json(['err' => 0, 'msg' => '绑定成功']);
            }
            return json(['err' => 1, 'msg' => '绑定失败']);
        }
    }
    /**邮箱发送验证码 --- 验证原邮箱*/
    public function sendOldEmail()
    {
        $res = (new Email)->send($this->user['email'], 'old_email', '修改邮箱验证原邮箱');
        return json($res);
    }

    /**发送短信 --- 修改邮箱*/
    public function sendMsgEmail()
    {
        $res = Sms::setSmsCode($this->user['tel'] , 'old_emailtel');
        return json($res);
    }

    /**邮箱发送验证码 --- 验证原邮箱*/
    public function checkOldEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '请输入验证码']);
            }
            $res = $this->checkMsgEmail($data, ['email' => 'old_email', 'tel' => 'old_emailtel']);
            if(!isset($res['err'])){
                return json(['err' => 0, 'msg' => '验证成功']);
            }
            return json($res);
        }
    }

    /**邮箱发送验证码 --- 绑定新邮箱*/
    public function sendNewEmail($email)
    {
        $res = (new Email)->send($email, 'new_email', '绑定新邮箱');
        return json($res);
    }

    /**绑定新邮箱 */
    public function checkNewEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            $setting = Setting::get(['email_checked']);
            $data['yzm'] = $setting['email_checked'] ? $data['yzm'] : '';
            if(!isset($data['email']) || !isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $res = (new Email)->checkSend($data['email'], 'new_email', $data['yzm']);
            if($res['err']){
                return json($res);
            }
            $res = (new AUser)->where('id', $this->user['id'])->setField('email', $data['email']);
            if($res){
                return json(['err' => 0, 'msg' => '绑定成功']);
            }
            return json(['err' => 1, 'msg' => '绑定失败']);
        }
    }
    /**邮箱发送验证码 --- 修改手机 */
    public function sendTelEmail()
    {
        $res = (new Email)->send($this->user['email'], 'change_tel', '修改手机号');
        return json($res);
    }

    /**邮箱发送验证码 --- 修改手机*/
    public function checkTelEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '请输入验证码']);
            }
            $res = $this->checkMsgEmail($data, ['email' => 'change_tel', 'tel' => 'changetel']);
            if(!isset($res['err'])){
                return json(['err' => 0, 'msg' => '验证成功']);
            }
            return json($res);
        }

    }

    /**邮箱发送验证码 --- 修改登录密码 */
    public function sendPwdEmail()
    {
        $res = (new Email)->send($this->user['email'], 'change_pwd', '修改登录密码');
        return json($res);
    }

    /**验证密码消息 */
    public function checkPwdEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['yzm'])){
                return json(['err' => 1, 'msg' => '请输入验证码']);
            }
            if($data['password'] != $data['password2']){
                return json(['err' => 1, 'msg' => '两次密码不一致']);
            }
            $newP = md5($data['password']);
            if($newP == $this->user['password']){
                return json(['err' => 1, 'msg' => '新密码不能和原密码一样']);
            }
            $res = (new Email)->checkSend($this->user['email'], 'change_pwd', $data['yzm']);
            if($res['err']){
                return json($res);
            }
            $result = (new AUser)->where('id', $this->user['id'])->setField('password', $newP);
            if($result){
                Session::delete('uid');
                Session::delete('sid');
                return json(['err' => 0, 'msg' => '修改成功']);
            }
            return json(['err' => 1, 'msg' => '修改失败']);
        }
    }

    /**邮箱发送验证码 --- 修改基本资料 */
    public function sendInfoEmail()
    {
        $res = (new Email)->send($this->user['email'], 'change_info', '修改基本资料');
        return json($res);
    }

    /**验证修改基本资料 */
    public function checkInfoChange()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = $this->checkMsgEmail($data, ['email' => 'change_info', 'tel' => 'changeinfo']);
            if(isset($res['err'])){
                return json($res);
            }
            $validate = new Validate([
                'nickname|昵称' => 'require|chsDash',
            //    'explan|个性签名' => 'require|chsDash'
            ]);
            $UserIdbank_model = (new UserIdbank());
            $has_info = $UserIdbank_model->where('userid', $this->user['id'])->find();
            $result = $validate->check($data);
            if ((!$has_info || !$has_info['idname']) and (!isset($data['real_name']) || !$data['real_name'])) {
                return ["err" => 1, "msg" => '请填写真实姓名'];
            }
            if ((!$has_info)) {
                $idbank_res = $UserIdbank_model->save(['userid' => $this->user['id'], 'idname' => $data['real_name']]);
            }
            if ($has_info and !$has_info['idname']) {
                $idbank_res = $UserIdbank_model->where(['userid' => $this->user['id']])->update(['idname' => $data['real_name']]);
            }
            if (!$result) {
                return ["err" => 1, "msg" => $validate->getError()];
            }
            $newData = ['nickname' => $data['nickname'], 'explan' => $data['explan'], 'sex' => $data['sex'], 'birth' => $data['birth']];
            if (isset($data['qq'])) {
                $newData['qq'] = $data['qq'];
            }
            $newData['update_time'] = date('Y-m-d H:i:s');
            $res = (new AUser)->where('id', $this->user['id'])->update($newData);
            if($res || (isset($idbank_res) and $idbank_res)){
                return json(['err' => 0, 'msg' => '修改成功']);
            }
            return json(['err' => 1, 'msg' => '修改失败']);
        }
    }

    /**提交兑换申请 */
    public function exchangeGift()
    {
        if(request()->isPost()){
            $data = request()->post();

            /**判断取款（兑换时间）时间 与次数*/
            $setting = (new Setting)->get(['getmoney_startime', 'getmoney_endtime', 'getmoney_times', 'getmoney_low', 'lottery_unit']);
            $day = date("Ymd");
            $nowtimestamp = time();
            $isnext = $setting['getmoney_startime'] > $setting['getmoney_endtime'] ? 1 : 0;
            $starttime = strtotime($day." ".$setting['getmoney_startime']);
            $endtime = strtotime($day." ".$setting['getmoney_endtime']);
            $endtime = $isnext ? ($endtime + 24 * 60 * 60) : $endtime;
            if($nowtimestamp <  $starttime && $nowtimestamp > ($endtime + 24 * 60 * 60)){
                return json(['err' => 1, 'msg' => '该时间不能进行兑换，请在兑换时间段进行兑换']);
            }
            if ($this->user['recharge_money'] > 0) {
                return json(['err' => 1, 'msg' => '您还需要消费' . $this->user['recharge_money'] . $setting['lottery_unit'] . ',才能兑换']);
            }
            $shopOrder = new ShopOrder;
            $todayTime = $shopOrder->where('userid', $this->user['id'])->where('create_time', '>=', date("Y-m-d"))->count();
            if($setting['getmoney_times'] - $todayTime <= 0){
                return json(['err' => 1, 'msg' => '今日兑换次数已用完']);
            }
            $res = $this->checkMsgEmail($data, ['email' => 'exchange_email', 'tel' => 'exchange_msg']);
            if(isset($res['err'])){
                return json($res);
            }
            $info = (new Shop)->field('name, total_num, convert_num,integral')->where('status',1)->where('id', $data['id'])->find();
            if(!$info){
                return json(['err' => 1, 'msg' => '商品错误']);
            }
            $info = $info->toArray();
            if($data['num'] > $info['total_num'] - $info['convert_num']){
                return json(['err' => 1, 'msg' => '兑换数量大于物品剩余数量']);
            }
            $allMoney = $data['num'] * $info['integral'];
            if ($allMoney < $setting['getmoney_low']) {
                return json(['err' => 11, 'msg' => '没达到最低兑换金额']);
            }
            if($allMoney > $this->user['money']){
                return json(['err' => 11, 'msg' => '余额不足']);
            }
            $saveData = [
                'userid' => $this->user['id'],
                'shop_id' => $data['id'],
                'num' => $data['num'],
                'money' => $allMoney
            ];
            $res =  $shopOrder->addData($saveData);
            if($res){
                (new MoneyHistory)->write(['userid' => $this->user['id'], 'money' => -$allMoney, 'type' => 3, 'remark' => '兑换:'.$info['name']]);
                return json(['err' => 0, 'msg' => '兑换成功']);
            }
            return json(['err' => 1, 'msg' => '兑换失败']);
        }
    }
    /**兑换 --- 发送手机验证码 */
    public function sendExchangeMsg()
    {
        if(request()->isGet()){
            $res = Sms::setSmsCode($this->user['tel'] , 'exchange_msg');
            return $res;
        }
    }
    /**兑换 --- 发送邮箱验证码 */
    public function sendExchangeEmail()
    {
        $res = (new Email)->send($this->user['email'], 'exchange_email', '兑换');
        return json($res);
    }

    /**邮箱发送验证码 --- 实名认证 */
    public function sendIdCardEmail()
    {
        $res = (new Email)->send($this->user['email'], 'id_card', '实名认证');
        return json($res);
    }

    public function sendTrueMsg()
    {
        if(request()->isGet()){
            $res = Sms::setSmsCode($this->user['tel'] , 'true_info');
            return $res;
        }
    }

    /***手机二选一邮箱验证 */
    public function checkMsgEmail($data, $sendtype)
    {
        $setting = Setting::get(['email_checked', 'tel_checked']);
        if (!$setting['email_checked'] and !$setting['tel_checked'] and $data['changeWay'] != 3) return true;
        if(!isset($data['yzm']) and $data['changeWay'] != 3){
            return ['err' => 1, 'msg' => '请输入验证码'];
        } elseif (!isset($data['yzm']) and $data['changeWay'] == 3){
            return ['err' => 1, 'msg' => '请输入安全密码'];
        }
        $checked = 0;
        /**安全密码验证 */
        if($data['changeWay'] == 3){
            $checked = 1;
            if ($this->user['safe_password'] != md5($data['yzm'])) {
                return ['err' => 1, 'msg' => '安全密码错误'];
            }
        }
        /**邮箱验证 */
        if($data['changeWay'] == 2){
            $checked = 1;
            $res = (new Email)->checkSend($this->user['email'], $sendtype['email'], $data['yzm']);
            if($res['err']){
                return $res;
            }
        }
        /**手机验证*/
        if($data['changeWay'] == 1){
            $checked = 1;
            $res = sms::getSmsCode($this->user['tel'], $sendtype['tel'], $data['yzm']);
            if($res['err']){
                return $res;
            }
            isset($res['smskey']) ? session($res['smskey'], null) : '';
        }

        if(!$checked){
            return ['err' => 1, 'msg' => '验证方式出错'];
        }
        return true;
    }

    /**QQ绑定 */
    public function lockQQ()
    {
        (new Qqlogin)->toLogin(1, '',1);
    }

    /***解除绑定 */
    public function unlockQW()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!$data['way'] || $data['way'] > 2){
                return json(['err' => 1, 'msg' => '参数错误']);
            }
            $way =  ['1' => 'qq', '2' => 'wei'];
            $res = (new AUser)->where('id', $this->user['id'])->setField($way[$data['way']], '');
            if($res){
                return json(['err' => 0, 'msg' => '解绑成功']);
            }
            return json(['err' => 1, 'msg' => '解绑失败']);
        }
    }

    /**
     * 虚拟币相互转换
     */
    public function transform()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = (new Recharge())->MoneyChange($this->user['id'], $data['type'], $data['num']);
            if (!$res['code']) {
                return json(['err' => 1, 'msg' => '转换失败']);
            }
            return json(['err' => 0, 'msg' => '转换成功']);
        }
        return $this->fetch('');
    }

    /**
     * 用户兑换撤单
     */
    public function returnChange($id)
    {
        $res = (new ShopOrder())->where('status', 0)->find($id);
        if (!$res) {
            return json(['err' => 1, 'msg' => '订单不能撤单']);
        }
        $money = new MoneyHistory();
        $money_res = $money->write([
            'money'=> $res['money'],
            'type' => 3,
            'userid' => $res['userid'],
            'remark' => '兑换撤单:'.$res['id']
        ]);
        if ($money_res['code']) {
            $res->save(['status' => 7]);
        } else {
            return json(['err' => 0, 'msg' => '撤单失败']);
        }
        return json(['err' => 0, 'msg' => '撤单成功']);
    }

    /**
     * 安全密码
     */
    public function setSafePwd()
    {
        $isBind = $this->user['safe_password'] ? 1 : 0 ;
        $this->assign('isBind', $isBind);

        $data = $this->telEmail();
        return $this->fetch('set_safepwd',['title' => '安全密码管理','data' => $data]);
    }
    /**
     * 设置安全密码
     */
    public function lockSafePwd()
    {
        if(request()->isPost()){
            $data = request()->post();
            if ($data['pwd'] != $data['pwd2']) {
                return json(['err' => 1, 'msg' => '两次密码不相同']);
            }
            $res = (new \app\web\model\User())->where('id', $this->user['id'])->update(['safe_password' => md5($data['pwd'])]);
            if ($res) {
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
    }
    /**
     * 修改安全密码--通过原密码
     */
    public function changeSafePwdByPwd()
    {
        if(request()->isPost()){
            $data = request()->post();
            $model = (new \app\web\model\User());
            if ($data['oldpwd'] and $this->user['safe_password'] != md5($data['oldpwd'])) {
                return ['err' => 1, 'msg' => '原密码错误'];
            }
            $res = $model->changeSafePwd($data, $this->user);
            return json($res);
        }
    }
    /**
     * 修改安全密码--通过手机验证码
     */
    public function changeSafePwdByTel()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = sms::getSmsCode($this->user['tel'], 'change_safepwd', $data['yzm']);
            if($res['err']){
                return json($res);
            }
            isset($res['smskey']) ? session($res['smskey'], null) : '';
            $model = (new \app\web\model\User());
            $res = $model->changeSafePwd($data, $this->user);
            return json($res);
        }
    }
    /**
     * 修改安全密码--通过邮箱验证码
     */
    public function changeSafePwdByEmail()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = (new Email)->checkSend($this->user['email'], 'change_safepwd', $data['yzm']);
            if($res['err']){
                return json($res);
            }
            $model = (new \app\web\model\User());
            $res = $model->changeSafePwd($data, $this->user);
            return json($res);
        }
    }

    /**邮箱发送验证码 --- 修改安全密码 */
    public function sendSafePwdEmail()
    {
        $res = (new Email)->send($this->user['email'], 'change_safepwd', '修改安全密码');
        return json($res);
    }
    /**手机发送验证码 --- 修改安全密码 */
    public function sendSafePwdMsg()
    {
        $res = Sms::setSmsCode($this->user['tel'] , 'change_safepwd');
        return json($res);
    }
}
