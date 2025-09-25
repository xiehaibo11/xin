<?php
namespace core\base;

abstract class GuessingBase
{
    /**
     * 投注
     * @param mixed $plan 下注的选项
     * @param int $money 追加的金额
     * @return array
     */
    abstract public function bet($plan, $money);    
}
