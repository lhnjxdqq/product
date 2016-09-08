<?php
/**
 * 模型 商品 规格 规格值 关系
 */
class   Goods_Spec_Value_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_spec_value_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_id,spec_id,spec_value_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'goods_id,spec_id,spec_value_id',
        );
        $condition  = "`goods_id` = '" . addslashes($data['spu_id']) . "' AND `spec_id` = '" . addslashes($data['spec_id']) . "' AND `spec_value_id` = '" . addslashes($data['spec_value_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据规格 规格值 子款式 品类ID 验证商品是否存在
     *
     * @param array $specValueList  规格 规格值
     * @param int   $styleId        款式ID
     * @param int   $categoryId     品类ID
     * @return array|void           商品信息
     */
    static public function validateGoods (array $specValueList, $styleId, $categoryId) {

        if (!$specValueList || !$styleId) {

            return;
        }

        $listGoodsInfo  = Goods_Info::listByCondition(array(
            'style_id'      => (int) $styleId,
            'category_id'   => (int) $categoryId,
        ));
        $listGoodsId    = $listGoodsInfo ? ArrayUtility::listField($listGoodsInfo, 'goods_id') : array();
        if (empty($listGoodsId)) {

            return;
        }

        $sql    = 'SELECT gi.goods_id, count(1) AS `cnt` FROM `' . self::_tableName() . '` AS `gsvr` LEFT JOIN `goods_info` AS `gi` ON `gsvr`.`goods_id`=`gi`.`goods_id` WHERE ';
        $where  = array();
        foreach ($specValueList as $specValue) {

            $where[] = '(`gsvr`.`spec_id` = "' . (int) $specValue['spec_id'] . '" AND `gsvr`.`spec_value_id` = "' . (int) $specValue['spec_value_id'] . '")';
        }

        $sql    .= "(".implode(' OR ', $where).")" . ' AND `gi`.`style_id` = "' . (int) $styleId . '" AND `category_id` = "' . (int) $categoryId . '" GROUP BY `gi`.`goods_id`';

        $result = self::_getStore()->fetchAll($sql);
        if (!$result) {

            return;
        }
        $result = ArrayUtility::indexByField($result, 'goods_id', 'cnt');
        $count  = count($specValueList);
        foreach ($result as $goodsId => $cnt) {

            if ($cnt == $count) {

                return  $goodsId;
            }
        }
    }
    
    /**
     * 根据规格 规格值 子款式 品类ID 验证商品,sku范围是否存在
     *
     * @param array $specValueList  规格 规格值
     * @param int   $styleId        款式ID
     * @param int   $categoryId     品类ID
     * @return array|void           商品信息
     */
    static public function getGoodsIdByValueList (array $specValueList, $styleId, $categoryId , array $listSkuId) {

        if (!$specValueList) {

            return;
        }

        $multiGoodsId   = array_map('intval', array_unique(array_filter($listSkuId)));

        Validate::testNull($multiGoodsId,'不存在的sku记录');
        
        $condition = array();
        if($styleId){
        
            $condition['style_id'] = (int) $styleId;
        }
        $condition['category_id']  = (int) $categoryId;
                
        $listGoodsInfo  = Goods_Info::listByCondition($condition);
        $listGoodsId    = $listGoodsInfo ? ArrayUtility::listField($listGoodsInfo, 'goods_id') : array();
        if (empty($listGoodsId)) {

            return;
        }

        $sql    = 'SELECT gi.goods_id, count(1) AS `cnt` FROM `' . self::_tableName() . '` AS `gsvr` LEFT JOIN `goods_info` AS `gi` ON `gsvr`.`goods_id`=`gi`.`goods_id` WHERE ';
        $where  = array();
        foreach ($specValueList as $specValue) {

            $where[] = '(`gsvr`.`spec_id` = "' . (int) $specValue['spec_id'] . '" AND `gsvr`.`spec_value_id` = "' . (int) $specValue['spec_value_id'] . '")';
        }
        
        $sqlStyle='';
        if($styleId){
        
            $sqlStyle = ' AND `gi`.`style_id` = ' . (int) $styleId;
        }

        $sql    .= "(".implode(' OR ', $where).")" . $sqlStyle . ' AND `gi`.`goods_id` IN ("' . implode('","', $multiGoodsId) . '")' .' AND `category_id` = "' . (int) $categoryId . '" GROUP BY `gi`.`goods_id` ';

        $result = self::_getStore()->fetchAll($sql);

        if (!$result) {

            return;
        }
        $result = ArrayUtility::indexByField($result, 'goods_id', 'cnt');
        
        $count  = count($specValueList);

        foreach ($result as $goodsId => $cnt) {

            if ($cnt == $count) {

                return  $goodsId;
            }
        }
    }

    /**
     * 根据一组商品ID获取商品的规格和规格值
     *
     * @param array $multiGoodsId   一组商品ID
     * @return array                改组商品的规格和规格值
     */
    static public function getByMultiGoodsId (array $multiGoodsId) {

        $multiGoodsId   = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` IN ("' . implode('","', $multiGoodsId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据商品ID查询该商品的规格和规格值ID
     *
     * @param $goodsId  商品ID
     * @return array    该商品的规格和规格值ID
     */
    static public function getByGoodsId ($goodsId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取所有符合该组 规格 规格值得商品
     *
     * @param $multiSpecValueList
     * @return array
     */
    static public function listByMultiSpecValueList ($multiSpecValueList) {

        $result = array();
        foreach ($multiSpecValueList as $specValueList) {

            if (is_array($temp = self::getBySpecValueList($specValueList))) {
                $result = array_merge($result, $temp);
            }
        }

        return  $result;
    }

    static public function getBySpecValueList ($specValueList) {

        $sql    = 'SELECT `goods_id`,COUNT(1) AS `cnt` FROM `' . self::_tableName() . '` WHERE ';
        $where  = array();
        foreach ($specValueList as $specValue) {

            $where[]    = '( `spec_id` = "' . (int) $specValue['spec_id'] . '" AND `spec_value_id` = "' . (int) $specValue['spec_value_id'] . '")';
        }
        $sql    .= implode(' OR ', $where) . ' GROUP BY `goods_id`';

        $data   = self::_getStore()->fetchAll($sql);
        if (!$data) {

            return;
        }
        $result = array();
        $count  = count($specValueList);
        foreach ($data as $goods) {
            if ($count == $goods['cnt']) {
                $result[]   = $goods['goods_id'];
            }
        }
        return  $result;
    }

    /**
     * 根据SKUID 删除SPEC_ID SPEC_VALUE_ID关系
     *
     * @param $goodsId  SKUID
     * @return int
     */
    static public function deleteByGoodsId ($goodsId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->execute($sql);
    }
}
