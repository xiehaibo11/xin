<?php
namespace app\news\model;

use think\Model;

class PluginNewsNav extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getPidNameAttr($value, $data)
    {
        if($data['pid'] != 0){
            $name = $this->where('id', $data['pid'])->column('title');
            $pidname = empty($name) ? '上级不存在' : $name[0];
        }
        return isset($pidname) ? $pidname : '一级栏目';
    }

    /**获取器--获取下级栏目 及栏目数据 */
    public function getColumnAttr($value, $data)
    {
        $list = $this->where('pid', $data['id'])->select();
        $list->append(['article']);
        return $list ? ($list->toArray()) : [];
    }

    /**获取器--获取栏目下的数据 */
    public function getArticleAttr($value, $data)
    {
        $list = (new PluginNewsList)->field('id, title, content')->where('nav_id', $data['id'])->select();
        return $list ? ($list->toArray()) : [];
    }
    public function dataAdd($data)
    {
        $res = $this->validate([
            'title|标题'  => 'require'
        ])->save($data);
        if (false === $res) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '添加成功'];
    }
}
