<?php
namespace app\index\controller;

use app\common\controller\Extend;
use app\common\model\UserAction;
use app\index\model\Banner;
use app\index\model\ExtShowList;
use think\Controller;

class Game extends Controller
{
    public function index()
    {   
        header('location:/');
        //游戏列表
        $extshowlist = new ExtShowList;
        $res = $extshowlist ->getList();
        $count = count($res);
        if($count == 0) $this->assign('count',0);
        $info = $res->toArray();
        $this->assign('info',$info);
        $banner = (new Banner)->where('class_id', 6)->where('status', 1)->order('msort', 'desc')->select();
        $this->assign('banner',$banner);
        //游戏推荐列表
        $rec = $extshowlist->getRecList();
        $countRec = count($rec);
        if($countRec == 0) $this->assign('countRec',0);
        $infoRec = $rec->toArray();
        $this->assign('infoRec',$infoRec);

        $first_action = $this->getFirstAction();
        $first_action_str = '[' . $first_action->user_info['nickname'] . '] ' . $first_action->content;
        return $this->fetch('index',['title' => '游戏大厅', 'first_action' => $first_action_str]);
    }

    public function getFirstAction()
    {
        $Action = new UserAction;
        $res = $Action->order('id', 'desc')->find();
        if ($res) {
            $res->append(['user_info']);            
        }
        return $res;
    }

    public function getAction($userid = '', $num = 10)
    {
        
        if ($userid === '') {
            $userid = $this->user['id'];
        }
        $Action = new UserAction;
        if ($userid != 0) {
            $Action->where(['userid' => $userid]);
        }
        $res = $Action->order('id', 'desc')->paginate($num);
        $res->append(['user_info', 'friend_time']);
        foreach ($res as &$item) {
            $ext_info = Extend::getInfo($item['ext_name']);
            $item['ext_info'] = [
                'logo' => $ext_info['logo'],
                'title' => $ext_info['title']
            ];
        }
        return ['code' => 1, 'data' => $res];
    }

    public function list()
    {
        $MoneyHistory = new MoneyHistory;
        $list = $MoneyHistory->where('userid', $this->user['id'])->order('id desc')->paginate(15);
        $list->append(['friend_time']);
        foreach ($list as &$item) {
            $ext_info = Extend::getInfo($item['ext_name']);
            $item['ext_info'] = [
                'logo' => $ext_info['logo'],
                'title' => $ext_info['title']
            ];
        }
        return $list;
    }

    /**
     * 获取游戏列表
     */
    public function getAllList($row = '',$type='')
    {
        $extshowlist = new ExtShowList;
        $res = $extshowlist->where('type', $type)->where('status',0)->order('sort ASC')->select();
        $game = $res->toArray();
        return json([
            "err" => 0,
            "data" => $game
        ]);
    }

    /**
     * 获取游戏列表
     */
    public function getNavList()
    {
        return json([
            "err" => 0,
            "data" => (new \app\web\controller\Common())->getNavLottery()
        ]);
    }

    /**
     * 获取推荐游戏
     */
    public function getRecList($row = '')
    {
        $extshowlist = new ExtShowList;
        $res = $extshowlist ->getRecList();
        return json([
            "err" => 0,
            "data" => $res
        ]);
    }
}
