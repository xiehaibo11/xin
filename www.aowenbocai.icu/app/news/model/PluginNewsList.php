<?php
namespace app\news\model;

use think\Model;

class PluginNewsList extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    public function getPicAttr($value, $data)
    {
        $content = $data['content'];
        preg_match('/<img .*src="(.+?)"/is', $content, $pic);
        return count($pic) > 0 ? $pic[1] : null;
    }

    public function getContentIndexAttr($value, $data)
    {
        $content = $data['content'];
        $content = iconv('utf-8','utf-8//IGNORE',$content);
        $content = strip_tags($content);
        $content = mb_substr($content, 0, 100);
        return $content;
    }

    public function getNavNameAttr($value, $data)
    {   
        $nav = (new PluginNewsNav)->get($data['nav_id']);
        if($nav){
            $nav = $nav->toArray();
            return $nav['title'];
        }
        return '请重新选择';
    }
    public function dataAdd($data)
    {
        unset($data['files']);
        $res = $this->validate([
            'title|标题'  => 'require',
            'content|内容' => 'require'
        ])->save($data);
        if (false === $res) {
            return ['code' => 0, 'msg' => $this->getError()];
        }
        return ['code' => 1, 'msg' => '添加成功'];
    }
}
