<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Validate;

class BannerClass extends BaseModel
{
    protected $autoWriteTimestamp = false;//自动更新时间


    //初始化属性
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * @return json
     */
    public function add($data)
    {
        $validate = new Validate([
            'name|名称', 'require',
            'remark|备注', 'require',
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        $allow_field=['name', 'remark', 'width', 'height'];

        if (isset($data['id'])) {
            $res = $this->allowField($allow_field)->where('id', $data['id'])->update($data);
        } else {
            $res = $this->allowField($allow_field)->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "提交失败"];
        }
        return ["code" => 1];

    }

}
?>