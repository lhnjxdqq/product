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
     * @param $app          键名为 app_id 和 app_key 的数组 ($appList['select'])
     * @param $signRand     随机字符串
     * @return string|void  签名
     */
    static public function sign ($app, $signRand) {

        if (!$app) {

            return;
        }
        $buff               = '';
        $app['signRand']   = $signRand;
        ksort($app);
        foreach ($app as $k => $v) {

            $buff .= $k . '=' . $v;
        }

        return  sha1($buff);
    }
}