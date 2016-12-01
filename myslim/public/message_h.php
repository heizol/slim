<?php
/**
* @desc  得到已卖数据
*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
define('ROOT_PATH', dirname(__FILE__) . '/../');
require ROOT_PATH . 'lib/config.php';
require  ROOT_PATH . 'lib/dayu/TopSdk.php';
require  ROOT_PATH . 'lib/redis.php';
$url = "http://www.51duoying.com/xintuo/list";
//初始化
$ch = curl_init();
//设置选项，包括URL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

//执行并获取HTML文档内容
$_html_page = curl_exec($ch);
//释放curl句柄
if (!empty($_html_page)) {
    $_redis = new CustomRedis();
    $_html_page = str_replace("\r\n", "", $_html_page);
    $_html_page = str_replace("\t", "", $_html_page);
    preg_match_all('/<li class="item"><div class="case">(.*?)<\/div><\/li>/i', $_html_page, $match_all);
    
    if (!empty($match_all[true])) {
        foreach ($match_all[true] as $key => $info) {
            $_temp_match = array();
            preg_match_all('/<div class="circle_num hide">61<\/div>/i', $info, $_temp_match);
            print_r($_temp_match);
            if (!empty($_temp_match[false][false])) {
                echo 'helloworld';
                preg_match_all('/信托•理财通(.*?)期/i', $info, $_temp_match);
                if (empty($_temp_match[true])) {
                    continue;
                }
                $_get_key = $_redis->get('key_' . $_temp_match[true][false] . '_step-1');
                if (empty($_get_key)) {
                    $_redis->set('key_' . $_temp_match[true][false] . '_step-1', 1);
                    // send msg
                    $c = new TopClient;
                    $c->appkey = '23397317';
                    $c->secretKey = 'cd513937b03001d73d16cdfb87d68aef';
                    $req = new AlibabaAliqinFcSmsNumSendRequest;
                    $req->setExtend("123456");
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("猜猜我是谁");
                    $req->setSmsParam("{\"code\":\"60%\",\"name\":\"信托.理财通".$_temp_match[true][false]."期\"}");
                    $req->setRecNum("18936309997");
                    $req->setSmsTemplateCode("SMS_31755010");
                    $resp = $c->execute($req);
                }
            }  
            preg_match_all('/<div class="circle_num hide">95<\/div>/i', $info, $_temp_match);
            if (!empty($_temp_match[false][false])) {
                preg_match_all('/信托•理财通(.*?)期/i', $info, $_temp_match);
                if (empty($_temp_match[true])) {
                    continue;
                }
                $_get_key = $_redis->get('key_' . $_temp_match[true][false] . '_step-2');
                if (empty($_get_key)) {
                    $_redis->set('key_' . $_temp_match[true][false] . '_step-2', 1);
                    // send msg
                    $c = new TopClient;
                    $c->appkey = '23397317';
                    $c->secretKey = 'cd513937b03001d73d16cdfb87d68aef';
                    $req = new AlibabaAliqinFcSmsNumSendRequest;
                    $req->setExtend("123456");
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("猜猜我是谁");
                    $req->setSmsParam("{\"code\":\"95%\",\"name\":\"信托.理财通".$_temp_match[true][false]."期\"}");
                    $req->setRecNum("18516235282");
                    $req->setSmsTemplateCode("SMS_31755010");
                    $resp = $c->execute($req);
                }
            }
            
            preg_match_all('/<div class="circle_num hide">98<\/div>/i', $info, $_temp_match);
            if (!empty($_temp_match[false][false])) {
                preg_match_all('/信托•理财通(.*?)期/i', $info, $_temp_match);
                if (empty($_temp_match[true])) {
                    continue;
                }
                $_get_key = $_redis->get('key_' . $_temp_match[true][false] . '_step-3');
                if (empty($_get_key)) {
                    $_redis->set('key_' . $_temp_match[true][false] . '_step-3', 1);
                    // send msg
                    $c = new TopClient;
                    $c->appkey = '23397317';
                    $c->secretKey = 'cd513937b03001d73d16cdfb87d68aef';
                    $req = new AlibabaAliqinFcSmsNumSendRequest;
                    $req->setExtend("123456");
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("猜猜我是谁");
                    $req->setSmsParam("{\"code\":\"98%\",\"name\":\"信托.理财通".$_temp_match[true][false]."期\"}");
                    $req->setRecNum("18516235282");
                    $req->setSmsTemplateCode("SMS_31755010");
                    $resp = $c->execute($req);
                }
            }
            
            preg_match_all('/<div class="circle_num hide">99<\/div>/i', $info, $_temp_match);
            if (!empty($_temp_match[false][false])) {
                preg_match_all('/信托•理财通(.*?)期/i', $info, $_temp_match);
                
                if (empty($_temp_match[true])) {
                    continue;
                }
                $_get_key = $_redis->get('key_' . $_temp_match[true][false] . '_step-4');
                if (empty($_get_key)) {
                    $_redis->set('key_' . $_temp_match[true][false] . '_step-4', 1);
                    // send msg
                    $c = new TopClient;
                    $c->appkey = '23397317';
                    $c->secretKey = 'cd513937b03001d73d16cdfb87d68aef';
                    $req = new AlibabaAliqinFcSmsNumSendRequest;
                    $req->setExtend("123456");
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("猜猜我是谁");
                    $req->setSmsParam("{\"code\":\"99%\",\"name\":\"信托.理财通".$_temp_match[true][false]."期\"}");
                    $req->setRecNum("18516235282");
                    $req->setSmsTemplateCode("SMS_31755010");
                    $resp = $c->execute($req);
                }
            }
        }
    }
}
exit;
$_redis = new Redis();
var_dump($_redis);
echo 'hello';
//phpinfo();exit;
// $ch = curl_init();
// $url = 'http://apis.baidu.com/apistore/point/search?keyWord=洗澡&cityName=上海&number=10&page=1&output=json';
// $header = array(
//     'apikey: 488cfb3427b29c1e82ab211c938e24d1',
// );
// // 添加apikey到header
// curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// // 执行HTTP请求
// curl_setopt($ch , CURLOPT_URL , $url);
// $res = curl_exec($ch);

// print_r(json_decode($res, true));exit;

$ch = curl_init();
$url = 'http://apis.baidu.com/geekery/music/query?s=醉罢挥毫弄墨&size=10&page=1';
$header = array(
    'apikey: 488cfb3427b29c1e82ab211c938e24d1',
);
// 添加apikey到header
curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 执行HTTP请求
curl_setopt($ch , CURLOPT_URL , $url);
$res = curl_exec($ch);

print_r(json_decode($res, true));exit;
?>
