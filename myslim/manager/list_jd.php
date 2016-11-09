<?php
use Slim\Http\Request;
use Slim\Http\Response;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;

$app->group('/listjd', function () use ($app) {
    /**
     * @desc 是否结婚
     */
    $app->get('/s_marrid', function(Request $request, Response $response, $args) {
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
        $args['title'] = '朋友是否已婚,已婚查询,婚姻查询,婚姻经历查询';
        $args['keywords'] = '朋友是否已婚,已婚查询,婚姻查询,婚姻经历查询';
        $args['description'] = '有技术的便民查询工具,专业查询朋友是否已婚,已婚查询,婚姻查询,婚姻经历查询询,所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/listjd_marrid.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_cart_number');
    
    $app->post('/s_marrid', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['name']) || empty($params['numberic'])) {
                $result['status'] = -1;
                $result['msg'] = '姓名和身份证不能为空';
            } else {
                if (empty($_SESSION['user_id'])) {
                    $result['status'] = -1;
                    $result['msg'] = '请先登陆';
                    $response->getBody()->write(json_encode($result));
                    return $response->withHeader(
                        'Content-Type',
                        'application/json'
                        );
                }
    
                $insert_columns = array();
                $insert_columns['product_name'] = '是否已婚';
                $insert_columns['sales'] = '0.5';
                $insert_columns['add_time'] = time();
                $insert_columns['is_flag'] = 2;
                $insert_columns['user_id'] = $_SESSION['user_id'];
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
                $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                
                if ($money_result == false) {
                    $result['status'] = -1;
                    $result['msg'] = '用户余额不足';
                } else {
                    $url = 'https://way.jd.com/51daas/qryMultiple2Data?name='.$params['name'].'&cardNum='.$params['numberic'].'&appkey=' . JD_KEY;
                    $get_user_info = json_decode(file_get_contents($url), true);
                    $result['status'] = 0;
                    $result['result'] = baidu_curl_get($url);
                }
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('car_number');
})->add(AuthQuery::class);