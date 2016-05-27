<?php
/**
 * 日志写入逻辑
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class   LogWriter {

    /**
     * error_log的日志类型
     */
    const   MESSAGE_TYPE    = 3;

    /**
     * 断行符号
     */
    const   LINE_SPLITER    = "\n";

    /**
     * 配置图
     */
    private $_options;

	/**
	 * 创建实例
	 *
	 * @param	array		$options	配置
	 * @return	LogWriter				本类实例
	 */
	public	static	function create ($options) {
		
		return	new self($options);
	}

    /**
     * 构造函数
     *
     * @param   array   $options    配置参数
     */
    public  function __construct ($options) {

        $this->setOption($options);
    }

    /**
     * 配置
     *
     * @param   array   $options    配置数据
     */
    public  function setOption ($options) {

        $this->_options = $options;
    }

    /**
     * 写入日志
     *
     * @param   array   $data   数据
     */
    public  function save ($data) {

        $search = array('%y', '%m', '%d', '%h', '%i', '%s');
        $goal   = explode(' ', date('Y m d H i s'));
        $path   = str_replace($search, $goal, $this->_options['path']);
        $clips  = array();
		ksort($data);

        foreach ($data as $field => $value) {

            $fieldInfo  = "{$field}=";
            $fieldInfo  .= is_array($value) ? json_encode($value)   : $value;
            $clips[]    = $fieldInfo;
        }

        $line   = date('Y-m-d H:i:s') . "\t" . implode("\t", $clips);

        $this->saveLine($path, $line);
    }

    /**
     * 写入行
     *
     * @param   string  $path   路径
     * @param   string  $line   行
     */
    public  function saveLine ($path, $line) {

        error_log($line . self::LINE_SPLITER, self::MESSAGE_TYPE, $path);
    }
}


