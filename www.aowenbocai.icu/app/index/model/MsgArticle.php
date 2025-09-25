<?php
namespace app\index\model;

use think\Model;
use think\Validate;

class MsgArticle extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - 获取发件人信息
     * @return string
     */
    public function getSendUsernameAttr($value, $data)
    {
        if ($data['send_userid']) {
            $user=(new User)->get($data['send_userid']);
            if (empty($user)) {
                return "会员已删除";
            }
            $username = $user['username'] ? $user['username'] : $user['nickname'];
            return $username;
        } else {
            return '系统信息';
        }
    }

    /**
     * 获取器 - title
     * @return string
     */
    public function getUsernameAttr($value, $data)
    {
        if ($data['userid']) {
            $user=(new User)->get($data['userid']);
            if (empty($user)) {
                return "会员已删除";
            }
            $username = $user['username'] ? $user['username'] : $user['nickname'];
            return $username;
        }else{
            return "会员已删除";
        }
    }

    /**
     * 获取器 - 时间
     * @return string
     */
    public function getCreateTimeAttr($value)
    {
        $mtime= (new \org\FriendlyDate())->getmyDate(strtotime($value));
        return $mtime;
    }

    /**
     * 修改阅读状态
     * @param  array $id  记录id
     * @return array
     */
    public function changeRead($id)
    {
        self::update([
            "id"=> $id,
            "status" => 1
        ]);
    }

    public function send($data)
    {
        $validate = new Validate([
            'userid|接收人'   => 'require',
            'content|内容'   => 'require',
            'send_userid|发送人'   => 'require'
        ]);

        $result = $validate->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['status'] = 0;
        $msg = $this->save($data);

        if (!$msg) {
            return ['code' => 0, 'msg' => '发送失败'];
        }

        return ['code' => 1, 'msg' => '发送成功'];
    }

        /**
     * 添加
     * @param  array $data 表单提交的值
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'content|内容'   => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['title']="系统消息-注册成功通知";

        $admin = $this->allowField(['title', 'userid', 'send_userid', 'content', 'create_time', 'status'])->save($data);
    }
    /**
     * 查看是信息
     */
    public function getList($userid)
    {
        return $this->where(['userid' => $userid ,'status' => 0])->count();
    }
}
