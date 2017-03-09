<?php
/**
 * 模型 生产订单
 */
class   Produce_Order_Arrive_Import {
    
    /**
     * 添加销售合同中的商品入库
     *
     *  @param  array   $salesOrderInfo 销售订单信息
     *  @param  string  $filePath       销售订单路径 
     */
    static public function importProduceArrive (array $orderInfo) {

        $filePath = Config::get('path|PHP', 'storage_import').$orderInfo['file_path'];

        $produceOrderId         = $orderInfo['produce_order_id'];
        Validate::testNull($produceOrderId, '生产订单ID不能为空');
        $csvHead          = array(
            '买款ID'             => 'source_code',
            '产品编号'           => 'product_sn',
            '三级分类'           => 'categoryLv3',
            '颜色'               => 'color_name',
            '成本工费'           => 'cost',
            '数量'               => 'quantity',
            '重量'               => 'weight',
        );

        $list               = array();
        $csv                = CSVIterator::load($filePath, $options);
        setlocale(LC_ALL, array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
        $reportNumber   = 0;

        foreach ($csv as $lineNumber => $line) {

            if (0 == $lineNumber) {

                $format = array();

                foreach ($line as $offset => $cellValue) {

                    $head   = Utility::GbToUtf8(trim($cellValue));

                    if (isset($csvHead[$head])) {

                        $format[$offset]    = $csvHead[$head];
                    }
                }

                if (count($format) != count($csvHead)) {

                    throw   new ApplicationException('无法识别表头');
                }

                $csv->setFormat($format);
                continue;
            }

            if (empty($line)) {

                continue;
            }

            ++ $reportNumber;
            
            foreach($line as &$info){
                $info = Utility::GbToUtf8(trim($info));
            }
            $list[] = $line;
        }

        setlocale(LC_ALL,NULL);

        Validate::testNull($list, '表中无内容,请检查后重新上传');

        $mapEnumeration = array();

        $addNums = 1;

        $listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
        $listSourceCode     = ArrayUtility::listField($list,'source_code');
        //根据导入数据查出所有的产品

        $produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
        $listMapSourceInfo = Source_Info::getBySourceCodeAndMulitSupplierId($listSourceCode,$produceOrderInfo['supplier_id']);

        $listSourceId       = ArrayUtility::listField($listMapSourceInfo,'source_id');
        Validate::testNull($listMapSourceInfo, '生产系统中不存在所有的买款ID');
        $indexSourceCode    = ArrayUtility::indexByField($listMapSourceInfo,'source_code');

        //获取生产订单中的所有产品
        $orderProductInfo   = Produce_Order_Product_Info::getByProduceOrderId($produceOrderId);

        $listProductId      = ArrayUtility::listField($orderProductInfo,'product_id');

        $listMapProudctInfo = Product_Info::getByMultiId($listProductId);

        $mapCategoryName    = Category_Info::listAll();
        $indexCategoryName  = ArrayUtility::indexByField($mapCategoryName,'category_name');
        
        $mapSpecInfo        = Spec_Info::listAll();
   
        $mapIndexSpecAlias  = ArrayUtility::indexByField($mapSpecInfo, 'spec_alias' ,'spec_id');
        $listSpecColorId    = array_unique(ArrayUtility::listField(Goods_Type_Spec_Value_Relationship::getBySpecId($mapIndexSpecAlias['color']),'spec_value_id'));
        $mapSpecValue       = Spec_Value_Info::getByMulitId($listSpecColorId);

        $mapEnumeration     = array(
           'mapIndexSpecAlias'    => $mapIndexSpecAlias,
           'mapSpecValue'         => $mapSpecValue,
           'mapSpecInfo'          => $mapSpecInfo,
           'indexCategoryName'    => $indexCategoryName,
           'listMapProudctInfo'   => $listMapProudctInfo,
           'listProductId'        => $listProductId,
           'indexSourceCode'      => $indexSourceCode,
        );

        $datas                  = array();

        foreach ($list as $offsetRow => $row) {
            
            $line  = $offsetRow+2;
            try{
                $productInfo    = Arrive::testStorage($row,$mapEnumeration);
                $datas[]        = $productInfo;
                $addNums++;

            }catch(ApplicationException $e){
                
                $errorList[]            = array(
                    'content'   => $e->getMessage(),
                    'line'      => $line ,
                );
                continue;
            }
        }

        if(!empty($errorList)){
            Produce_Order_Arrive_Info::update(array(
                'produce_order_arrive_id'   => $orderInfo['produce_order_arrive_id'],
                'order_file_status'         => Sales_Order_File_Status::ERROR,
                'error_log'                 => json_encode($errorList),
            ));
            exit;
        }

        $orderTotalWeight   = array_sum(ArrayUtility::listField($list,'weight'));
        $orderTotalQuantity = array_sum(ArrayUtility::listField($list,'quantity'));
        
        $indexOrderProductId    = ArrayUtility::indexByField($orderProductInfo,'product_id');
        $countProductId         = 0;
        foreach($datas as $storageProductInfo){

           //计算每个产品的平均重量
            $avegWeight = sprintf("%.2f",$storageProductInfo['weight']/$storageProductInfo['quantity']);

            //productId 排序(从小到大)
            asort($storageProductInfo['list_product_id']);

            $totalWeight    = 0;
            //获取最大的productID
            $maxProductId   = end($storageProductInfo['list_product_id']);

            foreach($storageProductInfo['list_product_id'] as $productId){

                //缺货数量
                $data           = array();
                $shortQuantity  = $indexOrderProductId[$productId]['short_quantity'];
                               
                if( $storageProductInfo['quantity'] == 0 ){
                    
                    continue;
                }
                //最后一个product
                if($productId == $maxProductId){
                    $quantity   = $storageProductInfo['quantity'];
                    $weight     = $storageProductInfo['weight'];
                    $data           = array(
                        'product_id'                => $productId,
                        'produce_order_arrive_id'   => $orderInfo['produce_order_arrive_id'],
                        'quantity'                  => $quantity,
                        'weight'                    => $weight,
                        'storage_weight'            => $weight,
                        'storage_quantity'          => $quantity,
                        'stock_quantity'            => $quantity,
                        'stock_weight'              => $weight,
                        'storage_cost'              => $storageProductInfo['cost'],
                    );
                    $storageProductInfo['quantity'] = 0;
                    $countProductId++;
                    Produce_Order_Arrive_Product_Info::create($data);
                    continue;
                }
                
                if( $shortQuantity <= 0 ){
                    
                    continue;
                }
                
                //缺货数量大于入库数量
                if($shortQuantity >= $storageProductInfo['quantity']){
                    
                    $quantity   = $storageProductInfo['quantity'];
                    $weight     = $storageProductInfo['weight'];
                    $data           = array(
                        'product_id'                => $productId,
                        'produce_order_arrive_id'   => $orderInfo['produce_order_arrive_id'],
                        'quantity'                  => $quantity,
                        'weight'                    => $weight,
                        'storage_weight'            => $weight,
                        'storage_quantity'          => $quantity,
                        'stock_quantity'            => $quantity,
                        'stock_weight'              => $weight,
                        'storage_cost'              => $storageProductInfo['cost'],
                    );
                    $storageProductInfo['quantity'] = 0;
                    $countProductId++;
                    Produce_Order_Arrive_Product_Info::create($data);
                    continue;
                }
           
                $quantity   = $shortQuantity;
                $weight     = $shortQuantity * $avegWeight;
                $data           = array(
                    'product_id'                => $productId,
                    'produce_order_arrive_id'   => $orderInfo['produce_order_arrive_id'],
                    'quantity'                  => $quantity,
                    'weight'                    => $weight,
                    'storage_weight'            => $weight,
                    'storage_quantity'          => $quantity,
                    'stock_quantity'            => $quantity,
                    'stock_weight'              => $weight,
                    'storage_cost'              => $storageProductInfo['cost'],
                );
                $storageProductInfo['quantity'] = $storageProductInfo['quantity'] - $quantity;
                $storageProductInfo['weight'] = $storageProductInfo['weight'] - $weight;
                $countProductId++;
                Produce_Order_Arrive_Product_Info::create($data);
            }
        }

        $arriveProductInfo  = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($orderInfo['produce_order_arrive_id']);

        Produce_Order_Arrive_Info::update(array(
            'produce_order_arrive_id'   => $orderInfo['produce_order_arrive_id'],
            'produce_order_id'          => $produceOrderId,
            'count_product'             => count($datas),
            'weight_total'              => array_sum(ArrayUtility::listField($arriveProductInfo,'weight')),
            'quantity_total'            => array_sum(ArrayUtility::listField($arriveProductInfo,'quantity')),
            'storage_weight'            => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_weight')),
            'storage_quantity_total'    => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_quantity')),
            'arrive_time'               => date('Y-m-d'),
        ));  
    }

}