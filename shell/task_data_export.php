<?php
/**
 * 任务数据导出
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

if(empty($argv[1])) {

    echo '任务参数缺失!执行任务命令为:php task_data_export.php 参数' . PHP_EOL;
    echo '参数为:getRepeatSkuSn(提取系统中重复的SKU编号),getDefectColor(提取缺失颜色),getDefectSize(提取缺失尺寸),all(前面三个全部获取)' . PHP_EOL;
} else {

    $taskName = $argv[1];
    if (selectTask($taskName)) {

        echo '任务执行完成!' . PHP_EOL;
    } else {

        echo '任务执行失败!' . PHP_EOL;
    }
}

function selectTask ($taskName) {

    if (empty($taskName)) {

        return false;
    }

    switch ($taskName) {
        case 'all':
            getGoodsData();
            getDefectData('color');
            getDefectData('size');
            break;
        case 'getRepeatSkuSn':
            getGoodsData();
            break;
        case 'getDefectColor':
            getDefectData('color');
            break;
        case 'getDefectSize':
            getDefectData('size');
            break;
        default :
            echo '参数错误' . PHP_EOL;
            break;
    }
    return true;
}

/**
提取系统中重复的SKU编号，重复规则：
1、SKU所有参数一致，包括品类、规格尺寸、规格重量、款式、子款式、辅料材质、主料材质、颜色。忽略空数据。
反馈的表头：SKU编号，SPU编号，关联订单号
同一SKU对应多个SPU，在一个单元格显示，逗号隔开。

1.迭代取出sku数据
2.迭代查询出sku 的品类、规格尺寸、规格重量、款式、子款式、辅料材质、主料材质、颜色 添加到临时表中
3.临时表条件分组,取出条数大于1的sku数据
4.根据sku数据取出spu及订单数据
 */
/**
 * 迭代查询sku数据
 */
function getGoodsData () {

    echo '提取系统中重复的SKU编号' . PHP_EOL;
    echo '任务开始执行了' . PHP_EOL;
    $count = Goods_Info::countByCondition(array());
    echo "sku数据共{$count}条" . PHP_EOL;
    echo '下面开始查询sku数据,每次查询500条,查询到的数据更新到tmp表中,供分组查询' . PHP_EOL;
    Tmp::truncate();
    for($offset=0, $limit=500; $offset * $limit < $count;) {

        $listGoodsInfo = Goods_Info::listByCondition(array(), array(), ($offset * $limit), $limit);
        getGoodsRelationData($listGoodsInfo);
        echo '第' . ++$offset . '次更新数据完成!' . PHP_EOL;
    }
    Tmp::addIndex();
    echo '下面开始分组数据' . PHP_EOL;
    $groupTmpData = Tmp::groupData();
    $data   = getRepeatGoodsId($groupTmpData);
    $header = array('序号', 'SKU编号', 'SPU编号', '订单号');
    array_unshift($data, $header);
    $filePath = TEMP;
    is_dir($filePath) || mkdir($filePath, 0777, true);
    $fileName = $filePath . date('YmdHis') . '重复的sku编号.csv';

    if (fileWriteData($data, $fileName)) {

        echo '重复的sku编号导出成功!' . PHP_EOL;
    } else {

        echo '重复的sku编号导出失败!' . PHP_EOL;
    }
}

/**
 * 数据写入文件
 * 
 * @param   array   $data 要写入文件数据
 * @return  BOOL
 */
function fileWriteData($data, $filePath) {

    if (empty($data) || !is_array($data)) {

        return false;
    }

    if ($fh = fopen($filePath, 'a')){

        fwrite($fh, file_get_contents(CONF . '/utf8bom'));
        foreach ($data as $value) {

            fputcsv($fh, $value);
        }

        fclose($fh);
        return true;
    }

    return false;

}

/**
 * 获取重复sku数据
 */
function getRepeatGoodsId($groupTmpData) {

    $listGoodsId = array();
    foreach ($groupTmpData as $data) {

        $condition = array(
            'category_id'       => (int)$data['category_id'],
            'style_id'          => (int)$data['style_id'],
            'spec_material_id'  => (int)$data['spec_material_id'],
            'spec_size_id'      => (int)$data['spec_size_id'],
            'spec_color_id'     => (int)$data['spec_color_id'],
            'spec_weight_id'    => (int)$data['spec_weight_id'],
            'spec_assistant_material_id' => (int)$data['spec_assistant_material_id'],
            );
        $tmpData    = array();
        $tmpGoodsId = array();
        $tmpData    = Tmp::listByCondition($condition, array());
        $listGoodsId[] = ArrayUtility::listField($tmpData, 'goods_id');
    }

    $goodsIdList    = convertArray($listGoodsId);
    $mapGoodsInfo   = ArrayUtility::indexByField(Goods_Info::getByMultiId($goodsIdList), 'goods_id');
    $groupGoodsSpuRelation  = Common_Spu::getGoodsSpu($goodsIdList);
    $listOrderGoodsInfo     = Sales_Order_Goods_Info::getBySkuId($goodsIdList);
    $groupOrderGoodsInfo    = ArrayUtility::groupByField($listOrderGoodsInfo, 'goods_id', 'sales_order_id');
    $listOrderId    = ArrayUtility::listField($listOrderGoodsInfo, 'sales_order_id');
    $listOrderInfo  = Sales_Order_Info::getByMultiId($listOrderId);
    $result = array();
    $i      = 1;
    foreach ($listGoodsId as $group) {

        foreach ($group as $goodsId) {

            if ($groupGoodsSpuRelation[$goodsId]) {

                $listSpuSn  = ArrayUtility::listField($groupGoodsSpuRelation[$goodsId], 'spu_sn');
                $spuSn      = implode('/', $listSpuSn);
            }

            if ($groupOrderGoodsInfo[$goodsId]) {

                $tmpOrderId     = ArrayUtility::listField($groupOrderGoodsInfo[$goodsId], 'sales_order_id');
                $tmpOrderInfo   = ArrayUtility::searchBy($listOrderInfo, array('sales_order_id'=>$tmpOrderId), 'searchAndInHandler');
                $listOrderSn    = ArrayUtility::listField($tmpOrderInfo, 'sales_order_sn');
                $orderSn        = implode('/', $listOrderSn);
            }

            $tmp = array(
                $i,
                $mapGoodsInfo[$goodsId]['goods_sn'],
                $spuSn   ? $spuSn    : '无',
                $orderSn ? $orderSn  : '无',
                );

            $result[] = $tmp;
        }

        $i++;
    }
    return $result;
}

/**
 * 获取sku的关联数据
 */
function getGoodsRelationData ($listGoodsInfo) {

    $listGoodsId    = ArrayUtility::listField($listGoodsInfo, 'goods_id');
    $listGSVRdata   = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
    $groupGSVRdata  = ArrayUtility::groupByField($listGSVRdata, 'goods_id');
    foreach ($listGoodsInfo as $goodsInfo) {

        $goodsId = $goodsInfo['goods_id'];
        $newData = array(
            'goods_id'          => $goodsId,
            'category_id'       => $goodsInfo['category_id'],
            'style_id'          => $goodsInfo['style_id'],
            'spec_material_id'  => 0,
            'spec_size_id'      => 0,
            'spec_color_id'     => 0,
            'spec_weight_id'    => 0,
            'spec_assistant_material_id' => 0,
            );
        foreach ($groupGSVRdata[$goodsId] as $data) {

            switch ((int)$data['spec_id']) {
                case 1:
                    $newData['spec_material_id'] = $data['spec_value_id'];
                    break;
                case 2:
                    $newData['spec_size_id'] = $data['spec_value_id'];
                    break;
                case 3:
                    $newData['spec_color_id'] = $data['spec_value_id'];
                    break;
                case 4:
                    $newData['spec_weight_id'] = $data['spec_value_id'];
                    break;
                case 5:
                    $newData['spec_assistant_material_id'] = $data['spec_value_id'];
                    break;
                default :
                    continue;
            }
        }
        Tmp::create($newData);
    }
}

/**
 * 获取specInfo
 */
function getSpecInfo() {

    static $listSpecInfo = array();

    if (empty($listSpecInfo)) {

        $listSpecInfo = Spec_Info::listAll();
    }
    return $listSpecInfo;
}


/**
 * 多维数据转一维数据, 键会被重置
 * 
 * @param   array   $data   待转换的数据
 * @return  array
 */
function convertArray(array $data) {

    $result = array();
    foreach ($data as $value) {

        if (is_string($value) || is_int($value)) {

            $result[] = $value;
        } else if (is_array($value)) {

            $result = array_merge($result, convertArray($value));
        }
    }
    return $result;
}


/*
提取缺失尺寸，规则：
1、检索SPU下ID最小的SKU，取其品类值；
2、通过品类值找到商品类型值；
3、商品类型对应的规格尺寸是否在当前SPU的SKU中全都存在，至少有一个规格尺寸不存在时，记录该SPU编号。
反馈的表头：SPU编号、三级分类、规格尺寸
不同规格尺寸以逗号隔开
*/


/*
提取缺失颜色，规则：
1、检索SPU下ID最小的SKU，取其品类值；
2、通过品类值找到商品类型值；
3、商品类型对应的颜色是否在当前SPU的SKU中全都存在，至少有一个颜色不存在时，记录该SPU编号。
反馈的表头：SPU编号、三级分类、颜色。
不同颜色以逗号隔开
*/

function getDefectData ($specAlias) {

    $listSpecInfo   = getSpecInfo();
    $mapSpecInfo    = ArrayUtility::indexByField($listSpecInfo, 'spec_alias');
    $enum   = $mapSpecInfo[$specAlias]['spec_id'];
    $header = array('SPU编号', '三级分类', $mapSpecInfo[$specAlias]['spec_name']);
    echo '提取缺失' . $mapSpecInfo[$specAlias]['spec_name'] . '任务执行开始了!' . PHP_EOL;
    $count  = Spu_Info::countByCondition(array());
    $data   = array();
    $flag   = true;
    echo '开始查询spu数据,每次查询50条' . PHP_EOL;
    for($offset=122, $limit=50; $offset * $limit < $count; ) {

        $listSpuInfo = Spu_Info::listByCondition(array(), array(), $offset * $limit, $limit);
        echo '这是第' . ++$offset . '次查询!' . PHP_EOL;
        $data        = getData($listSpuInfo, $enum);
        if (empty($data)) {

            continue;
        }

        if (!$fileName) {

            array_unshift($data, $header);
            $filePath = TEMP;
            is_dir($filePath) || mkdir($filePath, 0777, true);
            $fileName = $filePath . date('YmdHis') . '提取缺失' . $mapSpecInfo[$specAlias]['spec_name'] . '.csv';
        }
        if (!fileWriteData($data, $fileName)) {

            echo '提取缺失' . $mapSpecInfo[$specAlias]['spec_name'] . '任务执行失败!' . PHP_EOL;
            $flag = false;
            break;
        }
    }

    if ($flag) {

        echo '提取缺失' . $mapSpecInfo[$specAlias]['spec_name'] . '任务执行完成!' . PHP_EOL;
    }

}

/**
 * 根据枚举获查出缺失数据
 * 
 * @param   array   $listSpuInfo    spu实体数据列表
 * @param   int     $enum           枚举
 * 
 * @return  array
 */
function getData ($listSpuInfo, $enum) {

    $listSpuId  = ArrayUtility::listField($listSpuInfo, 'spu_id');
    $mapSpuInfo = ArrayUtility::indexByField($listSpuInfo, 'spu_id');
    $listSpuGoodsRelation = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
    $listGoodsId        = ArrayUtility::listField($listSpuGoodsRelation, 'goods_id');
    $listGSVRelation    = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
    $listGSVRelation    = ArrayUtility::searchBy($listGSVRelation, array('spec_id' => $enum));
    $mapGSVRelation     = ArrayUtility::indexByField($listGSVRelation, 'spec_value_id');

    $groupSpuGoodsRelation  = array_filter(ArrayUtility::groupByField($listSpuGoodsRelation, 'spu_id', 'goods_id'));
    $listMinGoodsId         = array();
    foreach ($listSpuId as $spuId) {

        $tmpGoodsId = $groupSpuGoodsRelation[$spuId];
        if (empty($tmpGoodsId)) {

            continue;
        }
        $minGoodsId = min($tmpGoodsId);
        $listMinGoodsId[$spuId] = $minGoodsId;
    }
    $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
    $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
    $goodsInfoList  = ArrayUtility::searchBy($listGoodsInfo, array('goods_id' => $listMinGoodsId), 'searchAndInHandler');
    $listCategoryId = ArrayUtility::listField($goodsInfoList, 'category_id');
    $listCategoryInfo   = Category_Info::getByMultiId($listCategoryId);
    $mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
    $listGoodsTypeId    = ArrayUtility::listField($listCategoryInfo, 'goods_type_id');
    $listGTSVR = Goods_Type_Spec_Value_Relationship::getByMulitGoodsTypeId($listGoodsTypeId);
    // spec_id : 2尺寸/3颜色
    $listGTSVR  = ArrayUtility::searchBy($listGTSVR, array('spec_id'=>$enum));
    $groupGTSVR = ArrayUtility::groupByField($listGTSVR, 'goods_type_id', 'spec_value_id');
    $listSpecValueInfo  = getSpecValueInfo();
    $mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
    $result = array();
    foreach ($listSpuId as $spuId) {

        if (empty($groupSpuGoodsRelation[$spuId])) {

            continue;
        }
        $listRelation   = ArrayUtility::searchBy($listGSVRelation, array('goods_id' => $groupSpuGoodsRelation[$spuId]), 'searchAndInHandler');
        $listSpecValueIdA = array_map('intval', array_filter(array_unique(ArrayUtility::listField($listRelation, 'spec_value_id'))));

        $minGoodsId     = $listMinGoodsId[$spuId];
        $categoryId     = $mapGoodsInfo[$minGoodsId]['category_id'];
        $goodsTypeId    = $mapCategoryInfo[$categoryId]['goods_type_id'];
        $listSpecValueIdB   = array_map('intval', array_filter(array_unique($groupGTSVR[$goodsTypeId])));
        $listSpecValueId    = array_diff($listSpecValueIdA, $listSpecValueIdB);

        if (empty($listSpecValueId)) {

            continue;
        }
        $specValueName  = array();
        $categoryName   = array();
        foreach ($listSpecValueId as $specValueId) {

            $specValueName[]    = $mapSpecValueInfo[$specValueId]['spec_value_data'];
            $goodsId            = $mapGSVRelation[$specValueId]['goods_id'];
            $categoryName[]     = $mapCategoryInfo[$mapGoodsInfo[$goodsId]['category_id']]['category_name'];
        }
        $result[] = array(
            $mapSpuInfo[$spuId]['spu_sn'],
            implode('/', $categoryName),
            implode('/', $specValueName),
            );
    }

    return $result;

}


/**
 * 获取规格值
 */
function getSpecValueInfo () {

    static $listSpecValueInfo = array();

    if(empty($listSpecValueInfo)) {

        $listSpecValueInfo = Spec_Value_Info::listAll();
    }

    return $listSpecValueInfo;
}





