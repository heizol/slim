<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
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
require_once ROOT_PATH . "lib/wxpay/lib/WxPay.Data.php";
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
        if (!empty($_SESSION['user_id'])) {
            $db = new CustomDb();
            $artistTable = new TableGateway('tools_user', $db->_adapter);
            $rowset = $artistTable->select(function (Select $select){
                $select->where(['id' => $_SESSION['user_id']])->order('id DESC');
            });
            $_user = $rowset->toArray();
            if (!empty($_user)) {
                $_SESSION['my_money'] = $_user[false]['my_money'];
            }
        }
        
        
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
                $insert_columns = array();
                $insert_columns['uid'] = empty($_SESSION['uid']) ? 0 : $_SESSION['uid'];
                $insert_columns['add_time'] = time();
                $insert_columns['message'] = $params['message'];
                OrderDDL::insertOrder('user_message', $insert_columns);
                $result['status'] = 1;
                $result['msg'] = '感谢您的留言，我们会尽快回复';
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('car_number');
    
    // 支付回掉
    $app->map(['get', 'post'], 'call_money_back', function(Request $request, Response $response, $args) {
	$params = AuthQuery::$queries;
	if (empty($params['transaction_id'])) {
            $status = 'FAIL';
            $info = '交易ID不存在';
        } else {
            $input = new WxPayOrderQuery();
            $input->SetTransaction_id($params['transaction_id']);
            $result = WxPayApi::orderQuery($input);
            if(array_key_exists("return_code", $result)
                && array_key_exists("result_code", $result)
                && $result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS")
            {
//                 $order_num = $result['out_trade_no'];
//                 $user_id = substr($order_num, 17);
//                 // 订单充值记录
//                 $insert_columns = array();
//                 $insert_columns['product_name'] = '预消费';
//                 $insert_columns['sales'] = '2.00';
//                 $insert_columns['add_time'] = time();
//                 $insert_columns['is_flag'] = 1;
//                 $insert_columns['user_id'] = $user_id;
//                 $insert_columns['order_id'] = $order_num;
//                 OrderDDL::insertOrder('tools_order', $insert_columns);
		        $status = 'SUCCESS';
                $info = 'OK';
            } else {
                $status = 'FAIL';
                $info = '交易失败';
            }
        }
        $xml = "<xml>
            <return_code><![CDATA[".$status."]]></return_code>
            <return_msg><![CDATA[".$info."]]></return_msg>
            </xml>";
        echo $xml;
        exit;
    });
    
    $app->get("get_wxpay", function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['order_num'])) {
                $result['status'] = -1;
                $result['msg'] = 'order_num is not null';
            } else {
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($params['order_num']);
                $wx_result = WxPayApi::orderQuery($input);
                if(array_key_exists("return_code", $wx_result)
                    && array_key_exists("result_code", $wx_result)
                    && $wx_result["return_code"] == "SUCCESS"
                    && $wx_result["result_code"] == "SUCCESS" && !empty($wx_result['transaction_id']))
                {
                    $order_num = $wx_result['out_trade_no'];
                    $user_id = substr($order_num, 17);
                    // 订单充值记录
                    $insert_columns = array();
                    $insert_columns['product_name'] = '预消费';
                    $insert_columns['sales'] = ($wx_result['total_fee'] / 100);
                    $insert_columns['add_time'] = time();
                    $insert_columns['is_flag'] = 1;
                    $insert_columns['user_id'] = $user_id;
                    $insert_columns['order_id'] = $order_num;
                    OrderDDL::insertOrder('tools_order', $insert_columns);
                    $result['status'] = 1;
                    $result['msg'] = 'SUCCESS';
                } else {
                    $result['status'] = -1;
                    $result['msg'] = 'error';
                }
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    });
    
    // 二维码图片
    $app->get('qrcode', function(Request $request, Response $response, $args) {
        $params = AuthQuery::$queries;
        require_once ROOT_PATH . 'lib/wxpay/example/phpqrcode/phpqrcode.php';
        $url = urldecode($_GET["data"]);
        QRcode::png($url);
        exit;
    });
    
    $app->get('pay_success', function(Request $request, Response $response, $args) {
        $args['route_name'] = '支付成功';
        $args['title'] = '车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号';
        $args['keywords'] = '车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号';
        $args['description'] = '有技术的便民查询工具,专业查询车辆尾号限行,车辆尾号限行查询,尾号限行,各城市尾号限行,杭州尾号,天津尾号,上海尾号,所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/pay_success.php', $args);
    });
})->add(AuthQuery::class);

// 聚合和百度过来的数据
require ROOT_PATH . 'manager/list.php';

// 京东万象过来的数据
require ROOT_PATH . 'manager/list_jd.php';

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
        if ($my_money <= 0 && $params['is_flag'] == 2 && $params['sales'] > 0) {
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
        $insert_r = $artistTable->insert($params);
	// 记录ID
        $log_id = $artistTable->getLastInsertValue();
	   if (!empty($log_id)) {
           $update_params = array();
	       $artistTable = new TableGateway('tools_user', $db->_adapter);
            if ($params['is_flag'] == 2) {
                // 消费时要扣除金额
                $update_params['my_money'] = $my_money - $params['sales'];
            } else if ($params['is_flag'] == 1) {
                $update_params['my_money'] = $my_money + $params['sales'];
            }
            $artistTable->update($update_params, ['id' => $_user[false]['id']]);
            $_SESSION['my_money'] = $update_params['my_money'];
            return true;
    	} else {
    		return false;
    	}
    }
}
