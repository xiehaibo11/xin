<?php
namespace app\admin\model;

use think\Model;
use think\Validate;

class MsgArticle extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器--获取用户信息
     */
    public function getUserInfoAttr($value, $data)
    {
        $info = (new User())->get($data['userid']);
        if($info) $info = $info -> toarray();
        return ['nickname' => $info['nickname'], 'username' => $info['username']];
    }

    /**
     * 获取器 - status
     */
    public function getStatusTxtAttr($value,$data)
    {
        $value = $data['status'];
        $status_array = [0 => '未读', 1 => '已读'];
        $html  = $value == 0 ?  '<font style="color: red;">' .$status_array[$value]. '</font>':'<font style="color: #5cb85c;" >' .$status_array[$value]. '</font>';
        return $html;
    }

    /**
     * 添加
     * @param  array $data 表单提交的值
     * @return array
     */
    public function add($data)
    {
        $validate = new Validate([
            'content|内容'   => 'require',
            'userid|被通知用户' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code"=>0,"msg"=>$validate->getError()];
        }
        $data['title'] = "系统消息";
        $saveData = [];
        $ids = explode('_', $data['userid']);

        foreach ($ids as $key => $value) {
            $saveData[$key] = ['content' => $data['content'], 'send_userid' => $data['send_userid'], 'userid' => $value];
        }
        $admin = $this->allowField(true)->saveAll($saveData);

        if (!$admin) {
            return ["code"=>0,"msg"=>"发送失败"];
        }

        return ["code"=>1];
    }

}
