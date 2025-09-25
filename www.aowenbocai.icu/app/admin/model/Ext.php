<?php
namespace app\admin\model;

class Ext extends BaseModel
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取所有已安装扩展
     * @return array
     */
    public function getAllExt()
    {
        return $this->select();
    }

    /**
     * 获取扩展信息
     * @return array
     */
    public function getInfo($ext_name)
    {
        return $this->where(['name' => $ext_name])->find();
    }

    /**
     * 获取所有已安装扩展
     * @return array
     */
    public function getList(){
        return $this->field(['name', 'title', 'logo', 'remark'])->select();
    }

    /**
     * 开启/关闭拓展
     * @return array
     */
    public function disable($ext_name)
    {
        $res = $this->get(['name' => $ext_name]);
        if ($res) {
            $status = $res->getData('status') == 0 ? 1 : 0;
            $res->status = $status;
            $res->save();
        }
        // return 
    }


}
