<?php
namespace core\base;

abstract class GridGameBase
{
    /**
     * 投注
     * @param mixed $plan 下注的选项
     * @param int $money 追加的金额
     * @return array
     */
    abstract public function bet($plan, $money);
    
    /**
     * 获取初始化信息
     * @return array
     */
    abstract public function getInfo();

    /**
     * 获取当前期的开奖情况
     * @return array
     */
    abstract public function getStatus();
    
}
