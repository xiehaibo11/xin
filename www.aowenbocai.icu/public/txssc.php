<?php $url="http://off0.com/list";
$content=file_get_contents($url);

$content=json_decode($content,true);
$data=$content[0];
if($data)
{
$ress['preDrawIssue']='20'.$data['issue'];
$ress['preDrawCode']=$data['result'];
}
$res['result']['data']=$ress;
echo json_encode($res);
?>