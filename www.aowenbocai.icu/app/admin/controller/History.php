<?php
namespace app\admin\controller; 

use think\Controller;
use app\admin\model\Admin;
use \think\Db;

class History extends Base {

    public function index()
    {
        $res = (new Admin)->getDatabases();
        return $this->fetch('index/history', ['title' => '数据删除', 'data' => $res]);
    }

    public function deleteData()
    {
        $data = request()->post();
        print_r($data);
        die;
        $name = $data['name'];
        $type = 0;
        switch ($name) {
        }
        if($type == 1){
            echo "彩票类";die;
            return json(['err' =>0]);
        }
        if(!empty($data['starttime'])){
            $handle->where('create_time', ">=", $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $handle->where('create_time', "<", $data['endtime']);
        }
        if(!empty($data['userid'])){
            $handle->where('userid',  $data['userid']);
        }
        if(!empty($data['dataid'])){
            $handle->where('id', $data['dataid']);
        }
        $ids = $handle->column('id');
        if(empty($ids)){
            return json(['err' => 2, 'msg' => '该条件下没有可清理的数据']);
        }
        $res = $handle ->where('id', 'in', $ids)->delete();
        if($res){
            return json(['err' => 0, 'msg' => '删除成功']);
        }
        return json(['err' => 3, 'msg' => '删除失败']);
        echo '<pre>';
        print_r($ids);
        die;
    }

}