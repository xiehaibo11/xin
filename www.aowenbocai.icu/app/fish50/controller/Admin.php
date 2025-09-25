<?php
namespace app\fish50\controller;

use app\admin\controller\Base;
use app\admin\model\User;
use app\common\model\UserBag;
use app\fish50\model\PluginFishHis as His;
use app\fish50\model\PluginFishData as FishData;
use app\admin\model\ExtShowList;

class Admin extends Base
{
    public function index($words = '')
    {
        $pageSize = 10;
        $Fish = new UserBag();
        $where = [];
        if ($words) {
            $user_array = $this->getUserId($words);
            $where['userid'] = ['in', $user_array];
        }
        $list = $Fish->getList('/fish50', $where, function($item, $key){

        });
        return $this->fetch('index',['title' => '设置', 'list' => $list, 'query' => ['words' => $words]]);
    }

    public function getUserId($words) {
        $res = (new User())->where('nickname|username', 'like', "%{$words}%")->column('id');
        return $res;
    }

    public function edit($id = '')
    {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $Fish = new UserBag();
        $info = $Fish->find($id);
        $info = $info->append(['user_info', 'name']);
        if (request()->isPost()) {
            $info->num = request()->post()['num'];
            $info->save();
            return $this->success('修改成功', 'index');            
        }

        return $this->fetch('edit', ['title' => '修改', 'info' => $info]);
    }

    public function remove($id) {
        if (empty($id)) {
            return $this->error('错误，操作非法');
        }
        $info = UserBag::get($id);
        if (!$info) {
            return $this->error('错误，操作非法');            
        }
        $info->delete();            
        
        return $this->success('删除成功', 'index');            
    }
    public function record()
    {
        $his = new His;
        $list = $his->getList();
        $list->append(['user_name']);
        $page = $list->render();
        $list = $list->toArray();
        $fishlist = (new FishData)->fishList();
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("fishlist",$fishlist);
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("page",$page);
        return $this->fetch('betting', ['title' => '打鱼记录', "query" => request()->get()]);
    }

    public function deleteFish()
    {
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new His)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new His)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
