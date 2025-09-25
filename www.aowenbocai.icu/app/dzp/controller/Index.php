<?php
namespace app\dzp\controller;

use app\admin\model\Prop;
use app\common\model\GameMoneyHistory;
use app\common\model\GameDzp;
use app\common\model\GameDzpSetting;
use app\common\model\MoneyHistory;
use app\common\model\GameDzpHistory;
use app\common\model\UserBag;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch('index', ['title' => '幸运转盘']);
    }

    /**
     * 首页
     */
    public function pc_index()
    {
        return $this->fetch('pc_index', ['title' => '幸运转盘']);
    }

    public function getRand()
    {
        $has_count = GameDzp::getCount($this->user['id']);
        $coupon = (new UserBag())->where('param_name', 'dzp_coupon')->where('userid', $this->user['id'])->find();
        if (!$has_count and ($coupon['num'] || !empty($coupon))) return json(['err' => 1, 'msg' => '没有次数了!']);
        $rand = mt_rand(1, 1000);
        $model = new GameDzpSetting();
        $list = $model->where('status = 1')->select();
        $start = 0;
        $award_id = 0;
        foreach ($list as $key => $value) {
            $end = $start + $value['chance'];
            if($rand > $start && $rand <= $end){
                $award_id = $value['id'];
                $award_type = $value['type'];
                $award_num = $value['num'];
                $award_rel_id = $value['rel_id'];
                $data['photo'] = $value['photo'];
                break;
            }
            $start += $value['chance'];
        }
        if (!$award_id) return json(['err' => 1, 'msg' => '开奖错误']);
        $data = [];
        switch ($award_type) {
            case 1://彩金
                (new MoneyHistory)->write([
                    'userid' => $this->user['id'],
                    'money' => $award_num,
                    'ext_name' => 'dzp',
                    'type' => 4,
                    'remark' => '每日转盘'
                ]);
                $data = ['msg' => '金豆 +' . $award_num];
                break;
            case 2://游戏币
                (new GameMoneyHistory())->write([
                    'userid' => $this->user['id'],
                    'money' => $award_num,
                    'ext_name' => 'dzp',
                    'type' => 1,
                    'remark' => '每日转盘'
                ]);
                $data = ['msg' => '钻石 +' . $award_num];
                break;
            case 3:
                $prop = (new Prop())->find($award_rel_id);
                if (empty($prop)) return json(['err' => 1, 'msg' => '奖品已经不存在']);
                (new UserBag())->add($this->user['id'], $prop['param_name'], $award_num);
                $data = ['msg' => $prop["name"] . ' +' . $award_num];
                break;
            case 7:
                $data = ['msg' => '谢谢惠顾'];
                break;
        }
        if ($has_count) {
            GameDzp::addCount($this->user['id'], -1);
        } else {
            (new UserBag())->where('userid', $this->user['id'])->where('param_name', 'dzp_coupon')->setDec('num');
        }
        (new GameDzpHistory)->addData($this->user['id'], $data);
        $data['err'] = 0;
        $data['award_id'] = $award_id;
        return json($data);
    }

    /**
     * 获取奖品列表
     */
    public function getList()
    {
        $model = new GameDzpSetting();
        $list = $model->field('id, type, rel_id, num, photo')->where('status = 1')->select();
        $list = $list->append(['name'])->toArray();
        return json(['err' => 0, 'data' => $list]);
    }

    /**
     * 获取会员信息
     */
    public function getInfo()
    {
        $coupon = (new UserBag())->where('param_name', 'dzp_coupon')->where('userid', $this->user['id'])->find();
        $data = [
            'id' => $this->user['id'],
            'money' => $this->user['money'],
            'diamonds' => $this->user['diamonds'],
            'nick_name' => $this->user['nickname'],
            'photo' => $this->user['photo'],
            'count' => empty($coupon) ? GameDzp::getCount($this->user['id']) : GameDzp::getCount($this->user['id'])  + $coupon['num']
        ];
        return json($data);
    }

    public function getCount()
    {
        return json(['code' =>1, 'data' => GameDzp::getCount($this->user['id'])]);
    }

    /**获取历史记录 */
    public function getHistory($num)
    {
        $res =  (new GameDzpHistory)->getList($num);
        $res->append(['nickname','time']);
        $res = $res->toArray();
        if(!$res['data']){
            return json(['err' => 1, 'msg' => '暂时没有相关数据']);
        }
        return json(['err' => 0, 'data' => $res['data']]);
    }
}
