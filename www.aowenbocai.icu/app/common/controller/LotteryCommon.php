<?php
namespace app\common\controller;

use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\lottery\controller\Pk10Buy;
use app\lottery\model\KsCommon;
use app\lottery\model\Pc28Common;
use app\lottery\model\Pk10Common;
use app\lottery\model\SscCommon;
use app\lottery\model\SyxwCommon;
use think\Controller;

class LotteryCommon extends Controller
{
    /**
     * 获取彩种对应模型
     */
    public static function getModel($ext, $type)
    {
        if ($type == 'join' || $type == 'Join') {
            $model =   'app\lottery\model\\' . $ext . '\Plugin'.ucfirst($ext).'Join';
        }
        if ($type == 'buy' || $type == 'Buy') {
            $model =   'app\lottery\model\\' . $ext . '\Plugin'.ucfirst($ext).'Buy';
        }
        if ($type == 'code' || $type == 'Code') {
            $model =   'app\lottery\model\\' . $ext . '\Plugin'.ucfirst($ext).'Code';
        }
        if ($type == 'expect' || $type == 'Expect') {
            $model =   'app\lottery\model\\' . $ext . '\Plugin'.ucfirst($ext).'Expect';
        }
        return  new $model;
    }

    /**
     * 获取彩种配置模型
     */
    public static function getSetting($name = '')
    {
        $isSSc = mb_substr($name, -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
        $isKs = mb_substr($name, -2, 2, 'utf-8') == 'ks' ? 1 : 0;
        $is28 = mb_substr($name, -2, 2, 'utf-8') == '28' ? 1 : 0;
        $is10 = mb_substr($name, -2, 2, 'utf-8') == '10' ? 1 : 0;
        if ($isSSc) {
            $Setting = (new \app\lottery\model\Ssc());
        } elseif ($isKs) {
            $Setting = (new \app\lottery\model\Ks());
        } elseif ($is28) {
            $Setting = (new \app\lottery\model\Pc28());
        } elseif ($is10) {
            $Setting = (new \app\lottery\model\Pk10());
        } else {
            $Setting = (new \app\lottery\model\Syxw);
        }
        return $Setting;
    }

    /**
     * 获取彩种期号对应的方法
     */
    public static function getCommon($name = '')
    {
        $isSSc = mb_substr($name, -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
        $isKs = mb_substr($name, -2, 2, 'utf-8') == 'ks' ? 1 : 0;
        $is28 = mb_substr($name, -2, 2, 'utf-8') == '28' ? 1 : 0;
        $is10 = mb_substr($name, -2, 2, 'utf-8') == '10' ? 1 : 0;
        if ($isSSc) {
            return (new SscCommon());
        } elseif ($isKs) {
            return (new KsCommon());
        } elseif ($is28) {
            return (new Pc28Common());
        } elseif ($is10) {
            return (new Pk10Common());
        } else {
            return (new SyxwCommon());
        }
    }

    /**
     * 获取彩种期号对应的方法
     */
    public static function getCpType($name = '')
    {
        if (!$name) return 0;
        $isSSc = mb_substr($name, -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
        $isKs = mb_substr($name, -2, 2, 'utf-8') == 'ks' ? 1 : 0;
        $is28 = mb_substr($name, -2, 2, 'utf-8') == '28' ? 1 : 0;
        $is10 = mb_substr($name, -2, 2, 'utf-8') == '10' ? 1 : 0;
        $is11 = mb_substr($name, -2, 2, 'utf-8') == '11' ? 1 : 0;
        if ($is10) return 'pk10';
        if ($isSSc) return 'ssc';
        if ($isKs) return 'ks';
        if ($is28) return 'pc28';
        if ($is11) return 'syxw';
       return 0;
    }

    /**
     * 获取彩种期号对应的方法
     */
    public static function getSettingValue($name = '', $type = '')
    {
        $name = strtolower($name);
        // 对于所有PK10类彩种（以10结尾的彩种），都使用 name_type 格式
        // 只有纯 pk10 使用 type 格式
        if ($name == 'pk10') {
            $config = $type;
        } else {
            $config = $name . '_' . $type;
        }
        return $config;
    }

    /**
     * 获取大类型的所有彩种  时时彩， 11选5
     * @return array
     */
    public static function getListByType($type = 'ssc')
    {
        $list = [];
        $model = new Ext();
        if ($type == 'ssc') {
            $list = $model->where('name', 'like', '%ssc')->column('name');
        } elseif ($type == 'syxw') {
            $list = $model->where('name', 'like', '%11')->column('name');
        } elseif ($type == 'ks') {
            $list = $model->where('name', 'like', '%ks')->column('name');
        } elseif ($type == 'pc28') {
            $list = $model->where('name', 'like', '%28')->column('name');
        } elseif ($type == 'pk10') {
            $list = $model->where('name', 'like', '%10')->column('name');
        }
        return $list;
    }

    public static function setUrl($name)
    {
        $cp_type = LotteryCommon::getCpType($name);
        if (!$cp_type) return [$name, $name];
        if($cp_type == 'ssc'){
            return  $name = ['Ssc/index/name/'.ltrim($name, '/'), 'history/index/name/ssc_'.ltrim($name, '/'),$name,'ssc'];
        }
        if($cp_type == 'syxw'){
            return  $name = ['Syxw/index/name/'.ltrim($name, '/'), 'history/index/name/syxw_'.ltrim($name, '/'),$name,'syxw'];
        }
        if($cp_type == 'ks'){
            return  $name = ['Ks/index/name/'.ltrim($name, '/'), 'history/index/name/ks_'.ltrim($name, '/'),$name,'ks'];
        }
        if($cp_type == 'pc28'){
            return  $name = ['Pc28/index/name/'.ltrim($name, '/'), 'history/index/name/pc28_'.ltrim($name, '/'), $name, 'pc28'];
        }
        if($cp_type == 'pk10'){
            return  $name = ['pk10/index/name/'.ltrim($name, '/'), 'history/index/name/pk10_'.ltrim($name, '/'), $name, 'pk10'];
        }
    }

    /**
     * 获取所有彩种分一级二级
     */
    public static function getNavLottery()
    {
        $res = [];
        $ext_model = new ExtShowList();
        $lottery =  $ext_model->where('type', 1)->where('status', 0)->select();
        $lottery = $lottery->toArray();
        foreach ($lottery as $value) {
            $value['name'] = trim($value['name'], '/');
            $isSSc = mb_substr($value['name'], -3, 3, 'utf-8') == 'ssc' ? 1 : 0;
            $isKs = mb_substr($value['name'], -2, 2, 'utf-8') == 'ks' ? 1 : 0;
            $isSyxw = mb_substr($value['name'], -2, 2, 'utf-8') == '11' ? 1 : 0;
            $is28 = mb_substr($value['name'], -2, 2, 'utf-8') == '28' ? 1 : 0;
            $is_pk10 = mb_substr($value['name'], -2, 2, 'utf-8') == '10' ? 1 : 0;
            $show_data = [
                'id' => $value['id'],
                'name' => $value['name'],
                'title' => $value['title'],
            ];
            if ($isSSc) {
                if (!isset($res[0])) {
                    $res[0] = [
                        'title' => '时时彩',
                        'name' => 'ssc',
                        'data' => []
                    ];
                }
                array_push($res[0]['data'], $show_data);
            }
            if ($isSyxw) {
                if (!isset($res[1])) {
                    $res[1] = [
                        'title' => '11选5',
                        'name' => 'syxw',
                        'data' => []
                    ];
                }
                array_push($res[1]['data'], $show_data);
            }
            if ($isKs) {
                if (!isset($res[2])) {
                    $res[2] = [
                        'title' => '快3',
                        'name' => 'ks',
                        'data' => []
                    ];
                }
                array_push($res[2]['data'], $show_data);
            }
            if ($is28) {
                if (!isset($res[3])) {
                    $res[3] = [
                        'title' => 'PC蛋蛋',
                        'name' => 'pc28',
                        'data' => []
                    ];
                }
                array_push($res[3]['data'], $show_data);
            }
            if ($is_pk10) {
                if (!isset($res[4])) {
                    $res[4] = [
                        'title' => '赛车飞艇',
                        'name' => 'pk10',
                        'data' => []
                    ];
                }
                array_push($res[4]['data'], $show_data);
            }
        }
        return $res;
    }
}
