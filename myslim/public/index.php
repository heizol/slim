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

$app = new Slim\App($container);
$app->add(new Slim\Csrf\Guard);
	
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
    $app->get('/login', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
        
        
        return $this->view->render($response, "/login.php", $args);
    })->setName('login');
    
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
    $app->get('/car_number', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
        
        return $this->view->render($response, '/list_cart_number.php', $args);
    })->setName('list_cart_number');
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