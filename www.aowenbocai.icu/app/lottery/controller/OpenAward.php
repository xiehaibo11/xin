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
        
        try {
            $get_code = json_decode($res_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return json(['code' => 0, 'msg' => '开奖数据格式错误']);
            }
        } catch (Exception $e) {
            Log::error('开奖数据解析失败: ' . $e->getMessage());
            return json(['code' => 0, 'msg' => '开奖数据解析失败']);
        }

        $code_model = LotteryCommon::getModel($name, 'code');
        $last = $code_model->max('expect');
        $ext_model = new Ext();
        $ext_info = $ext_model->where('name', $name)->find();
        if ($ext_info['is_system_code']) return json(['code' => 0, 'msg' => '系统开奖不能推送']);
        
        $setting_model = LotteryCommon::getSetting($name);
        $setting_config = json_decode($setting_model->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        $dataNum_len = strlen((string)$setting_config['startIssue']);
        
        $data = [];
        $batch_size = 100; // 批量处理大小
        $processed = 0;
        
        foreach ($get_code as $key => $v) {
            try {
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
                    'ext_name' => $name,
                    'create_time' => time()
                ];
                
                $processed++;
                
                // 批量处理，避免内存溢出
                if ($processed >= $batch_size) {
                    $this->processBatch($code_model, $data, $name, $signature, $timestamp);
                    $data = [];
                    $processed = 0;
                }
            } catch (Exception $e) {
                Log::error("处理开奖数据失败 [{$name}]: " . $e->getMessage());
                continue;
            }
        }
        
        // 处理剩余数据
        if (!empty($data)) {
            return $this->processBatch($code_model, $data, $name, $signature, $timestamp);
        }
        
        return json(['code' => 1, 'msg' => '数据已最新']);
    }
    
    /**
     * 批量处理开奖数据
     */
    private function processBatch($code_model, $data, $name, $signature, $timestamp)
    {
        if (empty($data)) {
            return json(['code' => 1, 'msg' => '数据已最新']);
        }
        
        // 去重检查
        $expects = array_column($data, 'expect');
        $existing = $code_model->whereIn('expect', $expects)->column('expect,code');
        
        foreach ($data as $key => $v) {
            $check_key = $v['expect'] . ',' . $v['code'];
            if (in_array($check_key, $existing)) {
                unset($data[$key]);
            }
        }
        
        $data = array_values(array_unique($data, SORT_REGULAR));
        
        if (empty($data)) {
            return json(['code' => 1, 'msg' => '数据已最新']);
        }
        
        // 使用事务确保数据一致性
        $code_model->startTrans();
        try {
            $res = $code_model->insertAll($data);
            if ($res) {
                // 异步处理遗漏计算
                $this->asyncProcessMiss($name, $data);
                
                // 发布开奖消息到Redis
                $redis = new Redis();
                $redis->pub('prize', "signature=" . $signature . "&timestamp=" . $timestamp . "&name=" . $name);
                
                // 发布WebSocket推送消息
                $latest_data = end($data);
                $websocket_data = json_encode([
                    'lotteryName' => $name,
                    'data' => [
                        'code' => $latest_data['code'],
                        'expect' => $latest_data['expect'],
                        'timestamp' => time()
                    ]
                ]);
                $redis->pub('lottery_update', $websocket_data);
                
                $code_model->commit();
                return json(['code' => 1, 'msg' => '处理成功', 'count' => count($data)]);
            } else {
                $code_model->rollback();
                return json(['code' => 0, 'msg' => '数据库执行失败']);
            }
        } catch (Exception $e) {
            $code_model->rollback();
            Log::error("开奖数据入库失败 [{$name}]: " . $e->getMessage());
            return json(['code' => 0, 'msg' => '数据处理异常']);
        }
    }
    
    /**
     * 异步处理遗漏计算
     */
    private function asyncProcessMiss($name, $data)
    {
        try {
            // 使用队列或异步任务处理遗漏计算，避免阻塞主流程
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            
            (new Award())->miss($name, $data);
        } catch (Exception $e) {
            Log::error("遗漏计算失败 [{$name}]: " . $e->getMessage());
        }
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
