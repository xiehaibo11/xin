<?php
namespace app\index\controller;

use app\index\model\Fans as AFans;
use app\index\model\User as usermodel;

class Fans extends Base
{

    public function __construct()
    {
        $this->baseModel  = new AFans();//当前模型
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch('index',['title' => '关注']);
    }

    /**
     * 关注列表
     * type 1 关注的列表  2 被关注的列表
     */
    public function _careList($type=1,$words="")
    {
        if ($type == 1){
            $userid_field='userid';
            $to_userid_field='to_userid';
        } else {
            $userid_field='to_userid';
            $to_userid_field='userid';
        }
        if ($words){
            $to_userid = $this->likeNameToId($words);
            $this->baseModel->whereOr($to_userid_field, 'in', $to_userid);
            $this->baseModel->whereOr('memo','like',"%$words%");//获取搜索条件
        }
        $pageSize = 10;
        $this->baseModel->where($userid_field, $this->user['id'])->field($to_userid_field);
        $list = $this->baseModel->order(['create_time' => 'desc'])->paginate($pageSize);
        $list = $list->append(['user']);
        if (empty($list)){
            return ['code'=>0];
        } else {
            return ['code'=>1,'data'=>$list];
        }
    }

    public function careList($word = "") {
        print_r($words);

        return $this->_careList(1, $word);
    }

    public function fansList($words = "") {
        return $this->_careList(0, $word);
    }

    /**
     * 由$nickname获得id数组
     */
    public function likeNameToId($nickname)
    {
        $User = new usermodel;
        return $User->where([
            'nickname' => ['like', "%".$nickname."%"],
            'id' => ['<>',$this->user['id']]
        ])->column('id');
    }

    /**
     * 关注操作
     */
    public function care($to_userid)
    {
        $has_user=(new usermodel)->find($to_userid);
        if (empty($has_user)) { return $this->error('不存在用户'); }
        $user=$this->user;
        $info=$this->baseModel->where('userid',$user['id'])->where('to_userid',$to_userid)->find();
        if (empty($info)){
            if (!$this->baseModel->save([
                'userid' => $user['id'],
                'to_userid' => $to_userid
            ])){
                return $this->error('关注失败');
            } else {
                return $this->success('关注成功');
            }
        } else {
            if (!$this->baseModel->where('userid',$user['id'])->where('to_userid',$to_userid)->delete()){
                return $this->error('取消关注失败');
            } else {
                return $this->success('取消关注成功');
            }
        }
    }

}
