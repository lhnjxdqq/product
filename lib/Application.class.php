<?php
/**
 * 应用基础逻辑封装
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class   Application {

    /**
     * 包分割符
     */
    const   SPLITOR_AUTOLOAD_PACKAGE    = '_';

    /**
     * 类型文件扩展名
     */
    const   CLASS_EXTENDNAME            = '.class.php';

    /**
     * 未知错误代码
     */
    const   UNKNOWN_ERROR_CODE          = 100000000;

    /**
     * 未知错误信息
     */
    const   UNKNOWN_ERROR_MESSAGE       = '未知系统级别错误';

    /**
     * 错误日志地址
     */
    const   LOG_PATH                    = 'exception_%y%m%d.log';

    /**
     * 日志引擎
     */
    static  private $_log;

    /**
     * 初始化
     */
    public  static  function    initialize () {

        spl_autoload_register('self::_autoloadCallback');
        self::_setAPIExceptionHandler();
    }

    /**
     * 自动加载回调方法
     *
     * @param   string  $className  类型名
     * @return  mixed               类型文件返回值 (如果有的话)
     */
    private static  function _autoloadCallback ($className) {

        $fileName   = str_replace(self::SPLITOR_AUTOLOAD_PACKAGE, '/', $className) . self::CLASS_EXTENDNAME;
        $filePath   = LIB . $fileName;

        if (is_file($filePath)) {

            return  require_once $filePath;
        }

        $filePath   = MODULE . $fileName;

        if (is_file($filePath)) {

            return  require_once $filePath;
        }
    }

    /**
     * 只在通过接口访问的时候设置接口异常句柄
     */
    private static  function _setAPIExceptionHandler () {

        if (isset($_SERVER) && isset($_SERVER['DOCUMENT_ROOT']) && '' != $_SERVER['DOCUMENT_ROOT']) {

            set_exception_handler(array(__CLASS__, 'exceptionHandler'));
        }
    }

    /**
     * 异常句柄
     *
     * @param   Exception   $exception  未被捕获的异常
     * @return  null                    无返回
     */
    public  static  function exceptionHandler ($exception) {

        if ($exception instanceof Prompt) {

            $exception->display();

            return  ;
        }

        if ($exception instanceof ApplicationException) {

            self::outputException($exception);
            return  ;
        }

        if ($exception->getCode() === ErrorCode::get('system.client')) {

            header('HTTP/1.1 400 Client Error');
            die('UNKNOWN_ERROR_MESSAGE');

            return  ;
        }

        self::_log()->save(
            array(
                'script'        => $exception->getFile(),
                'line'          => $exception->getLine(),
                'trace'         => $exception->getTraceASString(),
                'request_uri'   => $_SERVER['REQUEST_URI'],
                'code'          => $exception->getCode(),
                'message'       => $exception->getMessage(),
            )
        );
        header('HTTP/1.1 500 Internal Server Error');
        die('UNKNOWN_ERROR_MESSAGE');
    }

    /**
     * 输出异常
     *
     * @param   ApplicationException    $exception  异常
     * @return  void                                空
     */
    private static  function outputException ($exception) {

        if ('json' == $_SERVER['HTTP_X_OUTPUT_FORMAT'] || 'JSON' == $_REQUEST['__output_format']) {

            $view   = array(
                'success'   => false,
                'msg'       => $exception->getMessage(),
                'code'      => $exception->getCode(),
                'data'      => $exception->getData()
            );

            echo    json_encode($view, JSON_UNESCAPED_UNICODE);

            return  ;
        }

        $viewData   = $exception->getData();
        $template   = isset($viewData['template'])
                    ? $viewData['template']
                    : ApplicationException::TEMPLATE_DEFAULT;
        Template::getInstance()
            ->assign($viewData)
            ->display($template);

        return  ;
    }

    /**
     * 获取log实例
     *
     * @return  LogWriter   log写入实例
     */
    private static  function _log () {

        if (!(self::$_log instanceof LogWriter)) {

            self::$_log = new LogWriter(
                array(
                    'path'  => LOG . self::LOG_PATH
                )
            );
        }

        return  self::$_log;
    }
}

