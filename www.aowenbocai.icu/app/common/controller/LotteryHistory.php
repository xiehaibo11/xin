<?php
namespace app\common\controller;

use app\admin\model\Ext;
use think\Controller;
use app\index\model\ExtShowList;
use app\lottery\controller\Lottery;

class LotteryHistory extends Controller
{
     /**开奖结果详情*/
    public function index($name)
    {
        $url_name = explode('_', $name);
        $name = $url_name[count($url_name)-1];
        $this->checkName($name);
        $lottery =  (new ExtShowList)->field('name,title,image')->where('type', 1)->where('status',0)->order('sort ASC')->select();
        $lottery = $lottery->toArray();
        foreach ($lottery as &$value) {
            $value['name'] = trim($value['name'], '/');
            $value['url'] = $this->orderSetUrl($value['name']);
            $value['selected'] =  0;
            if($value['name'] == $name){

                $value['selected'] =  1;
                $nowLottery = collection(['logo' => $value['image'], 'title' => $value['title']]);
            }
        }
        $lottery_type = LotteryCommon::getCpType($name);

        $lottery_setting = json_decode((LotteryCommon::getSetting($name))->getValue(LotteryCommon::getSettingValue($name, 'setting')),true);
        $today = date("Ymd");
        $issue = (new Lottery)->getExpect($name);
        if ($issue['err']) return $this->error($issue['msg']);
        $issue = $issue['data'];
        $fristExp = $today .sprintf("%0" . strlen($lottery_setting['startIssue']) . "d",1);
        $codeClass = LotteryCommon::getModel($name, 'code');
        $open = $codeClass->where('expect', '>=', $fristExp)->order('expect ASC')->column('expect,code');
        $lastIssue = $issue['lastIssue'];
        if(!array_key_exists("$lastIssue", $open)){
            $open[(string)$lastIssue] = '开奖中..';
            $news = '正,在,开,奖,中';
        }
        $issue['frist'] = $fristExp;

        $openTime = (new Lottery())->issueToTime($name, $issue['expect']);
        $openTime = date("Y-m-d H:i:s", $openTime + 60);

        $newCode = isset($news) ? explode(',',$news) : explode(',', $open[(string)$lastIssue]);
        $getnewcode = isset($news) ? 1 : 0;
        return $this->fetch('',['open' => collection($open), 'lottery' => collection($lottery), 'issue' => collection($issue),'expect' => $lastIssue,
        'code' => collection($newCode),'getnewcode' => $getnewcode,'today' => $today, 'openTime' => $openTime, 'typeName' => $lottery_type, 'lotteryInfo' => $nowLottery,
        'name' => $name]);
    }
    public function orderSetUrl($name)
    {
        $ext_info = Ext::where('name', $name)->find();
        if (!$ext_info) return $this->error('参数错误<03>');
        if ($ext_info['expect_type']) {//期号累加型彩种
            return  url("history/all_list", "name=" . $name);
        } else {
            return  url("history/index", "name=" . $name);
        }
    }
    
    public function historyCode($name)
    {
        $data = request()->get();
        $time = implode('', explode('-', $data['times']));
        $class = 'app\lottery\model\\'.$name.'\Plugin'.ucfirst($data['name']).'Code';
        $codeClass = new $class;
        $length = 2;
        $test = $codeClass->order('id DESC')->limit(1)->column('expect');
        if(!empty($test)){
            $length = mb_strlen(mb_substr($test[0], 8));
        }
        $str = '';
        for($i = 0; $i < $length; $i++){
            $str .= '0';
        }
        $open = $codeClass->where('expect', '>', $time.$str)->where('expect', '<', ($time + 1).$str)->order('expect ASC')->column('expect,code');
        return json(['err' => 0, 'data' => $open]);
    }

    public function all_list($name)
    {
        $url_name = explode('_', $name);
        $name = $url_name[count($url_name)-1];
        $this->checkName($name, 1);

        $lottery =  (new ExtShowList)->field('name,title,image')->where('type', 1)->where('status',0)->order('sort ASC')->select();
        $lottery = $lottery->toArray();
        foreach ($lottery as &$value) {
            $value['name'] = trim($value['name'], '/');
            $value['url'] = $this->orderSetUrl($value['name']);
            $value['selected'] =  0;
            if($value['name'] == $name){
                $value['selected'] =  1;
                $nowLottery = collection(['name' => $name, 'logo' => $value['image'], 'title' => $value['title']]);
            }
        }
        $issue = (new Lottery)->getExpect($name)['data'];
        $sale = intval((time() - strtotime(date("Y-m-d").$issue['startTime'])) / ($issue['timelong'] * 60));
        $issue['issue'] = $issue['expect'];
        $fristExp = $issue['expect'] - $sale;
        $codeClass = LotteryCommon::getModel($name, 'code');
        $openTime = date("Y-m-d H:i:s", $issue['end_time'] / 1000 + 60);

        $lottery_type = LotteryCommon::getCpType($name);
        return $this->fetch('',['lottery' => collection($lottery), 'issue' => collection($issue), 'openTime' => $openTime, 'lotteryInfo' => $nowLottery,'name' => $name,'typeName' => $lottery_type]);
    }

    /**
     * 检测彩种是否正确
     * $expect_type 期号类型  1位累加型
    */
    public function checkName($name, $expect_type = 0)
    {
        $ext_info = Ext::where('name', $name)->find();
        if (!$ext_info) return $this->error('参数错误<01>');
        if ($ext_info['expect_type'] != $expect_type) {
            if ($ext_info['expect_type']) {
                $this->redirect(url('all_list', 'name=' . $name));
            } else {
                $this->redirect(url('index', 'name=' . $name));
            }
        }
    }

    public function get_list($name = '', $last = '', $expect = '')
    {
        $codeClass = LotteryCommon::getModel($name, 'code');
        if($expect){
            $codeClass->where('expect', $expect);
        }
        $open = $codeClass->order('expect DESC')->field('expect,code,create_time')->paginate(14);
        $open = $open->toArray();
        if($last){
            $expectList = array_column($open['data'], 'expect');
            $exsit = array_search($last, $expectList);
            $cp_type = LotteryCommon::getCpType($name);
            if(getType($exsit) == 'boolean'){
                switch ($cp_type) {
                    case 'ssc':
                    case 'syxw':
                        $open['new'] = ['正','在','开','奖','中'];
                        break;
                    case 'ks':
                    case 'pc28':
                        $open['new'] = ['开','奖','中'];
                        break;
                    case 'pk10':
                        $open['new'] = ['正','在','开','奖','中','?','?','?','?','?'];
                        break;
                }
                $open['state'] = 1;
            }else{
                $open['new'] = explode(',', $open['data'][$exsit]['code']);
            }
        }
        $open['err'] = 0;
        return json($open);
    }
}




