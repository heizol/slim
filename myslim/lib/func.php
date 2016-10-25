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

function juhe_curl_get($url) {
    $ch = curl_init();
//     $header = array(
//         'apikey: 53da462c4b4f60837aa4dbabba950114',
//     );
//     // 添加apikey到header
//     curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    return json_decode($res, true);
}

function object_to_array($obj){
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($_arr as $key => $val){
        $val = (is_array($val)) || is_object($val) ? object_to_array($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}