<?php
use Slim\Http\Request;
use Slim\Http\Response;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;

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
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
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
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
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
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
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
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
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
                $product_id = date("mdHis");
                $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
                $insert_columns['order_id'] = $order_num;
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
            
    // 失信黑名单
    $app->get('/black_man', function(Request $request, Response $response, $args) {
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
    
        $args['title'] = '失信黑名单';
        $args['keywords'] = '失信黑名单查询,个人黑名单查询,企业黑名单查询,失信记录,失信报告';
        $args['description'] = '有技术的便民查询工具,专业查询国内最大的信用黑名单数据库提供企业和个人失信、网贷逾期黑名单查询(超过一千万条信贷失信记录),所产生数据都可以得到追踪记录';
        return $this->view->render($response, '/list_black_man.php', $args);
    })->add($app->getContainer()->get('csrf'))->setName('list_ip');
    
    $app->post('/black_man', function(Request $request, Response $response, $args) {
        $result = array();
        if (false === $request->getAttribute('csrf_status')) {
            $result['result'] = -1;
            $result['msg'] = 'csrf faild';
        }else{
            $params = AuthQuery::$queries;
            if (empty($params['name']) || empty($params['number']) || empty($params['s_type'])) {
                $result['result'] = -1;
                $result['msg'] = '各个数据不能为空';
            } else {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://e.apix.cn/apixcredit/blacklist/dishonest?type=".$params['s_type']."&name=".$params['name']."&cardno=".$params['number'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "apix-key: dd55eefa649049316619fb5fa7d152d5",
                        "content-type: application/json"
                    ),
                ));
                $result['result'] = -1;
                $get_result = curl_exec($curl);
                $get_result = json_decode($get_result, true);
                if (!empty($get_result)) {
                    if ($get_result['code'] == 1) {
                        $result['msg'] = '没有失信数据';
                    } else if ($get_result['code'] == 0) {
                        $result['msg'] = '查询成功';
                        $result['result'] = 1;
                        if (!empty($get_result['data'])) {
                            $result['data'] = $get_result['data'];
                        }
                    } else if ($get_result['code'] == 101) {
                        $result['msg'] = '身份证号不存在';
                        
                    } else if ($get_result['code'] == 104) {
                        $result['msg'] = 'URL参数错误';
                        
                    } else {
                        $request['msg'] = $get_result['code'];
                    }
                }
                $err = curl_error($curl);
                if (!empty($err)) {
                    $request['msg'] = $err;
                }
            }
        }
        $response->getBody()->write(json_encode($result));
        return $response->withHeader(
            'Content-Type',
            'application/json'
            );
    })->setName('list_black_man');
})->add(AuthQuery::class);