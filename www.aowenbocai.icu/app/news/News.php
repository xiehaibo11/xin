<?php
namespace app\news;

use think\Controller;
use app\news\model\PluginNewsList;
use app\news\model\PluginNewsNav;
use app\index\model\User;
use app\index\controller\Base;

class News extends Controller
{
    public static function getNewOne($nav_id = 0)
    {
        $list = new PluginNewsList;
        if ($nav_id > 0) {
            return $list->order('id', 'DESC')->getByNavId($nav_id);
        }
    }
    
    public static function getNewList($nav_id = 0, $rows = 10, $name = '', $has_content = false)
    {
        $News = new PluginNewsList;
        if($name != ''){
            $ids = (new PluginNewsNav)->where('title', $name)->column('id');
            $nav_id = empty($ids) ? '' : $ids[0];
        }
        $News->where('nav_id', $nav_id);
        $list = $News->order("id DESC")->paginate($rows);
        $list->append(['pic','content_index']);
        !$has_content ? $list->hidden(['content']) : null;
        return $list->toArray()['data'];
    }

    public static function getNewAList($nav_id = 0, $rows = 10)
    {
        $News = new PluginNewsList;
        if (!empty($nav_id) && !empty($rows)) {
            $News->where('nav_id', $nav_id);
        }
        $list = $News->order("id DESC")->paginate($rows, false, ['query'=>['navid' => $nav_id]]);
        $list->append(['pic','content_index']);
        $list->hidden(['content']);
        return $list;
    }
}
