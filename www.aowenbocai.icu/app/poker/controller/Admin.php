<?php
namespace app\poker\controller;

use think\Controller;
use app\admin\controller\Base;
use app\poker\model\GameDishu as Dishu;
use app\poker\model\GamePokerRecord as Poker;
use app\admin\model\ExtShowList;

class Admin Extends Base
{

    public function index()
    {
        return $this->fetch('index',['title'=>'后台管理']);
    }

    public function record()
    {
        $data = request()->get();
        $his = new Poker;
        $list = $his->getList();
        $page = $list->render();
        $extList = (new ExtShowList)->getRecodeExt();

        if(isset($data['sort']) && $data['sort'] != ''){
            $name = ['奖金升序','奖金降序','消耗金币升序','消耗金币降序'];
            $this->assign("sortname",$name[$data['sort']]);
        }
        $this->assign("extList",$extList);
        $this->assign("page",$page);
        $this->assign("list",$list->toArray());
        
        return $this->fetch('', ['title' => '翻扑克记录', "query" => $data]);
    }

    public function deletePk()
    {
        $data = request()->get();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new Poker)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new Poker)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除成功');
    }
}

