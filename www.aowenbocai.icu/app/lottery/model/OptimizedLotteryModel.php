<?php
namespace app\lottery\model;

use think\Model;
use think\Cache;
use think\Db;
use think\Log;

/**
 * 优化的彩票模型类
 * 提供高性能的数据库查询和缓存机制
 */
class OptimizedLotteryModel extends Model
{
    protected $cache_expire = 300; // 5分钟缓存
    protected $cache_prefix = 'lottery_opt_';
    
    /**
     * 获取最新开奖号码（优化版）
     */
    public function getLatestCode($lottery_type, $ext_name, $limit = 1)
    {
        $cache_key = $this->cache_prefix . "latest_{$lottery_type}_{$ext_name}_{$limit}";
        
        // 尝试从缓存获取
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $table_name = "plugin_{$lottery_type}_code";
            
            $query = Db::table($table_name)
                      ->field('expect, code, create_time')
                      ->where('ext_name', $ext_name)
                      ->order('expect', 'desc')
                      ->limit($limit);
            
            if ($limit == 1) {
                $result = $query->find();
            } else {
                $result = $query->select();
            }
            
            // 缓存结果
            if ($result) {
                Cache::set($cache_key, $result, $this->cache_expire);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("获取最新开奖号码失败: {$lottery_type}_{$ext_name}, 错误: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 批量获取近期开奖号码（优化版）
     */
    public function getRecentCodes($lottery_type, $ext_name, $limit = 10)
    {
        $cache_key = $this->cache_prefix . "recent_{$lottery_type}_{$ext_name}_{$limit}";
        
        // 尝试从缓存获取
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $table_name = "plugin_{$lottery_type}_code";
            
            $result = Db::table($table_name)
                       ->field('expect, code, create_time')
                       ->where('ext_name', $ext_name)
                       ->order('expect', 'desc')
                       ->limit($limit)
                       ->select();
            
            // 缓存结果
            if ($result) {
                Cache::set($cache_key, $result, $this->cache_expire);
            }
            
            return $result ?: [];
            
        } catch (\Exception $e) {
            Log::error("获取近期开奖号码失败: {$lottery_type}_{$ext_name}, 错误: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 获取当前期号信息（优化版）
     */
    public function getCurrentExpect($lottery_type, $ext_name)
    {
        $cache_key = $this->cache_prefix . "expect_{$lottery_type}_{$ext_name}";
        
        // 尝试从缓存获取（短期缓存）
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $table_name = "plugin_{$lottery_type}_expect";
            
            $result = Db::table($table_name)
                       ->field('expect, start_time, end_time, status, timelong')
                       ->where([
                           'ext_name' => $ext_name,
                           'status' => 0
                       ])
                       ->order('expect', 'desc')
                       ->find();
            
            // 缓存结果（1分钟缓存，因为期号信息变化较快）
            if ($result) {
                Cache::set($cache_key, $result, 60);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("获取当前期号失败: {$lottery_type}_{$ext_name}, 错误: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 批量插入开奖号码（优化版）
     */
    public function batchInsertCodes($lottery_type, $data)
    {
        if (empty($data)) {
            return false;
        }
        
        try {
            $table_name = "plugin_{$lottery_type}_code";
            
            // 使用事务确保数据一致性
            Db::startTrans();
            
            // 批量插入，使用IGNORE避免重复数据错误
            $sql = "INSERT IGNORE INTO {$table_name} (expect, code, ext_name, create_time) VALUES ";
            $values = [];
            $params = [];
            
            foreach ($data as $item) {
                $values[] = "(?, ?, ?, ?)";
                $params[] = $item['expect'];
                $params[] = $item['code'];
                $params[] = $item['ext_name'];
                $params[] = $item['create_time'] ?? time();
            }
            
            $sql .= implode(', ', $values);
            
            $result = Db::execute($sql, $params);
            
            Db::commit();
            
            // 清理相关缓存
            $this->clearRelatedCache($lottery_type, $data[0]['ext_name']);
            
            return $result;
            
        } catch (\Exception $e) {
            Db::rollback();
            Log::error("批量插入开奖号码失败: {$lottery_type}, 错误: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 获取遗漏数据（优化版）
     */
    public function getMissData($lottery_type, $ext_name)
    {
        $cache_key = $this->cache_prefix . "miss_{$lottery_type}_{$ext_name}";
        
        // 尝试从缓存获取
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $table_name = "plugin_{$lottery_type}_miss";
            
            $result = Db::table($table_name)
                       ->where('ext_name', $ext_name)
                       ->find();
            
            // 缓存结果
            if ($result) {
                Cache::set($cache_key, $result, $this->cache_expire);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("获取遗漏数据失败: {$lottery_type}_{$ext_name}, 错误: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 使用视图快速获取所有彩种的最新开奖
     */
    public function getAllLatestCodes()
    {
        $cache_key = $this->cache_prefix . "all_latest";
        
        // 尝试从缓存获取
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $result = Db::query("
                SELECT lottery_type, ext_name, expect, code, create_time 
                FROM v_latest_lottery_codes 
                WHERE rn = 1
                ORDER BY create_time DESC
            ");
            
            // 缓存结果
            if ($result) {
                Cache::set($cache_key, $result, 180); // 3分钟缓存
            }
            
            return $result ?: [];
            
        } catch (\Exception $e) {
            Log::error("获取所有最新开奖失败: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 获取当前所有彩种的期号信息
     */
    public function getAllCurrentExpects()
    {
        $cache_key = $this->cache_prefix . "all_expects";
        
        // 尝试从缓存获取
        $result = Cache::get($cache_key);
        if ($result !== false) {
            return $result;
        }
        
        try {
            $result = Db::query("
                SELECT lottery_type, ext_name, expect, start_time, end_time, status 
                FROM v_current_lottery_expects
                ORDER BY end_time ASC
            ");
            
            // 缓存结果（1分钟缓存）
            if ($result) {
                Cache::set($cache_key, $result, 60);
            }
            
            return $result ?: [];
            
        } catch (\Exception $e) {
            Log::error("获取所有当前期号失败: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 清理相关缓存
     */
    private function clearRelatedCache($lottery_type, $ext_name)
    {
        $patterns = [
            $this->cache_prefix . "latest_{$lottery_type}_{$ext_name}_*",
            $this->cache_prefix . "recent_{$lottery_type}_{$ext_name}_*",
            $this->cache_prefix . "expect_{$lottery_type}_{$ext_name}",
            $this->cache_prefix . "miss_{$lottery_type}_{$ext_name}",
            $this->cache_prefix . "all_latest",
            $this->cache_prefix . "all_expects"
        ];
        
        foreach ($patterns as $pattern) {
            try {
                Cache::clear($pattern);
            } catch (\Exception $e) {
                Log::warning("清理缓存失败: {$pattern}, 错误: " . $e->getMessage());
            }
        }
    }
    
    /**
     * 数据库性能监控
     */
    public function getPerformanceStats()
    {
        try {
            $stats = [];
            
            // 查询慢查询数量
            $slow_queries = Db::query("SHOW STATUS LIKE 'Slow_queries'");
            $stats['slow_queries'] = $slow_queries[0]['Value'] ?? 0;
            
            // 查询连接数
            $connections = Db::query("SHOW STATUS LIKE 'Threads_connected'");
            $stats['connections'] = $connections[0]['Value'] ?? 0;
            
            // 查询缓存命中率
            $cache_hits = Db::query("SHOW STATUS LIKE 'Qcache_hits'");
            $cache_inserts = Db::query("SHOW STATUS LIKE 'Qcache_inserts'");
            $hits = $cache_hits[0]['Value'] ?? 0;
            $inserts = $cache_inserts[0]['Value'] ?? 0;
            $stats['cache_hit_rate'] = $inserts > 0 ? round($hits / ($hits + $inserts) * 100, 2) : 0;
            
            // 查询表大小
            $table_sizes = Db::query("
                SELECT TABLE_NAME, 
                       ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME LIKE 'plugin_%_code'
                ORDER BY size_mb DESC
            ");
            $stats['table_sizes'] = $table_sizes;
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error("获取性能统计失败: " . $e->getMessage());
            return [];
        }
    }
}