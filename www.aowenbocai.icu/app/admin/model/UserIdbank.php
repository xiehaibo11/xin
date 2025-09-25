<?php
namespace app\admin\model;

use think\Model;
use think\Validate;

class UserIdbank extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     *  银行选择项
     */
    public function bankOption($value = "")
    {
        $html = '';
        $bak_array = ['中国工商银行', '中国农业银行', '中国银行', '建设银行'];
        foreach ($bak_array as  $v) {
            $selected = $v == $value ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $v . '">' . $v . '</option>';
        }
        return $html;
    }

    public function changeData($data)
    {
        $data['idname'] = trim($data['idname']);
        $rules = [
            'num' => 'require|number',
            'idname|身份证名' => 'require|chs',
            'idnum|身份证号' => 'require|alphaNum',
        ];
        $validate = new Validate($rules);
        $checked = $validate->check($data);
        if(!$checked){
            return ['err' => 3, 'msg' => $validate->getError()];
        }
        $res = $this->allowField(true)->save($data,['id' => $data['num']]);
        if($res){
            return ['err' => 0, 'msg' => '修改成功'];
        }
        return ['err' => 2, 'msg' => '修改失败'];
    }

}

?>