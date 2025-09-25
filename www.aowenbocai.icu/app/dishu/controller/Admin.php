<?php
namespace app\dishu\controller;

use app\admin\controller\Base;
use app\dishu\model\GameDishu as Dishu;
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
        $his = new Dishu;
        $list = $his->getList();
        $page = $list->render();
        $extList = (new ExtShowList)->getRecodeExt();

        $mouse = ['1' => '萌萌鼠', '2' => '矿矿鼠', '3' => '海盗鼠', '4' => '呆呆鼠', '5' => '调皮鼠'];
        if(!empty($data['name'])){
            $name = ['value' => $data['name'], 'name' => $mouse[$data['name']]];
            $this->assign("name",$name);
        }
        if(isset($data['sort']) && $data['sort'] != ''){
            $name = ['奖金升序','奖金降序','消耗金币升序','消耗金币降序'];
            $this->assign("sortname",$name[$data['sort']]);
        }
        $this->assign("extList",$extList);
        $this->assign("giftList",$mouse);
        $this->assign("page",$page);
        $this->assign("list",$list->toArray());
        return $this->fetch('', ['title' => '打地鼠记录', "query" => $data]);
    }

    public function deleteDi()
    {
        $data = request()->get();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = (new Dishu)->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = (new Dishu)->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除成功');
    }


}

