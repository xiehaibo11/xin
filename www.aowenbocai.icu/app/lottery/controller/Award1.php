<?php

namespace app\lottery\controller;

use app\common\controller\LotteryCommon;
use app\lottery\model\Syxw;
use app\lottery\model\Ssc;
use app\lottery\model\Pk10;
use app\lottery\model\common\BaseBuy;
use app\lottery\model\common\BaseJoin;
use think\Controller;
use think\Db;

class Award extends Controller
{
    // 实际使用的30种彩种配置
    protected $config = [
        // ==================== 时时彩类 ====================
        'hn5fc' => "hnssc",           // 河内五分彩 - 每天开奖288期 ✅
        'qqffc' => "qqssc",           // QQ分分彩 - 每天开奖1440期 ✅
        'txffc(qq)' => "txssc",       // 腾讯分分彩 - 每天开奖1440期 ✅
        'cqssc' => "ssc",             // 重庆时时彩 - 每天开奖59期 ✅
        'tjssc' => "tjssc",           // 天津时时彩 - 每天开奖42期 ✅
        'xjssc' => "xjssc",           // 新疆时时彩 - 每天开奖48期
        'az3fc' => "azssc",           // 澳洲3分彩 - 每天开奖480期
        'blsffc' => "blsssc",         // 比利时分分彩 - 每天开奖1440期
        'tx5fc' => "ynissc",          // 腾讯五分彩 - 每天开奖288期
        'mnl45sc' => "mnlssc",        // 马尼拉45秒彩 - 每天开奖1920期
        'jn15fc' => "jndssc",         // 加纳1.5分彩 - 每天开奖960期
        'dj15fc' => "djssc",          // 东京1.5分彩 - 每天开奖960期
        'jnd3fc' => "jndssc",         // 加拿大3分彩 - 每天开奖480期
        
        // ==================== 11选5类 ====================
        'jx11x5' => "jx11",           // 江西11选5 - 每天开奖42期 ✅
        'sd11x5' => "sd11",           // 11运夺金(山东11选5) - 每天开奖43期
        'gd11x5' => "gd11",           // 广东11选5 - 每天开奖42期 ✅
        '5f11x5' => "jis11",          // 5分11选5 - 每天开奖288期
        'ff11x5' => "ff11",           // 分分11选5 - 每天开奖1440期
        
        // ==================== 快3类 ====================
        '1fks' => "ffks",             // 一分快3 - 每天开奖1440期
        '3fks' => "jsks",             // 3分快3 - 每天开奖288期
        
        // ==================== 28类 ====================
        'jd28' => "js28",             // 极速28 - 每天开奖480期 ✅
        'jnd28' => "jnd28",           // 加拿大28 - 每天开奖338期
        'ff28' => "ff28",             // 分分28 - 每天开奖1440期
        'xy28' => "xy28",             // 幸运28 - 每天开奖179期
        'bj28' => "bj28",             // 北京28 - 每天开奖179期
        
        // ==================== PK10类 ====================
        'jsft' => "jsft10",           // 极速飞艇 - 每天开奖480期 ✅
        'ffsc' => "ff10",             // 分分赛车 - 每天开奖1440期
        'jssc' => "js10",             // 极速赛车 - 每天开奖480期 ✅
        'ffft' => "ffft10",           // 分分飞艇 - 每天开奖1440期
        'xyft' => "xyft10",           // 幸运飞艇 - 每天开奖180期
    ];
    
    protected $cjUrl = [
        // ==================== 时时彩类 API接口 ====================
        'hnssc'   => "https://bfc31c9e14f1a84523984c03dfe16be5.lotterydata.net/",    // 河内五分彩 ✅
        'qqssc'   => "https://14d3d5447021af42318b4ca8e90ab767.lotterydata.net/",    // QQ分分彩 ✅
        'txssc'   => "https://5b84ae5faac95c22cc77f2536f679107.lotterydata.net/",    // 腾讯分分彩 ✅
        'ssc'     => "https://91b56da46dd5b808163e6243fe9eb038.lotterydata.net/",    // 重庆时时彩 ✅
        'tjssc'   => "https://2eba223c0a98b5a868af8f4e6f6d565f.lotterydata.net/",    // 天津时时彩 ✅
        'xjssc'   => "https://api.lottery.com/xjssc/",                               // 新疆时时彩 (预留)
        'azssc'   => "https://api.lottery.com/azssc/",                               // 澳洲3分彩 (预留)
        'blsssc'  => "https://api.lottery.com/blsssc/",                              // 比利时分分彩 (预留)
        'ynissc'  => "https://api.lottery.com/ynissc/",                              // 腾讯五分彩 (预留)
        'mnlssc'  => "https://api.lottery.com/mnlssc/",                              // 马尼拉45秒彩 (预留)
        'jndssc'  => "https://api.lottery.com/jndssc/",                              // 加纳1.5分彩 (预留)
        'djssc'   => "https://api.lottery.com/djssc/",                               // 东京1.5分彩 (预留)
        
        // ==================== 11选5类 API接口 ====================
        'jx11'    => "https://505ba99e038a1a29dd94301acede2f47.lotterydata.net/",    // 江西11选5 ✅
        'sd11'    => "https://api.lottery.com/sd11/",                                // 11运夺金(山东11选5) (预留)
        'gd11'    => "https://c2f1541c6e506a7b78b081f2be4098a6.lotterydata.net/",    // 广东11选5 ✅
        'jis11'   => "https://api.lottery.com/jis11/",                               // 5分11选5 (预留)
        'ff11'    => "https://api.lottery.com/ff11/",                                // 分分11选5 (预留)
        
        // ==================== 快3类 API接口 ====================
        'ffks'    => "https://api.lottery.com/ffks/",                                // 一分快3 (预留)
        'jsks'    => "https://api.lottery.com/jsks/",                                // 3分快3 (预留)
        
        // ==================== 28类 API接口 ====================
        'js28'    => "https://a715a474b2fde208e7751735405229b1.lotterydata.net/",    // 极速28 ✅
        'jnd28'   => "https://a715a474b2fde208e7751735405229b1.lotterydata.net/",    // 加拿大28 ✅
        'ff28'    => "https://api.lottery.com/ff28/",                                // 分分28 (预留)
        'xy28'    => "https://api.lottery.com/xy28/",                                // 幸运28 (预留)
        'bj28'    => "https://api.lottery.com/bj28/",                                // 北京28 (预留)
        
        // ==================== PK10类 API接口 ====================
        'jsft10'  => "https://3b2ada90f8a6c6861da9b3b2928823fd.lotterydata.net/",    // 极速飞艇 ✅
        'ff10'    => "https://api.lottery.com/ff10/",                                // 分分赛车 (预留)
        'js10'    => "https://b444b3a032283f5c52ef76e838c4fd6b.lotterydata.net/",    // 极速赛车 ✅
        'ffft10'  => "https://api.lottery.com/ffft10/",                              // 分分飞艇 (预留)
        'xyft10'  => "https://3b2ada90f8a6c6861da9b3b2928823fd.lotterydata.net/",    // 幸运飞艇 ✅
    ];
    
    
    public function autoOrder(){
    	$buy_model = (new BaseBuy());
    	
    	$data = $buy_model->field('end_time,total_money,id,ext_name')->where(['status'=>0,'is_join'=>1])->select()->toArray();
    	
    	foreach($data as $k=>$v){
    		
    	
    	//	echo (strtotime($v['end_time'])-20).'----'.time().'<br/>';
    		//	echo date('Y-m-d H:i:s',(strtotime($v['end_time'])-20)).'----'.date('Y-m-d H:i:s',time()).'<br/>';
    		if((strtotime($v['end_time']) - 15) <time()){
    			

    			
    		}else{
    			
    			echo '未到规定时间';
    			
    		}
    	
    	}
    	
    	
    }
    
   
    /**
     * 开奖号码记录数据库
     */
    public function getAwards($name)
    {
        if(!array_key_exists($name, $this->config)){
            return json_encode(['err' => 1, 'msg' => '彩种名不正确']);
        }
        
        $config_name = $this->config[$name];
        $url = $this->cjUrl[$config_name];

        //获取号码
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (compatible; LotteryBot/1.0)\r\n"
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return json_encode(['err' => 1, 'msg' => 'API请求失败']);
        }
        
        $get_code = json_decode($response, true);
        if (!$get_code) {
            return json_encode(['err' => 1, 'msg' => 'API数据解析失败']);
        }
        
        // 检查API响应 - 智能匹配数据键名
        $original_name = $name;
        $lottery_data = null;
        
        // 定义实际的API返回键名映射 (基于实际测试结果)
        $api_key_mapping = [
            'hn5fc' => 'hn5fc',              // 河内五分彩 ✅
            'qqffc' => 'qqtxffc',            // QQ分分彩 ✅
            'txffc(qq)' => 'txffc(qq)',      // 腾讯分分彩 ✅
            'cqssc' => 'cqssc',              // 重庆时时彩 ✅
            'tjssc' => 'tjssc',              // 天津时时彩 ✅
            'jx11x5' => 'jx11x5',            // 江西11选5 ✅
            'gd11x5' => 'gd11x5',            // 广东11选5 ✅
            'jd28' => 'jnd28(bs)(jgsf)',     // 极速28 ✅
            'jnd28' => 'jnd28(bs)(jgsf)',    // 加拿大28 ✅
            'jsft' => 'xxyft',               // 极速飞艇 ✅
            'jssc' => 'jssc',                // 极速赛车 ✅
            'xyft' => 'xxyft',               // 幸运飞艇 ✅
        ];
        
        // 尝试多种可能的键名
        $possible_keys = [
            isset($api_key_mapping[$original_name]) ? $api_key_mapping[$original_name] : $original_name,
            $original_name, 
            $config_name
        ];
        
        // 添加一些常见变体键名
        if ($name == 'jd28') $possible_keys[] = 'jnd28';
        if ($name == 'jnd28') $possible_keys[] = 'jd28';
        if ($name == 'xyft') $possible_keys[] = 'xyft10';
        if ($name == 'jsft') $possible_keys[] = 'jsft10';
        
        foreach ($possible_keys as $key) {
            if (isset($get_code[$key]) && isset($get_code[$key]['opens'])) {
                $lottery_data = $get_code[$key]['opens'];
                break;
            }
        }
        
        // 如果还是找不到，尝试获取第一个有效的数据
        if (!$lottery_data && !empty($get_code)) {
            foreach ($get_code as $key => $data) {
                if (isset($data['opens']) && !empty($data['opens'])) {
                    $lottery_data = $data['opens'];
                    break;
                }
            }
        }
        
        if (!$lottery_data) {
            return json_encode(['err' => 1, 'msg' => 'API数据获取失败: ' . $original_name]);
        }
        
        // $lottery_data 已经在上面设置了
        if(isset($get_code[$original_name]["nextTerm"])){
            $last = $get_code[$original_name]["nextTerm"];
        }else{
            $last = isset($lottery_data[0]['term']) ? $lottery_data[0]['term'] + 1 : 1;
        }
        
        $name = $config_name;    

        $classUrl = 'app\lottery\model\\'.$name.'\Plugin'.ucfirst($name).'Code';
        $code_model = new  $classUrl;
        // $last = $code_model->max('expect');
        
        foreach ($lottery_data as $key => $v) {
            if($name == "ssc" || $name == "gd11" || $name == "sd11" || $name == "jx11" || $name == "tjssc" || $name == "xjssc"){
                $expect = date("Y",time()) . substr($v['term'], 2);
                $expect = str_replace('-','',$expect);
                
            }else{
                $expect = str_replace('-','',$v['term']);
            }
            //数据库查重
            $existing_record = Db::table("kr_lottery_code")->where(["ext_name"=>$name,"expect"=>$expect])->find();
            if(!$existing_record){
                echo $expect.'---'.$last.',';
            	$code = $v['number'];
                $data[] = [
                    'code' => $code,
                    'expect' => $expect,
                    'ext_name' => $name
                ];
                
                $res = $code_model->insertAll($data);
                $return = 'error';
                if($res){
                    /**派奖 */
                    $return = $code_model->setPrize($data);
                    /**遗漏--彩种名到时候配置*/
                    $this->miss($name,$data);
                }
                return $return;
            }else{
                return '彩虹提示您：该开奖数据已存在！';
            }
        }
    }

    /**遗漏处理 */
    public function miss($name, $data)
    {
        $cp_type = LotteryCommon::getCpType($name);
        $setting = LotteryCommon::getSetting($name);
        $add_data = json_decode($setting->getValue(LotteryCommon::getSettingValue($name, 'miss')), true);
        $add_data = $add_data ? $add_data : [];
        foreach ($data as $key => $value) {
            switch ($cp_type) {
                case 'ssc':
                    $add_data = $this->sumMissSsc($name, $value['code'], $add_data);
                    break;
                case 'syxw':
                    $add_data = $this->sumMissSyxw($name, $value['code'], $add_data);
                    break;
                case 'pk10':
                    $add_data = $this->sumMissPk10($name, $value['code'], $add_data);
                    break;
            }
        }
        $setting->setValue(LotteryCommon::getSettingValue($name, 'miss'), json_encode($add_data));
    }

    /**计算遗漏pk10 */
    public function sumMissPk10($name, $code, $data)
    {
        $code = explode(',', $code); 
        if(empty($code) || count($code) < 10){
            return false;
        }
        for($j = 0; $j < 10; $j++){
            $data['pt'][$j] = $this->pk10Count($code, $data, $j);
        }
        $data['dx'] = $this->missDxPk10($code, $data);
        $data['lh'] = $this->missLhPk10($code, $data);
        return $data;
    }

    public function pk10Count($code, $data, $j)
    {
        $res = [];
        for ($i = 1; $i < 11; $i++) { 
            $key = $i < 10 ? '0'.$i : $i;
            $add = intval($code[$j]) == $key ? 0 : 1;
            $res[$i-1] = isset($data[$j][$i-1]) && $add ? ($data[$j][$i-1] + $add) : $add;
        }
        return $res;
    }

    /**大小单双 */
    public function missDxPk10($code, $data)
    {
        $dx = !empty($data['dx']) ? $data['dx'] : [];
        for ($i = 0; $i < 10; $i++) {
            $da = $code[$i] > 5 ? 1 : 0;
            $xiao = $da ? 0 : 1;
            $dan = $code[$i] % 2 == 0 ? 0 : 1;
            $shuang = $dan ? 0 : 1;
            $dx[$i]['da'] = !empty($dx[$i]['da']) ?($dx[$i]['da'] + $da) : $da;
            $dx[$i]['sm'] = !empty($dx[$i]['sm']) ?($dx[$i]['sm'] + $xiao) : $xiao;
            $dx[$i]['dan'] = !empty($dx[$i]['dan']) ?($dx[$i]['dan'] + $dan) : $dan;
            $dx[$i]['shuang'] = !empty($dx[$i]['shuang']) ?($dx[$i]['shuang'] + $shuang) : $shuang;
        }
        $gyh = $code[0] + $code[1];
        $gyh_da = $gyh > 11 ? 1 : 0;
        $gyh_xiao = $gyh_da ? 0 : 1;
        $gyh_dan = $gyh % 2 == 0 ? 0 : 1;
        $gyh_shuang = $gyh_dan ? 0 : 1;
        $dx['gyh']['da'] = !empty($dx['gyh']['da']) ?($dx['gyh']['da'] + $gyh_da) : $gyh_da;
        $dx['gyh']['sm'] = !empty($dx['gyh']['sm']) ?($dx['gyh']['sm'] + $gyh_xiao) : $gyh_xiao;
        $dx['gyh']['dan'] = !empty($dx['gyh']['dan']) ?($dx['gyh']['dan'] + $gyh_dan) : $gyh_dan;
        $dx['gyh']['shuang'] = !empty($dx['gyh']['shuang']) ?($dx['gyh']['shuang'] + $gyh_shuang) : $gyh_shuang;
        return $dx;
    }

    /**龙虎 */
    public function missLhPk10($code, $data)
    {
        $lh = !empty($data['lh']) ? $data['lh'] : [];
        for ($i=0; $i < 5; $i++) {
            $is_long = $code[$i] > $code[9-$i] ? 1 : 0;
            $is_hu = $is_long ? 0 : 1;
            $lh[$i+1]['long'] = !empty($lh[$i+1]['long']) ?($lh[$i+1]['long'] + $is_long) : $is_long;
            $lh[$i+1]['hu'] = !empty($lh[$i+1]['long']) ?($lh[$i+1]['long'] + $is_hu) : $is_hu;
        }
        return $lh;
    }

    /**计算遗漏 --初始值，到时要去掉*/
    public function sumMissSyxw($name,$code, $data)
    {
        if($name == '' || $code == ''){
            return false;
        }
        $code = explode(',', $code); 
        if(empty($code) || count($code) < 5){
            return false;
        }
        for ($i = 1; $i < 12; $i++) { 
            $key = $i < 10 ? '0'.$i : $i;
            $data['qy'][$i] = $this->missZhiSyxw($key, $code[0], isset($data['qy'][$i]) ? $data['qy'][$i] : 0);
            $data['qe'][$i] = $this->missZhiSyxw($key, $code[1], isset($data['qe'][$i]) ? $data['qe'][$i] : 0);
            $data['qs'][$i] = $this->missZhiSyxw($key, $code[2], isset($data['qs'][$i]) ? $data['qs'][$i] : 0);
            $data['rx'][$i] = $this->missRx($key, $code, isset($data['rx'][$i]) ? $data['rx'][$i] : 0);
            $data['ze'][$i] = $this->missZe($key, $code, isset($data['ze'][$i]) ? $data['ze'][$i] : 0);
            $data['zs'][$i] = $this->missZs($key, $code, isset($data['zs'][$i]) ? $data['zs'][$i] : 0);
        }
        return $data;
    }

    /**任选 */
    public function missRx($key, $code, $old)
    {
        $add = in_array($key, $code) ? 0 : 1;
        $num = $add ? ($old + $add) : $add;
        return $num;
    }

    /** 直选*/
    public function missZhiSyxw($key, $code, $old)
    {
        $add = $key == $code ? 0 : 1;
        $num = $add ? ($old + $add) : $add;
        return $num;
    }

    /**组二 */
    public function missZe($key, $code, $old)
    {
        $code = array_slice($code, 0, 2);
        $add = in_array($key, $code) ? 0 : 1;
        $num = $add ? ($old + $add) : $add;
        return $num;
    }

    /**组三 */
    public function missZs($key, $code, $old)
    {
        $code = array_slice($code, 0, 3);
        $add = in_array($key, $code) ? 0 : 1;
        $num = $add ? ($old + $add) : $add;
        return $num;
    }

    /**计算遗漏 --初始值，到时要去掉*/
    public function sumMissSsc($name, $code, $data)
    {
        if($name == '' || $code == ''){
            return false;
        }
        $code = explode(',', $code);
        if(empty($code) || count($code) < 5){
            return false;
        }
        for ($i = 0; $i < 10; $i++) {
            $data['qy'][$i] = $this->missZhiSsc($i, $code[0], isset($data['qy'][$i]) ? $data['qy'][$i] : 0);
            $data['qe'][$i] = $this->missZhiSsc($i, $code[1], isset($data['qe'][$i]) ? $data['qe'][$i] : 0);
            $data['qs'][$i] = $this->missZhiSsc($i, $code[2], isset($data['qs'][$i]) ? $data['qs'][$i] : 0);
            $data['qsi'][$i] = $this->missZhiSsc($i, $code[3], isset($data['qsi'][$i]) ? $data['qsi'][$i] : 0);
            $data['qw'][$i] = $this->missZhiSsc($i, $code[4], isset($data['qw'][$i]) ? $data['qw'][$i] : 0);
            $data['ze_h'][$i] = $this->missZeSsc($i, $code, 1, isset($data['ze_h'][$i]) ? $data['ze_h'][$i] : 0);
            $data['ze_q'][$i] = $this->missZeSsc($i, $code, 2, isset($data['ze_q'][$i]) ? $data['ze_q'][$i] : 0);
            $data['zs_h'][$i] = $this->missZsSsc($i, $code, 1, isset($data['zs_h'][$i]) ? $data['zs_h'][$i] : 0);
            $data['zs_z'][$i] = $this->missZsSsc($i, $code, 2, isset($data['zs_z'][$i]) ? $data['zs_z'][$i] : 0);
            $data['zs_q'][$i] = $this->missZsSsc($i, $code, 3, isset($data['zs_q'][$i]) ? $data['zs_q'][$i] : 0);
        }
        $data['dx'] = $this->missDxSsc($code, $data);
        return $data;
    }

    /** 直选*/
    public function missZhiSsc($key, $code, $old)
    {
        $add = $key == $code ? 0 : 1;
        $num = $add ? ($old + $add) : $add;
        return $num;
    }

    /**二星组选 - type 1  后二  2 前二 */
    public function missZeSsc($key, $code, $type, $old)
    {
        if ($type == 1) {
            $code = array_slice($code, 3);
        } else {
            $code = array_slice($code, 0, 2);
        }
        $add = in_array($key, $code) ? 0 : 1;
        $num = $add ?($old + $add) : $add;
        return $num;
    }

    /**三星组选 - type 1  后三  2 中三 3 前三 */
    public function missZsSsc($key, $code, $type, $old)
    {
        if ($type == 1) {
            $code = array_slice($code, 2);
        } elseif ($type == 2) {
            $code = array_slice($code, 1, 3);
        } else {
            $code = array_slice($code, 0, 3);
        }
        $add = in_array($key, $code) ? 0 : 1;
        $num = $add ?($old + $add) : $add;
        return $num;
    }

    /**大小单双 --分十位和个位*/
    public function missDxSsc($code, $data)
    {
        $dx = isset($data['dx']) ? $data['dx'] : [];
        $code = array_slice($code, 3);
        foreach ($code as $key => $value) {
            $da = $value > 4 ? 0 : 1;
            $dan = $value % 2 == 0 ? 0 : 1;
            $dx[$key]['da'] = isset($dx[$key]['da']) ?($dx[$key]['da'] + $da) : $da;
            $dx[$key]['sm'] = isset($dx[$key]['sm']) ?($dx[$key]['sm'] + (1 - $da)) : (1 - $da);
            $dx[$key]['dan'] = isset($dx[$key]['dan']) ?($dx[$key]['dan'] + $dan) : $dan;
            $dx[$key]['shuang'] = isset($dx[$key]['shuang']) ?($dx[$key]['shuang'] + (1 - $dan)) : (1 - $dan);
        }
        return $dx;
    }
}
