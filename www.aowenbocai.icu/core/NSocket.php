<?php
namespace core;

class NSocket
{
    private static $link = [];

    private $url;
    private $userid;
    private $conf;

    /**
     * 配置链接信息
     * @param $conf = [ 'host' => 'http://127.0.0.1', 'port' => '8829', 'id' => 'sdl$RERDFasuihft37h', 'sid' => 'b9433d10-3565-11e7-806d-ef7453a91e13' ]; id 为服务端nodejs配置分配的id, sid 为服务端nodejs配置分配的使用者组
     */
    static function link($conf)
    {
        $api = new NSocket();
        $api->conf = $conf;
        return $api;
    }

    static function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
                CURLOPT_TIMEOUT        => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                //CURLOPT_HEADER => TRUE,
                CURLOPT_HTTPHEADER     => $header
        );

        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = json_encode($params); //$multi ? $params : http_build_query($params);
                //print_r($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        //if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }

    /**
     * 选择要发送的用的
     * @param string $socketid 用户的socketid
     * @return $this
     */
    public function user($socketid)
    {
        $this->socketid = $socketid;
        return $this;
    }

    /**
     * 调用前端配置的函数
     * @param string name 配置的方法名
     * @param array $data 参数
     * @return $this
     */
    public function emit($name, $data = [])
    {
        $params = http_build_query([
            'id' => $this->conf['id'],
            'sid' => $this->conf['sid'],
            'emit' => $name,
            'socketid' => $this->socketid
        ]);
        $url = $this->conf['host'] . ':' . $this->conf['port'] . '/api/?' . $params;
        //print_r($url);
        return self::http($url, $data, 'POST', ['Content-Type: application/json', 'Accept: application/json']);
    }

    public function getSid()
    {
        return $this->conf['sid'];
    }
}
//
// $conf = [
//     'host' => 'http://127.0.0.1',
//     'port' => '8829',
//     'id' => 'sdl$RERDFasuihft37h'
// ];
//
// $socketApi = NSocket::link($conf);
//
// $socketApi->user($order['userid'])->emit('chat add',[
//     'data' => [
//         'userid' => $this->user['id'],
//         'username' => $this->user['username'],
//         'photo' => $this->user['photo'],
//         'content' => removeXSS(I('post.content', '', false)),
//         'addtime' => date('Y-m-d H:i:s')
//     ],
//     'id'
