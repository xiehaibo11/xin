<?php
namespace app\guess\model;

use app\admin\model\BaseModel;
use app\common\model\GameMoneyHistory;
use think\Validate;
use think\Db;
use think\Model;
use app\index\model\User;

class GuessBuy extends BaseModel
{

    protected $updateTime = false;
    protected $resultSetType = 'collection';
    protected $status_array;

    protected function initialize()
    {
        parent::initialize();
        $this->status_array = [
            '0' => '未开奖',
            '1' => '未中奖',
            '2' => '已派奖',
            '7' => '系统撤单'
        ];
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value,$data)
    {
        return $value;
    }

    /**
     * 获取器 - status
     */
    public function getStatusTxtAttr($value,$data)
    {
        $value = $data['status'];
        if ( $value == 1) {
            $color = 'red';
        } else if ($value == 2) {
            $color = '#5cb85c';
        } else {
            $color = '#999';
        }
        $html = '<font style="color: ' . $color . ';">' .$this->status_array[$value]. '</font>';
        return $html;
    }

    /**
     * 获取器---获取总投注资金
     */
    public function getBettingMoneyAttr($value, $data)
    {
        $code = json_decode($data['code'],true);
        $money = 0;
        foreach ($code as $v) {
            $money += $v;
        }
        return $money;
    }

    /**
     * 获取器---获取奖金
     */
    public function getBonusTxtAttr($value, $data)
    {
        return $data['status'] != 0 ? $data['bonus'] : '未开奖';
    }

    /**
     * 获取器---获取投注信息
     */
    public function getCodeTxtAttr($value, $data)
    {
        $code = json_decode($data['code'],true);
        if (empty($code)) {
            return '没有投注内容';
        }
        $txt='';
        foreach ($code as $key => $v) {
            $txt .= $key . '->' . $v .' ';
        }
        return $txt;
    }

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['username'];
    }
    /**
     * 获取器 - username
     * @return json
     */
    public function getNicknameAttr($value, $data)
    {
        $user_info = Db::name('user')->find($data['userid']);
        return $user_info['nickname'];
    }
    /**
     * 获取器 - 竞猜题目
     */
    public function getGuessTitleAttr($value, $data)
    {
        $guess = Guess::get($data['guess_id']);
        return empty($guess)? '题目已删除' : $guess['title'];
    }

    /**
     * 撤单
     * $admin_do 管理员操作
     */
    public function backMoney($id='')
    {
        $buy = $this->where('status', 0)->find($id);
        (new GameMoneyHistory())->write([
            'userid' => $buy['userid'],
            'money' => $buy['betting_money'],
            'ext_name' => Request()->module(),
            'remark' => '参与的竞猜' . $buy['guess_title']. '撤单'
        ]);
        $res = $this->where('id', $buy['id'])->update([
            'status' => 7
        ]);
        if ($res) {
            return ['code' => 1];
        }
        return ['code' => 0];
    }

    /**
     * 获取器--获取选项数据
     */
    public function getOptionsInfoAttr($value, $data)
    {
        $value = json_decode($data['options'], true);
        foreach ($value as $key => $val) {
            $name = key($val);
            $options[$key]['title'] = $name;
            $options[$key]['sp'] = $val[$name];
        }
        return $options;
    }

    /**
     * 添加竞猜投注记录
     * @param array $data 投注数据
     * ----data数据结构----
     * [
     *  'userid' => '用户id',
     *  'guess_id' => '竞猜题目id',
     *  'code' => '投注内容',
     * ]
     * ---code----
     * code = [
     *  '选项名' => '竞猜金额'
     * ]
     */
    public function addData($data){
        /**验证数据格式 */
        $rules = [
            'userid' => "require|number|max:11",
            'guess_id' => "require|number|max:11",
            'code' => "require|array"
        ];
        $validate = new Validate($rules);
        if(!$validate->check($data)){
            $msg = $validate->getError();
            return ['err' => 8, 'msg' => $msg];
        }

        /**判断用户信息 */
        $user = (new User)->get($data['userid']);
        if(!$user){
            return ['err' => 1, 'msg' => '用户信息错误'];
        }
        $user = $user->toArray();

        /**判断该题目有没有过期及投注时间是否结束 */
        $guess = (new Guess)->get($data['guess_id']);
        if(!$guess){
            return ['err' => 2, 'msg' => '竞猜题目信息错误'];
        }
        $guess = $guess->toArray();
        $nowtime = date("Y-m-d H:i:s");
        if($guess['start_time'] > $nowtime){
            return ['err' => 5, 'msg' => '该竞猜题目还未开始竞猜操作'];
        }

        if($guess['end_time'] <= $nowtime){
            return ['err' => 6, 'msg' => '该竞猜题目已停止竞猜操作'];
        }

        if($guess['open_time'] < $nowtime){
            return ['err' => 7, 'msg' => '该竞猜题目已过期'];
        }

        /**========== 判断金额与余额是否合理============== */
        $money = 0;
        $codeinfo = [];
        foreach($data['code'] as $key => $value){
            $money += $value;
            $codeinfo[]= ['name' => $key, 'money' => $value];
        }

        $moneyData = [
            'userid' => $data['userid'],
            'money' => -$money,
            'ext_name' => 'guess',
            'remark' => '竞猜游戏'
        ];
        $moneyRes = (new GameMoneyHistory())->write($moneyData);

        if($moneyRes['code'] == 0){
            return ['err' => 3, 'msg' => $moneyRes['msg']];
        }

        /**========== 判断用户是否已对该竞猜进行投注============== */
        $buy = $this->where(['userid' => $data['userid'], 'guess_id' => $data['guess_id']])->find();
        if($buy){
            $buy = $buy->toArray();
            $before = json_decode($buy['code'], true);
            foreach($codeinfo as $value){
                if(array_key_exists($value['name'], $before)){
                    $before[$value['name']] += $value['money'];
                }else{
                    $before[$value['name']] = $value['money'];
                }
            }

            $_before  = json_encode($before, JSON_FORCE_OBJECT);
            $res = $this->save(['code' => $_before], ['id' => $buy['id']]);

            /**计算该单的投注信息及总金额 */
            $lotterySpend = 0;
            $codeinfo = [];
            foreach($before as $key => $value){
                $lotterySpend += intval($value);
                $codeinfo[]['name'] = ['name' => $key, 'money' => $value];
            }
        }else{
            $lotterySpend = $money;
            $data['code'] = json_encode($data['code'],JSON_FORCE_OBJECT);
            $res = $this->insert($data);
        }

        if($res){
            return ['err' => 0, 'msg' => '竞猜成功', 'after' => $moneyRes['afterMoney'], 'betting' => $codeinfo ,'money' => $lotterySpend];
        }

        return ['err' => 4, 'msg' =>'数据写入失败'];

    }

    /**
     * 获取列表
     * @param array $where 查询条件
     * $where 中可有以下几个条件
     *  start_time 开始时间(当天)
     *  end_time 结束时间(当天)
     *  open_time 开奖时间(当天)
     *  type 类型
     *  status 是否开启 （默认全部查询）
     *  selectType 查询方式（默认全部查询） 1 为翻页查询
     */
    public function getList($where = [])
    {
        if(!empty($where['start_time'])){
            $time = ['start_time' => ['between', $where['start_time'].",".$where['start_time']." 23:59:59"]];
            $this->where($time);
        }

        if(!empty($where['end_time'])){
            $time = ['end_time' => ['between', $where['end_time'].",".$where['end_time']." 23:59:59"]];
            $this->where($time);
        }

        if(!empty($where['open_time'])){
            $time = ['open_time' => ['between', $where['open_time'].",".$where['open_time']." 23:59:59"]];
            $this->where($time);
        }

        if(!empty($where['type'])){
            $this->where('type', $where['type']);
        }

        if(!empty($where['status'])){
            $this->where('status', $where['status']);
        }

        if(!empty($where['selectType']) && $where['selectType'] == 1){
            return $this->paginate(14);
        }else{
            return $this->select();
        }
    }

    /**
     * 删除数据
     * @param array $where 删除条件
     * 删除竞猜题目前，先将相应的竞猜记录删除，再删题目
     */
    public function deleteData($where){
        $ids = $this->where($where)->column("id");
        if($ids){
            $res = (new GuessBuy)->where(['guess_id' => ['in', $ids]])->delete();
            if($res){
                return $this->where($where)->delete();
            }
            return $res;
        }else{
            return $this->where($where)->delete();
        }
    }

    /**
     * 获取器--获取选项数据
     */
    public function getPlan($options)
    {
        return (new Guess)->getPlanAttr('', ['options' => $options]);
    }


    /**
     * 获取器--获取order_id
     */
    public function getOrderId($time, $id)
    {
        return date('Ymd', strtotime($time)) . $id;
    }

    /**
     * 获取器--获取总投注金额
     */
    public function getBeans($code)
    {
        $code = json_decode($code, true);

        $money = 0;
        foreach($code as $value){
            $money += $value;
        }
        return $money;
    }

    public function getRecordList($user_id) {
        $pageSize = 10;
        $list = $this->where('userid', $user_id)->order(['create_time' => 'desc'])->paginate($pageSize);
        $list = $list->toArray();
        $data = [];
        foreach ($list['data'] as &$v) {
            $new = [];
            $guess = Guess::get($v['guess_id']);
            $new['id'] = $guess['id'];
            $new['title'] = $guess['title'];
            $new['img'] = $guess['logo'];
            $new['end_time'] = $guess['end_time'];
            $new['basis'] = $guess['basis'];
            $new['plan'] = $this->getPlan($guess['options']);
            $new['code'] = ($guess['answer'] == '' || $guess['answer'] == null) ? -1 : $guess['answer'];
            $new['order_id'] = $this->getOrderId($v['create_time'], $v['id']);
            $new['state'] = $v['status'];
            $new['create_time'] = $v['create_time'];
            $new['order_cont'] = json_decode($v['code'], true);
            $new['beans'] = $this->getBeans($v['code']);
            $new['gain'] = $v['bonus'];
            array_push($data, $new);
        }
        $list['data'] = $data;
        return $list;
    }
}
