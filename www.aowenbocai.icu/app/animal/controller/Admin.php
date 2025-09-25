<?php
namespace app\animal\controller;

use app\admin\controller\Base;
use app\animal\model\AnimalConfig;
use app\animal\model\Record;
use app\admin\model\User;
use app\admin\model\ExtShowList;
use think\Db;

class Admin extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new Record();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();//测试一下
    }

    public function index()
    {
        return $this->fetch('index',['title'=>'后台管理']);
    }

    public function record($username = '', $animal_type = '', $status = -1, $starttime = '', $endtime = '')
    {
        $param  = $this->param;
        if ($username) {
            $userid = $this->likeNameToId($username);
            $this->baseModel->where('userid', 'in', $userid);
        }
        if ($animal_type) {
            $this->baseModel->where('animal_type', $animal_type);
        }
        if ($status != -1) {
            $this->baseModel->where('status', $status);
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        $order=getOrder($param);//排序
        $list = $this->baseModel->order($order)->paginate(13,false,['query'=>$param]);
        $animal_list = (new AnimalConfig())->column('title','type');
        $extList = (new ExtShowList)->getRecodeExt();
        $this->assign("extList",$extList);
        $this->assign("list",$list);
        $this->assign("animal_list",$animal_list);
        $this->assign("query",$param);
        return $this->fetch('record',['title' => '动物记录']);
    }


    public function likeNameToId($username)
    {
        $User = new User;
        return $User->where([
            'username' => ['like', "%{$username}%"]
        ])->column('id');
    }

    /**
     * 动物管理
     * @return mixed
     */
    public function animal( $title = '')
    {
        $model = new AnimalConfig();
        $param  = $this->param;
        if ($title) {
            $model->where('title', $title);
        }
        $order=getOrder($param);//排序
        $list = $model->order($order)->paginate(13,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        return $this->fetch('animal',['title'=>'动物管理']);
    }

    /**
     * 添加动物
     * @return mixed
     */
    public function addAnimal()
    {
        if(request()->isPost()){
            $model = new AnimalConfig();
            $data = $this->post;
            $res = $model->add($data);
            if ($res['code']) {
                return $this->success($res['msg'], url('animal'));
            } else {
                return $this->error($res['msg']);
            }
        }
        return $this->fetch('add_animal', ['title' => '添加动物']);

    }

    /**
     * 修改动物
     */
    public function editAnimal()
    {
        $id = $this->id;
        $model = new AnimalConfig();
        $info = $model->get($id);
        if(request()->isPost()){
            $data = $this->post;
            $res = $model->edit($data);
            if ($res['code']) {
                return $this->success($res['msg'], url('animal'));
            } else {
                return $this->error($res['msg']);
            }
        }
        $status_option =$model->statusOption($info->getData('status'));
        $this->assign('info',$info);
        $this->assign('status_option', $status_option);
        return $this->fetch('edit_animal', ['title' => '编辑动物']);

    }

    /**
     * 删除
     */
    public function animal_delete()
    {
        $id = $this->id;
        $model = new AnimalConfig();
        $info = $model->get($id);
        if($info->delete()){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }

    public function deleteRecord()
    {
        $data = request()->param();
        if(isset($data['id'])){
            if( $data['id'] == ''){
                $this->error('数据错误');
            }
            $res = $this->baseModel->destroy($data['id']);
        }
        if(isset($data['day'])){
            $time = date('Y-m-d');
            $day = $data['day'] == 'all' ? 0 : $data['day'];
            $endtime = strtotime($time) - $day * 24 * 60 * 60;
            $endtime = $day == 0 ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",$endtime);
            $res = $this->baseModel->where('create_time', "<=", $endtime)->delete();
        }
        if($res){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

}
