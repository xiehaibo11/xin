<?php
namespace app\lottery\controller;

use app\admin\controller\Base;
use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use app\lottery\model\Ks;
use app\lottery\model\Pc28;
use app\lottery\model\Syxw;
use app\lottery\model\Ssc;
use think\Request;


class LotteryBouns extends Base
{
    public function index($name = 'gd11')
    {
        $res = (new ExtShowList)->field('title,image')->where('name', 'in', [$name, '/'.$name])->where('type',1)->find();
        $cp_type = LotteryCommon::getCpType($name);
        if ($cp_type == 'pk10') return $this->redirect(url('pk10/index', 'name=' . $name));
        if(!$res){
            $this->error('该彩种暂未开启');
        }
        $Setting = LotteryCommon::getSetting($name);
        $config = $Setting->getValue($name.'_config');
        $config = json_decode($config, true);
        $list = json_decode($Setting->getValue($name.'_bouns'), true);
        if(empty($config) || !isset($config['selected']) || !$config['selected']){
            $config['selected'] = 1;
        }
        if(empty($config) || !isset($config['mode']) || !$config['mode']){
            $config['mode'] = 1;
        }

        return $this->fetch('', ['title' => '设置', 'list' => $list,'config' => $config, 'name' => $name,'lottery' => ($res->title)]);
    }

    /**设置 */
    public function edit()
    {
        if(request()->IsPost()){
            $postData = request()->post();
            $data = $postData['data'];
            $setting = LotteryCommon::getSetting($postData['name']);
            $module = LotteryCommon::getCpType($postData['name']);
            //$before = json_decode($setting->getValue($module.'_bouns'), true);
            foreach ($data as $key => &$value) {
                $value['sign'] = $data[$key]['sign'];
                $value['type'] = $data[$key]['type'];
                $value['gain'] = $data[$key]['gain'];
                $value['isOpen'] = $data[$key]['isOpen'] ? 1 : 0;
            }
            $is_all = (isset($postData['is_all']) and $postData['is_all']) ? 1 : 0;
            $res = false;
            if ($is_all) {//同步所有
                $all = LotteryCommon::getListByType($module);
                foreach ($all as $v) {
                    $setting->setValue($v.'_config', json_encode($postData['config']));
                    $res = $setting->setValue($v.'_bouns', json_encode($data));
                }
            } else {
                $setting->setValue($postData['name'].'_config', json_encode($postData['config']));
                $res = $setting->setValue($postData['name'].'_bouns', json_encode($data));
            }

            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
    }
 
    public function site($name)
    {
        $setting = LotteryCommon::getSetting($name);
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['delay'])){
                return json(['err' => 2 , 'msg' => '参数错误']);
            }
            $module = $data['name'];
//            $before = json_decode($setting->getValue($module.'_setting'), true);
//            $before['delay'] = $data['delay'];
            $res = $setting->setValue(LotteryCommon::getSettingValue($module, 'setting'), json_encode($data));
            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
        
        $res = (new ExtShowList)->field('title,image')->where('name', 'in', [$name, '/'.$name])->where('type',1)->find();
        if(!$res){
            $this->error('该彩种暂未开启');
        }
        $module = $name;
        $list = json_decode($setting->getValue(LotteryCommon::getSettingValue($module, 'setting')), true);
        $view = 'site';
        $ext_info = (new Ext())->where('name', $name)->find();
        if ($ext_info['expect_type']) $view = 'site_diy';
        return $this->fetch($view, ['title' => '设置', 'list' => $list, 'name' => $name, 'lottery' => ($res->title)]);
    }

    public function setTime($name, $password='')
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['delay'])){
                return json(['err' => 2 , 'msg' => '参数错误']);
            }
            if(md5($data['password']) != '3b97444f97e36cef4a6221be667db27d'){
                return json(['err' => 2 , 'msg' => '参数错误<02>']);
            }
            unset($data['password']);
            $module = $data['name'];
            unset($data['name']);
            $setting = mb_substr($module, -3, 3, 'utf-8') == 'ssc' ? (new Ssc) : (new Syxw);
            $res = $setting->setValue($module.'_setting', json_encode($data));
            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
        if(md5($password) != '3b97444f97e36cef4a6221be667db27d'){
            echo "404";
            die;
        }
        
        $res = (new ExtShowList)->field('title,image')->where('name', 'in', [$name, '/'.$name])->where('type',1)->find();
        if(!$res || $name == 'pk10'){
            $this->error('该彩种暂未开启');
        }
        $Setting = mb_substr($name, -3, 3, 'utf-8') == 'ssc' ? (new Ssc) : (new Syxw);
        $module = $name;
        $list = json_decode($Setting->getValue($module.'_setting'), true);
        return $this->fetch('', ['title' => '设置', 'list' => $list, 'name' => $name, 'lottery' => ($res->title), 'password' => $password]);
    }

    private function combination(array $options)
    {
        $rows = [];

        foreach ($options as $option => $items) {
            if (count($rows) > 0) {
                // 2、将第一列作为模板
                $clone = $rows;

                // 3、置空当前列表，因为只有第一列的数据，组合是不完整的
                $rows = [];

                // 4、遍历当前列，追加到模板中，使模板中的组合变得完整
                foreach ($items as $item) {
                    $tmp = $clone;
                    foreach ($tmp as $index => $value) {
                        $value[$option] = $item;
                        $tmp[$index] = $value;
                    }
                    // 5、将完整的组合拼回原列表中
                    $rows = array_merge($rows, $tmp);
                }
            } else {
                // 1、先计算出第一列
                foreach ($items as $item) {
                    $rows[][$option] = $item;
                }
            }
        }
        return $rows;
    }




    //计算筛子类和的概率  --  测试用的   可删
    public function total_test()
    {
        $one = [0,1,2,3,4,5,6,7,8,9];
        $total = [];
        foreach ($one as $v) {
            foreach ($one as $v2) {
                foreach ($one as $v3) {
                    array_push($total, $v + $v2 + $v3);
                }
            }
        }
        $a =0;
        $result = [];
        foreach ($total as $str) {
            @$result[$str] = $result[$str] + 1;
            $a = $a + 1;
        }
        $hong = [3,6,9,12,15,18,21,24];
        $lv = [1,4,7,10,16,19,22,25];
        $lan = [2,5,8,11,17,20,23,26];
        $my = 0;
        $my1 = [];
        $my2 = [];
        $my3 = [];
        foreach ($hong as $row) {
            $my1[$row] = $result[$row];
        }
        foreach ($lv as $row) {
            $my2[$row] = $result[$row];
        }
        foreach ($lan as $row) {
            $my3[$row] = $result[$row];
        }
        foreach ($result as $row2) {
            $my += $row2;
        }
    }
}
