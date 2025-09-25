<?php
namespace app\common\model;

use think\Model;
use app\index\model\User;

class GameDzp extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * [获取次数]
     * @return array
     */
    static public function getCount($userid)
    {
        $dzp = self::_init($userid);
        return $dzp->count;
    }

    /**
     * [增加次数]
     * @return array
     */
    static public function addCount($userid, $num)
    {
        $dzp = self::_init($userid);
        $dzp->count += $num;
        $dzp->last_time = date("Y-m-d H:i:s");
        $dzp->save();
        return $dzp->count;
    }

    /**
     * [初始化]
     * @return init
     */
    static private function _init($userid)
    {
        $new = new self;
        $dzp = $new->get(function($query) use($userid) {
            $query->where('userid', $userid);
        });
        $setting = \core\Setting::get(['dzp_count']);
        if(!$dzp) {
            $new->userid = $userid;
            $new->count = intval($setting['dzp_count']);
            $new->last_time = date('Y-m-d H:i:s');
            $new->save();
            $dzp = $new;
        }
        return $dzp;
    }
}
