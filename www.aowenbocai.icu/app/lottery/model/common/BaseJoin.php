<?php
namespace app\lottery\model\common;

use app\admin\model\ExtShowList;
use app\common\controller\LotteryCommon;
use think\Model;
use app\index\model\User;

class BaseJoin extends Model
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $ext_name = '';
    protected $name = 'lottery_join';//表名
    protected $insert = ['ext_name'];
    //status  0  未派奖   1 中奖  2 未中奖

    // 定义全局的查询范围
    protected function base($query)
    {
        if ($this->ext_name) {
            $query->where('ext_name', $this->ext_name);
        }
    }
    public function setExtNameAttr($value)
    {
        return $this->ext_name;
    }

      /**
     * 获取器 - url
     */
    public function getUrlAttr($value,$data)
    {
        return url($data['ext_name'] . "/Index/joinArticle").'?id='.$data['id'];
    }

    /**
     * 获取器 - bonus
     */
    public function getBonusTxtAttr($value,$data)
    {
        $bonus = $data['bonus'];
        $buy = ((LotteryCommon::getModel($data['ext_name'], 'buy')))->field('status')->find($data['buy_id']);
        if(!$buy){
            return '<em class="hs">信息错误</em>';
        }
        $buy = $buy->toArray();
        if ($buy['status'] == 7) {
            return '<em class="hs">系统撤单</em>';
        }

        $expect = ((LotteryCommon::getModel($data['ext_name'], 'expect')))->where('buy_id', $data['buy_id'])->column('expect');
        if (empty($expect)) {
            return '<em class="hs">投注的期数不存在</em>';
        }

        $code = ((LotteryCommon::getModel($data['ext_name'], 'code')))->where('expect', min($expect))->find();
        if (empty($code)) {
            return '<em class="yellow">未开奖</em>';
        }
        if ($bonus > 0) {
            $res =  '<em class="red">中奖 ' . $bonus . ' 元</em>';
        } else {
            $res =  '<em class="hs">未中奖</em>';
        }
        return $res;
    }

    /**
     * 获取器 -
     */
    public function getUserNameAttr($value,$data)
    {
        $user =  User::get($data['userid']);
        $user = $user->toArray();
        return empty($user) ? '会员已删除' : ($user['nickname'] ? $user['nickname'] : $user['username']);
    }

    /**
     * 获取器 -
     */
    public function getBettingUrlAttr($value,$data)
    {
        return url($data['ext_name'] . "/Index/bettingArticle").'?id='.$data['id'];
    }

    /**
     * 获取器 - 期号
     */
    public function getExpectAttr($value,$data)
    {
        $res = ((LotteryCommon::getModel($data['ext_name'], 'expect')))->where('buy_id', $data['buy_id'])->column('expect');
        if (empty($res)) {
            return '未找到该期';
        }
        $count = count($res);
        $minExp = min($res);
        $html = $count > 1 ?  $minExp. '期  - 追' . $count . '期' : $minExp . '期';
        return $html;
    }

    /**
     * 获取器 - 期号
     */
    public function getExtTxtAttr($value,$data)
    {
        $info = (new ExtShowList())->where('name', '/' . $data['ext_name'])->find();
        return $info ? $info['title'] : $data['ext_name'];
    }

     /**根据合买数据获取具体数据 */
     public function getBuyInfoAttr($value, $data)
     {
         $status = ['0' => '未出票', '1' => '已出票', '2' => '已派奖', '3' => '中奖停止', '5' => '停止追号', '6'=>'流产撤单', '7' => '系统撤单', '8' => '用户撤单'];
         $expect = ((LotteryCommon::getModel($data['ext_name'], 'expect')))->field('status, expect, bonus')->where('buy_id', $data['buy_id'])->order('id ASC')->select()->toArray();
         $num = count($expect);
         $isChase = $num > 1 ? 1 : 0;
         $buy = ((LotteryCommon::getModel($data['ext_name'], 'buy')))->field('lottery_id,is_join,userid,total_money,end_time,total_share')->where('id', $data['buy_id'])->find();
         $buy->append(['nickname']);
         $data = [
             'isChase' => $isChase,
             'expect' => $isChase ? $expect[0]['expect']."(追".$num."期)" : $expect[0]['expect'],
             'status' => $status[$expect[0]['status']],
             'bonus' => $expect[0]['bonus'],
             'statusCode' => $expect[0]['status'],
             'finsh' => strtotime($buy->end_time) < time() ? 1 : 0,
             'lottery_id' => $buy->lottery_id,
             'total_money' => $buy->total_money,
             'total_share' => $buy->total_share,
             'is_join' => $buy->is_join,
             'fristPerson' => $buy->nickname,
         ];
         return $data;
     }

    /**购买金额 */
    public function getUserBuyAttr($value, $data)
    {
        $buy = ((LotteryCommon::getModel($data['ext_name'], 'buy')))->field('lottery_id,is_join,userid,total_money,end_time,total_share')->where('id', $data['buy_id'])->find();
        if ($buy->is_join) {
            return number_format(($data['money']/$buy->total_share) * $buy->total_money, 2);
        } else {
            return number_format($data['money'], 2);
        }
    }


    public function getList($userid)
    {
        $pageSize = 12;
        $list = $this->where('userid', $userid)->order('create_time desc')->paginate($pageSize);
        $list = $list->append(['expect', 'betting_url', 'bonus_txt']);
        return $list;
    }
}