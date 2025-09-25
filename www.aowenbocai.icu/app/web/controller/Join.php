<?php
namespace app\web\controller;

use app\index\model\ExtShowList;
use app\web\model\User;
use core\Setting;

class Join extends Base
{
    public function index()
    {
        $extShow = new ExtShowList;
        $gamenav =  $extShow->field('id,title')->where('type', 1)->where('status',0)->order('sort ASC')->select();
        $this->assign('gameInfo', $gamenav);
        /**读取发起人提成是否开启 , 彩金比例*/
        $system = (new Setting)->get(['isGain','lottery_unit','coin_lottery']);
        $this->assign('system', collection($system));
        return $this->fetch('', ['record' => $this->recordList()]);
    }

    /**获取战绩排行版 */
    public function recordList()
    {
        $list = (new User)->field('id,nickname,record')->order('record DESC')->limit(0,21)->select();
        $list = $list->append(['playNum'])->toArray();
        
        $setting = (new Setting)->get(['star_num']);
        $star_num = empty($setting) ? 10000000 : $setting['star_num'];
        foreach ($list as $key => &$value) {
            $localKey = mb_stripos($value['record'], '.');
            if($localKey){
                $value['record'] = mb_substr($value['record'], 0, $localKey + 3);
            }
            $allStar = intval($value['record'] / $star_num);
            $value['starNum'] = $allStar % 10;
            $MoonNum = intval($allStar / 10);
            $value['MoonNum'] = $MoonNum % 10;
            $sunNum = intval($MoonNum / 10);
            $value['sunNum'] = $sunNum % 10;
            $value['queen'] = intval($sunNum / 10);
            $value['nickname'] = mb_substr($value['nickname'], 0, 1, 'utf-8')."***".mb_substr($value['nickname'], -1, 1, 'utf-8');
        }
        return collection($list);
    }
}




