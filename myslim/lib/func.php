<?php
/**
 * public function
 */

function baidu_curl_get($url) {
    $ch = curl_init();
    $header = array(
        'apikey: 488cfb3427b29c1e82ab211c938e24d1',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);
    
   return json_decode($res, true);
}