<?php
class Common_Api {

    /**
     * 鉴定外部请求 是否有权限使用API接口
     */
    static public function validate () {

        $clientIp       = Utility::getClientIp();
        $ipWhiteList    = Config::get('api|PHP', 'ip_white_list');
        if (!in_array($clientIp, $ipWhiteList)) {

            Utility::sendHttpStatus(403);
            echo 'ip is forbidden';
            exit;
        }
    }

    /**
     * 生成签名
     *
     * @param $params       加密参数
     * @return string|void  签名
     */
    static public function createSign ($params) {

        ksort($params);
        $result = '';
        foreach ($params as $k => $v) {

            $result .= $k . '=' . $v;
        }

        return  sha1($result);
    }
}