<?php
namespace app\admin\controller;

use app\admin\model\MsgArticle;
use app\admin\model\User; 


class Inform extends Base
{
    public function index()
    {
        $inf = new MsgArticle();
        if($data = request()->get()){
            if($data['starttime']){
                $inf->where(["create_time" => [ ">" , $data['starttime']]]);
            }
            if($data['endtime']){
                $inf->where(["create_time" => [ "<" , $data['endtime']]]);
            }
            if($words = $data['words']){
                $user = (new User)->where('nickname', 'like', "%{$words}%")->whereOr('username', 'like', "%{$words}%")->column('id');
                $inf->where(['userid' => ['in' , $user]])->whereor(['content' => ['like' , "%{$words}%"]]);
            }
        }

        $res = $inf->order('id desc')->paginate(14);
        $page = $res -> render();
        $res->append(['userinfo', 'status_txt']);
        $res = $res->toArray();
        if(!$res['data']) {
            $this->assign('_empty' , 1);
        } 
        $this->assign('list', $res['data']);
        $this->assign('page', $page);
        $this->assign('query', $data);
        return $this->fetch('',['title' =>'通知管理']);
    }

    /**添加新的通知 */
    public function addData()
    {
        if(request()->IsPost()){
            $data = request()->post();
            $data['send_userid'] = $this->admin['id'];
            $res = (new MsgArticle)->add($data);
            if(!$res['code']){
                $this->error($res['msg']);
            }else{
                $this->success('发送成功', url('index'));
            }
        }
        return $this->fetch('add',['title' => '添加通知']);
    }

    /**更新通知状态 */
    public function isUpdate($id)
    {
        if($id == 'all'){
            $res = (new MsgArticle)->save(['status' => 1], ['id' => ['>' , 0]]);
        }else{
            $res = (new MsgArticle)->save(['status' => 1], ['id' => $id]);
        }
        $return = $res ? json(['err' => 0, 'msg' => '设置成功']) : json(['err' => 1, 'msg' => '设置失败']);
        return $return;
    }


    public function isDelete($id = '', $day = 0)
    {
        $model = new MsgArticle;
        if($id){
            $res = $model->destroy($id);
        }
        if($day){
            $today = strtotime(date("Y-m-d"));
            $start = $day == 1 ? date("Y-m-d H:i:s") : date("Y-m-d",strtotime("-".$day." day", $today));
            $res = $model ->destroy(['create_time' => ['<' , $start]]);
        }
        if(!$res) $this->error('删除失败');
        $this->success('删除成功');
    }

}