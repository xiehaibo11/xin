<?php
namespace app\index\controller;

use app\admin\model\Ext;
use app\common\controller\LotteryCommon;
use app\common\model\MoneyHistory;
use app\index\model\ExtShowList;
use app\lottery\controller\Award;
use core\Redis;
use think\Cache;
use think\Config;
use think\Controller;

class Api extends Controller
{

    /**
     * 零点统计
    */
	public function index(){
        $res = (new MoneyHistory)->newSatis();
        if ($res['err'] == 2) $res['err'] = 0;
        return json($res);
	}

    /**
     * 系统开奖
     */
    public function openCode($name = '', $is_new = 0){
        if (!$name) {
            $name_array = (new Ext())->where('is_system_code', 1)->column('name');
        } else {
            $info = (new Ext())->where('name', $name)->find();
            if (!$info) return json(['err' => 1, 'msg' => '彩种不存在']);
            $name_array = [$name];
        }
        $now = date('Y-m-d');
        foreach ($name_array as $v){
            $day = substr(date('Ymd') , 2, 6);
            $old_list = Cache::get('system_code_' . $v);
            $data = [];
            if ($old_list) {
                $old_list = json_decode($old_list, true);
                if (isset($old_list[$now]) and !empty($old_list[$now]) and !$is_new) continue;
                $zt_date = date("Y-m-d",strtotime("-1 day"));
                isset($old_list[$zt_date]) ? $data[$zt_date] = $old_list[$zt_date] : null;
            }
            $lottery_setting= LotteryCommon::getSetting($v)->getValue(LotteryCommon::getSettingValue($v, 'setting'));
            $lottery_setting = json_decode($lottery_setting, true);
            if (empty($lottery_setting)) {
                continue;
            }
            if ( $lottery_setting['startIssue'] >= 1000) {
                $num_len = 4;
            } elseif ($lottery_setting['startIssue'] >= 100 and $lottery_setting['startIssue'] < 1000) {
                $num_len = 3;
            } else {
                $num_len = 2;
            }

            $now_0_timestamp = strtotime(date('Y-m-d', time()));//零点的时间戳
            $buy_timestamp = $lottery_setting['timelong'] * 60 ;//售卖的时间戳
            $start_time = explode(':', $lottery_setting['startTime']);//售卖的时间戳
            $start_timestamp = $start_time[0] * 60 * 60 + $start_time[1] * 60;
            for ($i = 0; $i < intval($lottery_setting['startIssue']); $i++) {
                $data[$now][$i] = [
                    'expect' => $day . '-' . sprintf("%0" . $num_len ."d",$i + 1),
                    'create_time' => date('Y-m-d H:i:s', $now_0_timestamp + $start_timestamp + $buy_timestamp * ($i + 1)),
                    'code' => $this->randSystemCode(LotteryCommon::getCpType($v))
                ];
            }
            Cache::set('system_code_' . $v, json_encode($data));
        }
        return json(['err' => 0]);
    }

    /**
     * 系统开奖 - 随机开奖号码
     */
    private function randSystemCode($cp_type){
        switch ($cp_type) {
            case 'pk10'://10个开奖号码
                $my_array = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
                shuffle($my_array);
                return implode(',', $my_array);
                break;
            case 'ssc'://0-9 可重复
                $my_array = [];
                for ($i = 0; $i < 5; $i++) {
                    $my_array[$i] = rand(0, 9);
                }
                return implode(',', $my_array);
                break;
            case 'syxw'://01～11 不重复
                $my_array = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'];
                shuffle($my_array);
                return implode(',', array_slice($my_array,0,5));
                break;
            case 'ks'://1-6 可重复
                $my_array = [];
                for ($i = 0; $i < 3; $i++) {
                    $my_array[$i] = rand(1, 6);
                }
                return implode(',', $my_array);
                break;
            case 'pc28'://0-9 可重复
                $my_array = [];
                for ($i = 0; $i < 3; $i++) {
                    $my_array[$i] = rand(0, 9);
                }
                return implode(',', $my_array);
                break;
        }
    }

    /**
     * 获取所有系统开奖彩种配置信息
     */
    public function getSystemLotteryConfig(){
        $list = (new Ext())->where('is_system_code', 1)->column('name');
        $config = [];
        foreach ($list as $v) {
            $config[$v] = json_decode(LotteryCommon::getSetting($v)->getValue(LotteryCommon::getSettingValue($v, 'setting')), true);
        }
        return json($config);
    }

    /**
     * 系统开奖推送
     */
    public function pushSystemCode($name){
        $ext_model = new ExtShowList();
        $ext_info = $ext_model->where('name', '/' . $name)->find();
        if (!$ext_info) return json(['err' => 1, 'msg' => '彩种不存在']);
        $ext_info = $ext_info->append(['is_system_code'])->toArray();
        if (!$ext_info['is_system_code']) return json(['err' => 1, 'msg' => '非系统开奖']);
        if ($ext_info['pause']) return json(['err' => 1, 'msg' => '彩种暂停']);
        if ($ext_info['status']) return json(['err' => 1, 'msg' => '彩种关闭']);
        $list = Cache::get('system_code_' . $name);
        $list = json_decode($list, true);
        if (empty($list)) return json(['err' => 1, 'msg' => '没有开奖数据']);
        $expect_info = (new \app\lottery\controller\Lottery())->getExpect($name, 1)['data'];
        $now_expect = $expect_info['expect'];
        foreach ($list as $key => $v) {
            $last_time = $key;
            break;
        }
        if ($last_time < date('Y-m-d H:i:s')) {
            $this->openCode($name);
            $list = Cache::get('system_code_' . $name);
            $list = json_decode($list, true);
        }
        $data = [];//要推送开奖数据
        $num = 0;
        $last_expect = LotteryCommon::getModel($name, 'code')->order('id desc')->find();
        if ($last_expect) {
            $last_expect = $last_expect['expect'];
        } else {
            $last_expect = 0;
        }
        foreach ($list as $v) {
            foreach ($v as $row) {
                $expect = '20' . str_replace('-', '', $row['expect']);
                if ($now_expect > $expect and $last_expect < $expect) {
                    $num++;
                    array_push($data, [
                        'code' => $row['code'],
                        'expect' => $expect,
                        'ext_name' => $name
                    ]);
                }
            }
        }
        if(empty($data)){
            return json(['err' => 1, 'msg' => '开奖已最新或无开奖数据', 'next_time' => $expect_info['down_time']]);
        }
        $res = LotteryCommon::getModel($name, 'code')->insertAll($data);
        if($res){
            /**遗漏--彩种名到时候配置*/
            (new Award())->miss($name,$data);
            $token = Config::get('baseConfig.authorization_token');
            $timestamp = time();
            $signature = md5($token . $timestamp);
            /**派奖*/
            $redis = new Redis();
            $redis->pub('prize', "signature=" . $signature . "&timestamp=" . $timestamp . "&name=" . $name);
            return json(['err' => 0, 'expect' => $now_expect - 1, 'next_time' => $expect_info['down_time']]);
        }
        return json(['err' => 1, 'msg' => '数据库错误']);
    }
}