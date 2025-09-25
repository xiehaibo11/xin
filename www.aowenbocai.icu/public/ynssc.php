<?php
$url='http://api.b1api.com/api?p=json&t=yn5fc&token=DD95E9D7C9BDC671&limit=5';

$data=json_decode(file_get_contents($url),true);
$data=$data['data'];
$expect=$data[0]['expect'];

$opencode=$data[0]['opencode'];
$opentime=$data[0]['opentime'];
$result['result']['data']['preDrawIssue']=$expect;
$result['result']['data']['preDrawCode']=$opencode;
$result['result']['data']['preDrawTime']=$opentime;
echo json_encode($result);
?>