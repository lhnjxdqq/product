<?php
/**
 * 模型 地区
 */
class   Area_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'area_info';

    /**
     * 字段
     */
    const   FIELDS      = 'area_id,parent_id,area_name,area_type,agency_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'area_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'area_id',
        );
        $condition  = "`area_id` = '" . addslashes($data['area_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据ID获取地区信息
     *
     * @param $areaId   地区ID
     * @return array
     */
    static public function getById ($areaId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `area_id` = "' . (int) $areaId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 获取一组地区信息
     *
     * @param array $multiId    一组地区ID
     * @return array
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', $multiId);

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `area_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据地区ID 获取省市区名字
     *
     * @param array $multiId
     * @return array
     */
    static public function getFullAreaName (array $multiId) {

        $areaFullName = array(
            'province'  => '',
            'city'      => '',
            'district'  => '',
        );
        $result = array();

        $listAreaInfo   = self::getByMultiId($multiId);
        foreach ($listAreaInfo as $areaInfo) {
            $areaId     = $areaInfo['area_id'];
            $areaType   = $areaInfo['area_type'];
            switch ($areaType) {
                case '1' :
                    $provinceInfo               = $areaInfo;
                    $areaFullName['province']   = $provinceInfo['area_name'];
                break;
                case '2' :
                    $cityInfo                   = $areaInfo;
                    $provinceInfo               = self::getById($cityInfo['parent_id']);
                    $areaFullName['province']   = $provinceInfo['area_name'];
                    $areaFullName['city']       = $cityInfo['area_name'];
                break;
                case '3' :
                    $districtInfo               = $areaInfo;
                    $cityInfo                   = self::getById($districtInfo['parent_id']);
                    $provinceInfo               = self::getById($cityInfo['parent_id']);
                    $areaFullName['province']   = $provinceInfo['area_name'];
                    $areaFullName['city']       = $cityInfo['area_name'];
                    $areaFullName['district']   = $districtInfo['area_name'];
                break;
            }
            $result[$areaId]    = $areaFullName;
        }
        return  $result;
    }

    /**
     * 获取省份
     *
     * @return array
     */
    static public function getProvince () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `area_type` = "1"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取子地区
     *
     * @param $areaId
     */
    static public function getChildArea ($areaId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `parent_id` = "' . (int) $areaId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取父级地区
     *
     * @param $areaId
     * @return array
     */
    static public function getParentArea ($areaId) {

        $allArea    = ArrayUtility::indexByField(self::listAll(), 'area_id');
        $result     = array();
        while ($area    = $allArea[$areaId]) {
            $result[]   = $area;
            $areaId     = $area['parent_id'];
        }
        array_pop($result);
        return      $result;
    }
}
