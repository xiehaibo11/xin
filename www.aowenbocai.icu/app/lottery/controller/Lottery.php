<?php
namespace app\lottery\controller;

use app\admin\model\Ext;
use app\common\controller\LotteryCommon;
use app\lottery\model\Ks;
use app\lottery\model\Pc28;
use think\Controller;
use app\lottery\model\Syxw;
use app\lottery\model\Ssc;
use app\lottery\model\Pk10;

class Lottery extends Controller
{
    
      private  function get_url($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);  //设置访问的url地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
    $result =  curl_exec($ch);
    curl_close ($ch);
    return $result;
}
    /**获取配置项及时间期号 */
    public function getExpect($name = '', $no_delay = 0)
    {
        $ext_info = Ext::where('name', $name)->find();
        if ($ext_info['expect_type']) return $this->accumulate($name, '', $no_delay);
        if($name == "ssc"){
            return  $this->cqssc();
        }
        /**彩种配置 */
     
        $model = LotteryCommon::getSetting($name);
        
        $info = json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        if (!$info) {
            return $this->error('彩种配置不完全');
        }
        $now_timestamp = time();//当前日期和时间时间戳
        $expect_date = date('Ymd');
        $now_day = strtotime($expect_date);//当前日期时间戳

        /**根据配置文件设置时间 */
        $delay_time = $no_delay ? 0 : $info['delay'];/**延时时间 正数为提前，负数为推迟 */
        $today_timestamp = $now_timestamp - $now_day;//当前时间与当日时间差
        $start_time = explode(':', $info['startTime']);
        $end_time = explode(':', $info['endTime']);
        $ten_timestamp = floatval($info['timelong']) * 60;//间隔时间
        $timestamp_start = $start_time[0] * 60 * 60 + $start_time[1] * 60 + $ten_timestamp;//每期第一期开奖时间
        $timestamp_end = $end_time[0] * 60 * 60 + $end_time[1] * 60;//每期结束时间
        $is_cross_day = $timestamp_start > $timestamp_end ? 1 : 0;//是否跨天
        $timestamp_start -= $delay_time;//延时
        $timestamp_end -= $delay_time;//延时

        if ($today_timestamp >= $timestamp_end || $today_timestamp < $timestamp_start) {//计算第一期开奖剩余时间
            $expect = sprintf("%0" . strlen((string)$info['startIssue']) . "d",1);
            if ($today_timestamp >= $timestamp_end and !$is_cross_day){
                $yu_timestamp = 86400 - $today_timestamp + $timestamp_start;
                $expect_date = date('Ymd',strtotime('+1 day'));
            } else {
                $yu_timestamp = $timestamp_start - $today_timestamp;
            }
        }
        if (!$is_cross_day) {
            if ($today_timestamp >= $timestamp_start && $today_timestamp < $timestamp_end) { //计算销售时间内
                $my_timestamp = $today_timestamp - $timestamp_start;
                $yu_timestamp = $ten_timestamp - $my_timestamp % $ten_timestamp;
                $expect =  sprintf("%0" . strlen((string)$info['startIssue']) . "d",ceil(($my_timestamp + 0.01) / $ten_timestamp) + 1);
            }
        } else {
            if ($today_timestamp >= $timestamp_start) {//今天的开始期数
                $my_timestamp = $today_timestamp - $timestamp_start;
                $yu_timestamp = $ten_timestamp - $my_timestamp % $ten_timestamp;
                $expect =  sprintf("%0" . strlen((string)$info['startIssue']) . "d",ceil(($my_timestamp + 0.01) / $ten_timestamp) + 1);
            }
            if ($today_timestamp < $timestamp_end) {//第二天的期数
                //计算出0点的最后一期以及最后一期的剩余时间
                $my_timestamp_0 =  86400 - $timestamp_start;
                $yu_timestamp_0 = $my_timestamp_0 % $ten_timestamp;
                $expect_0 =  ceil($my_timestamp_0 / $ten_timestamp);
                $expect_date = date('Ymd',strtotime('-1 day'));
                $yu_timestamp = $ten_timestamp - ($today_timestamp + $yu_timestamp_0) % $ten_timestamp;
                $expect =  sprintf("%0" . strlen((string)$info['startIssue']) . "d",ceil(($today_timestamp + $yu_timestamp_0 + 0.01) / $ten_timestamp) + $expect_0);
            }
        }

        $today = date('Ymd');
        $last = ($expect_date.$expect) - 1;
        if(intval($expect) == 1){
            $last = $expect_date == $today ? (date('Ymd', strtotime($expect_date) - 3600*24)).$info['startIssue'] : $today.$info['startIssue'];
        }
        if($name=='ynssc')
        {

           $url='https://api.api68.com/QuanGuoCai/getLotteryInfoList.do?lotCode=10041';

        $data=json_decode($this->get_url($url),true);
        if($data && isset($data['result']['data']) && is_array($data['result']['data']) && !empty($data['result']['data'])) {
            $result=$data['result']['data'];
            $last = isset($result[0]['preDrawIssue']) ? $result[0]['preDrawIssue'] : $last;
        }
        if(strtotime('21:00')>strtotime(date('H:i',time())))
        {
        $yu_timestamp=strtotime(date('Y-m-d 21:00:00'))-time();
         }
        else $yu_timestamp=strtotime(date('Y-m-d 21:00:00',time()+86400))-time();

        }
        
        
         if($name=='jlssc')
        {

           $url='https://api.api68.com/QuanGuoCai/getLotteryInfoList.do?lotCode=10043';

         $data=json_decode($this->get_url($url),true);
         if($data && isset($data['result']['data']) && is_array($data['result']['data']) && !empty($data['result']['data'])) {
             $result=$data['result']['data'];
             if(isset($result[0]['preDrawIssue'])) {
                 $last='20'.$result[0]['preDrawIssue'];
             }
         }
         if(strtotime('21:00')>strtotime(date('H:i',time())))
         {
         $yu_timestamp=strtotime(date('Y-m-d 21:00:00'))-time();
         }
         else $yu_timestamp=strtotime(date('Y-m-d 21:00:00',time()+86400))-time();

        }

        $return = ['down_time' => $yu_timestamp, 'expect' => $expect_date.$expect, 'sort_expect' => $expect, 'lastIssue' => $last, 'now' => date("Y-m-d H:i:s")];
        $new = array_merge($info, $return);
        return ['err' => 0, 'data' => $new];
    }

    /** 计算期号 - 期数累加的彩票类型*/
    public function accumulate($name , $issue = '', $no_delay = '')
    {
//        if($name == "jnd28"){
//            return  $this->jnd28($name);
//        }
        $model = LotteryCommon::getSetting($name);
        $setting = json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')), true);
        $issue = $issue ? $issue : $this->accumulateTimeToIssue($name, '', $setting);
        $delay_time = $no_delay ? 0 : $setting['delay'];
        $data = [
            'expect' => $issue,
            'server_time' => time() * 1000,
            'end_time' => $this->issueToTime($name, $issue) * 1000
        ];
        $data['down_time'] = ($data['end_time'] - $data['server_time']) / 1000;

        if($data['down_time'] <= 0){
            return $this->accumulate($name, $issue + 1, $no_delay);
        }
        $data['now'] = date("Y-m-d H:i:s");
        $data['lastIssue'] = $issue - 1;
        $data['sort_expect'] = $issue;
        $new = array_merge($data, $setting);
        return ['err' => 0, 'data' => $new];
    }

    /**时时彩获取配置项及时间期号 */
    public function cqssc($no_delay = 0)
    {
        /**彩种配置 */
        $info = json_decode((new Ssc)->getValue('ssc_setting'),true);

        /**根据配置文件设置时间 */
        $delay_time = $no_delay ? 0 : $info['delay'];/**延时时间 正数为提前，负数为推迟 */
        $now_timestamp = time();//当前日期和时间时间戳
        $now_day = strtotime(date('Y-m-d', time()));//当前日期时间戳
        $expect_date = date('Ymd');
        $today_timestamp = $now_timestamp - $now_day;//当前的时间戳
        $ten_timestamp = 1200;//20分钟的时间戳
        $timestamp_1 = 10 * 60 + $ten_timestamp - $delay_time;//今天00:30的时间戳
        $timestamp_2 = 3 * 60 * 60 + 10 * 60 - $delay_time;//今天03:10的时间戳
        $timestamp_3 = 7 * 60 * 60 + 10 * 60 + $ten_timestamp - $delay_time;//今天07:30的时间戳
        $timestamp_4 = 23 * 60 * 60 + 50 * 60 - $delay_time;//今天23:50的时间戳
        if ($today_timestamp >= 0 && $today_timestamp < $timestamp_1) { // 计算 0:0 - 00:30
            $yu_timestamp = $timestamp_1 - $today_timestamp;
            $expect = '01';
        }
        if ($today_timestamp >= $timestamp_4) { // 计算23:50 - 0:0
            $yu_timestamp = 86400 - $today_timestamp + $timestamp_1;
            $expect_date = date('Ymd',strtotime('+1 day'));
            $expect = '01';
        }
        if ($today_timestamp >= $timestamp_2 and $today_timestamp < $timestamp_3) { // 计算03:10 - 07:30
            $yu_timestamp = $timestamp_3 - $today_timestamp;
            $expect = '10';
        }
        if ($today_timestamp < $timestamp_2 and $today_timestamp >= $timestamp_1) { //计算 0:30 - 03:10
            $my_timestamp = $today_timestamp - $timestamp_1;
            $yu_timestamp = $ten_timestamp - ($my_timestamp % $ten_timestamp);
            $expect = sprintf("%02d", ceil($my_timestamp / $ten_timestamp) + 1);
        }
        if ($today_timestamp >= $timestamp_3 and $today_timestamp < $timestamp_4) { //计算 07:30 - 23:50
            $my_timestamp = $today_timestamp - $timestamp_3;
            $yu_timestamp = $ten_timestamp - ($my_timestamp % $ten_timestamp);
            $expect = sprintf("%02d", ceil($my_timestamp / $ten_timestamp) + 10);
        }  
        $nowExpect = $expect_date.$expect;
        $last = $nowExpect - 1;
        if(intval($expect) == 1){
            $last = (date('Ymd', strtotime($expect_date) - 3600*24)).$info['startIssue'];
        }
        $return = ['down_time' => $yu_timestamp, 'expect' => $nowExpect, 'sort_expect' => $expect, 'lastIssue' => $last];
        $new = array_merge($info, $return); 
        return ['err' => 0, 'data' => $new];
    }

    /**加拿大28 */
    public function jnd28($name, $no_delay = 0)
    {
        /**彩种配置 */
        $setting = json_decode((new Pc28())->getValue('jnd28_setting'),true);

        $delay_time = $no_delay ? 0 : $setting['delay'];/**延时时间 正数为提前，负数为推迟 */
        $now_timestamp = time();//当前日期和时间时间戳
        $now_day = strtotime(date('Y-m-d', time()));//当前日期时间戳
        $today_timestamp = $now_timestamp - $now_day;//当前的时间戳
        $time_long_timestamp = $setting['timelong'] * 60;//投注时长的时间戳
        $timestamp_1 = 2 * 60 - $delay_time;//今天00:02:00的时间戳  392
        $timestamp_2 = 21 * 60 * 60 + $time_long_timestamp - $delay_time;//今天21:03:30的时间戳
        $timestamp_3 = 7 * 60 * 60 + 10 * 60 + 90 - $delay_time;//今天20:01:30的时间戳
        return ['err' => 0 ];

    }

    /**
     * 期号转时间，根据期号计算对应的开奖时间
     * @param  string $issue 期号
     * @return int 时间戳
     */
    public function issueToTime($name = '', $issue)
    {
        $ext_info = Ext::where('name', $name)->find();
        if ($ext_info['expect_type']) return $this->accumulateIssueToTime($name, $issue);
        if ($name == 'ssc') {
            return $this->sscIssueToTime($issue);
        }
        $model = LotteryCommon::getSetting($name);
        $setting = json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')), true);
        $date =substr($issue, 0, 8);
        $sort = (int)substr($issue,8);//简短期数格式

        $start_time = explode(':', $setting['startTime']);
        $end_time = explode(':', $setting['endTime']);
        $ten_timestamp = floatval($setting['timelong']) * 60;//间隔时间
        $timestamp_start = $start_time[0] * 60 * 60 + $start_time[1] * 60 + $ten_timestamp;//每期第一期开奖时间
        $timestamp_end = $end_time[0] * 60 * 60 + $end_time[1] * 60;//每期结束时间
        $is_cross_day = $timestamp_start > $timestamp_end ? 1 : 0;//是否跨天
        if ($is_cross_day) {
            //计算出0点的最后一期
            $my_timestamp_0 =  86400 - $timestamp_start;
            $expect_0 =  ceil($my_timestamp_0 / $ten_timestamp);
            if ($sort > $expect_0) $date = date('Ymd', strtotime($date) + 3600*24);
        }

        $starttime = $date.$setting['startTime'];
        $endtime = strtotime($starttime) + $sort * $setting['timelong'] * 60 - $setting['delay'];
        return $endtime;
    }

    /**
     * 获取时时彩开奖时间 -- 期数获取时间
     * $expect  期数号码
     */
    public function sscIssueToTime($expect, $no_delay = 0)
    {
        $info = json_decode((new Ssc)->getValue('ssc_setting'),true);
        $date =substr($expect, 0, 8);
        $sort = (int)substr($expect,8);//简短期数格式
        $starttime = $date.$info['startTime'];
        $delay_time = $no_delay ? 0 : $info['delay'];/**延时时间 正数为提前，负数为推迟 */
        $endtime = strtotime($starttime) + $sort * $info['timelong'] * 60 - $delay_time;
        if ($sort == 1) { // 计算第一期开奖时间
            $endtime = strtotime($date) + 30 * 60 - $delay_time;
        }
        if ($sort < 10) { //计算 00:10 - 03:10
            $endtime = strtotime($date) + $sort * 20 * 60 + 10 * 60 - $delay_time;
        }
        if ($sort >= 10) { //计算 07:10-23:50
            $starttime = $date.'07:10:00';
            $endtime = strtotime($starttime) + ($sort - 9) * 20 * 60 - $delay_time;
        }
        return $endtime;
    }


    /**
     * 时间转期号，根据时间计算对应的期号  -- 累加型彩种
     * @param  string $time 时间
     * @return string
     */
    public function accumulateTimeToIssue($name = 'pk10', $time = null, $setting = null)
    {
        $time = !empty($time) ? strtotime($time) : time();
        $model = LotteryCommon::getSetting($name);
        $setting = !empty($setting) ? $setting : json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')), true);

        $d_s_time = date('Y-m-d ' . $setting['startTime'], $time); //每天开始销售时间
        $o_day = date('Y-m-d', strtotime($setting['pktime']));
        $n_day = date('Y-m-d', $time);
        $ex_day = (strtotime($n_day) - strtotime($o_day)) / (24 * 60 * 60) - 1; //相差天数
        $m_num = $ex_day * $setting['startIssue']; //相差期数
        $ex_min = $time - strtotime($d_s_time);
        $ex_time = $setting['timelong'] * 60;
        $ex_num = min(ceil($ex_min / $ex_time), $setting['startIssue']);
        $issue = $setting['pkstart'] + $m_num;
        if ($ex_num > 0) {
            $issue += $ex_num;
        }
        return $issue;
    }

    /**
     * 期号转时间，根据期号计算对应的开奖时间 -- 累加型彩种
     * @param  string $issue 期号
     * @return int 时间戳
     */
    public function accumulateIssueToTime($name = 'pk10', $issue = null)
    {
        $issue = $issue ?? $this->accumulateTimeToIssue($name);

        $model = LotteryCommon::getSetting($name);
        $setting = json_decode($model->getValue(LotteryCommon::getSettingValue($name, 'setting')), true);
        $ex_issue = $issue - $setting['pkstart'];
        $ex_day = floor($ex_issue / $setting['startIssue']);
        $n_ex_num = $ex_issue % $setting['startIssue'];

        if ($n_ex_num > 0) {
            $ex_day ++;
            $n_ex_num = $n_ex_num - $setting['startIssue'];
        }

        $l_time = strtotime($setting['pktime']) + $ex_day * 24 * 60 * 60;
        $time = $l_time + $n_ex_num * $setting['timelong'] * 60 - $setting['delay'];
        return $time;
    }

}
