<?php
namespace app\web\controller;

use think\Controller;
use app\index\model\MsgArticle;
use core\Setting;
use app\news\model\PluginNewsList;
use app\news\News as NewsCore;
use app\news\model\PluginNewsNav;
use app\index\model\ExtShowList;
use app\index\model\Banner;
use app\index\model\BannerClass;

class Common extends Controller
{
    public function getInit($userInfo)
    {
        $setting = Setting::get(['company','logo_url','company_person','company_qq','company_email','company_wx', 'company_img','company_img_qq','company_tel','isGain',
        'login_way','reg_way','is_reg','tel_checked','email_checked','web_bottom','wap_pic','android_pic','ios_pic','moblie_status','user_web','web_service','user_service','site_name',
        'join_isOpen','unit_isOpen','mode2_min_money','rebate_max','rebate_isOpen','game_unit','lottery_unit','lottery_status','game_status','mode_unit_value','recharge_award',
         'mode_unit_value','agent_recharge','web_online_qq','getmoney_info','notice_popup_open','bind_idcard','max_chase']);
        $setting['web_bottom'] = htmlspecialchars_decode($setting['web_bottom']);
        if ($setting['mode_unit_value']) {
            $mode_unit_value = explode(',', $setting['mode_unit_value']);
            $unit_map = [1 => '元', 2 => '角', 3 => '分', 4 => '厘'];
            $mode_unit_res = [];
            foreach ($mode_unit_value as $v) {
                array_push($mode_unit_res, [
                    'value' => $v,
                    'label' => $unit_map[$v],
                ]);
            }
            $setting['mode_unit_value'] = json_encode($mode_unit_res);
        }

        $user_web = str_replace('***', $setting['company'], $setting['user_web']);
        $setting['user_web'] = str_replace('%%%', $setting['site_name'], $user_web);

        $web_service = str_replace('***', $setting['company'], $setting['web_service']);
        $setting['web_service'] = str_replace('%%%', $setting['site_name'], $web_service);

        $user_service = str_replace('***', $setting['company'], $setting['user_service']);
        $setting['user_service'] = str_replace('%%%', $setting['site_name'], $user_service);

        $qq_num = preg_replace('/([\x80-\xffa-zA-Z]*)/i','',$setting['company_qq']);
        $this->assign('qq_num', $qq_num);

        $this->assign('company', $setting);
        $count = $userInfo ? ((new MsgArticle) ->getList($userInfo['id'])) : 0;
        $this->assign('msgNum', $count);
        $this->assign('unitArr', $setting['mode_unit_value']);
        // 底部文章
        $article = ['course' => NewsCore::getNewList('',3,'新手教程'),'betting' => NewsCore::getNewList('',3,'彩票投注'),'recharge' => NewsCore::getNewList('',3,'充值兑换'),
        'playinfo' => NewsCore::getNewList('',3,'玩法介绍')];
        $this->assign(['footArticle' => $article]);

        //全部游戏列表
        $extShow = new ExtShowList;
        $game =  $extShow->where('type', 0)->where('status',0)->order('sort ASC')->select();
        $game = $game->toArray();
        foreach ($game as &$value) {
            $value['name'] = $this->setUrlBase($value['name']);
        }
        $lottery =  $extShow->where('type', 1)->where('status',0)->order('sort ASC')->select();
        $lottery = $lottery->toArray();
        foreach ($lottery as &$value) {
            $value['name'] = $this->setUrlBase($value['name']);
        }
        $this->assign('navGame', collection($game));
        $this->assign('navLottery', $lottery);
        $this->assign('navList', collection($this->getNavLottery()));

        $recs = $extShow->where('reco', 1)->where('status',0)->order('sort ASC')->select();
        $recs = $recs->toArray();
        foreach ($recs as &$values) {
            $values['name'] = $this->setUrlBase($values['name']);
        }
        $this->assign('recs', $recs);

        //顶部测试说明图片
        $bannid = (new BannerClass)->where('name', '测试说明')->column('id');
        $topImg = [];
        if(!empty($bannid)){
            $topImg = (new Banner)->where('class_id', $bannid[0])->where('status', 1)->order('msort', 'desc')->select();
        }
        $this->assign('topImg', $topImg);

        $navClass = new PluginNewsNav;
        $newsId = $navClass->where('title', '新闻资讯')->column('id');
        $noticeId = $navClass->where('title', '网站公告')->column('id');
        $activityId = $navClass->where('title', '优惠活动')->column('id');
        $this->assign(['newsId' => implode($newsId) , 'noticeId' => implode($noticeId), 'activityId' => implode($activityId)]);
    }

    /**
     * $type 简约字段
    */
    public function getNavLottery($type = false)
    {
        $res = [];
        $extShow = new ExtShowList;

        $lottery =  $extShow->where('type', 1)->where('status',0)->order('sort ASC')->select();
        $lottery = $lottery->toArray();
        foreach ($lottery as &$value) {
            $isSSc = mb_substr($value['name'], -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
            $isKs = mb_substr($value['name'], -2, 2, 'utf-8') == 'ks' ? 1 : 0;
            $isSyxw = mb_substr($value['name'], -2, 2, 'utf-8') == '11' ? 1 : 0;
            $is28 = mb_substr($value['name'], -2, 2, 'utf-8') == '28' ? 1 : 0;
            $is_pk10 = mb_substr($value['name'], -2, 2, 'utf-8') == '10' ? 1 : 0;
            $names = $this->setUrlBase($value['name']);
            $show_data = [
                'label' => $value['title'],
                'pause' => $value['pause'],
            ];
            if (!$type) {
                $show_data = array_merge([
                    'remark' => $value['remark'],
                    'image' => $value['image'],
                    'type' => $value['type'],
                    'img' => $value['img'],
                    'info' => $value['info'],
                    'reco' => $value['reco'],
                    'link' => ltrim($value['name'], '/'),
                    'name' => $names,
                ], $show_data);
            } else {
                $show_data['name'] = $value['name'];
            }
            if ($isSSc) {
                if (!isset($res[0])) {
                    $res[0] = [
                        'label' => '时时彩',
                        'type' => 'ssc',
                        'data' => []
                    ];
                }
                array_push($res[0]['data'], $show_data);
            }
            if ($isSyxw) {
                if (!isset($res[1])) {
                    $res[1] = [
                        'label' => '11选5',
                        'type' => 'syxw',
                        'data' => []
                    ];
                }
                array_push($res[1]['data'], $show_data);
            }
            if ($isKs) {
                if (!isset($res[2])) {
                    $res[2] = [
                        'label' => '快3',
                        'type' => 'ks',
                        'data' => []
                    ];
                }
                array_push($res[2]['data'], $show_data);
            }
            if ($is28) {
                if (!isset($res[3])) {
                    $res[3] = [
                        'label' => 'PC蛋蛋',
                        'type' => 'pc28',
                        'data' => []
                    ];
                }
                array_push($res[3]['data'], $show_data);
            }
            if ($is_pk10) {
                if (!isset($res[4])) {
                    $res[4] = [
                        'label' => '赛车飞艇',
                        'type' => 'pk10',
                        'data' => []
                    ];
                }
                array_push($res[4]['data'], $show_data);
            }
        }
        return $res;
    }

    public function setUrlBase($name)
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
        $pc28 = explode('28',$name);
        if(count($pc28) > 1){
            return  $name = 'Pc28/index/name/'.ltrim($name, '/');
        }
        $pk10 = explode('10',$name);
        if(count($pk10) > 1){
            return  $name = 'Pk10/index/name/'.ltrim($name, '/');
        }
        $game = explode('game/', $name);
        $num = count($game);
        if($num > 1){
            return $name = '/'.$game[$num-1];
        }
        return  $name = ltrim($name, '/');
    }
}
