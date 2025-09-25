<?php
namespace app\admin\controller;

use think\Controller;
use core\Setting;
use app\news\model\PluginNewsNav;
use app\news\model\PluginNewsList;

class SystemSet extends Base
{
    public function index()
    {
        $setting = Setting::get(['login_in','recharge_award','web_bottom', 'is_reg','pay_way','share_title','share_desc','share_link','share_img','site_name', 'logo_url','pc_status',
        'pc_domain','moblie_domain','login_name','coin_name','coin_unit','moblie_status','user_service','web_service','wap_pic', 'android_pic', 'ios_pic', 'is_reg_code', 'notice_popup_open',
        'bind_idcard']);
        $this->assign('setting', $setting);
        return $this->fetch('index',['title' => '系统设置']);
    }

    public function site()
    {
        $setting = Setting::get(['site_name', 'logo_url','pc_status','pc_domain','moblie_domain']);
        // $setting['pc_domain'] = json_decode($setting['pc_domain'], true);
        // if(count($setting['pc_domain']) > 1) {
        //     $setting['pc_domain'] = implode(";", $setting['pc_domain']);
        // }
        // $setting['moblie_domain'] = json_decode($setting['moblie_domain'], true);
        // if(count($setting['moblie_domain']) > 1) {
        //     $setting['moblie_domain'] = implode(";", $setting['moblie_domain']);
        // }
        return $this->fetch('site',['title' => '网站设置', 'setting' => $setting]);
    }

    public function user()
    {
        $setting = Setting::get(['login_way', 'reg_way', 'lockTel_award','reg_award', 'login_award', 'action_award', 'sign_in_award', 'reg_msg_status', 'reg_msg','diamonds_recharge','recharge_award','other_recharge_award','flower_money', 'user_level', 'wx_checked', 'qq_checked', 'tel_checked','reg_in','user_web','is_reg','wxsm_checked', 'agent_recharge']);
        if($setting['other_recharge_award']){
            $setting['other_recharge_award'] = json_decode($setting['other_recharge_award'],true);
        }
        if($setting['diamonds_recharge']){
            $setting['diamonds_recharge'] = json_decode($setting['diamonds_recharge'],true);
        }

        $setting['login_way'] = array_flip(explode(',', $setting['login_way']));
        $setting['reg_way'] = array_flip(explode(',', $setting['reg_way']));
        $setting['pay_way'] = array_flip(explode(',', $setting['pay_way']));
        return $this->fetch('user',['title' => '会员设置', 'setting' => $setting]);
    }

    /**
     * 检测数据是否有变动
    */
    public function checkIsChange($data)
    {
        $config = [];
        foreach ($data as $key => $v2) {
            array_push($config, $key);
        }
        $old_data = Setting::get($config);
        $change_data = [];
        foreach ($old_data as $key => $v) {
            if ($old_data[$key] != $data[$key]) {
                array_push($change_data, ['key' => $key, 'value' => $data[$key], 'old_value' => $old_data[$key]]);
            }
        }
        if (empty($change_data)) return ;
        $date = date("Y-m-d H:i:s");
        $ip = request()->ip();
        $broswer = $this->get_broswer();
        $os = $this->get_os();
        $msg = '';
        foreach ($change_data as $row) {
            $msg .= '['. $date .']'.'[ip]：'. $ip . '[管理员]：' . $this->admin['username'] . '[浏览器]：' . $broswer . '[系统]：' . $os .'[标识]：'.$row['key'].'[值]：'. $row['value'] .'[旧值]：'.$row['old_value'] . PHP_EOL;
        }
        $path = ROOT_PATH.'public'. DS .'logs'. DS .date("Ymd").'.txt';
        file_put_contents($path, $msg,FILE_APPEND);
    }


    public function set($data)
    {
        if ($this->admin['id'] != 1) return ['code' => 0, 'msg' => '测试账号不能提交'];
        $config = json_decode($data, true);
        $res = false;
        if($config['flower_mone']){
            $recharge = (double) $config['flower_mone'];
            $config['flower_mone'] = $recharge < 1 ? 1 : $recharge;
        }
        // if(isset($config['pc_domain'])){
        //     $config['pc_domain'] = json_encode(explode(";", $config['pc_domain']));
        // }
        if(isset($config['login_name'])){
            $setting = Setting::get(['login_name'])['login_name'];
            if($config['login_name'] != $setting && $config['login_name'] != ''){
                $oldname = $setting.".php";
                $newname = $config['login_name'].".php";
                $res = rename($oldname,$newname);
            }
        }
        if (isset($config['recharge_award'])) {
            $config['recharge_award'] = $config['recharge_award'] > 0 ? $config['recharge_award'] : 1;
        }
        // if(isset($config['moblie_domain'])){
        //     $config['moblie_domain'] = json_encode(explode(";", $config['moblie_domain']));
        // }
        $this->checkIsChange($config);
        Setting::set($config);
        return ['code' => 1,'res' => $res];
    }

    public function setLogo($base64_data)
    {
        $url = '/static/images/logo.png';
        $setting = Setting::set('logo_url', $url . '?_v=' . time());
        $path = base64_upload($base64_data,'./static/images/', 'logo') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    public function setWxImg($base64_data, $name = '')
    {
        $path = new_base64_upload($base64_data,'static/images/', $name) . '?t=' . time();
        $name = $name ? $name : 'company_img';
        Setting::set($name, $path);
        return ['code' => 1, 'data' => $path];
    }

    public function setShare($base64_data, $name)
    {
        $name = $name."_pic";
        $path = new_base64_upload($base64_data,'./static/images/', $name) . '?t=' . time();
        Setting::set($name, $path);
        return json(['err' => 0, 'data' => $path]);
    }

    public function setRecharge()
    {
        $setting = Setting::get(['diamonds_recharge','wx_recharge_ewm','zfb_recharge_ewm','other_recharge_award','recharge_award','flower_money','other_pay','sao_pay','getmoney_info','getmoney_times','getmoney_startime','getmoney_endtime','getmoney_low','recharge_info','recharge_send_times','lottery_unit', 'bank_config', 'bank_open', 'recharge_change']);
        if($setting['other_recharge_award']){
            $setting['other_recharge_award'] = json_decode($setting['other_recharge_award'],true);
        }
        if($setting['wx_recharge_ewm']){
            $setting['wx_recharge_ewm'] = json_decode($setting['wx_recharge_ewm'],true);
        }
        if($setting['bank_config']){
            $setting['bank_config'] = json_decode($setting['bank_config'],true);
        }
        if($setting['zfb_recharge_ewm']){
            $setting['zfb_recharge_ewm'] = json_decode($setting['zfb_recharge_ewm'],true);
        }
        if($setting['diamonds_recharge']){
            $setting['diamonds_recharge'] = json_decode($setting['diamonds_recharge'],true);
        }
        $third_pay = (new \app\admin\model\ThirdPay())->column('name', 'id');
        $this->assign('third_pay_list', $third_pay);
        if($setting['other_pay']){
            $other_pay = json_decode($setting['other_pay'],true);
            foreach ($other_pay as $key => $value) {
                $setting[$value['name']] = $value['value'];
                if ($value['name'] == 'third_pay') {
                    foreach ($value['value'] as $row) {
                        $setting[$value['name']][$row['id']] = $row['id'];
                    }
                }
            }
        }
        if($setting['sao_pay']){
            $sao_pay = json_decode($setting['sao_pay'],true);
            foreach ($sao_pay as $key => $value) {
                $setting[$value['name']]['value'] = $value['value'];  
                $setting[$value['name']]['url'] = $value['url'] ?? '/static/images/logo.png';  
            }
        }
        $setting['RMB'] = $setting['recharge_award'] * $setting['flower_money'];
        return $this->fetch('recharge',['title' => '充值设置', 'setting' => $setting]);
    }

    public function company()
    {
        $setting = Setting::get(['company','company_person','company_qq','company_email','company_img','company_img_qq','company_icp','company_wx','company_tel','wap_online_qq','web_online_qq']);
        return $this->fetch('company',['title' => '充值设置', 'setting' =>  $setting]);
    }

    /**设置服务协议条款*/
    public function setService()
    {
        $id = (new PluginNewsNav)->where('title', '网站协议')->column('id');
        if(empty($id)){
            return json(['err' => 1, 'msg' => '还未添加【网站协议】栏目']);
        }
        $list = (new PluginNewsList)->field('id,title')->where('nav_id', $id[0])->select();
        if($list){
            $list = $list->toArray();
            return json(['err' => 0, 'data' => $list]);
        }
        return json(['err' => 2, 'msg' => '【网站协议】栏目下还未添加相关文章']);
    }

    public function setPicValue()
    {
        $data = request()->post();
        $config = [$data['name'] => ''];
        $setting = Setting::set($config);        
        return ['code' => 1,'res' => $res];
    }

    /**
     * 编辑器上传图片
     */
    public function editor_upload($dir = '')
    {
        $files = request()->file("file");
        $getfile = uploadFile($files, "image_text", $dir);//编辑富文本 上传图片

        if ($getfile['code']) {
            return $getfile['dir'];
        } else {
            return $getfile['msg'];
        }
    }

    /**
     * 获取客户端浏览器信息 添加win10 edge浏览器判断
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public function get_broswer(){
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif(stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        }else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0].'('.$exp[1].')';
    }

    /**
     * 获取客户端操作系统信息包括win10
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public function get_os(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;

        if (preg_match('/win/i', $agent) && strpos($agent, '95'))
        {
            $os = 'Windows 95';
        }
        else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
        {
            $os = 'Windows ME';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
        {
            $os = 'Windows 98';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
        {
            $os = 'Windows Vista';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
        {
            $os = 'Windows 7';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
        {
            $os = 'Windows 8';
        }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
        {
            $os = 'Windows 10';#添加win10判断
        }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
        {
            $os = 'Windows XP';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
        {
            $os = 'Windows 2000';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
        {
            $os = 'Windows NT';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
        {
            $os = 'Windows 32';
        }
        else if (preg_match('/linux/i', $agent))
        {
            $os = 'Linux';
        }
        else if (preg_match('/unix/i', $agent))
        {
            $os = 'Unix';
        }
        else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
        {
            $os = 'SunOS';
        }
        else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
        {
            $os = 'IBM OS/2';
        }
        else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
        {
            $os = 'Macintosh';
        }
        else if (preg_match('/PowerPC/i', $agent))
        {
            $os = 'PowerPC';
        }
        else if (preg_match('/AIX/i', $agent))
        {
            $os = 'AIX';
        }
        else if (preg_match('/HPUX/i', $agent))
        {
            $os = 'HPUX';
        }
        else if (preg_match('/NetBSD/i', $agent))
        {
            $os = 'NetBSD';
        }
        else if (preg_match('/BSD/i', $agent))
        {
            $os = 'BSD';
        }
        else if (preg_match('/OSF1/i', $agent))
        {
            $os = 'OSF1';
        }
        else if (preg_match('/IRIX/i', $agent))
        {
            $os = 'IRIX';
        }
        else if (preg_match('/FreeBSD/i', $agent))
        {
            $os = 'FreeBSD';
        }
        else if (preg_match('/teleport/i', $agent))
        {
            $os = 'teleport';
        }
        else if (preg_match('/flashget/i', $agent))
        {
            $os = 'flashget';
        }
        else if (preg_match('/webzip/i', $agent))
        {
            $os = 'webzip';
        }
        else if (preg_match('/offline/i', $agent))
        {
            $os = 'offline';
        }
        else
        {
            $os = '未知操作系统';
        }
        return $os;
    }

}
