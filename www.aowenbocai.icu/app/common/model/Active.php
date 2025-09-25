<?php
namespace app\common\model;

use think\Model;

class  Active extends Model
{
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = "collection";

    public function getid($pid){
        return $this->field('id')->where(['pid' => $pid])->find();
    }
    public function getList($where = [], $pagesize = 14)
    {
        return $this->where($where)->order('id desc')->paginate($pagesize);
    }

    /**添加数据 */
    public function addtask($data)
    {
        return $this->allowField(true)->save($data);
    }

    public function getAllList($where = [])
    {
        return $this->where($where)->order('id desc')->select();
    }

}