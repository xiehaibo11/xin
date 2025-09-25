<?php
namespace app\lottery\controller;

use app\admin\controller\Base;
use app\admin\model\Ext;
use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use app\lottery\model\Pk10 as ASetting;

class Pk10 extends base
{
    public function index($name = 'pk10')
    {
        $Setting = new ASetting;
        $res = (new ExtShowList())->field('title,image')->where('name', 'in', [$name, '/'.$name])->where('type',1)->find();
        if(!$res || mb_substr($name, -2, 2, 'utf-8') != '10'){
            $this->error('该彩种暂未开启');
        }

        $config = $Setting->getValue(LotteryCommon::getSettingValue($name, 'config'));
        $config = json_decode($config, true);
        $list = json_decode($Setting->getValue(LotteryCommon::getSettingValue($name, 'bouns')), true);
        return $this->fetch('index', ['title' => '设置','config' => $config, 'name' => $name,'lottery' => ($res->title), 'list' => $list]);
    }

    /**
     * 编辑奖金
     * @param  int $id 配置ID
     * @param  
     */
    public function edit()
    {
        $data = request()->post();
        $index = strtoupper($data['key']);
        foreach ($data['value'] as $key => $value) {
            if ($value['key'] == 'isOpen') {
                $arr[$value['key']] = intval($value['value']);
            } else {
                $arr[$value['key']] = $value['value'];
            }
        }
        $Setting = new ASetting;
        $list = json_decode($Setting->getValue(LotteryCommon::getSettingValue($data['name'], 'bouns')), true);
        if (!$list) $list = [];
        if(empty($list) || !array_key_exists($index, $list)){
            $list[$index] = $arr;
        }else{
            foreach ($list as $key => &$value) {
                $value = $key == $index ? $arr : $value;
            }
        }
        $is_all = (isset($data['is_all']) and $data['is_all']) ? 1 : 0;
        $res = false;
        if ($is_all) {//同步所有
            $all = LotteryCommon::getListByType(LotteryCommon::getCpType($data['name']));
            foreach ($all as $v) {
                $Setting->setValue(LotteryCommon::getSettingValue($v, 'config'), json_encode($data['config']));
                $res = $Setting->setValue(LotteryCommon::getSettingValue($v, 'bouns'), json_encode($list));
            }
        } else {
            $Setting->setValue(LotteryCommon::getSettingValue($data['name'], 'config'), json_encode($data['config']));
            $res = $Setting->setValue(LotteryCommon::getSettingValue($data['name'], 'bouns'), json_encode($list));
        }
        if($res){
            return json(['err' => 0, 'msg' => '设置成功']);
        }
        return json(['err' => 1, 'msg' => '设置失败']);

    }
    
    public function site()
    {
        if(request()->isPost()){
            $data = request()->post();
            $res = (new ASetting)->setValue('setting', json_encode($data));
            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
        
        $list = json_decode((new ASetting)->getValue('setting'), true);
        return $this->fetch('',['title' => 'pk10设置', 'list' => $list]);
    }
}
