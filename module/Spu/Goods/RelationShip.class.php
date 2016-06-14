<?php
/**
 * 模型 SPU
 */
class   Spu_Goods_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_goods_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,goods_id,spu_goods_name';
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
        return      self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id,goods_id',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "' AND `goods_id` = '" . addslashes($data['goods_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 把一组商品和一个SPU进行关联
     *
     * @param array $multiGoods 一组商品, 每组商品信息如: array('goodsId'=>1, 'spuGoodsName'=>'abc')
     * @param $spuId            SPU ID
     * @return bool
     */
    static public function createMultiSpuGoodsRelationship (array $multiGoods, $spuId) {

        $multiGoodsId   = ArrayUtility::listField($multiGoods, 'goodsId');
        $listGoodsInfo  = Goods_Info::getByMultiId($multiGoodsId);
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

        foreach ($multiGoods as $goods) {

            $goodsId        = (int) $goods['goodsId'];
            $spuGoodsName   = empty($goods['spuGoodsName']) ? $mapGoodsInfo[$goodsId]['goods_name'] : addslashes(trim($goods['spuGoodsName']));

            $data   = array(
                'spu_id'            => (int) $spuId,
                'goods_id'          => $goodsId,
                'spu_goods_name'    => $spuGoodsName,
            );
            self::create($data);
        }
        return  true;
    }

    /**
     * 根据SPUID获取该SPU下的所有商品信息
     *
     * @param $spuId    SPUID
     * @return array    该SPU下的商品
     */
    static public function getBySpuId ($spuId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组SPUID 获取这组SPU下的所有商品
     *
     * @param array $multiSpuId 一组SPUID
     * @return array
     */
    static public function getByMultiSpuId (array $multiSpuId) {

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
}
