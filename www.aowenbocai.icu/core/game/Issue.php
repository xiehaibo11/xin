<?php
namespace core\game;

use core\base\IssueBase;

class Issue extends IssueBase{
	private $config = [
        'begin_time' => '02:00:00',
        'end_time' => '22:00:00',
        'refresh_time' => 30 //单位“秒”
    ];
	
	/**
	* 读取配置
	* @prame $config array 配置项
	*/
	public function __construct($config = '') 
	{
		$this->begin_time = isset($config['begin_time']) ? $config['begin_time'] : ($this->config['begin_time']);//开始时间
		$this->end_time = isset($config['end_time']) ? $config['end_time'] : ($this->config['end_time']);//结束时间
		$this->refresh_time = isset($config['refresh_time']) ? $config['refresh_time'] : ($this->config['refresh_time']);//间隔时间
	}
	
	/**开始时间*/
	private function beginTimeToTime($time = '')
	{
		return strtotime(date("Y-m-d {$this->begin_time}"));
	}
	
	/**结束时间*/
	private function endTimeToTime($time = '')
	{
		return strtotime(date("Y-m-d {$this->end_time}"));
	}
	
	//一天总期数
	private function total($time = '')
	{
		return ceil(($this->endTimeToTime($time) - $this->beginTimeToTime($time)) / $this->refresh_time);
	}
	
	/**
	* 时间转期号(获取当前期数)
	* @prame $time int 时间戳
	* @return string 期号
	*/
	public function timeToIssue($time = '') 
	{
		$issue = $this->issueCode($time);
		if($issue['issue'] < 86400){
			$issue['issue'] = date('Ymd') . $issue['issue'];
		}
		return $issue;
	}
	
	//计算期号
	/**返回期号和当期开始时间 */
	public function issueCode($time = '')
	{
		$time = empty($time) ? time() : $time;
		$todayStart = $this->beginTimeToTime($time);
		if($time < $todayStart){
			$lastday = date('Ymd', strtotime("-1 day")) . $this->total($time);
			$start_diff = $todayStart - $time;
			return ['issue' => $lastday, 'pass_time' => -1, 'ure_time' => $start_diff];
		}
		$todayEnd = $this->endTimeToTime($time);
		if($time > $todayEnd){
			$todaylast = date('Ymd', $time) . $this->total($time);
			$nextday = strtotime(date('Ymd', strtotime("1 day")).$this->begin_time);
			$start_diff = $nextday - $time;
			return ['issue' => $todaylast, 'pass_time' => -1, 'ure_time' => $start_diff];
		}

		$diff = $time - ($this->beginTimeToTime($time));
		$pass_time = $diff % ($this->refresh_time);
		$ure_diff = $diff % $this->refresh_time;
		$expect = ceil($diff / ($this->refresh_time));
		$expect = $ure_diff == 0 ? ($expect + 1) : $expect;
		$issue = str_pad($expect, 4, '0', STR_PAD_LEFT);

		return ['issue' => $issue, 'pass_time' => $pass_time, 'ure_time' => 0];
	}

	/**
	* 期号转时间(该方法个人觉得可能有问题，用到在再确认)
	* @prame $issue string 期号
	* @return int 时间
	*/
	public function issueToTime($issue = '') 
	{
		if(is_numeric($issue)){
			$time = strtotime(substr($issue,0,8));
			$begin_time = $this->beginTimeToTime($time);
			$except = ltrim(substr($issue, -4), '0');
			if($except<$this->total($time) && $except>0){
				return $time = $begin_time + $except*($this->refresh_time);
			}
		}
		$except = $this->timeToIssue();
		return $this->issueToTime($except); 
	}

	/**
	* 当前期结束剩余时间
	*/
	public function endTime()
	{
		return $this->issueToTime() - time();
	}
}
