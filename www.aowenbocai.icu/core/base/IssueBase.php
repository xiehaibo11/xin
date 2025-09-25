<?php
namespace core\base;

abstract class IssueBase
{
    private $config = [
        'beagin_time' => '02:00:00',
        'end_time' => '22:00:00',
        'refresh_time' => 30 //单位“秒”
    ];

    /**
    * 读取配置
    * @param $config array 配置项
    */
    abstract public function __construct($config);
    
    /**
    * 时间转期号
    * @param $time int 时间戳, 值为空时取当前期
    * @return string 期号
    */
    abstract public function timeToIssue($time = '');
    
    /**
    * 期号转时间
    * @param $issue string 期号, 值为空时取当前期的下一期时间
    * @return int 时间
    */
    abstract public function issueToTime($issue = '');
    
    /**
    * 当前期结束剩余时间
    * @param  int 剩余时间
    */
    public function endTime()
    {
        return $this->IssueToTime() - now();
    }
}
