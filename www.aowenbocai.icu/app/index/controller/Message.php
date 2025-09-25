<?php
namespace app\index\controller;

use think\Request;
use app\index\model\MsgArticle;
use app\index\model\User;

class Message extends Base
{

    /**
     * [发送消息]
     * @return array
     */
    public function send($userid = 0, $content = '')
    {
        if (Request::instance()->isPost()) {
            $MsgArticle = new MsgArticle;
            if($userid == $this->user['id']){
                return json(['err' =>1, 'msg' => '不能给自己发消息']);
            }
            $msg = $MsgArticle->send([
                'userid' => $userid,
                'send_userid' => $this->user['id'],
                'content' => $content
            ]);
            return $msg;
        }
        $User = new User;
        $user = $User->get($userid);
        if ($user) {
            $user = $user->toArray();
        }

        $username = $user['username'] ? $user['username'] : $user['nickname'];
        return $this->fetch('send', ['title' => '发送消息', 'userid' => $userid, 'username' => $username]);
    }

    /**
     * [消息记录]
     * @return array
     */
    public function messageRecord ($send_userid = '')
    {
        if (!$send_userid) return ['err' => 0];
        $pageSize = 10;
        $userid = $this->user['id'];
        $ids = [$userid, $send_userid];
        $list = (new MsgArticle)->where("send_userid", 'in', $ids)->where("userid", 'in', $ids)->order(['status' => ' asc', 'create_time' => 'desc'])->paginate($pageSize);
        $list = $list->append(['username', 'send_username']);

        if (empty($list)){
            return json(['err' =>1]);
        } else {
            return json(['err' => 0, 'list' => $list]);
        }
    }

    /**
     * 消息中心
     */
    public function index()
    {
        return $this->fetch('index', ['title' => '消息中心']);        
    }

}
