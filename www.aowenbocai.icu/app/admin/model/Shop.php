<?php

namespace app\admin\model;

use think\Validate;

class Shop extends BaseModel
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - 剩余数量
     * @return string
     */
    public function getHasNumAttr($value, $data)
    {
        $has_num = $data['total_num'] - $data['convert_num'];
        $has_num = $has_num >= 0 ? $has_num : 0;
        return $has_num;
    }

    /**获取器--获取栏目 */
    public function getTypeAttr($value, $data)
    {
        $name = (new ShopNav)->where('id', $value)->column('name');
        return empty($name) ? '栏目错误' : $name[0];
    }
    public function add($data, $type = '')
    {
        $validate = new Validate([
            'name|商品名' => 'require',
            'money|商品价格' => 'require',
            'total_num|商品件数' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if ($type == 'edit') {
            $res = $this->where('id', $data['id'])->update($data);
        } else {
            $res = $this->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "添加失败"];
        }
        return ["code" => 1];
    }
}
