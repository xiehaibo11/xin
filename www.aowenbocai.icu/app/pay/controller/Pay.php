<?php
namespace app\pay\controller;

use app\admin\model\ThirdPay;
use app\index\controller\Base;
use app\common\model\Recharge;
use core\Setting;
use think\Config;
use think\Session;
use think\Request;

class Pay extends Base
{
    /**
     * 订单确认页面
     * @param num int 金额
     * @param type int 类型  1 为充值金豆  2位充值钻石
     */
    public function index($num = 0, $type = 1)
    {
        $num = (float) $num;
        /**判断金额是否在后台配置金额中 */
        $setting = Setting::get([ 'diamonds_recharge']);
        $diamonds_list = json_decode($setting['diamonds_recharge'], true);
        if (!$diamonds_list) {
            return $this->error("网站还未设置充值数据");
        }
        $money = array_column($diamonds_list, 'money');
        if(!in_array($num, $money)){
            return $this->error("充值金额需是[".implode(',',$money)."]中的一个");
        }

        /**=========结束========== */
        $username = $this->user["username"] ? $this->user["username"] : $this->user["nickname"];
        $this->assign("money", $num);
        $this->assign("remark", $username);
        $this->assign("type", $type);
        return $this->fetch('index', ['title' => '支付']);
    }

    /**
     * 自定义第三方支付
    */
    public function payToThird($third_id, $total_amount, $third_type = '', $type)
    {
        $third_info = (new ThirdPay())->find($third_id);
        if (!$third_info) return $this->error("支付方式未开通");
        $setting = Setting::get(['other_pay']);
        $other_pay = json_decode($setting['other_pay'], true);
        $has = 0;
        foreach ($other_pay as $key => $value) {
            if ($value['name'] == 'third_pay') {
                foreach ($value['value'] as $row) {
                    if ($row['id'] == $third_id) {
                        $has = 1;
                    }
                }
            }
        }
        if (!$has) return $this->error("支付方式未开通");
        /**对金额与支付方式进行判断 */
        $data = $this->addRecharge('other_third', $total_amount, $type);
        if (!$data['code']) {
            return $data;
        }
        $data = $data['data'];
        $param = [
            'total_amount' => $total_amount,
            'subject' => '网站充值',
            'out_trade_no' => $data['order']
        ];
        if ($third_type !== '') $param['type'] = $third_type;
        return $this->buildRequestForm($third_info['post_url'], $third_info['pay_charset'], $param);

    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    private function buildRequestForm($url, $pay_charset, $param) {
        $sHtml = "<form id='alipaysubmit' name='thirdsubmit' action='".$url."?charset=".trim($pay_charset)."' method='POST'>";
        foreach ($param as $key => $val) {
            $val = str_replace("'","&apos;",$val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['thirdsubmit'].submit();</script>";
        return $sHtml;
    }

    public function payToAlipay($total_amount, $type)
    {
        /**对金额与支付方式进行判断 */
        $data = $this->setToPay('other_alipay', $total_amount, $type);
        \alipay\Wappay::pay([
            'subject' => $data['name'],
            'out_trade_no' => $data['order'],
            'total_amount' => $total_amount
        ]);
    }

    public function payToWei($total_amount = 100, $type)
    {
        /**对金额与支付方式进行判断 */
        $data = $this->setToPay('other_wx', $total_amount, $type);
         /**微信支付数据处理 */
         $params = [
            'body' => $data['name'],
            'out_trade_no' => $data['order'],
            'total_fee' => $total_amount * 100,
        ];
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($user_agent,'MicroMessenger') !== false){
            $result = \wxpay\JsapiPay::getParams($params,$this->user['wei']);
            return $this->fetch('pay', ['title' => '支付','data' => $result]);
        }else{
            $result = \wxpay\WapPay::getPayUrl($params);

            session('weipay', 1);
            return "{$result}";
            // header("location:{$result}");
        }
    }

    /**
     * 支付宝电脑端扫码支付或者登陆账户
     */
    public function pcAlipay($total_amount = 100)
    {
        /**对金额与支付方式进行判断 */
        $data = $this->setToPay('other_alipay', $total_amount);
        \alipay\Pagepay::pay([
            'subject' => $data['name'],
            'out_trade_no' => $data['order'],
            'total_amount' => $total_amount
        ]);
    }
    /**
     * 微信电脑端扫码支付或者登陆账户
     */
    public function saoMaWei($total_amount = 100)
    {
        /**对金额与支付方式进行判断 */
        $data = $this->setToPay('other_wx', $total_amount);
         /**微信支付数据处理 */
         $params = [
            'body' => $data['name'],
            'out_trade_no' => $data['order'],
            'total_fee' => $total_amount * 100,
            'product_id' => $this->user['id'].time()
        ];
        return \wxpay\NativePay::getPayImage($params);
    }

    /**
     * 微信电脑端扫码支付或者登陆账户
     */
    public function wxQRCodePay($total_amount = 100)
    {
        $qrcode = $this->saoMaWei($total_amount);
        preg_match ("/^<img.+src='(.+?)'.+\/>$/", $qrcode, $out);
        $qrcode = $out[1];
        $this->assign(['total_amount' => sprintf('%.2f', $total_amount), 'qrcode' => $qrcode]);
        return $this->fetch();
    }

    public function saoToPay($way, $total_amount, $type = 1, $info = '')
    {
        $payname = ['other_alipay', 'other_wx', 'pay_wx', 'pay_alipay', 'bank_pay'];
        $res = $this->setToPay($payname[$way], $total_amount, $type, $info);
        if (isset($res['code']) and !$res['code']) {
            return ['err' => 1, 'msg' => $res['msg']];
        }
        return ['err' => 0, 'msg' => '提交成功,等待管理员审核'];
    }

    /**
     * 支付方式
     */
    public function getPayName()
    {
        $payname = [
            'other_alipay' => '支付宝',
            'other_wx' => '微信',
            'other_pay' => '其他三方',
            'other_third' => '其他三方',
            'pay_wx' => '微信扫码',
            'pay_alipay' => '支付宝扫码',
            'bank_pay' => '银行卡转账',
        ];
        return $payname;
    }

    /**
     * 调用支付接口之前的判断
     * @param  string $way 支付方式
     * @param  int $total_amount 金额
     * @param  string $info 备注[ip,浏览器信息]
     */
    private function setToPay($way, $total_amount, $type = 1, $info = '')
    {
        $param = request()->param();
        $payname = $this->getPayName();
        /**判断金额是否在后台配置金额中 */
        $setting = Setting::get(['other_pay','sao_pay', 'diamonds_recharge', 'bank_open', 'bank_config']);
        
        /**判断后台是否开启支付方式 */
        $other_pay = json_decode($setting['other_pay'], true);

        $sao_pay = json_decode($setting['sao_pay'], true);
        $bank_config = $setting['bank_config'] ? json_decode($setting['bank_config'], true) : [];
        $has_way = 0;
        if($way == 'bank_pay') {//银行卡转账
            if ($setting['bank_open'] and isset($bank_config[$param['re_bank_suffix']])) {
                $has_way = 1;
                if (!isset($param['name']) || !$param['name'] || !isset($param['bank_name']) || !$param['bank_name']) {
                    return $this->error("请完善提交信息");
                }
                $info = '付款人姓名：' . $param['name'] . ', 付款人银行名称：' . $param['bank_name'];
            }
        }
        if ($way == 'other_alipay' || $way == 'other_wx') {
            foreach ($other_pay as $v) {
                if ($v['name'] == $way) {
                    $has_way = $v['value'];
                }
            }
        }
        if ($way == 'pay_alipay' || $way == 'pay_wx') {
            foreach ($sao_pay as $v) {
                if ($v['name'] == $way) {
                    $has_way = $v['value'];
                }
            }
        }
        if (!$has_way) {
            return $this->error("支付方式" . $payname[$way] . '未开通');
        }
        /**=========支付==结束========== */

        //$diamonds_list = json_decode($setting['diamonds_recharge'], true);
        // if (!$diamonds_list) {
        //     return $this->error("网站还未设置充值数据");
        // }
        // $money = array_column($diamonds_list, 'money');
        // if(!in_array($total_amount, $money)){
        //     return $this->error("充值金额需是[".implode(',',$money)."]中的一个");
        // }

        /**=========结束========== */

        $data = $this->addRecharge($way, $total_amount, $type, $info);
        if ($data['code']) {
            return $data['data'];
        }
        return $data;
    }

    private function addRecharge($way, $total_amount, $type, $info = "")
    {
        $payname = $this->getPayName();
        $total_amount = intval($total_amount);
        if($total_amount < 1){
            return $this->error("充值金额需是一个大于0的正整数");
        }
        $out_trade_no = $this->user['id'].'Re'.time();
        $subject = $payname[$way].'充值';
        $data = [
            'userid' => $this->user['id'],
            'order' => $out_trade_no,
            'money' => $total_amount,
            'name' => $subject,
            'type' => $type,
            'info' => $info ? $info : Request::instance()->ip(),
            'create_time' => date('Y-m-d H:i:s')
        ];
        $res = (new Recharge)->insert($data);
        if(!$res){
            $this->error('出错了');
        }
        return ['code' => 1, 'data' => $data];
    }

    public function getResult()
    {
        $res = session("?weipay") ? 1 : 0;
        if(!$res){
            $res = session('weipay') == 'used' ? 0 : 1;
            Session::delete('weipay');
        }
        return json(['err' => $res]);
    }

}
