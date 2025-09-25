<?php
namespace app\guess\model;

use app\admin\model\BaseModel;
use app\common\model\GameMoneyHistory;
use think\Model;
use app\common\model\MoneyHistory;
use think\Validate;

class Guess extends BaseModel
{

    protected  $createTime = false;
    protected $updateTime = false;
    protected $type_array;

    protected function initialize()
    {
        $this->type_array = [
            '1' => '体育',
            '2' => '财经',
            '3' => '娱乐',
            '4' => '彩票'
        ];
        parent::initialize();
    }

    /**
     * 获取器
     */
    public function getAnswerAttr($value)
    {
        return $value == '' ? '未开奖' : $value;
    }

    /**
     * 获取器
     */
    public function getOptionsAttr($value, $data)
    {
        return $this->getOptionsInfoAttr($value, $data);
    }

    /**
     * 获取器
     */
    public function getTypeAttr($value)
    {
        return $this->type_array[$value];
    }

    /**
     * 添加数据
     * $data 数据中包含以下数据
     */
    public function add($data)
    {
        $validate = new Validate([
            'title|题目' => 'require'
        ]);
        $result = $validate->check($data);
        if (!$result) {
            return ["code" => 0, "msg" => $validate->getError()];
        }
        if (isset($data['id'])) {
            $res = $this->where('id', $data['id'])->update($data);
        } else {
            $res = $this->save($data);
        }
        if (!$res) {
            return ["code" => 0, "msg" => "提交失败"];
        }
        return ["code" => 1, "msg" => "提交成功"];
    }

    /**
     * 派奖
     */
    public function awards($guess_id, $post)
    {
        $info = $this->whereTime('open_time', '<', date('Y-m-d H:i:s'))->where(function($query){
            $query->where('answer', null)->whereOr('answer', '');
        })->find($guess_id);
        if (empty($info)) {
            return ["code" => 0, "msg" => "该竞猜不能派奖"];
        }
        $res = $this->save(['answer' => $post['answer']], ['id' => $guess_id]);
        if (!$res) {
            return ["code" => 0, "msg" => "开奖错误"];
        }
        $Model = new GuessBuy();
        $buy = $Model->where('guess_id', $guess_id)->where('status', 0)->select();
        if (empty($buy)) {
            return ["code" => 1, "msg" => "没有参与者了"];
        }
        foreach ($buy as $row) {
            $row['code'] = json_decode($row['code'],true);
            $money =  $this->isWin($info, $row['code'], $post['answer']);
            if ($money) {
                $status = 2;
                (new GameMoneyHistory())->write([
                    'userid' => $row['userid'],
                    'money' => $money,
                    'remark' => '参加' .$info['title']. '竞猜,赢取' . $money . '金豆',
                    'ext_name' => Request()->module()
                ]);//添加资金明细
                (new \app\common\model\UserAction())->write([
                    'userid' => $row['userid'],
                    'content' => '参加' .$info['title']. '竞猜,赢取' . $money . '金豆',
                    'ext_name' => Request()->module()
                ]);//添加会员活动
            } else {
                $money = 0;
                $status = 1;
            }
            $Model->save(['bonus' => $money, 'status' => $status], ['id' => $row['id']]);

        }
        return ["code" => 1, "msg" => "派奖成功"];
    }

    /**
     * 判断是否中奖
     */
    public function isWin($info, $code, $answer) {
        $money = 0;
        $option_answer = $info['options'];
        foreach ($code as $key => $v) {
            if ($key == $answer) {
                $money += $v * $option_answer[$key]['sp'];
            }
        }
        return $money;
    }

    /**
     * 获取器--获取选项数据
     */
    public function getOptionsInfoAttr($value, $data)
    {
        if (is_array($data['options'])) {
            $value = $data['options'];
        } else {
            $value = json_decode($data['options'], true);
        }
        foreach ($value as $key => $val) {
            $name = key($val);
            $options[$key]['title'] = $name;
            $options[$key]['sp'] = $val[$name];
        }
        return $options;
    }

    /**
     * 添加数据
     * $data 数据中包含以下数据
     * [
     *  'logo' => '问题logo'（有默认值）
     *  'title' => '问题题目'
     *  'option' => '问题选项json格式，其中包括了选项答案及赔率（下面具体介绍）'
     *  'basis' => '开奖依据'
     *  'start_time' => '开始投注时间'
     *  'end_time' => '结束投注时间'
     *  'open_time' => '开奖时间'
     *  'type' => '问题类型'
     *  'status' => '是否启用投注'（有默认值 默认值0启用投注）
     * ]
     * 有默认值的可以不用添加
     * option -- json格式
     * option = {
     *  '选项名' : '赔率'
     *  }
     * option -- 介绍完
     */
    public function addData($data)
    {
        return $this->insert($data);
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
    public function getPlanAttr($value, $data)
    {
        return $this->getOptionsInfoAttr($value, $data);
    }

}
