<?php
/**
 * 模板
 *
 * @author    yaoxiaowei
 */
class    Template {

    /**
     * 实例
     */
    private    static   $_instance;

    /**
     * Smarty实例
     */
    private    $_smarty;

    /**
     * 获取实例
     *
     * @return    Template    本类实例
     */
    public    static    function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self;
        }

        return    self::$_instance;
    }

    /**
     * 获取Smarty实例
     *
     * @return    Smarty    Smaty实例
     */
    private    function _getSmarty () {

        if (!($this->_smarty instanceof Smarty)) {

            require_once    LIB . 'Smarty-3.1.14/Smarty.class.php';

            $config = Config::get('template|PHP', 'base');
            $smarty = new Smarty();
            $smarty->template_dir       = $config['template_dir'];
            $smarty->compile_dir        = $config['compiled_dir'];
            $smarty->plugins_dir        = array(SMARTY_DIR . '/plugins', $config['plugins_dir']);
            $smarty->left_delimiter     = $config['left_delimiter'];
            $smarty->right_delimiter    = $config['right_delimiter'];
            $this->_smarty              = $smarty;
            $varibles                   = Config::get('template|PHP', 'varibles');

            if ($varibles) {

                $this->_smarty->assign($varibles);
            }
        }

        return    $this->_smarty;
    }

    /**
     * 获取结果
     *
     * @param   string          $templateFile   模板文件
     * @param   string|array    $data           变量
     * @return  string                          内容
     */
    public    function fetch ($templateFile, $data = array(), $cacheId = NULL) {

        if (is_array($data)) {

            $this->assign($data);
        } else {

            $cacheId    = $data;
        }

        $smarty     = $this->_getSmarty();
        $content    = $smarty->fetch($templateFile, $cacheId);
        $this->_closeTemplate();

        return    $content;
    }

    /**
     * 输出结果
     *
     * @param   string        $templateFile   模板文件
     * @param   string|array  $data           变量
     * @return  string        $cacheId        内容
     */
    public    function display ($templateFile, $data = array(), $cacheId = NULL) {

        if (is_array($data)) {

            $this->assign($data);
        } else {

            $cacheId    = $data;
        }

        $smarty    = $this->_getSmarty();
        $smarty->display($templateFile, $cacheId);
        $this->_closeTemplate();

        return    $this;
    }

    /**
     * 分配变量
     *
     * @param   string|array    $name   变量名
     * @param   mixed           $value  变量值
     * @return  Template                模板实例
     */
    public    function assign ($name = NULL, $value = NULL) {

        $smarty    = $this->_getSmarty();

        if (is_array($name)) {

            foreach ($name as $key => $data) {

                $smarty->assign($key, $data);
            }

            return    $this;
        }

        $smarty->assign($name, $value);

        return    $this;
    }

    /**
     * 清理模板
     */
    public    function clear () {

        $this->_closeTemplate();
    }

    /**
     * 关闭模板
     */
    private    function _closeTemplate () {

        $this->_smarty    = NULL;
    }
}
