<?php
use Slim\Http\Request;
use Slim\Http\Response;

require ROOT_PATH . 'lib/dayu/TopSdk.php';

$app->group('/member', function () use ($app) {
    // 登录页
    $app->get('/login', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['route_name'] = $route_name;
        // CSRF token name and value
        $args['params'] = AuthQuery::$queries;
        $nameKey = $this->csrf->getTokenNameKey();
		$valueKey = $this->csrf->getTokenValueKey();

		// Fetch CSRF token name and value
		$name  = $request->getAttribute($nameKey);
		$value = $request->getAttribute($valueKey);
		$args['csrf_name_key']  = $nameKey;
		$args['csrf_value_key'] = $valueKey;
		
		$args['csrf_name'] = $name;
		$args['csrf_value'] = $value;
		
    
        $args['title'] = '登录中心--请登录';
        $args['keywords'] = '有技术的便民查询工具';
        $args['description'] = '有技术的便民查询工具,所产生数据都可以得到追踪记录';
        
        return $this->view->render($response, '/member_login.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('login');
    
    $app->post('/login', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $redis = new CustomRedis();
            $db = new CustomDb();
            $params = AuthQuery::$queries;
            if (empty($params['mobile'])) {
                $result['status'] = -1;
                $result['msg'] = '手机号不能为空';
            } else if (!preg_match('/^1(3|4|5|7|8){1}\d{9}$/i', $params['mobile'])){
                $result['status'] = -1;
                $result['msg'] = '手机号格式不正确';
            } else {
                $get_code = $params['mobile_code'];
                $redis_code = $redis->get('code_' . $params['mobile']);
                if (empty($redis_code)) {
                    $result['status'] = -1;
                    $result['msg'] = '验证码已经失效，请重新发送';
                } else if ($redis_code != $get_code){
                    $result['status'] = -1;
                    $result['msg'] = '验证码不正确，请重新输入';
                } else {
                    $sql = 'select * from tools_user where mobile = "'. $params['mobile'] .'"';
                    $_user = $db->GetOne($sql);
                    var_dump($_user);
                    if (empty($_user)) {
                        $insert_param = array();
                        $insert_param['mobile'] = $params['mobile'];
                        $insert_param['my_money'] = '0.00';
                        $insert_param['add_time'] = time();
                        $user_id = $db->create('tools_user', $insert_param);
                        var_dump($user_id);
                        echo 'helloworld';exit;
                        $my_money = '0.00';
                    } else {
                        $user_id = $_user['id'];
                        $my_money = $_user['my_money'];
                    }
                    
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['mobile'] = $params['mobile'];
                    $_SESSION['my_money'] = $my_money;
                    $result['status'] = -1;
                    $result['msg'] = '登录成功';
                }
            }
        }
         
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('login_post');
    
    $app->post('/set_code', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['mobile'])) {
                $result['status'] = -1;
                $result['msg'] = '手机号不能为空';
            } else if (!preg_match('/^1(3|4|5|7|8){1}\d{9}$/i', $params['mobile'])){
                $result['status'] = -1;
                $result['msg'] = '手机号格式不正确';
            } else {
                $redis = new CustomRedis();
                if (!empty($redis->get('code_' . $params['mobile']))) {
                    $result['status'] = -1;
                    $result['msg'] = '已发送，剩余有效期['.$redis->ttl('code_' . $params['mobile']).'秒]';
                } else {
                    $code = rand(1111, 9999);
                    // 发送短信
                    $m = new TopClient;
                    $m->appkey = MESSAGE_KEY;
                    $m->secretKey = MESSAGE_SECRET;
                    $m->format = MESSAGE_FORMAT;
                    $req = new AlibabaAliqinFcSmsNumSendRequest;
                    $req->setExtend("");
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("身份验证");
                    $req->setSmsParam("{\"code\":\"". $code ."\",\"product\":\"【有技术的便民查询】\"}");
                    $req->setRecNum($params['mobile']);
                    $req->setSmsTemplateCode("SMS_11320047");
                    $resp = $m->execute($req);
                    
                    if (!empty($resp)) {
                        $resp = object_to_array($resp);
                        if (!empty($resp['result']['success']) && $resp['result']['err_code'] == 0) {
                            $redis->setEx('code_' . $params['mobile'], 36400, $code);
                            $result['status'] = 1;
                            $result['msg'] = '已发送';
                        } else {
                            $result['status'] = -1;
                            $result['msg'] = json_encode($resp);
                        }
                    } else {
                        $result['status'] = -1;
                        $result['msg'] = '短信系统阻塞';
                    }
                }
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('car_number');
    
    // 我的订单页
    $app->get('/my_order', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
    
         
        return $this->view->render($response, "/myorder.php", $args);
    })->setName('myorder');
})->add(AuthQuery::class);