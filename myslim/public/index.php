<?php
session_start();
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;

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
require_once ROOT_PATH . "lib/wxpay/lib/WxPay.Api.php";
require_once ROOT_PATH . 'lib/wxpay/lib/WxPay.Notify.php';

// system show
// $config['displayErrorDetails'] = true;
// $config['addContentLengthHeader'] = false;
// $config['determineRouteBeforeAppMiddleware'] = true;
$container = new Slim\Container;


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
    // 留言给我
    $app->get('more', function(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $route_name = $route->getName();
        
        
        $args['name'] = $route_name;
        $args['params'] = AuthQuery::$queries;
        $args['nameKey'] =  $request->getAttribute('csrf_name');
        $args['nameValue'] =  $request->getAttribute('csrf_value');
        $args['title'] = '想要其它查询工具？';
        $args['keywords'] = '查询工具，更多查询';
        $args['description'] = '有技术的便民查询工具,如果你想要其它工具，请联系我们,所产生数据都可以得到追踪记录';
        return $this->view->render($response, "/more.php", $args);
    })->add($app->getContainer()->get('csrf'))->setName('more');
    $app->post('more', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['message'])) {
                $result['status'] = -1;
                $result['msg'] = '留言内容不能为空';
            } else {
                
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('car_number');
    
    // 支付回掉
    $app->get('call_money_back', function(Request $request, Response $response, $args) {
        $params = AuthQuery::$queries;
        if (empty($params['transaction_id'])) {
            $args['title'] = '充值失败';
            $args['status'] = -1;
        } else {
            $input = new WxPayOrderQuery();
            $input->SetTransaction_id($params['transaction_id']);
            $result = WxPayApi::orderQuery($input);
            if(array_key_exists("return_code", $result)
                && array_key_exists("result_code", $result)
                && $result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS")
            {
                $order_num = $result['out_trade_no'];
                $user_id = substr($order_num, 17);
                // 订单充值记录
                $insert_columns = array();
                $insert_columns['product_name'] = '预消费';
                $insert_columns['sales'] = '2.00';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 1;
                $insert_columns['user_id'] = $user_id;
                OrderDDL::insertOrder('tools_order', $insert_columns);
                return true;
            }
            return false;
        }
    });
    
    // 二维码图片
    $app->get('qrcode', function(Request $request, Response $response, $args) {
        $params = AuthQuery::$queries;
        require_once ROOT_PATH . 'lib/wxpay/example/phpqrcode/phpqrcode.php';
        $url = urldecode($_GET["data"]);
        QRcode::png($url);
        exit;
    });
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
        $args['title'] = '车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号';
        $args['keywords'] = '车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号';
        $args['description'] = '有技术的便民查询工具,专业查询车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号,所产生数据都可以得到追踪记录';
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
                if (empty($_SESSION['user_id'])) {
                    return $response->withRedirect('/member/login');
                }
                
                $insert_columns = array();
                $insert_columns['product_name'] = '车辆尾号限行';
                $insert_columns['sales'] = '0.5';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                if ($money_result == false) {
                    $result['result'] = -1;
                    $result['msg'] = '用户余额不足';
                } else {
                    $url = 'http://apis.baidu.com/netpopo/vehiclelimit/query?city='. $params['city_name'] .'&date=' . $today_time;
                    $result = baidu_curl_get($url);
                }
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
		$args['keywords'] = 'IP地址查询,IP详细地址,ip具体地址,ip跟踪,ip找人';
		$args['description'] = '有技术的便民查询工具,专业查询IP地址查询,IP详细地址,ip具体地址,ip跟踪,ip找人,所产生数据都可以得到追踪记录';
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
                
                if (empty($_SESSION['user_id'])) {
                    return $response->withRedirect('/member/login');;
                }
                
                $insert_columns = array();
                $insert_columns['product_name'] = 'IP真实地址查询';
                $insert_columns['sales'] = '0.5';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                if ($money_result == false) {
                    $result['result'] = -1;
                    $result['msg'] = '用户余额不足';
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
        $args['keywords'] = '药品名称,药品信息,药品条形码,药品名称查询,药品信息查询,药品常规数据,药品用量';
        $args['description'] = '有技术的便民查询工具,专业查询药品名称,药品信息,药品条形码,药品名称查询,药品信息查询,药品常规数据,药品用量,所产生数据都可以得到追踪记录';
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
                if (empty($_SESSION['user_id'])) {
                    return $response->withRedirect('/member/login');
                }
                
                $insert_columns = array();
                $insert_columns['product_name'] = '药品查询';
                $insert_columns['sales'] = '0.5';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                if ($money_result == false) {
                    $result['result'] = -1;
                    $result['msg'] = '用户余额不足';
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
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    });
    
    /**
     * @desc 车架号查询
     */
    $app->get('/car_unno', function(Request $request, Response $response, $args) {
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
    
        $args['title'] = '车架号查询车辆配置信息';
        $args['keywords'] = '车架号,新车配置,二手车配置,车架号查询,新车配置查询,二手车配置查询,新车出厂配置,新车硬件配置';
        $args['description'] = '有技术的便民查询工具,专业查询车架号,新车配置,二手车配置,车架号查询,新车配置查询,二手车配置查询,新车出厂配置,新车硬件配置,所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/list_car_unno.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_car_unno');
    
    $app->post('/car_unno', function(Request $request, Response $response, $args) {
        $result = array();
        if (false === $request->getAttribute('csrf_status')) {
            $result['result'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            $car_unno = trim(htmlspecialchars($params['car_unno']));
            if (empty($car_unno) || strlen($car_unno) != 17) {
                $result['result'] = -1;
                $result['msg'] = '车架号长度不能为空或者不等于17位';
            } else {
                if (empty($_SESSION['user_id'])) {
                    return $response->withRedirect('/member/login');;
                }
                
                $insert_columns = array();
                $insert_columns['product_name'] = '车架号查询';
                $insert_columns['sales'] = '1';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                if ($money_result == false) {
                    $result['result'] = -1;
                    $result['msg'] = '用户余额不足';
                } else {
                    $url = 'http://getVIN.api.juhe.cn/CarManagerServer/getVINFormat?VIN=' . $car_unno . '&key=53da462c4b4f60837aa4dbabba950114';
                    $_temp_result = juhe_curl_get($url);
                    if ($_temp_result['error_code'] != 0) {
                        $result['result'] = -1;
                        $result['msg'] = $_temp_result['reason'] ;
                    } else {
                        $result['result'] = 1;
                        $result['msg'] = $_temp_body = $_temp_result['result']['body']['CARINFO'];
                    }
                }
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    });
    
    // 企业投资融资历史
    $app->get('/company', function(Request $request, Response $response, $args) {
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
    
        $args['title'] = '企业投资或融资历史记录';
        $args['keywords'] = '企业投资,企业融资,创业投资,融资记录,投资历史,融资历史,投资历史记录,融资历史记录';
        $args['description'] = '有技术的便民查询工具,专业查询企业投资,企业融资,创业投资,融资记录,投资历史,融资历史,投资历史记录,融资历史记录,所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/list_company_info.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_company');
    
    $app->post('/company', function(Request $request, Response $response, $args) {
        $result = array();
        if (false === $request->getAttribute('csrf_status')) {
            $result['result'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            $company_name = trim(htmlspecialchars($params['company_name']));
            if (empty($company_name)) {
                $result['result'] = -1;
                $result['msg'] = '企业名称不能为空';
            } else {
                if (empty($_SESSION['user_id'])) {
                    return $response->withRedirect('/member/login');;
                }
                
                $insert_columns = array();
                $insert_columns['product_name'] = '企业投资融资查询';
                $insert_columns['sales'] = '3';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                if ($money_result == false) {
                    $result['result'] = -1;
                    $result['msg'] = '用户余额不足';
                } else {
                    $url = 'http://apis.baidu.com/beijingprismcubetechnology/qmpapi/rongzibyname?company=' . $company_name;
                    $_temp_result = baidu_curl_get($url);
                    if ($_temp_result['error_code'] != 0 ) {
                        $result['result'] = -1;
                        $result['msg'] = $_temp_result['reason'] ;
                    } else if(!empty($_temp_result['errNum'])){
                        $result['result'] = -1;
                        $result['msg'] = '请联系系统管理员,error_code' . $_temp_result['errNum'];
                    }else {
                        $result['result'] = 1;
                        $result['msg'] = $_temp_result['result'];
                    }
                }
        }
    }
    $response->getBody()->write(json_encode($result));
    return $response->withHeader(
        'Content-Type',
        'application/json'
        );
    });
})->add(AuthQuery::class);

// 用户中心
require ROOT_PATH . 'manager/member.php';

$app->run();

/**
 * 解析用户参数
 * @author new
 */
class AuthQuery {
    public static $queries = array();
    public static $uid;
    public static $my_money;
    
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

/**
 * 写入订单表
 */
class OrderDDL {
    
    /**
     * @param string $table 订单表名称
     * @param array $columns 写入订单参数
     * @return boolean
     */
    static function insertOrder($table = 'tools_order', $params) {
        $db = new CustomDb();
        $artistTable = new TableGateway('tools_user', $db->_adapter);
        $rowset = $artistTable->select(function (Select $select) use ($params){
            $select->where(['id' => $params['user_id']])->order('id DESC');
        });
        $_user = $rowset->toArray();
        if (empty($_user)) {
            return false;
        }
        $my_money = $_user[false]['my_money'];
        
        // 金额不够
        if ($my_money <= 0) {
            return false;
        }
        
        // 金额不够本次消费
        if ($params['is_flag'] == 2 && $params['sales'] > $my_money) {
            return false;
        }
        
        // 消费后结果小于0
        $mins = $my_money - $params['sales'];
        if ($params['is_flag'] == 2 && $mins < 0) {
            return false;
        }
        
        $artistTable = new TableGateway($table, $db->_adapter);
        $artistTable->insert($params);
        // 记录ID
        $log_id = $artistTable->getLastInsertValue();
        $update_params = array();
        if ($params['is_flag'] == 2) {
            // 消费时要扣除金额
            $update_params['my_money'] = $my_money - $params['sales'];
        } else if ($params['is_flag'] == 1) {
            $update_params['my_money'] = $my_money + $params['sales'];
        }
        $artistTable->update($update_params, ['id' => $_user[false]['id']]);
        return true;
    }
}