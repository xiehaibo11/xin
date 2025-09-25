<?php
namespace app\fa\controller;

use app\fa\model\Buy;
use app\fa\model\Code;
use think\Controller;

class Award extends Controller
{
    /**
     * 开奖号码记录数据库  -- 一天 1920期  45秒一期
     */
    public function getAwards()
    {
        $code_model = new Code();
        $dq_expect = (new Buy())->checkExpect();//获取当前开奖
        if (!$dq_expect['allow_betting']) {
            $code_expect = $dq_expect['expect'];
        }
        //补期 - 10期
        $back_expect = (new Buy())->backExpect(10);
        foreach ($back_expect as $value) {
            $has_expect = $code_model->where('expect', $value)->find();
            if (empty($has_expect)) {
                $num = rand(1,11);
                $data['code'] = $num;
                $data['expect'] = $value;
                $res = $code_model->add($data);
            }
        }
        $has_expect = $code_model->where('expect', $code_expect)->find();
        if (empty($has_expect)) {
            $num = rand(1,11);
            $data['code'] = $num;
            $data['expect'] = $code_expect;
            $res = $code_model->add($data);
            if (!$res['code']) {
                return 0;
            }
        }
        return 1;
    }


}
