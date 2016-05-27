<?php
/**
 * 页码逻辑封装
 *
 * @author  yaoxiaowei
 */

class   PageList {

    /**
     * 总数配置名
     */
    const   OPT_TOTAL           = 'total';

    /**
     * 每页显示数量配置名
     */
    const   OPT_PERPAGE         = 'perpage';

    /**
     * 当前页数配置名
     */
    const   OPT_CURRENT         = 'current';

    /**
     * 页码参数配置名
     */
    const   OPT_PAGE_PARAM      = 'param_page';

    /**
     * 页码变量配置名
     */
    const   OPT_PAGE_VAR        = 'page_var';

    /**
     * 地址配置名
     */
    const   OPT_URL             = 'url';

    /**
     * 变量配置名
     */
    const   OPT_VAR             = 'var';

    /**
     * 默认每页显示数量
     */
    const   DEFAULT_PERPAGE     = 20;

    /**
     * 默认当前页数
     */
    const   DEFAULT_CURRENT     = 1;

    /**
     * 默认页码参数
     */
    const   DEFAULT_PAGE_PARAM  = 'page';

    /**
     * 默认页码变量
     */
    const   DEFAULT_PAGE_VAR    = '_GET';

    /**
     * 默认总数
     */
    const   DEFAULT_TOTAL       = 0;

    /**
     * 参数
     *
     * @var     array
     */
    private $_options   = array();

    /**
     * 获取偏移量
     *
     * @access  public
     * @return  int     偏移量
     */
    public  function getOffset () {

        $currentPage    = $this->_getCurrentPage();

        return          ($currentPage - 1) * $this->_getNumberPerpage();
    }

    /**
     * 构造函数
     *
     * @param   array   $options    配置参数
     */
    public  function __construct (array $options = array()) {

        $this->setOptionMulti($options);
    }

    /**
     * 设置参数
     *
     * @param   string  $name   参数名
     * @param   mixed   $value  参数值
     */
    public  function setOption ($name, $value) {

        $this->_options[$name]  = $value;
    }

    /**
     * 设置多项参数
     *
     * @param   array   $options    参数名=>参数值 一组参数
     */
    public  function setOptionMulti ($options) {

        foreach ($options as $name => $value) {

            $this->_options[$name]  = $value;
        }
    }

    /**
     * 获取要显示的数据
     *
     * @return  array   要显示的数据
     */
    public  function getViewData () {

        return  $this->_options + array(
            'max_page'              => $this->_getMaxPage(),
            'offset'                => $this->getOffset(),
            self::OPT_TOTAL         => $this->_getTotal(),
            self::OPT_PERPAGE       => $this->_getNumberPerpage(),
            self::OPT_CURRENT       => $this->_getCurrentPage(),
            self::OPT_URL           => $this->_getCurrentURL(),
            self::OPT_VAR           => $this->_getCurrentVar(),
            self::OPT_PAGE_PARAM    => $this->_getPageParamName()
        );
    }

    private function _getTotal () {
        return  isset($this->_options[self::OPT_TOTAL])
                ? (int) $this->_options[self::OPT_TOTAL]
                : self::DEFAULT_TOTAL;
    }

    /**
     * 获取最大页数
     *
     * @return  int     最大页数
     */
    private function _getMaxPage () {

        $perpage    = $this->_getNumberPerpage();
        $total      = $this->_getTotal();

        return      ceil($total / $perpage);
    }

    /**
     * 获取每页显示数量
     *
     * @return  int             每页显示数量
     * @throw   SystemException 当配置数据错误时 抛出异常
     */
    private function _getNumberPerpage () {

        if (!isset($this->_options[self::OPT_PERPAGE])) {

            return  self::DEFAULT_PERPAGE;
        }

        $numberPerPage  = (int) $this->_options[self::OPT_PERPAGE];

        if ($numberPerPage > 0) {

            return  $numberPerPage;
        }

        return  self::DEFAULT_PERPAGE;
    }

    /**
     * 获取当前页数
     *
     * @return  int     当前页数
     */
    private function _getCurrentPage () {

        if (isset($this->_options[self::OPT_CURRENT])) {

            return  $this->_options[self::OPT_CURRENT];
        }

        $paramName  = $this->_getPageParamName();
        $varName    = $this->_getPageVarName();
        $pageVar    = $GLOBALS[$varName];
        $current    = isset($pageVar[$paramName])
                    ? (int) $pageVar[$paramName]
                    : self::DEFAULT_CURRENT;

        return      $current > 0    ? $current  : self::DEFAULT_CURRENT;
    }

    /**
     * 获取页码参数名
     *
     * @return  string  页码变量名
     */
    private function _getPageParamName () {

        return  isset($this->_options[self::OPT_PAGE_PARAM])
                ? $this->_options[self::OPT_PAGE_PARAM]
                : self::DEFAULT_PAGE_PARAM;
    }

    /**
     * 获取页码变量名
     *
     * @return  string  页码变量名
     */
    private function _getPageVarName () {

        return  isset($this->_options[self::OPT_PAGE_VAR])
                ? $this->_options[self::OPT_PAGE_VAR]
                : self::DEFAULT_PAGE_VAR;
    }

    /**
     * 当前地址
     *
     * @return  string  地址
     */
    private function _getCurrentURL () {

        if (isset($this->_options[self::OPT_URL])) {

            return  $this->_options[self::OPT_URL];
        }

        $urlInfo    = parse_url($_SERVER['REQUEST_URI']);

        return      $urlInfo['path'];
    }

    /**
     * 当前变量
     *
     * @return  string  地址
     */
    private function _getCurrentVar () {

        if (isset($this->_options[self::OPT_VAR]) && is_array($this->_options[self::OPT_VAR])) {

            return  $this->_options[self::OPT_VAR];
        }

        if (!isset($_SERVER['QUERY_STRING']) || '' === $_SERVER['QUERY_STRING']) {

            return  array();
        }

        parse_str(filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_UNSAFE_RAW), $varibles);
        $varName    = $this->_getPageVarName();

        if (
            '_REQUEST'  === $varName ||
            '_GET'      === $varName ||
            '_POST'     === $varName
        ) {

            unset($varibles[$this->_getPageParamName()]);
        }

        return      $varibles;
    }
}
