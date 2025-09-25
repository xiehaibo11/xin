<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Validate;

class Banner extends BaseModel
{
    protected function initialize()
    {
        $this->baseClassModel  = new BannerClass();
        parent::initialize();
    }
    
    //时间
    public function getClassIdAttr($value)
    {
       $class = Db::name('banner_class')->find($value);
        return empty($class) ? '分类已删除':$class['name'];
    }

    /**
     *  数据操作
     * @param  array $data 表单提交的值
     * @return json
     */
    public function add($data)
    {
        $validate = new Validate([
            'name|图片名称' => 'require',
            'img_url|图片地址' => 'require'
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
            return ["code" => 0, "msg" => "添加失败"];
        }
        return ["code" => 1];
    }

}
?>