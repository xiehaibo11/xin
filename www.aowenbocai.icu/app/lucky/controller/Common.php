<?php
namespace app\lucky\controller;

use app\lucky\model\PluginLucky;
use app\lucky\model\PluginLuckyAward as Award;
use app\lucky\model\PluginLuckySetting;

class Common
{

     /**获取转盘结果 */
    function openAward($sign)
     {
         //计算随机数
         $max = (new PluginLucky)->max('sign');
         $award = (new Award());
        $list = $award->where('id', 'neq', '2')->where('type', 0)->select();
        $total_sp = 0;
        $total_money = 0;
        $award_array = [];
        foreach ($list as $key => $value) {
            $teshu = $award->where('type', 1)->where('sort', $value['id'])->where('plan', '["'. $sign .'","'. $sign .'"]')->find();
            $total_sp += intval($value['sp']);
            $teshu_sp = ($teshu['sp']/100) * $value['sp'];
            $total_money += intval($value['sp'] - $teshu_sp) * $value['multiple'] + $teshu_sp * $value['multiple'] * $teshu['multiple'];
            array_push($award_array,[
                'sp' => $teshu_sp,
                'id' => $value['id'],
                'teshu' => $teshu['id']
            ]);
            array_push($award_array,[
                'sp' => $value['sp'] - $teshu_sp,
                'id' => $value['id'],
                'teshu' => 0
            ]);
        }
        $nomal_teshu = $award->where('type', 1)->where('sort', 2)->where('plan', '["'. $sign .'","'. $sign .'"]')->find();
        $nomal_teshu_sp = $nomal_teshu['sp']/100;
        $sp = ($total_money - $total_sp)/(0.1 * (100 - $nomal_teshu['sp'])/100 + $nomal_teshu_sp * $nomal_teshu['multiple']);
        $profit = PluginLuckySetting::where('name', 'profit')->find();
        $sp += $sp * (100 - $profit['value'])/100 * 2;
         $total_sp += floor($sp);
         array_push($award_array,[
             'sp' => floor($sp * (100 - $nomal_teshu['sp'])/100),
             'id' => 2,
             'teshu' => 0
         ]);
         array_push($award_array,[
             'sp' => floor($sp * $nomal_teshu['sp']/100),
             'id' => 2,
             'teshu' => $nomal_teshu['id']
         ]);
         $rand = mt_rand(1, $total_sp);

         /**查询是否有命中率在这范围的中奖的普通规则 */
         $start = 0;
         $sp_award = [];//中奖号码
         $mu_award = [];//中奖号码
         foreach ($award_array as $value) {
            $end = $start + $value['sp'];
            if($rand > $start && $rand <= $end){
                $sp_award['id'] = $value['id'];
                $sp_award['teshu'] = $value['teshu'];
                if ($value['teshu']) {
                    $teshu = $award->find($value['teshu']);
                    $mu_award['lucky'] = $teshu['multiple'];
                } else {
                    $mu_award['lucky'] = 0;
                }
                $common_award = $award->find($value['id']);
                $mu_award['common'] = $common_award['multiple'];
                break;
            }
            $start += $value['sp'];
         }
         $common = $this->getCommon($sp_award, $sign, $max);
         /**************************/
         $sp_award['lucky'] = $common['lucky'];
         /********************* */
         return ['common' => $common['common'], 'lucky' => $common['lucky'], 'id' => $sp_award['id'], 'teshu' => $sp_award['teshu'], 'sp_award' =>  $mu_award];
     }
 
     private function randNum($num,$max,$common)
     {
         for ($i=0; $i < $num; $i++) { 
             $rand_num = mt_rand(1,$max);
             if(in_array($rand_num, $common)){
                 $rand_num = $this->mtRand($rand_num, $common, $max);
             }
             array_push($common, $rand_num);
         }
         return $common;
     }
 
     private function mtRand($num, $array, $max)
     {
         $rand_num = mt_rand(1,$max);
         if(in_array($rand_num, $array)){
             $rand_num = $this->mtRand($rand_num, $array, $max);
         }
         return $rand_num;
     }

    private function getCommon($data, $sign, $max)
    {
        $lucky = '';
        $plan_info = (new Award)->find($data['id']);
        $plan = json_decode($plan_info['plan'], true);
        if ($data['teshu']) {
            $lucky = $sign;
        } else {
            $lucky = $this->randNum(1, $max, [$sign])[1];
        }
        $num = 0;
        $common = [];
        foreach ($plan as $v) {
            if ($v == 0) {
                $num += 1;
            } else {
                array_push($common, $v);
            }
        }
        $common = $this->randNum($num, $max, $common);
        return ['common' => $common, 'lucky' => $lucky];
    }

}