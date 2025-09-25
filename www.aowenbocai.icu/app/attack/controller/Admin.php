<?php
namespace app\attack\controller;

use app\admin\controller\Base;
use app\attack\model\Buy;
use app\attack\model\BuyNpc;
use app\admin\model\ExtShowList;
use app\admin\model\User;
use think\Db;

class Admin extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        return $this->fetch('index',['title'=>'后台管理']);
    }

    /**
     * 人机对战
    */
    public function bettingNpc($nickname = '', $starttime = '', $endtime = '')
    {
        $model = new BuyNpc();
        $param  = $this->param;
        if (nickname) {
            $userid = $this->likeNameToId($nickname);
            $model->where('userid', 'in', $userid);
        }
        if ($starttime) {
            $model->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $model->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $model->order($order)->paginate(13,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        return $this->fetch('betting_npc',['title' => '人机投注管理']);
    }

    /**
     * 玩家对战
     */
    public function record($username = '', $starttime = '', $endtime = '')
    {
        $model = new Buy();
        $param  = $this->param;
        if ($username) {
            $userid = $this->likeNameToId($username);
            $model->where('userid', 'in', $userid);
        }
        if ($starttime) {
            $model->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $model->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $model->order($order)->paginate(13,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        return $this->fetch('betting',['title' => '玩家对战管理']);
    }


    public function likeNameToId($username)
    {
        $User = new User;
        return $User->where([
            'nickname' => ['like', "%{$username}%"]
        ])->column('id');
    }


    /**
     * 删除人机投注内容
     * @return mixed
     */
    public function BuyNpcDelete()
    {
        $model = new BuyNpc();
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $info = $model->get($data['id']);
            $res = $info->delete();
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = $model->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');

    }

    /**
     * 删除对局投注内容
     * @return mixed
     */
    public function BuyDelete()
    {
        $model = new Buy();
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $info = $model->get($data['id']);
            $res = $info->delete();
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = $model->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }



}
