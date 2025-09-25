<?php
namespace app\lottery\controller;

use app\admin\controller\Base;
use app\lottery\model\Syxw as ASetting;
use think\Request;


class Gd11 extends Base
{
    public function index()
    {
        $Setting = new ASetting;
        $module = strtolower(Request()->controller());
        $selected = $Setting->getValue($module.'_selected');
        $list = json_decode($Setting->getValue($module.'_bouns'), true);
        if(empty($selected)){
            $selected = 1;
            $Setting->setValue($module.'_selected', 1);
        }
        return $this->fetch('', ['title' => '设置', 'list' => $list,'selected' => $selected]);
    }

    /**设置 */
    public function edit()
    {
        if(request()->IsPost()){
            $postData = request()->post();
            $data = $postData['data'];
            $module = strtolower(Request()->controller());
            $setting = new ASetting;
            $before = json_decode($setting->getValue($module.'_bouns'), true);
            foreach ($before as $key => &$value) {
                $value['gain'] = $data[$key]['gain'];
                $value['isOpen'] = $data[$key]['isOpen'] ? 1 : 0;
            }
            $setting->setValue($module.'_selected', $postData['selected']);
            $res = $setting->setValue($module.'_bouns', json_encode($before));
            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
    }
 
    public function site()
    {
        if(request()->isPost()){
            $data = request()->post();
            if(!isset($data['delay'])){
                return json(['err' => 2 , 'msg' => '参数错误']);
            }
            $module = strtolower(Request()->controller());
            $setting = new ASetting;
            $before = json_decode($setting->getValue($module.'_setting'), true);
            $before['delay'] = $data['delay'];
            $res = $setting->setValue($module.'_setting', json_encode($before));
            if($res){
                return json(['err' => 0, 'msg' => '设置成功']);
            }
            return json(['err' => 1, 'msg' => '设置失败']);
        }
        
        $module = strtolower(Request()->controller());
        $list = json_decode((new ASetting)->getValue($module.'_setting'), true);
        return $this->fetch('', ['title' => '设置', 'list' => $list]);
    }
}
