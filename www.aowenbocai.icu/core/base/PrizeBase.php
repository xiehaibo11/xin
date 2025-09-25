<?php
namespace core\base;

abstract class PrizeBase
{
    // private $config = [
    //     'code_rule' => [

    //     ]
    // ];

    // /**
    //  * 读取配置
    //  * @param array $config 配置项
    //  */
    // abstract public function __construc($config);

    /**
     * 投注内容配置，根据传入不同的参数判断奖金计算的方式
     * @param array $plan 投注的内容
     */
    abstract public function setPlan($plan);

    /**
     * 开奖内容配置
     * @param array $plan 开奖内容
     */
    abstract public function setCode($code);
    
    /**
     * 判断是否中奖
     * @return boolean
     */
    abstract public function isWin();

    /**
     * 奖金
     * @return float
     */
    abstract public function bonus();

    /**
     * 自动生成一个开奖号码
     * @return array 生成的开奖号码
     */
    abstract public function createCode($list, $max_sign, $sp, $length);
    
    abstract public function openAward($award, $list);
}
