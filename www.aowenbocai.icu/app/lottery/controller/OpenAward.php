<?php
namespace app\lottery\controller;

use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use app\lottery\controller\Award;
use core\Redis;
use core\Setting;
use think\Config;
use think\Log;

class OpenAward
{
    protected $config = [];

    /**
     * 开奖接口
     * $signature 签名标识 计算方式  md5(token + timestamp)
     * $data 开奖数据
    */
    public function index($signature = '', $timestamp = '', $name = '', $data = '')
    {
        if (!$signature or !$timestamp) return json(['code' => 0, 'msg' => '参数错误']);
        $token = Config::get('baseConfig.authorization_token');
        $check_signature = md5($token . $timestamp);
        if ($signature != $check_signature) return json(['code' => 0, 'msg' => '签名不通过']);
        $setting = Setting::get(['openCj']);
        if (!$setting['openCj']) {
            return json(['code' => 0, 'msg' => '网站已关闭采集']);
        }
        return $this->getAwards($signature, $timestamp, $name, $data);
    }

    /**
     * 开奖接口
     * $signature 签名标识 计算方式  md5(token + timestamp)
     * $data 开奖数据
     */
    public function get_config()
    {
        $res =  (new ExtShowList())->where('type', 1)->column('name');
        if (!$res) $res = [];
        return $res;
    }

    private function getAwards($signature, $timestamp, $name, $res_data)
    {
        $this->config = $this->get_config();
        if(!in_array('/' . $name, $this->config) and !in_array($name, $this->config)){
            return json(['code' => 0, 'msg' => '彩种不存在']);
        }
        if (!$res_data) {
            return json(['code' => 0, 'msg' => '开奖号码为空']);
        }
        $get_code = json_decode($res_data, true);

        $code_model = LotteryCommon::getModel($name, 'code');
        $last = $code_model->max('expect');
        $ext_model = new Ext();
        $ext_info = $ext_model->where('name', $name)->find();
        if ($ext_info['is_system_code']) json(['code' => 0, 'msg' => '系统开奖不能推送']);
        $setting_model = LotteryCommon::getSetting($name);
        $setting_config = json_decode($setting_model->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        $dataNum_len = strlen((string)$setting_config['startIssue']);
        foreach ($get_code as $key => $v) {
            $dataNum = explode('-', $v['dataNum']);
            if ($ext_info['expect_type']) {
                $expect = $dataNum[1];
            } else {
                $expect = (int)'20' . $dataNum[0] . sprintf("%0" . $dataNum_len . "d", intval($dataNum[1]));
            }
            if($expect <= $last) continue;
            array_shift($v);
            $code = implode(",", $v);
            if ($code == '-' || $code == '') continue;
            $data[] = [
                'code' => $code,
                'expect' => $expect,
                'ext_name' => $name
            ];
        }
        if(empty($data)){
            return json(['code' => 1, 'msg' => '数据已最新']);
        }
        foreach ($data as $key => $v) {
            $has_info = $code_model->where('expect', $v['expect'])->where('code', $v['code'])->find();
            if ($has_info) unset($data[$key]);
        }
        $data = array_unique($data, SORT_REGULAR);
        $res = $code_model->insertAll($data);
        if($res){
            /**遗漏--彩种名到时候配置*/
            (new Award())->miss($name,$data);
            /**派奖*/
            $redis = new Redis();
            $redis->pub('prize', "signature=" . $signature . "&timestamp=" . $timestamp . "&name=" . $name);
            return json(['code' => 1]);
        }
        return json(['code' => 0, 'msg' => '数据库执行失败']);
    }

    /**
     * 开奖
     * @$signature 签名标识 计算方式  md5(token + timestamp)
     * @$name 彩票类型
     * @$expect 期号
    */
    public function setPrize($signature, $timestamp, $name, $data = '')
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->config = $this->get_config();
        if (!$signature or !$timestamp) return json(['err' => 1, 'msg' => '参数错误']);
        $token = Config::get('baseConfig.authorization_token');
        $check_signature = md5($token . $timestamp);
        if ($signature != $check_signature) return json(['err' => 1, 'msg' => '签名不通过']);
        $code_model = LotteryCommon::getModel($name, 'code');
        if (!$data) {
            $expect_model = LotteryCommon::getModel($name, 'expect');
            $new_code = $code_model->order('expect desc')->find();
            if (!$new_code) return json(['err' => 1, 'msg' => '没有开奖']);
            $expect_data = $expect_model->group("expect")->where('expect', '<=', $new_code['expect'])->where('status', '<', 2)->column('expect');
            $data = $code_model->whereIn('expect', $expect_data)->column("code, expect, ext_name");
        } else {
            $data = json_decode($data, true);
        }
        return json(['err' => 0, 'msg' => $code_model->setPrize($data)]);
    }


}
