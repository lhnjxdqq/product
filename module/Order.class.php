<?php
/**
 * 报价单
 */
class   Order {

    /**
     * 验证销售订单
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     *
     * @return  array                     验证后的数据
     */
    static public function testOrder (array $data, array $mapEnumeration) {

        Validate::testNull($data['spu_sn'],'spu编号不能为空');
        Validate::testNull($mapEnumeration['indexSpuSn'][$data['spu_sn']],'spu编号不存在销售报价单中');
        $data['spu_id']         = $mapEnumeration['indexSpuSn'][$data['spu_sn']];
        Validate::testNull($data['categoryLv3'],'三级分类不能为空');
        Validate::testNull($data['material_main_name'],'主料材质不能为空');
        Validate::testNull($data['weight_name'],'规格重量不能为空');
        
        if($data['quantity'] == 0 || empty($data['quantity'])){
            
            throw   new ApplicationException('产品数量不能为零,且不能为空');
        }
        
        $data['weight_name'] = sprintf('%.2f', $data['weight_name']);
        
        $categoryInfo       = ArrayUtility::searchBy($mapEnumeration['mapCategory'], array("category_name"=>$data['categoryLv3'],'category_level'=>2));

        
        if(empty($categoryInfo)){
            
            throw   new ApplicationException('产品分类不存在');
        }
        $indexCategoryName      = ArrayUtility::indexByField($categoryInfo,'category_name');
        $data['category_id']    = $indexCategoryName[$data['categoryLv3']]['category_id'];      
        $goodsType              = $indexCategoryName[$data['categoryLv3']]['goods_type_id'];
        $data['goods_type_id']     = $goodsType;
        $listTypeSpecValue      = ArrayUtility::searchBy($mapEnumeration['mapTypeSpecValue'], array('goods_type_id'=>$goodsType));
        $mapMatetialSpecValue   = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['material']));
        $mapWeightSpecValue     = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['weight']));
        $mapColorSpecValue      = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['color']));
        
        foreach($listTypeSpecValue as $key=>$val){
            
            if(in_array($val['spec_id'],$mapEnumeration['mapSizeId'])){
                
                $sizeSpecId = $val['spec_id'];
                $data['size_spce_id']   =$sizeSpecId;
                break;
            }
        }
        
        $mapSizeSpecValue    = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$sizeSpecId));

        $data['material_id'] = trim($data['material_id']);
        $data['weight_id']   = trim($data['weight_id']);
        $data['material_id'] = self::_getSpecValueId($data['material_main_name'],$mapMatetialSpecValue,"主料材质不正确",$mapEnumeration);
        $data['weight_id']   = self::_getSpecValueId($data['weight_name'],$mapWeightSpecValue,"规格重量不正确",$mapEnumeration);
        $data['color_id']    = self::_getSpecValueId($data['color_name'],$mapColorSpecValue,"颜色不正确",$mapEnumeration);
        
        if(!empty($data['size_name'])) {
                
                $data['size_id'] = self::_getSpecValueId($data['size_name'],$mapSizeSpecValue,"规格尺寸不正确",$mapEnumeration);
        }
        
        if(!empty($data['style_two_level'] && !empty($data['style_one_level']))){
            
            $styleOneLevelInfo  = ArrayUtility::searchBy($mapEnumeration['mapStyle'],array('style_name'=>$data['style_one_level'],'style_level'=>0));
            Validate::testNull($styleOneLevelInfo, "款式不正确");
            $indexStyleOneLevelName = ArrayUtility::indexByField($styleOneLevelInfo,'style_name','style_id');
            $styleTwoInfo  = ArrayUtility::searchBy($mapEnumeration['mapStyle'],array('style_name'=>$data['style_two_level'],'parent_id'=>$indexStyleOneLevelName[$data['style_one_level']]));
            Validate::testNull($styleTwoInfo, "子款式不正确");
            $indexTwoLevelStyleOneLevelName = ArrayUtility::indexByField($styleTwoInfo,'style_name','style_id');
            $data['style_id']   = $indexTwoLevelStyleOneLevelName[$data['style_two_level']];
            
        }
        
        $goodsId  = self::_getGoodsId($data,$mapEnumeration);
        Validate::testNull($goodsId, '不存在该规格的产品');
        $data['goods_id'] = $goodsId;

        return $data;        
    }
    
    /**
     * 判断skuID是否存在,返回goods_id
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     *
     * return   int                       goods_id
     */
    static private function _getGoodsId($data,$mapEnumeration){
        
        $specInfo = array(
            $data['size_spce_id']                               => $data['size_id'],
            $mapEnumeration['mapIndexSpecAlias']['material']    => $data['material_id'],
            $mapEnumeration['mapIndexSpecAlias']['weight']      => $data['weight_id'],
            $mapEnumeration['mapIndexSpecAlias']['color']       => $data['color_id'],    
        );
        $specValueList  = array();
        foreach($specInfo as $specId=>$specName){
            if(empty($specName)){
                
                continue;
            }
            $specValueList[]    = array(
                'spec_id'       => $specId,
                'spec_value_id' => $specName,
            );   
        }
        
        return Goods_Spec_Value_RelationShip::getGoodsIdByValueList($specValueList, $data['style_id'], $data['category_id'],$mapEnumeration['groupSpuId'][$data['spu_id']]);

    }
    
    /**
     * 判断属性值是否正确,返回属性ID
     *
     * @param   string  $data               属性值
     * @param   array   $mapGoodsTypeValue  产品类型对应属性值
     * @param   string  $message            对应错误提示
     * @param   string  $mapEnumeration     数据
     * @return  string                      枚举值ID
     */
    static  private function _getSpecValueId ($data,array $mapGoodsTypeValue,$message,$mapEnumeration){

        $specValueInfo      = ArrayUtility::searchBy($mapEnumeration['mapSpecValue'],array("spec_value_data"=>$data));

        if(empty($specValueInfo)){

            throw   new ApplicationException($message);
        }
        $indexSpecValue = ArrayUtility::indexByField($specValueInfo,'spec_value_data','spec_value_id');
        $specGoodsValueInfo = ArrayUtility::searchBy($mapGoodsTypeValue,array("spec_value_id"=>$indexSpecValue[$data]));
        if(empty($specGoodsValueInfo)){
            
            throw   new ApplicationException($message);
        }
        return $indexSpecValue[$data];
        
    }
    /**
      * 获取文件路径
      *
      */
    static  public  function getFilePathByOrderSn ($salesOrderSn) {

        $year       = substr($salesOrderSn,0,4);
        $month      = substr($salesOrderSn,4,2);
        
        return   Config::get('path|PHP', 'sales_order_import') . $year .'/' . $month .'/'. $salesOrderSn. '.xlsx';
    }
        
    /**
     * 输出到流 excel格式
     */
    static  public  function outputShortExcel ($taskInfo) {

        $tableHead = "产品图片,产品编号,SKU编号,SPU编号,买款ID,三级分类,款式,子款式,主料材质,颜色,规格重量,规格尺寸,下单件数,下单重量,入库重量,入库件数,缺货件数,缺货重量,缺货比率";
        
        $tableHead          = explode(",",$tableHead);
        $order              = array();

        $listDraw           = array();
        $excel              = ExcelFile::create();
        $sheet              = $excel->getActiveSheet();
        $sheet->getRowDimension(1)->setRowHeight(-1);
        self::_saveExcelRow($sheet, 1, $tableHead);

        $maxWidth           = 0;
        $filePath                   = self::getFilePathByBorrowId($taskInfo['produce_order_id']);
        $stream                     = Config::get('path|PHP', 'shortages_export').$filePath;
        $dir                        = pathinfo($stream, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            
            mkdir($dir, 0766, true);
        }

        $produceOrderId             = $taskInfo['produce_order_id'];
        //到货单中的产品
        $listProduceOrderProduct    = Produce_Order_Product_Info::getShortByProduceOrderId($produceOrderId);

        $indexProductId             = ArrayUtility::indexByField($listProduceOrderProduct,'product_id');
        $listProductId              = ArrayUtility::listField($listProduceOrderProduct,'product_id');
        $produceOrderInfo           = Produce_Order_Info::getById($produceOrderId);
        
        if (!$produceOrderInfo) {

            Utility::notice('生产订单不存在');
        }
        // 生产订单详情
        $listOrderProduct   = Produce_Order_List::getDetailByMultiProduceOrderId((array) $produceOrderId);
       
        $mapProductImage    = Common_Product::getProductThumbnail($listProductId);
        $listGoodsId        = ArrayUtility::listField($listOrderProduct, 'goods_id');
        $mapGoodsSpuList    = Common_Spu::getGoodsSpu($listGoodsId);
        $listGoodsSpecValue = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
        $mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
        $produceOrderInfo['count_goods']    = count($listOrderProduct);
        $produceOrderInfo['count_quantity'] = 0;
        $produceOrderInfo['count_weight']   = 0;
        foreach ($listOrderProduct as $orderProduct) {

            $goodsId            = $orderProduct['goods_id'];
            $quantity           = $orderProduct['quantity'];
            $weightValueData    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
            $produceOrderInfo['count_quantity'] += $quantity;
            $produceOrderInfo['count_weight']   += $quantity * $weightValueData;
        }
        // 供应商信息
        $supplierId         = $produceOrderInfo['supplier_id'];
        $supplierInfo       = Supplier_Info::getById($supplierId);
        // 销售订单信息
        $salesOrderId       = $produceOrderInfo['sales_order_id'];
        $salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
        // 客户信息
        $customerId         = $salesOrderInfo['customer_id'];
        $customerInfo       = Customer_Info::getById($customerId);
        // 用户信息
        $listUserInfo       = User_Info::listAll();
        $mapUserInfo        = ArrayUtility::indexByField($listUserInfo, 'user_id', 'username');
        // 分类信息
        $listCategoryInfo   = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
        $mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
        // 款式信息
        $listStyleInfo      = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
        $mapStyleInfo       = ArrayUtility::indexByField($listStyleInfo, 'style_id');

        $condition['produce_order_id']  = $produceOrderId;
        $condition['delete_status']     = Produce_Order_DeleteStatus::NORMAL;
        $condition['list_product_id']   = $listProductId;
        $perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;

        // 分页
        $page               = new PageList(array(
            PageList::OPT_TOTAL     => Produce_Order_Product_List::countByCondition($condition),
            PageList::OPT_URL       => '',
            PageList::OPT_PERPAGE   => $perpage,
        ));

        $listOrderDetail    = Produce_Order_Product_List::listByCondition($condition, array(), $page->getOffset(), $perpage);

        if(empty($listProductId)){

            $listOrderDetail = array();
        }
        foreach ($listOrderDetail as &$detail) {

            $goodsId        = $detail['goods_id'];
            $productId      = $detail['product_id'];
            $categoryId     = $detail['category_id'];
            $childStyleId   = $detail['style_id'];
            $parentStyleId  = $mapStyleInfo[$childStyleId]['parent_id'];
            $detail['category_name']        = $mapCategoryInfo[$categoryId]['category_name'];
            $detail['parent_style_name']    = $mapStyleInfo[$parentStyleId]['style_name'];
            $detail['child_style_name']     = $mapStyleInfo[$childStyleId]['style_name'];
            $detail['weight_value_data']    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
            $detail['size_value_data']      = $mapGoodsSpecValue[$goodsId]['size_value_data'];
            $detail['color_value_data']     = $mapGoodsSpecValue[$goodsId]['color_value_data'];
            $detail['material_value_data']  = $mapGoodsSpecValue[$goodsId]['material_value_data'];
            $detail['spu_list']             = $mapGoodsSpuList[$goodsId];
            $detail['image_url']            = $mapProductImage[$productId]['image_url'];
    
            $detail['order_weight']         = $mapGoodsSpecValue[$goodsId]['weight_value_data'] * $indexProductId[$productId]['quantity'];
            $detail['order_quantity']       = $indexProductId[$productId]['quantity'];
            $detail['short_quantity']       = $indexProductId[$productId]['short_quantity'];
            $detail['short_weight']         = $indexProductId[$productId]['short_weight'];
            $detail['storage_weight']       = $detail['order_weight'] - $indexProductId[$productId]['short_weight'];
            $detail['storage_quantity']     = $indexProductId[$productId]['quantity'] - $indexProductId[$productId]['short_quantity'];
            if(!empty($indexProductId[$productId]['quantity'])){

                $detail['short_ratio']          = sprintf('%.4f',($indexProductId[$productId]['short_quantity'])/($indexProductId[$productId]['quantity']));
                
            }
            $listIsArrive[] = $detail['is_arrive'];
        }
        if(!empty($listOrderDetail)){
            
            foreach($listOrderDetail as $key => $info){
                
                $fieldArr[$key] = $info['short_ratio'];
            }

            array_multisort($fieldArr,SORT_DESC,$listOrderDetail);            
        }
        $data['produceOrderInfo']   = $produceOrderInfo;
        $data['supplierInfo']       = $supplierInfo;
        $data['customerInfo']       = $customerInfo;
        $data['mapUserInfo']        = $mapUserInfo;
        $data['listOrderDetail']    = $listOrderDetail;
        $data['pageViewData']       = $page->getViewData();
        $data['mapOrderType']       = Produce_Order_Type::getOrderType();        

        $numberRow = 1;
        foreach ($listOrderDetail as $offsetInfo => $goodsInfo) {
            
            $numberRow++;
            $goodsId    = $goodsInfo['goods_id'];
            $goodsInfo['image_url']     = $goodsInfo['image_url'];
            $goodsInfo['refund_quantity']   = $refundQuantity;
            $goodsInfo['refund_weight']     = $refundWeight;
            $row        = self::_getExcelRow($goodsInfo,$data);

            self::_saveExcelRow($sheet, $numberRow, array_values($row));
            $draw       = self::_appendExcelImage($sheet, $numberRow, $row, $goodsInfo['image_url']);

            if ($draw instanceof PHPExcel_Worksheet_MemoryDrawing) {
                
                $imageWidth = $draw->getWidth();
                $maxWidth   = $maxWidth < $imageWidth   ? $imageWidth   : $maxWidth; 
                $sheet->getRowDimension($numberRow)->setRowHeight($draw->getHeight() * (3 / 4));
                
            }
        }
        $listDraw[] = $draw;
        if ($maxWidth > 0) {
        
            $sheet->getColumnDimension('A')->setWidth($maxWidth / 7.2);
        }
        $writer   = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save($stream);

        return $filePath;
        
    }
    
    /**
      * 获取文件路径
      *
      */
    static  public  function getFilePathByBorrowId ($orderId) {

        $year       = date('Y');
        $month      = date('m');
        
        return  $year .'/' . $month .'/'. $orderId. '.xlsx';
    }
    
    /**
     *
     */
    static private function _getExcelRow(array $info,array $data) {

        return  array(
            'image_url'             => '',
            'product_sn'            => $info['product_sn'],
            'goods_sn'              => $info['goods_sn'],
            'spu_sn'                => implode(',',ArrayUtility::listField($info['spu_list'],'spu_sn')),
            'source_code'           => $info['source_code'],
            'categoryLv3'           => $info['category_name'],
            'parentStyleName'       => $info['parent_style_name'],
            'childStyleName'        => $info['child_style_name'],
            'material_value_data'   => $info['material_value_data'],
            'color_value_data'      => $info['color_value_data'],
            'weight_value_data'     => $info['weight_value_data'],
            'size_value_data'       => $info['size_value_data'],
            'quantity'              => $info['order_quantity'],
            'weight'                => $info['order_weight'],
            'storage_weight'        => $info['storage_weight'],
            'storage_quantity'      => $info['storage_quantity'],
            'short_quantity'        => $info['short_quantity'],
            'short_weight'          => $info['short_weight'],
            'short_ratio'           => ($info['short_ratio']*100).'%',
        );
    }
        
    static private function _appendExcelImage ($sheet, $numberRow, array $row, $imagePath) {

        if (empty($imagePath)) {

            return  ;
        }

        if(!@fopen( $imagePath, 'r' ) ) 
        { 
            return ;
        }

        $coordinate = $sheet->getCellByColumnAndRow(0, $numberRow)->getCoordinate();
        $draw       = self::_loadImage($imagePath);

        if ($draw instanceof PHPExcel_Worksheet_MemoryDrawing) {

            $draw->setWorksheet($sheet);
            $draw->setCoordinates($coordinate);

            return      $draw;
        }
    }
    
    
    static private function _loadImage ($path) {

        $info   = getimagesize($path);

        switch ($info['mime']) {
            case    'image/jpeg'    :
                $image  = imagecreatefromjpeg($path);
                break;

            case    'image/png'     :
                $image  = imagecreatefrompng($path);
                break;

            case    'image/gif'     :
                $image  = imagecreatefromgif($path);
                break;

            default :
                return  ;
        }
        // 更改图像资源大小
        $height = 150;
        $image = self::_resizeImage($image, $height);

        $draw   = new PHPExcel_Worksheet_MemoryDrawing();
        $draw->setImageResource($image);
        $draw->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $draw->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);

        return  $draw;
    }
    
    
    /**
     * 重置图像资源大小
     *
     * @param  resource  $srcImage  源图像资源
     * @param  int       $dstHeight 更改后的图像资源高度
     * @return resource  $dstImage  更改后的图像资源
     */
    static private function _resizeImage ($srcImage, $dstHeight) {
        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);
        if ($srcHeight <= $dstHeight) {

            return $srcImage;
        }
        # 重新生成图像资源
        $dstWidth = ($dstHeight/$srcHeight)*$srcWidth;
        $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);
        imagecopyresized($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
        return $dstImage;
    }
    
    static private function _saveExcelRow ($sheet, $rowNumber, $listCell) {

        foreach ($listCell  as $cellOffset => $cellValue) {

            $sheet->setCellValueByColumnAndRow($cellOffset, $rowNumber, $cellValue);
        }
    }
    
    /**
     * 通过API传输过来数据生成订单
     */
    static public function apiCreate(array $orderInfo) {

        $customerId     = $orderInfo['customerId'];
        $orderInfo      = $orderInfo['orderGoodsList'];
        $mapGoodsId     = ArrayUtility::listField($orderInfo,'goodsId');
        $listSalesQuotationId     = implode(",",array_unique(ArrayUtility::listField($orderInfo,'quotationId')));
        $goodsInfo      = Goods_Info::getByMultiId($mapGoodsId);
        $indexGoodsId   = ArrayUtility::indexByField($goodsInfo,'goods_id');
        $auPrice        = Au_Price_Log::getNewsPrice();
        $customeInfo    = Customer_Info::getById($customerId);
        Validate::testNull($customeInfo['salesperson_id'], '客户渠道拓展未补全');
        Validate::testNull($customeInfo['commodity_consultant_id'], '客户商品顾问未补全');

        $salesOrderId    = Sales_Order_Info::create(array(
            'sales_order_sn'            => Sales_Order_Info::createOrderSn(),
            'sales_order_status'        => Sales_Order_Status::NEWS,
            'sales_quotation_id'        => $listSalesQuotationId,
            'create_user_id'            => 0,
            'salesperson_id'            => $customeInfo['salesperson_id'],
            'commodity_consultant_id'   => $customeInfo['commodity_consultant_id'],
            'order_time'                => date('Y-m-d',time()),
            'create_time'               => date('Y-m-d H:i:s',time()),
            'update_time'               => date('Y-m-d H:i:s',time()),
            'order_type_id'             => Sales_Order_Type::ORDERED,
            'audit_person_id'           => 0,
            'customer_id'               => $customerId,
            'create_order_au_price'     => $auPrice,
        ));
        $referenceAmount            = 0;
        $estimatedCost              = 0;

        foreach($orderInfo as $key => $info) {
             
            $content    = array(
                'sales_order_id'    => $salesOrderId,
                'goods_id'          => $info['goodsId'],
                'goods_quantity'    => (int)$info['quantity'],
                'reference_weight'  => ((int) $info['quantity']) * $info['specWeight'],
                'sales_quotation_id'=> $info['quotationId'],
                'spu_id'            => $info['spuId'],
                'cost'              => $info['cost'],
                'remark'            => $info['remark'],
            );
            
            if($indexGoodsId[$info['goodsId']]['valuation_type'] == Valuation_TypeInfo::GRAMS){//克
                
                $estimatedCost+= $info['cost']*((int) $info['quantity']) * $info['specWeight'];
            }else if($indexGoodsId[$info['goodsId']]['valuation_type'] == Valuation_TypeInfo::PIECE){//件
                
                $estimatedCost+= $info['cost']*((int) $info['quantity']);
            }
            
            $referenceAmount+= (($auPrice * 0.75)+$info['cost'])*((int) $info['quantity']) * $info['specWeight'];
            Sales_Order_Goods_Info::create($content);
           
        }     
                
        $salesSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);

        Sales_Order_Info::update(array(
                'sales_order_id'    => $salesOrderId,
                'count_goods'       => count($salesSkuInfo),    
                'quantity_total'    => array_sum(ArrayUtility::listField($salesSkuInfo,'goods_quantity')),
                'update_time'       => date('Y-m-d H:i:s', time()),
                'reference_weight'  => array_sum(ArrayUtility::listField($salesSkuInfo,'reference_weight')),
                'reference_amount'  => $referenceAmount,
                'estimated_cost'    => $estimatedCost,
            )
        );
        
        return $salesOrderId;
    }
}   