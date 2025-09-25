<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User as user;
use app\index\model\Banner;
use app\index\model\BannerClass;
use core\Setting;
use app\index\model\ExtShowList;

use app\admin\model\Shop;
use app\admin\model\ShopNav;
use app\admin\model\ShopOrder;
use app\common\model\MoneyHistory;
use think\Db;

class Moblie extends Controller
{

    /****************07-24 ************************* */
    /**获取配置信息*/
    public  function getSetting()
    {
        $setting = (new Setting)->get(['tel_checked', 'email_checked','company_qq','company_email','company','company_tel','login_in','login_way','reg_way','wx_checked','qq_checked','wxsm_checked',
        'is_reg','logo_url','lottery_unit','coin_lottery','isGain','user_web','web_service','user_service','site_name','getmoney_startime','getmoney_endtime','getmoney_times','recharge_info',
        'join_isOpen','unit_isOpen','mode2_min_money','rebate_max','rebate_isOpen','game_unit','lottery_unit','lottery_status','game_status','mode_unit_value','is_reg_code','recharge_award',
        'user_level','agent_recharge','company_wx','moblie_domain','wap_online_qq','getmoney_info','notice_popup_open','bind_idcard','max_chase','getmoney_low']);
        // if(session("?sid")){
        //     $setting['tel'] = $this->user['tel'] ? substr_replace($this->user['tel'], '****',3,4) : '';
        //     $email = $this->user['email'];
        //     if($email){
        //         $emailArr = explode('@', $email);
        //         $len = mb_strlen($emailArr[0]);
        //         $email = substr_replace($emailArr[0], '****', 2, $len-4)."@".$emailArr[1];
        //     }
        //     $setting['email'] = $email;
        // }

        if ($setting['user_level']) {
            $new_user_level = [];
            $user_level = json_decode($setting['user_level'], true);
            foreach ($user_level as $key => $v) {
                $new_user_level[$key]['grade'] = 'VIP' . $v['level'];
                $new_user_level[$key]['gradeName'] = $v['level_name'];
                $new_user_level[$key]['gradeGrow'] = $v['explan'];
            }
            $setting['user_level'] = json_encode($new_user_level);
        }
        $way =array_flip(explode(',', $setting['login_way']));
        $login_way['common'] = isset($way['common']) ? 1 : 0;
        $login_way['tel'] = isset($way['tel']) && $setting['tel_checked'] ? 1 : 0;
        $login_way['wx'] = isset($way['wei']) && $setting['wx_checked'] ? 1 : 0;
        $login_way['qq'] = isset($way['qq']) && $setting['qq_checked'] ? 1 : 0;
        $setting['login_way'] = $login_way;

        $ways =array_flip(explode(',', $setting['reg_way']));
        $reg_way['common'] = isset($ways['common']) ? 1 : 0;
        $reg_way['tel'] = isset($ways['tel']) ? 1 : 0;
        $setting['reg_way'] = $reg_way;

        $user_web = str_replace('***', $setting['company'], $setting['user_web']);
        $setting['user_web'] = str_replace('%%%', $setting['site_name'], $user_web);

        $web_service = str_replace('***', $setting['company'], $setting['web_service']);
        $setting['web_service'] = str_replace('%%%', $setting['site_name'], $web_service);

        $user_service = str_replace('***', $setting['company'], $setting['user_service']);
        $setting['user_service'] = str_replace('%%%', $setting['site_name'], $user_service);

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

        $user = (new user)->where('sid', session("sid"))->find();
        $todayTime = (new ShopOrder)->where('userid', $user['id'])->where('create_time', '>=', date("Y-m-d"))->count();
        $setting['ure_times'] = $setting['getmoney_times'] - $todayTime;

        if ($setting['mode_unit_value']) {
            $mode_unit_value = explode(',',$setting['mode_unit_value']);
            $unit_map = [1 => '元', 2 => '角', 3 => '分', 4 => '厘'];
            $mode_unit_res = [];
            foreach ($mode_unit_value as $v) {
                array_push($mode_unit_res, [
                    'value' => $v,
                    'label' => $unit_map[$v],
                ]);
            }
            $setting['mode_unit_value'] = $mode_unit_res;
        }

        $setting['qq_num'] = preg_replace('/([\x80-\xffa-zA-Z]*)/i','',$setting['company_qq']);

        return json(['err' => 0, 'data' => $setting]);
    }

    /**验证登录 */
    public function checkLogin()
    {
        if(session("?sid")){
            $user = (new User)->where('sid', session("sid"))->find();
            if(!$user){
                return json(['err' => 1, 'msg' => '您还未登录，请先登录','status' => false]);
            }
            return json(['err' => 0,'msg' => '已登录','status' => true]);
        }
        return json(['err' => 1, 'msg' => '您还未登录，请先登录','status' => false]);
    }

    /**获取首页banner图 */
    public function getBanner()
    {
        $bannid = (new BannerClass())->where('name', 'app首页banner')->column('id');
        $banner = [];
        if(!empty($bannid)){
            $banner = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }
        return json(['err' => 0, 'data' => $banner]);
    }

    /**获取游戏导航列表 */
    public function getLotteryNav()
    {
       // $extShow = new ExtShowList;
       // $gamenav =  $extShow->field('id,title')->where('type', 1)->order('sort ASC')->select();
        $res =  (new ExtShowList)->getGamesList(1);
        $data = [];
        foreach($res as $key => $value){
            $data[] = [
                'value'=> ltrim($value['name'], '/'),
                'label' => $value['title'],
                'id' => $value['id'],
            ];
        }
        array_unshift($data, ['value' => '', 'label' => '所有彩种','id' => '']);


         $res2 =  (new ExtShowList)->getGamesList();
         $data2 = [];
         foreach($res2 as $key => $value){
            $data2[] = [
                'value'=> ltrim($value['name'], '/'),
                'label' => $value['title'],
                'id' => $value['id'],
            ];
         }

         return json(['err' => 0, 'data' => $data,'game' => $data2]);
    }
    /**中奖累积前十排行 */
     public function getTop10()
    {
        /**中奖累积前十排行 */
        $list = (new User)->field('nickname, award,photo')->order('award DESC')->limit(0,10)->select();
        foreach ($list as $key => &$value){
            $value['nickname'] = mb_substr($value['nickname'], 0, 1, 'utf-8')."***".mb_substr($value['nickname'], -1, 1, 'utf-8');
        }
        return json(['err' => 0, 'data' => $list]);
    }

    /**中奖资讯 */
     public function getAwardList()
    {
        $awardList = (new MoneyHistory)->field('userid,money,remark')->where('type', 1)->where('money > 0')->order('id desc')->limit(0,20)->select();
        $awardList = $awardList->append(['nickname', 'lotteryName','photo'])->toArray();
        foreach ($awardList as $key => &$value){
            $value['nickname'] = mb_substr($value['nickname'], 0, 1, 'utf-8')."***".mb_substr($value['nickname'], -1, 1, 'utf-8');
        }
        return json(['err' => 0, 'data' => $awardList]);
    }
}