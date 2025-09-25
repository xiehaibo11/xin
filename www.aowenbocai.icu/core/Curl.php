<?php
namespace core;

class Curl
{
    static public function get($url, $type = 'json'){ 
        $ch = curl_init(); 
        $timeout = 10; 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        
        $contents = trim(curl_exec($ch)); 
        curl_close($ch);
        if ($type == 'json') return json_decode($contents, true);
        return $contents;
    }
}
