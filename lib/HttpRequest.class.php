<?php
class HttpRequest {

    static private $_instance;
    static private $_url;

    static public function getInstance ($url) {

        return new self($url);
    }

    private function __construct($url) {

        self::$_url     = $url;
    }

    /**
     * GET 请求
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function get($params = array()) {

        return $this->request(self::$_url, 'GET', $params);
    }

    /**
     * POST请求
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function post($params = array()) {

        return self::request(self::$_url, 'POST', $params);
    }

    /**
     * 发起一个HTTP请求
     * @param $url
     * @param $method
     * @param $params
     * @return mixed
     */
    public function request($url, $method, $params) {

        switch ($method) {
            case 'GET':
                $url = $url . '?' . http_build_query($params);
                return $this->http($url, 'GET');
            default :
                $body = http_build_query($params);
                return $this->http($url, $method, $body);
        }
    }

    /**
     * HTTP请求 基于CURL
     * @param $url
     * @param $method
     * @param null $postfields
     * @param array $headers
     * @return mixed
     */
    public function http($url, $method, $postfields = null, $headers = array()) {

        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        if (version_compare(phpversion(), '5.4.0', '<')) {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        } else {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        if ("POST" === $method) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);

            }
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        $response = curl_exec($ci);
        if ( curl_errno($ci) ) {
            $error= 'curl http request error : curl_getinfo->'.curl_getinfo($ci).' curl_error : '.curl_error($ci);
            $info = print_r($error,true);
            throw new ApplicationException($info);
        }
        curl_close($ci);

        return $response;
    }
}