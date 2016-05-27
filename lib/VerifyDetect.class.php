<?php
/**
 * 图形验证码识别
 */
class   VerifyDetect {

    const   BOUNDARY_SIMILAR    = 90.0;
    const   METHOD_PREFIX       = '_match';
    const   METHOD_DEFAULT      = 'ByPixel';
    const   LIB_DEFAULT         = '/../config/verify_data/*.txt';

    /**
     * 字符库向量图
     *
     * @var array
     */
    private $_mapCharactor      = array();

    /**
     * 创建实例
     *
     * @return  VerifyDetect    本类实例
     */
    static  public  function create () {

        return  new self;
    }

    /**
     * 执行
     *
     * @param   string  $path       图片路径
     * @param   array   $options    配置
     * @return  array               匹配结果
     */
    public  function run ($path, $options = array()) {

        $libPath    = $this->_getOptionLib($options);
        $method     = $this->_getOptionMethod($options);
        $this->_loadLib($libPath);
        $data       = $this->sharpen($path);
        $result     = $this->_getMatchResult($data);

        return      $this->_filterRepeatValue($result);
    }

    /**
     * 过滤掉连续的结果
     *
     * @param   array   $result 匹配结果
     * @return  array           筛选后的结果
     */
    private function _filterRepeatValue ($result) {

        $lastValue  = '';

        foreach ($result as $score => $value) {

            if ($lastValue == $value) {

                unset($result[$score]);
            } else {

                $lastValue  = $value;
            }
        }

        return  array_values($result);
    }

    /**
     * 获取匹配结果
     *
     * @param   array   $data   锐化数据
     * @return  array           匹配结果
     */
    private function _getMatchResult ($data) {

        foreach ($this->_mapCharactor as $charactor => $against) {

            $scoreList  = $this->detect($data, $against, $method);

            foreach ($scoreList as $scoreItem) {

                if (isset($scoreItem['x'])) {

                    $result[$scoreItem['x']]    = $charactor;
                }
            }
        }

        ksort($result);

        return  $result;
    }

    /**
     * 获取配置数据中的特征库地址
     *
     * @param   array   $options    配置
     * @return  string  特征库地址
     */
    private function _getOptionMethod ($options) {

        return  isset($options['method'])
                ? $options['method']
                : self::METHOD_DEFAULT;
    }

    /**
     * 获取配置数据中的特征库地址
     *
     * @param   array   $options    配置
     * @return  string  特征库地址
     */
    private function _getOptionLib ($options) {

        return  isset($options['lib'])
                ? $options['lib']
                : __DIR__ . self::LIB_DEFAULT;
    }

    /**
     * 加载特征库
     *
     * @param   string  $path   特征库地址
     */
    private function _loadLib ($path) {

        $index  = 'WMNXYZABDEFGHKPRSJTUVQ98452763CILO01';
        $map    = array();

        foreach (glob($path) as $file) {

            $charactor          = preg_replace('~^.+(\w)\.txt$~', "$1", $file);
            $map[$charactor]    = trim(file_get_contents($file));
        }

        foreach (str_split($index) as $charactor) {

            if (isset($map[$charactor])) {

                $this->_mapCharactor[$charactor]    = $map[$charactor];
            }
        }
    }

    /**
     * 锐化
     *
     * @param   string  $path   图片地址
     * @return  array           锐化结果
     */
    public  function sharpen ($path) {

        $resource   = imagecreatefromjpeg($path);
        list($width, $height)   = getimagesize($path);
        $data       = array();

        for ($y = 0; $y < $height; $y ++) {

            $data[$y]   = '';

            for ($x = 0; $x < $width; $x ++) {

                $index      = imagecolorat($resource,$x,$y);
                $rgb        = imagecolorsforindex($resource, $index);
                $data[$y]   .= $this->_rgbDetect($rgb);
            }
        }

        $data   = $this->_ignoreSpace($data);

        return  $data;
    }

    /**
     * 检测
     *
     * @param   array   $data   锐化数据
     * @param   array   $aginst 特征数据
     * @param   string  $method 方法
     * @return  array           匹配结果
     */
    public  function detect ($data, $against, $method) {

        $method = self::METHOD_PREFIX . $method;

        if (!is_callable(array($this, $method))) {

            return  false;
        }

        return  $this->$method($data, $against);
    }

    /**
     * 按像素进行匹配
     *
     * @param   array   $data       数据
     * @param   array   $against    特征数据
     * @param   array               匹配结果
     */
    private function _matchByPixel ($data, $against) {

        $length         = strpos($against, "\n");
        $line           = substr_count($against, "\n");
        $height         = count($data);
        $width          = strlen($data[0]);
        $score          = 0.0;

        if ($height < $line || $length > $width) {

            return  [['score'=>0]];
        }

        $offsetCellMax  = $width - $length;
        $offsetRowMax   = $height - $line;
        $result         = [];

        for ($offsetRow = 0; $offsetRow < $offsetRowMax; $offsetRow ++) {

            for ($offsetCell = 0; $offsetCell <= $offsetCellMax; $offsetCell ++) {

                $score  = $this->_getScore($data, $offsetRow, $offsetCell, $against);

                if ($score > self::BOUNDARY_SIMILAR) {

                    $result[]   = ['x'=>$offsetCell,'y'=>$offsetRow,'score'=>$score];
                }
            }
        }

        return  $result;
    }

    /**
     * 获取分值
     *
     * @param   array   $data       锐化数据
     * @param   int     $offsetRow  行偏移量
     * @param   int     $offsetCell 列偏移量
     * @param   array   $against    特征数据
     * @return  float               相似度计算结果
     */
    private function _getScore ($data, $offsetRow, $offsetCell, $against) {

        $length     = strpos($against, "\n");
        $line       = substr_count($against, "\n") + 1;
        $content    = '';

        for ($offset = 0; $offset < $line; $offsetRow ++, $offset ++) {

            $content    .= substr($data[$offsetRow], $offsetCell, $length);
        }

        similar_text(str_replace("\n", '', $content), str_replace("\n", '', $against), $ratio);

        return  $ratio;
    }

    /**
     * 去除空白
     *
     * @param   array   $data   锐化数据
     * @return  array           处理后的数据
     */
    private function _ignoreSpace ($data) {

        $length = strlen($data[0]);
        $space  = str_repeat('0', $length);
        $left   = $right    = $length;

        foreach ($data as $offset => $row) {

            if ($space == $row) {

                unset($data[$offset]);
            }

            $clips  = array();
            preg_match('~^0+~', $row, $clips);
            $left   = min($left, strlen($clips[0]));
            preg_match('~(0)+$~', $row, $clips);
            $right  = min($right, strlen($clips[0]));
        }

        $width  = $length - $right - $left;

        foreach ($data as $offset => $row) {

            $data[$offset]  = substr($row, $left, $width);
        }

        return  array_values($data);
    }

    /**
     * 滤镜阀值
     *
     * @param   array   $rgb    RGB色值数据
     * @return  bool            校验结果
     */
    private function _rgbDetect ($rgb) {

        return  $rgb['red'] < 125 || $rgb['green'] < 125 || $rgb['blue'] < 125  ? 1 : 0;
    }
}
