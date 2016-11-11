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
    
    // 企业组织查询
    $app->get('/company_true', function(Request $request, Response $response, $args) {
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
        $args['title'] = '全国性民间组织查询';
        $args['keywords'] = '全国性民间组织查询,基金会查询,校友会查询,商会查询,社会团体查询,代表机构查询';
        $args['description'] = '有技术的便民查询工具,专业查询全国性民间组织,基金会查询,校友会查询,商会查询,社会团体查询,代表机构查询,所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/list_company_true.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_cart_number');
    
    $app->post('/company_true', function(Request $request, Response $response, $args) {
        if (false === $request->getAttribute('csrf_status')) {
            $result['status'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['company_name'])) {
                $result['result'] = -1;
                $result['msg'] = '组织名称不能为空';
            } else {
                if (empty($_SESSION['user_id'])) {
                    $result['result'] = -1;
                    $result['msg'] = '请先登陆';
                    $response->getBody()->write(json_encode($result));
                    return $response->withHeader(
                        'Content-Type',
                        'application/json'
                        );
                }
    
                $key_words = urlencode(urlencode($params['company_name']));
                $url = 'http://www.chinanpo.gov.cn/search/searchOrgList.do?action=searchOrgList&queryTypeRadio=1&orgName=' . $key_words;
                $s_result =file_get_contents($url);
                if ($s_result) {
                    $insert_columns = array();
                    $insert_columns['product_name'] = '全国性组织查询';
                    $insert_columns['sales'] = '0.5';
                    $insert_columns['add_time'] = time();
                    $insert_columns['is_flag'] = 2;
                    $insert_columns['user_id'] = $_SESSION['user_id'];
                    $product_id = date("mdHis");
                    $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                    $insert_columns['order_id'] = $order_num;
                    $money_result = OrderDDL::insertOrder('tools_order', $insert_columns);
                    
                    if ($money_result == false) {
                        $result['result'] = -1;
                        $result['msg'] = '用户余额不足';
                    } else {
                        $s_result = str_replace(array("\r\n", "\r", "\n", '             ', '    '), '', $s_result);
                        preg_match_all('/<span style=" COLOR:  #FF0000;">没有找到符合条件的社会组织！<\/span>/i', $s_result, $match_all);
                        $result['result'] = 1;
                        if (!empty($match_all[false][false])) {
                            $result['msg'] = '<tr><td>没有找到相关组织,说明该组织不存在</td></tr>';
                        } else {
                            preg_match_all('/<tr><td height="35" align="center" valign="middle" bgcolor="#FFFFFF">1<\/td>(.*?)<\/tr>/i', $s_result, $match_all);
                            $result['msg'] = $match_all[false][false];
                        }
                    }
                } else {
                    $result['result'] = -1;
                    $result['msg'] = '系统繁忙，请稍后重试，本次不扣费';
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