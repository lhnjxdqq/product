<?php
/**
 * App公共业务逻辑封装
 */
class   Api_Base_App {

    /**
     * 成功代码
     */
    const   CODE_SUCCESS    = 0;

    /**
     * 成功消息
     */
    const   MESSAGE_SUCCES  = 'OK';

    /**
     * 响应JSON
     *
     * @param   int     $code       代码
     * @param   string  $message    消息
     * @param   array   $data       数据
     */
    static  public  function responseJSON ($code = self::CODE_SUCCESS, $message = self::MESSAGE_SUCCES, $data = NULL) {

        header('Access-Control-Allow-Origin: ' . FRONTEND_DOMAIN);
        header('Access-Control-Allow-Headers: token,Content-Type');
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: text/json');
        $response   = array(
            'code'      => $code,
            'message'   => $message,
        );

        if (is_array($data)) {

            $response['data']   = $data;
        }

        echo    json_encode($response);
    }

    /**
     * 响应JSON 数据 成功
     *
     * @param   array   $data   数据
     */
    static  public  function reponseDataSuccess (array $data) {

        self::responseJSON(self::CODE_SUCCESS, self::MESSAGE_SUCCES, $data);
    }
}
