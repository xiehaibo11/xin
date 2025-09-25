<?php
namespace app\index\model;

use think\Model;
use think\Validate;

class UserIdbank extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function addTrue($data)
    {
        $rules = [
            'userid' => 'require',
            'idname' => 'require',
            'idnum' => 'require|alphaNum',
        ];
        $validate = new Validate($rules);
        $checked = $validate->check($data);
        if(!$checked){
            return ['err' => 3, 'msg' => $validate->getError()];
        }
        $res = $this->save($data);
        if($res){
            return ['err' => 0, 'msg' => '实名认证完成'];
        }
        return ['err' => 2, 'msg' => '实名认证失败'];
    }
}

?>