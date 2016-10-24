<?php
session_start();
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */
define('ROOT_PATH', dirname(__FILE__) . '/../');

require ROOT_PATH . 'vendor/autoload.php';
require ROOT_PATH . 'lib/config.php';
require ROOT_PATH . 'lib/func.php';
require ROOT_PATH . 'lib/redis.php';
require ROOT_PATH . 'lib/mysql.php';


// system show
// $config['displayErrorDetails'] = true;
// $config['addContentLengthHeader'] = false;
// $config['determineRouteBeforeAppMiddleware'] = true;
$container = new Slim\Container;

// redis register
$container['redis'] = function ($c) {
    if (!$c->redis) {
        $c->redis = new CustomRedis();
    }
};
// db
$container['db'] = function ($c) {
    if (!$c->db) {
        $c->db = new CustomDb();
    }
};
// templates
$container['view'] = function ($container) {
    return new Slim\Views\PhpRenderer(TEMPLATE_ROOT);
};

$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
	$guard->setFailureCallable(function ($request, $response, $next) {
		$request = $request->withAttribute("csrf_status", false);
		return $next($request, $response);
	});
	return $guard;
};
$app = new Slim\App($container);

	
$app->group('/', function () use ($app) {
    // 首页
    $app->get('', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
        
        return $this->view->render($response, "/index.php", $args);
    })->setName('index');
    // 登录页
    $app->post('/login', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
    
         
        return $response->withStatus(200)->withHeader('Location', '/myorder');
    })->setName('login_post');
    // 我的订单页
    $app->get('/myorder', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
    
         
        return $this->view->render($response, "/myorder.php", $args);
    })->setName('myorder');
    
})->add(AuthQuery::class);

// add other group list
$app->group('/list', function () use ($app) {
    /**
     * @desc 限行查询
     */
    $app->get('/car_number', function(Request $request, Response $response, $args) {
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
        $args['title'] = '车辆尾号限行查询';
        
        return $this->view->render($response, '/list_car_number.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_cart_number');
    
    $app->post('/car_number', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            $today_time = date("Y-m-d", strtotime($params['today_time']));
            if (empty($params['city_name']) || empty($params['today_time']) || $today_time != $params['today_time']) {
               $result['status'] = -1;
               $result['msg'] = '城市名称和时间不能为空';
            } else {
                $url = 'http://apis.baidu.com/netpopo/vehiclelimit/query?city='. $params['city_name'] .'&date=' . $today_time;
                $result = baidu_curl_get($url);
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('car_number');
    
    
    // ip
    $app->get('/ip', function(Request $request, Response $response, $args) {
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
		
		$args['title'] = 'IP地址查询';
        return $this->view->render($response, '/list_ip.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_ip');
    // 查询ip 
    $app->post('/ip', function(Request $request, Response $response, $args) {
        $result = array();
        if (false === $request->getAttribute('csrf_status')) {
            $result['result'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            $result['result'] = 1;
            $result['msg'] = 'ok';
            $reg_ip = "/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/";
            if (empty($params['ip_val']) || ! preg_match($reg_ip, $params['ip_val'])) {
                $result['result'] = -1;
                $result['msg'] = 'ip格式不正确，目前仅支持ipv4';
            } else {
                $url = 'http://apis.baidu.com/apistore/iplookup/iplookup_paid?ip=' . $params['ip_val'];
                $msg = baidu_curl_get($url);
                if (!empty($msg['retData'])) {
                    $result['ip'] = $msg['retData']['ip'];
                    $result['country'] = $msg['retData']['country'];
                    $result['province'] = $msg['retData']['province'];
                    $result['city'] = $msg['retData']['city'];
                    $result['district'] = $msg['retData']['district'];
                    $result['carrier'] = $msg['retData']['carrier'];
                }
            }
        }
		$response->getBody()->write(json_encode($result));
		return $response->withHeader(
			'Content-Type',
			'application/json'
		);
    })->setName('post_ip');
    
    /**
     * @desc 药品查询
     */
    $app->get('/drugs', function(Request $request, Response $response, $args) {
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
    
        $args['title'] = '药品信息查询';
        return $this->view->render($response, '/list_drugs.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_drugs');
    
    $app->post('/drugs', function(Request $request, Response $response, $args) {
        $result = array();
        if (false === $request->getAttribute('csrf_status')) {
            $result['result'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            $name = trim(htmlspecialchars($params['name']));
            $numberic = trim(htmlspecialchars($params['numberic']));
            if (empty($name) && empty($numberic)) {
                $result['result'] = -1;
                $result['msg'] = '药品名称或者条形码都不能为空';
            } else {
                if (!empty($name)) {
                    $url = 'http://apis.baidu.com/tngou/drug/name?name=' . $name;
                    $msg = baidu_curl_get($url);
                } else if (!empty($numberic)) {
                    $url = 'http://apis.baidu.com/tngou/drug/code?code=' . $numberic;
                    $msg = baidu_curl_get($url);
                }
                $result['result'] = 1;
                $result['msg'] = $msg;
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    });
})->add(AuthQuery::class);

$app->run();

class AuthQuery {
    public static $queries = array();
    
    public function __invoke($request, $response, $next) {
        $uri = $request->getUri();
        $query = $uri->getQuery();
        if ($query) {
            parse_str($query, $params);
        }
        $queryQarams = array();
        if (!empty($_POST)) {
            $params = array_merge($params, $_POST);
        }
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $queryQarams[trim(htmlentities($key))] = trim(htmlspecialchars($val));
            }
        }
        
        AuthQuery::$queries = $queryQarams;
        return $next($request, $response);
    }
}