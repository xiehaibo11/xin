<?php
namespace app\poker\controller;

use app\common\model\UserBag;
use app\index\controller\Base;
use app\poker\model\GamePoker;
use app\poker\model\GamePokerRecord;
use core\Setting;
use think\Db;

class Index extends Base
{
    private $pokerUser;

    public function __construct()
    {
        parent::__construct();
        $poker = new GamePoker;
        if (!$this->pokerUser = $poker->get(['userid' => $this->user['id']])) {
            $this->pokerUser = $poker->initUser($this->user['id']);
        }
    }

    public function bet()
    {
        $data = request()->get();
        $res = [];
        if (isset($data['bet_money'])) {
            $res = $this->pokerUser->bet($data['bet_money']);
        } else if (isset($data['bet_code'])) {
            $res = $this->pokerUser->betNext($data['bet_code']);
        }
        return json($res);        
    }

    public function over()
    {
        return json($this->pokerUser->over());
    }

    public function play()
    {
		return json($this->pokerUser->getInit());
    }
	
    public function history()
    {
        $data = (new GamePokerRecord)->record($this->user['id']);
        return json([
            'err' => 0,
            'data' => $data
        ]);
    }
	
	public function index()
	{
		return $this->fetch('index', ['title' => '翻扑克']);
	}

    /**
     * 获取资料
     */
    public function getInfo()
    {
        $tel = $this->user['tel'] ? 1: 0;
        $setting = Setting::get(['free_coupon_coin']);
        $coupon = (new UserBag())->where('param_name', 'poker_coupon')->where('userid', $this->user['id'])->find();
        $data = [
            'err' => 0,
            'data' => [
                'id' => $this->user['id'],
                'username' => $this->user['username'],
                'money' => $this->user['money'],
                'diamonds' => $this->user['diamonds'],
                'nickname' => $this->user['nickname'],
                'photo' => $this->user['photo'],
                'coupon' => empty($coupon) ? 0 : $coupon['num'],
                'coupon_coin' => $setting['free_coupon_coin']
            ],
        ];
        return json($data);
    }
}
