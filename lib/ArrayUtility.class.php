<?php
/**
 * 数组工具类
 *
 * @author  yaoxiaowei
 */
class ArrayUtility {

    const   SRCH_LOGIC_AND_EQU  = 'searchAndEquHandler';
    const   SRCH_LOGIC_AND_NEQU = 'searchAndNotEquHandler';
    const   SRCH_LOGIC_AND_IN   = 'searchAndInHandler';
    const   ASC                 = 'ASC';
    const   DESC                = 'DESC';

    static  public  $arraySearchOptions;
    static  public  $arraySortField;
    static  public  $arraySortOrder;

    /**
     * 根据一个关联数组的字段整理数组为map的格式
     *
     * @static
     * @access  public
     * @param   array   $array          待整理数组
     * @param   string  $indexFieldName 索引字段名
     * @param   string  $valueFieldName 值字段名 为空时返回全部数据 默认为空
     * @return  array                   整理好的数组
     */
    static  public  function    indexByField ($array, $indexFieldName, $valueFieldName = NULL) {

        $result = array();

        foreach ($array as $item) {

            if (isset($item[$indexFieldName])) {

                $result[$item[$indexFieldName]] = NULL === $valueFieldName
                                                ? $item
                                                : $item[$valueFieldName];
            }
        }

        return  $result;
    }

    /**
     * 根据一个关联数组的字段整理数组为map的格式
     *
     * @static
     * @access  public
     * @param   array   $array          待整理数组
     * @param   string  $indexFieldName 索引字段名
     * @param   string  $valueFieldName 值字段名 为空时返回全部数据 默认为空
     * @return  array                   整理好的数组
     */
    static  public  function    groupByField ($array, $indexFieldName, $valueFieldName = NULL) {

        $result = array();

        foreach ($array as $item) {

            if (isset($item[$indexFieldName])) {

                $result[$item[$indexFieldName]] = isset($result[$item[$indexFieldName]])
                                                ? $result[$item[$indexFieldName]]
                                                : array();

                $result[$item[$indexFieldName]][]   = NULL === $valueFieldName
                                                    ? $item
                                                    : $item[$valueFieldName];
            }
        }

        return  $result;
    }

    /**
     * 过滤出某一个字段的数据
     *
     * @static
     * @access  public
     * @param   array   $array      待处理数组
     * @param   string  $fieldName  键名
     * @return  array               键值组成的数组
     */
    static  public  function    listField ($array, $fieldName) {

        $result = array();

        foreach ($array as $key => $item) {

            if (isset($item[$fieldName])) {

                $result[$key]   = $item[$fieldName];
            }
        }

        return  $result;
    }

    /**
     * 数组按照某一个键值进行排序
     *
     * @static
     * @access  public
     * @param   array   $array      待整理数组
     * @param   string  $fieldName  键名
     * @param   string  $order      排序依据
     * @return  array               整理好的数组
     */
    static  public  function    sortByField (& $array, $fieldName, $order = self::ASC) {

        self::$arraySortField   = $fieldName;
        self::$arraySortOrder   = $order;
        uasort($array, array('ArrayUtility', 'sortByFieldHandler'));
    }

    /**
     * 数组按照某一个键值进行排序 句柄
     *
     * @static
     * @access  public
     * @param   array   $a  待整理数组a
     * @param   string  $b  待整理数组b
     * @return  int         排序依据
     */
    static  public  function    sortByFieldHandler ($a, $b) {

        if ($a[self::$arraySortField] == $b[self::$arraySortField]) {

            return  0;
        }

        if (self::ASC == self::$arraySortOrder) {

            return  $a[self::$arraySortField] > $b[self::$arraySortField]
                    ? 1
                    : -1;
        }

        return  $a[self::$arraySortField] < $b[self::$arraySortField]
                    ? 1
                    : -1;
    }

    /**
     * 返回一组key对应的数据
     *
     * @param   array   $array  数组
     * @param   array   $keys   一组key
     * @return  array           对应的数据
     */
    static  public  function    listByKeyMulti ($array, $keys) {

        $result = array();

        foreach ($keys as $key) {

            if (isset($array[$key])) {

                $result[$key]   = $array[$key];
            }
        }

        return  $result;
    }

    /**
     * 根据相等于逻辑过滤数组句
     *
     * @static
     * @access  public
     * @param   array   $array      待过滤的数组
     * @param   array   $options    过滤条件 '字段名'=>'字段条件'
     * @param   string  $handler    过滤逻辑
     * @return                      过滤结果
     */
    static  public  function    searchBy ($array, $options, $handler = self::SRCH_LOGIC_AND_EQU) {

        self::$arraySearchOptions   = $options;

        return  array_filter($array, array('ArrayUtility', $handler));
    }


    /**
     * 根据相等于逻辑过滤数组句柄
     *
     * @static
     * @access  public
     * @param   array   $element    数组元素
     * @return  bool                判断结果
     */
    static  public  function    searchAndEquHandler ($element) {

        foreach (self::$arraySearchOptions as $fieldName => $value) {

            if (!isset($element[$fieldName]) || $element[$fieldName] != $value) {

                return  false;
            }
        }

        return  true;
    }

    /**
     * 根据相不等于逻辑过滤数组句柄
     *
     * @static
     * @access  public
     * @param   array   $element    数组元素
     * @return  bool                判断结果
     */
    static  public  function searchAndNotEquHandler ($element) {

        foreach (self::$arraySearchOptions as $fieldName => $value) {

            if (isset($element[$fieldName]) && $element[$fieldName] == $value) {

                return  false;
            }
        }

        return  true;
    }

    /**
     * 根据IN逻辑过滤数组句柄
     *
     * @static
     * @access  public
     * @param   array   $element    数组元素
     * @return  bool                判断结果
     */
    static  public  function searchAndInHandler ($element) {

        foreach (self::$arraySearchOptions as $fieldName => $value) {

            if (isset($element[$fieldName]) && in_array($element[$fieldName], $value)) {

                return  true;
            }
        }

        return  false;
    }

    /**
     * 按字段划分开数组
     *
     * @static
     * @access  public
     * @param   array   $array      数组数据源
     * @param   string  $fieldName  键名
     * @return  array               划分好的数组
     */
    static  public  function    chuckByField ($array, $fieldName) {

        $result = array();

        foreach ($array as $item) {

            if (isset($item[$fieldName])) {

                $result[$item[$fieldName]]      = isset($result[$item[$fieldName]])
                                                ? $result[$item[$fieldName]]
                                                : array();
                $result[$item[$fieldName]][]    = $item;
            }
        }

        return  $result;
    }

    /**
     * 解析xml对象为数组
     *
     * @param   SimpleXMLElement    $xml    xml对象
     * @return  array                       数组
     */
    static  public  function    fromXml ($xml) {

        $map    = array();

        foreach ($xml->children() as $child) {

            $map[$child->getName()] = self::fromXml($child);
        }

        return  count($map) ? $map  : '' . $xml;
    }

    /**
     * 解析数组为xml对象
     *
     * @param   array               array   数组
     * @param   string              $root   根节点元素名
     * @return  SimpleXMLElement            xml对象
     */
    static  public  function    toXmlNode ($array, $root) {

        if (!is_array($array)) {

            return  $array;
        }

        $xmlRoot    = new SimpleXMLElement("<{$root}></{$root}>");
        $current    = $xmlRoot;

        foreach ($array as $subName => $value) {

            self::_sxmlElement($array, $root, $current);
        }

        return      $xmlRoot;
    }

    /**
     * 组合xml元素
     *
     * @param   array               $array      源数组
     * @param   string              $root       根节点元素名
     * @param   SimpleXMLElement    $current    当前元素
     */
    private static  function _sxmlElement ($array, $root, & $current) {

        if (is_numeric(key($array))) {

            $clips      = array();
            $subName    = preg_match('~^(\w+)(?:_list|s)$~', $root, $clips) ? $clips[1] : "{$root}_{$subName}";
        }

        $subValue   = self::toXmlNode($value, $subName);

        if ($subValue instanceof SimpleXMLElement) {

            self::sxmlAppend($current, $subValue);
        } elseif (preg_match('~^[0-9a-z_\-.\[\]]+$~i', $subValue)) {

            $current->addChild($subName, $subValue);
        } else {

            $subNode    = $current->addChild($subName);
            self::_appendCData($subNode, $subValue);
        }
    }

    /**
     * 将SimpleXMLElement增加到节点中
     *
     * @param   SimpleXMLElement    $to     目标节点
     * @param   SimpleXMLElement    $from   加入节点
     */
    public  static  function sxmlAppend (SimpleXMLElement $to, SimpleXMLElement $from) {

        $toDom      = dom_import_simplexml($to);
        $fromDom    = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * 添加CData数据
     *
     * @param   simpleXMLElement    $xmlNode    节点
     * @param   string              $content    内容
     */
    static  private function _appendCData (& $xmlNode, $content) {

        $dom    = dom_import_simplexml($xmlNode);
        $cData  = $dom->ownerDocument->createCDATASection($content);
        $dom->appendChild($cData);
    }

    /**
     * 对一个二维数组按字段值进行排序
     *
     * @param array $multiArray 二维数组
     * @param $fieldName        字段名
     * @param int $sortBy       排序
     * @return array|void
     */
    static public function sortMultiArrayByField (array $multiArray, $fieldName, $sortBy = SORT_ASC) {

        $temp = array();
        foreach ($multiArray as $key => $array) {
            if (!isset($array[$fieldName])) {

                return;
            }
            $temp[$key] = $array[$fieldName];
        }
        array_multisort($temp, $sortBy, $multiArray);
        return  $multiArray;
    }
}
