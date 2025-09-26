<?php
namespace app\lottery\controller;

use app\common\controller\LotteryCommon;
use core\Redis;
use think\Log;
use think\Cache;
use Exception;

/**
 * 彩票API控制器 - 增强错误处理和容错机制
 */
class LotteryApi
{
    private $redis;
    private $cache_prefix = 'lottery_api_';
    private $cache_expire = 300; // 5分钟缓存
    
    public function __construct()
    {
        $this->redis = new Redis();
    }
    
    /**
     * 获取开奖号码 - 增强版
     */
    public function getNewCode($name = '', $issue = '')
    {
        $start_time = microtime(true);
        
        try {
            // 参数验证
            if (empty($name)) {
                return json(['err' => 1, 'msg' => '彩种名称不能为空']);
            }
            
            // 缓存键
            $cache_key = $this->cache_prefix . "newcode_{$name}_{$issue}";
            
            // 尝试从缓存获取
            $cached_result = Cache::get($cache_key);
            if ($cached_result !== false) {
                Log::info("从缓存获取开奖号码: {$name}");
                return json($cached_result);
            }
            
            // 获取最新开奖号码
            $result = $this->fetchNewCodeWithRetry($name, $issue, 3);
            
            if ($result['err'] == 0) {
                // 缓存成功结果
                Cache::set($cache_key, $result, $this->cache_expire);
            }
            
            // 记录性能指标
            $execution_time = round((microtime(true) - $start_time) * 1000, 2);
            Log::info("获取开奖号码完成: {$name}, 耗时: {$execution_time}ms");
            
            return json($result);
            
        } catch (Exception $e) {
            Log::error("获取开奖号码异常: {$name}, 错误: " . $e->getMessage());
            return json(['err' => 1, 'msg' => '系统异常，请稍后重试']);
        }
    }
    
    /**
     * 带重试机制的开奖号码获取
     */
    private function fetchNewCodeWithRetry($name, $issue, $max_retries = 3)
    {
        $attempt = 0;
        $last_error = '';
        
        while ($attempt < $max_retries) {
            $attempt++;
            
            try {
                $code_model = LotteryCommon::getModel($name, 'code');
                if (!$code_model) {
                    throw new Exception("彩种模型不存在: {$name}");
                }
                
                // 获取最新开奖号码
                $latest_code = $code_model->where('expect', '>', $issue)
                                         ->order('expect desc')
                                         ->find();
                
                if ($latest_code) {
                    // 获取近10期开奖号码
                    $recent_codes = $this->getRecentCodesWithFallback($name, $code_model);
                    
                    return [
                        'err' => 0,
                        'msg' => '获取成功',
                        'data' => [
                            'codeOpen' => $latest_code['code'],
                            'tenCode' => $recent_codes,
                            'expect' => $latest_code['expect'],
                            'timestamp' => time()
                        ]
                    ];
                } else {
                    throw new Exception("暂无新开奖号码");
                }
                
            } catch (Exception $e) {
                $last_error = $e->getMessage();
                Log::warning("获取开奖号码失败 (尝试 {$attempt}/{$max_retries}): {$name}, 错误: {$last_error}");
                
                if ($attempt < $max_retries) {
                    // 指数退避策略
                    usleep(pow(2, $attempt) * 100000); // 0.2s, 0.4s, 0.8s
                }
            }
        }
        
        // 所有重试都失败，返回错误
        return [
            'err' => 1,
            'msg' => "获取失败: {$last_error}",
            'retry_count' => $attempt
        ];
    }
    
    /**
     * 带降级策略的近期开奖号码获取
     */
    private function getRecentCodesWithFallback($name, $code_model)
    {
        try {
            // 尝试获取最近10期
            $recent_codes = $code_model->order('expect desc')
                                     ->limit(10)
                                     ->select();
            
            if ($recent_codes) {
                return $recent_codes->toArray();
            }
            
            // 降级策略：从缓存获取
            $cache_key = $this->cache_prefix . "recent_{$name}";
            $cached_codes = Cache::get($cache_key);
            
            if ($cached_codes) {
                Log::info("使用缓存的近期开奖数据: {$name}");
                return $cached_codes;
            }
            
            return [];
            
        } catch (Exception $e) {
            Log::error("获取近期开奖号码失败: {$name}, 错误: " . $e->getMessage());
            
            // 最后的降级策略：返回空数组
            return [];
        }
    }
    
    /**
     * 获取期号信息 - 增强版
     */
    public function getIssueInfo($name = '')
    {
        $start_time = microtime(true);
        
        try {
            if (empty($name)) {
                return json(['err' => 1, 'msg' => '彩种名称不能为空']);
            }
            
            // 缓存键
            $cache_key = $this->cache_prefix . "issue_{$name}";
            
            // 尝试从缓存获取
            $cached_result = Cache::get($cache_key);
            if ($cached_result !== false) {
                return json(['err' => 0, 'data' => $cached_result]);
            }
            
            // 获取期号信息
            $result = $this->fetchIssueInfoWithRetry($name, 3);
            
            if ($result['err'] == 0) {
                // 缓存成功结果（较短时间）
                Cache::set($cache_key, $result['data'], 60); // 1分钟缓存
            }
            
            $execution_time = round((microtime(true) - $start_time) * 1000, 2);
            Log::info("获取期号信息完成: {$name}, 耗时: {$execution_time}ms");
            
            return json($result);
            
        } catch (Exception $e) {
            Log::error("获取期号信息异常: {$name}, 错误: " . $e->getMessage());
            return json(['err' => 1, 'msg' => '系统异常，请稍后重试']);
        }
    }
    
    /**
     * 带重试机制的期号信息获取
     */
    private function fetchIssueInfoWithRetry($name, $max_retries = 3)
    {
        $attempt = 0;
        $last_error = '';
        
        while ($attempt < $max_retries) {
            $attempt++;
            
            try {
                $expect_model = LotteryCommon::getModel($name, 'expect');
                $code_model = LotteryCommon::getModel($name, 'code');
                
                if (!$expect_model || !$code_model) {
                    throw new Exception("模型不存在: {$name}");
                }
                
                // 获取当前期号信息
                $current_expect = $expect_model->where('status', 0)
                                              ->order('expect desc')
                                              ->find();
                
                if (!$current_expect) {
                    throw new Exception("未找到当前期号");
                }
                
                // 获取最新开奖号码
                $latest_code = $code_model->order('expect desc')->find();
                
                // 获取近期开奖
                $recent_codes = $code_model->order('expect desc')
                                          ->limit(10)
                                          ->select();
                
                $data = [
                    'expect' => $current_expect['expect'],
                    'sort_expect' => substr($current_expect['expect'], -3),
                    'down_time' => max(0, $current_expect['end_time'] - time()),
                    'lastIssue' => $latest_code ? $latest_code['expect'] : '',
                    'timelong' => $current_expect['timelong'] ?? 5,
                    'todayTime' => date('Y-m-d H:i:s'),
                    'awardNumber' => [
                        'code' => $latest_code ? $latest_code['code'] : ''
                    ],
                    'open' => $recent_codes ? $recent_codes->toArray() : [],
                    'getnewcode' => $latest_code ? ($latest_code['expect'] < $current_expect['expect']) : true,
                    'expect_type' => 0,
                    'firstIssue' => $current_expect['expect']
                ];
                
                return [
                    'err' => 0,
                    'msg' => '获取成功',
                    'data' => $data
                ];
                
            } catch (Exception $e) {
                $last_error = $e->getMessage();
                Log::warning("获取期号信息失败 (尝试 {$attempt}/{$max_retries}): {$name}, 错误: {$last_error}");
                
                if ($attempt < $max_retries) {
                    usleep(pow(2, $attempt) * 100000);
                }
            }
        }
        
        return [
            'err' => 1,
            'msg' => "获取失败: {$last_error}",
            'retry_count' => $attempt
        ];
    }
    
    /**
     * 健康检查接口
     */
    public function healthCheck()
    {
        $health_info = [
            'status' => 'ok',
            'timestamp' => time(),
            'services' => []
        ];
        
        // 检查Redis连接
        try {
            $this->redis->ping();
            $health_info['services']['redis'] = 'ok';
        } catch (Exception $e) {
            $health_info['services']['redis'] = 'error: ' . $e->getMessage();
            $health_info['status'] = 'warning';
        }
        
        // 检查数据库连接
        try {
            \think\Db::query('SELECT 1');
            $health_info['services']['database'] = 'ok';
        } catch (Exception $e) {
            $health_info['services']['database'] = 'error: ' . $e->getMessage();
            $health_info['status'] = 'error';
        }
        
        // 检查缓存
        try {
            Cache::set('health_check', time(), 10);
            $health_info['services']['cache'] = 'ok';
        } catch (Exception $e) {
            $health_info['services']['cache'] = 'error: ' . $e->getMessage();
            $health_info['status'] = 'warning';
        }
        
        return json($health_info);
    }
    
    /**
     * 清理缓存接口
     */
    public function clearCache($type = 'all')
    {
        try {
            $cleared = 0;
            
            switch ($type) {
                case 'lottery':
                    // 清理彩票相关缓存
                    $keys = Cache::tag('lottery')->clear();
                    $cleared = is_array($keys) ? count($keys) : 1;
                    break;
                case 'all':
                default:
                    Cache::clear();
                    $cleared = 'all';
                    break;
            }
            
            Log::info("缓存清理完成: {$type}, 清理数量: {$cleared}");
            
            return json([
                'err' => 0,
                'msg' => '缓存清理成功',
                'cleared' => $cleared
            ]);
            
        } catch (Exception $e) {
            Log::error("缓存清理失败: " . $e->getMessage());
            return json(['err' => 1, 'msg' => '缓存清理失败']);
        }
    }
}