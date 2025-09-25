<?php
namespace app\zhuawawa\controller;

use app\admin\controller\Base;
use app\zhuawawa\model\PluginZhuawawa As Zhuawawa;
use app\zhuawawa\model\PluginZhuawawaSetting As zSetting;
use app\admin\model\ExtShowList;

class Admin extends Base
{
    public function index($words = '')
    {
        $this->redirect('/zhuawawa/Setting');
        // $this->fetch('index', ['title' => '管理配置', 'query' => ['words' =>$words]]);
    }
    public function record()
    {
        $data = request()->get();
        $his = new Zhuawawa;
        $list = $his->getList();
        $page = $list->render();
        $extList = (new ExtShowList)->getRecodeExt();
        $setting = new zSetting;
        $giftList = $setting->getGift();
        if(isset($data['name']) && $data['name'] != ''){
            $name = $setting->get($data['name']);
            $this->assign("name",$name);
        }
        $this->assign("extList",$extList);
        $this->assign("giftList",$giftList);
        $this->assign("page",$page);
        $this->assign("list",$list->toArray());
        return $this->fetch('betting', ['title' => '抓娃娃记录', "query" => $data]);
    }

    public function deleteWa()
    {
        $data = request()->get();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new Zhuawawa)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new Zhuawawa)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除成功');
    }
}