<?php
require_once dirname(__FILE__) . '/../../api.init.inc.php';

$rawContent     = file_get_contents("php://input");
$rawParams      = json_decode($rawContent, true);

if (!$rawParams) {

    Api_Base_App::responseJSON(
        ErrorCode::get('application.no_raw_content'),
        'raw content is error'
    );
    exit;
}

$apiWhiteList   = Config::get('auth|PHP', 'white_list_api');
$hostWhiteList  = Config::get('auth|PHP', 'white_list_host');
$ip             = $_SERVER['REMOTE_ADDR'];
$data           = array();
// print_r( $rawParams );
foreach ($rawParams as $func => $paramsList) {

    // 非api白名单方法
    if (!in_array($func, $apiWhiteList) || !in_array($ip, $hostWhiteList) ) {

        Api_Base_App::responseJSON(
            ErrorCode::get('application.auth_validate_failure'),
            '需要登录'
        );
        exit;
    }

    list($controller, $method)  = explode('.', $func);
    $class                      = 'Api_Controller_' . $controller;

    if (!class_exists($class)) {

        Api_Base_App::responseJSON(
            ErrorCode::get('application.class_not_exists'),
            $controller . ' class is not exists'
        );
        exit;
    }

    if (!method_exists($class, $method) || !is_callable(array($class, $method))) {

        Api_Base_App::responseJSON(
            ErrorCode::get('application.method_not_exists'),
            $controller . '::' . $method . ' method is not exists'
        );
        exit;
    }

    try {

        $data[$func]    = $class::$method($paramsList);
    } catch (Exception $e) {

        $data[$func]    = array(
            'code'      => $e->getCode(),
            'message'   => $e->getMessage(),
        );

        if (is_callable(array($e, 'getData'))) {

            $data[$func]['data']    = $e->getData();
        }
    }
}

Api_Base_App::reponseDataSuccess($data);
