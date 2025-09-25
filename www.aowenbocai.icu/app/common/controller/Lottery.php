<?php
namespace app\common\controller;

use app\index\model\Banner;
use app\index\model\BannerClass;
use app\index\model\ExtShowList;
use app\common\model\MoneyHistory;
use app\lottery\model\common\BaseBuy;
use app\web\model\User;
use think\Controller;
use think\Cookie;
use think\Session;
use app\news\News;
use think\Db;
use core\Page;
use core\Setting;
use core\Smtp;

class Lottery extends Controller
{

    /**获取游戏记录*/
    public function game($gameid = '')
    {
        $setting = (new Setting)->get(['star_num']);
        $star_num = empty($setting) ? 10000000 : $setting['star_num'];
        $data = request()->get();
        $extShow = (new ExtShowList);
        if($gameid == ''){
            $extShow->where('type', 1)->where('status',0);
        }else{
            $extShow->where('id',  $gameid);
        }
        $tables = $extShow->column(['name','title','id']);
        $tables = array_values($tables);
        if(empty($tables)){
            return json(['err' => 0]);
        }

        $buy_model = (new BaseBuy());
        if ($gameid) {
            $buy_model->where('ext_name', trim($tables[0]['name'], '/'));
        }
        if(!empty($data['words'])){
            $id = (new User)->where('nickname|username', 'like', "%".$data['words']."%")->column('id');
            if(empty($id)){
                $data = [
                    'err' => 1,
                ];
                return json($data);
            }
            $buy_model->whereIn('userid', $id);
        }

        if(!empty($data['userid'])){
            $buy_model->where('userid', $data['userid']);
        }
        if(!empty($data['starttime'])){
            $buy_model->where('create_time', '>=', $data['starttime']);
        }
        if(!empty($data['endtime'])){
            $buy_model->where('create_time', '<=', $data['endtime']." 23:59:59");
        }
        /**总金额 */
        if(!empty($data['money'])){
            $moneyArr = ['0,100', '100,500', '500,1000','1000,0'];
            $_moneyArr = explode(',', $moneyArr[$data['money'] - 1]);
            $buy_model->where('total_money', '>=', $_moneyArr[0]);
            if($moneyArr[1] > 0){
                $buy_model->where('total_money', '<=', $_moneyArr[1]);
            }
        }
        /**提成 */
        if(!empty($data['gain'])){
            $buy_model->where('gain', '<=', $data['gain'] - 1);
        }
        if(isset($data['bounsStatus']) && $data['bounsStatus'] !=  ''){
            $buy_model->where('status', '<=', $data['bounsStatus']);
        }
        $ids = array_column($tables, 'name');
        $user_model = (new User);
        $res = $buy_model->where('is_join', 1)->whereIn('status',[0,1])->order('end_time desc')->paginate(14);
        $res = $res->append(['buy_info'])->toArray();
        if (!empty($res['data'])) {
            foreach ($res['data'] as &$item) {
                $table_name = strtolower(explode('|', $item['lottery_id'])[0]);
                $ids_key = array_search($table_name, $ids);
                $ids_key = $ids_key ? $ids_key : array_search('/'.$table_name, $ids);
                $item['title'] = $tables[$ids_key]['title'];
                $buy_info = $item['buy_info'];
                $item['issue'] = $buy_info['expect'];
                $item['buy'] = (LotteryCommon::getModel($item['ext_name'], 'join'))->where('buy_id', $item['id'])->sum('money');
                $item['ure'] = $item['total_share'] - $item['buy'];
                if (!$item['total_share']) continue;
                $item['buyprecent'] = intval($item['buy'] / $item['total_share'] * 10000) / 100;
                $item['bdprecent'] = intval($item['assure_money'] / $item['total_share'] * 10000) / 100;
                $item['isFish'] = strtotime($item['end_time']) <= time() ? 1 : 0;
                $user = $user_model->where('id', $item['userid'])->column(['id', 'record','nickname']);
                if($user){
                    $item['record'] = $user[$item['userid']]['record'];
                    $item['nickname'] = mb_substr($user[$item['userid']]['nickname'], 0, 1, 'utf-8')."***".mb_substr($user[$item['userid']]['nickname'], -1, 1, 'utf-8');
                    $allStar = intval($item['record'] / $star_num);
                    $item['starNum'] = $allStar % 10;
                    $MoonNum = intval($allStar / 10);
                    $item['MoonNum'] = $MoonNum % 10;
                    $sunNum = intval($MoonNum / 10);
                    $item['sunNum'] = $sunNum % 10;
                    $item['queen'] = intval($sunNum / 10);
                    $item['ure_finsh'] = $item['isFish'] ? -1 : $item['ure'];
                }
            }
        }
        $res['err'] = 0;
        return json($res);
    }

    /**
     *  开奖公告
     */
    public function get_kaijiang()
    {
        $tables = (new ExtShowList)->where('type', 1)->where('status',0)->select();
        $open = [];
        if(!empty($tables)){
            $tables = $tables->append(['expect_type'])->toArray();
            foreach ($tables as $key => $value) {
                try {
                    $name = ltrim($value['name'], '/');
                    $code_model = LotteryCommon::getModel($name, 'code');
                    $one = $code_model->order('id DESC')->find();

                    // 检查是否有开奖数据
                    if(!$one) {
                        // 如果没有开奖数据，创建默认数据
                        $one = [
                            'id' => 0,
                            'expect' => '暂无期号',
                            'code' => '0,0,0,0,0',
                            'time' => date('Y-m-d H:i:s')
                        ];
                    } else {
                        $one = $one->toArray();
                    }

                    $json_array = json_decode($one['code'], true);
                    if(!$json_array){
                        $json_array = explode(',', $one['code']);
                    }
                    $one['code'] = $json_array;
                    $one['name'] = $value['title'];
                    $url =  LotteryCommon::setUrl($name);
                    $one['url'] = $url[0];
                    $one['expect_type'] = $value['expect_type'];
                    $one['open'] = $url[1];
                    $one['link'] = $url[2];
                    $one['cz'] = $url[3];
                    array_push($open, $one);
                } catch (Exception $e) {
                    // 记录错误但继续处理其他彩种
                    error_log("开奖数据获取失败 - 彩种: {$value['name']}, 错误: " . $e->getMessage());
                    continue;
                }
            }
        }
        return collection($open);
    }

    /**彩种排序 */
    public function setSort($res, $data)
    {
        $sort = [SORT_DESC, SORT_ASC];
        $type = ['total_money', 'status', 'create_time', 'buyprecent','ure_finsh','record','ure'];
        /**排序类型 */
        $resType = isset($data['type']) ? in_array($data['type'], $type) : false;
        if(!$resType){
            $data['type'] = 'ure_finsh';
            $data['sort'] = 0;
        }

        $sortArr = [];
        $sortArr1 = [];
        // $sortArr2 = [];
        foreach ($res as $value) {
            $sortArr[] = $value[$data['type']];
            $sortArr1[] = $value['create_time'];
            // $sortArr2[]= $value['status'];
        }
        array_multisort($sortArr, $sort[$data['sort']],$sortArr1, SORT_DESC, $res);
        // array_multisort($sortArr, $sort[$data['sort']],$sortArr2, SORT_ASC,$sortArr1, SORT_DESC, $res);
        return $res;

    }

    public function getLotteryCommon($name, $user)
    {
        $cp_type = LotteryCommon::getCpType($name);
        if(session('?uid')){//设置浏览记录
            $user = User::get(['sid' => session('sid')]);
            if (!empty($user)) {
                $user = $user->toArray();
                $ext = checkExt(ucfirst($cp_type) . '_'.$name, $user['extname']);
                $user['extname'] = (new User)->setExtName(ucfirst($cp_type) . '_'.$name,$ext, $user['extname']);
            }
        }

        // 防护检查：确保user数组存在且rebate字段存在
        if (!is_array($user) || !isset($user['rebate'])) {
            return ['user_rebate' => 0, 'up_user_rebate' => 0];
        }

        // 处理rebate字段
        if (!is_array($user['rebate'])) {
            $user['rebate'] = json_decode($user['rebate'], true);
            // 如果JSON解码失败，设置默认值
            if (!is_array($user['rebate'])) {
                $user['rebate'] = [
                    'ssc' => 0,
                    'ks' => 0,
                    'syxw' => 0,
                    'pk10' => 0,
                    'pc28' => 0
                ];
            }
        }

        $user_rebate_array = $user['rebate'];
        $cp_type = LotteryCommon::getCpType($name);
        $user_rebate = isset($user_rebate_array[$cp_type]) ? $user_rebate_array[$cp_type] : 0;//玩家返点
        $up_user_rebate = 0;//上级返点

        // 防护检查：确保top_agents字段存在
        if (isset($user['top_agents']) && $user['top_agents']) {//判断存在上级时
            $user_top_agents = User::get($user['top_agents']);
            $up_user_rebate_array = $user_top_agents ? $user_top_agents['rebate'] : [];
            if (!is_array($up_user_rebate_array)) {
                $up_user_rebate_array = json_decode($up_user_rebate_array, true);
                // 如果JSON解码失败，设置为空数组
                if (!is_array($up_user_rebate_array)) {
                    $up_user_rebate_array = [];
                }
            }
            $up_user_rebate = (isset($up_user_rebate_array[$cp_type]) and $up_user_rebate_array[$cp_type]) ? $up_user_rebate_array[$cp_type] - $user_rebate : 0;
        }
        return ['user_rebate' => $user_rebate, 'up_user_rebate' => $up_user_rebate];
    }
}
