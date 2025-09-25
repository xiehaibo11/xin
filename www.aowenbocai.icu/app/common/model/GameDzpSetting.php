<?php
namespace app\common\model;

use app\admin\model\BaseModel;
use app\admin\model\Prop;
use think\Model;
use app\index\model\User;
use think\Validate;

class GameDzpSetting extends BaseModel
{
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        $this->type_array = [1 => '金币', 2 => '游戏币', 3 => '道具', 7 => '谢谢惠顾'];
        parent::initialize();
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value, $data)
    {
        return $value;
    }

    /**
     * 获取器 - name
     */
    public function getNameAttr($value,$data)
    {
        $name = '未定义';
        if ($data['type'] == 3 and $data['rel_id']) {
            $prop = (new Prop())->find($data['rel_id']);
            $name = $prop['name'];
        } else {
            $name = $this->type_array[$data['type']];
        }
        return $name;
    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * @return json
     */
    public function add($data)
    {
        $validate = new Validate([
            'type|类型' => 'require',
            'num|数量' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if (isset($data['id'])) {
            $res = $this->where('id', $data['id'])->update($data);
        } else {
            $res = $this->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "执行失败"];
        }
        return ["code" => 1];
    }
}