<?php

/**
 *  通过URL获取页面信息
 * @param $url  地址
 * @return mixed  返回页面信息
 */
function get_url($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);  //设置访问的url地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
    $result =  curl_exec($ch);
    curl_close ($ch);
    return $result;
}
$url='http://kaijiang588.com/api?token=d1287633f0a0c8d0&code=heineione&rows=5&format=json';

$data=json_decode(get_url($url),true);

$data=$data['data'];
$expect=substr($data[0]['expect'],0,8).substr($data[0]['expect'],9,3);

$opencode=$data[0]['opencode'];
$opentime=$data[0]['opentime'];
$result['result']['data']['preDrawIssue']=$expect;
$result['result']['data']['preDrawCode']=$opencode;
$result['result']['data']['preDrawTime']=$opentime;
echo json_encode($result);
?>