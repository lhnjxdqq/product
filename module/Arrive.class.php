<?php
/**
 * 报价单
 */
class   Arrive {

    /**
     * 验证销售订单
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     *
     * @return  array                     验证后的数据
     */
    static public function testStorage (array $data, array $mapEnumeration) {

        Validate::testNull($data['source_code'],'买款ID不能为空');
        $data['source_id']                  = $mapEnumeration['indexSourceCode'][$data['source_code']]['source_id'];
        Validate::testNull($data['source_id'],'买款ID不存生产订单中');
        $listProductInfo                    = ArrayUtility::searchBy($mapEnumeration['listMapProudctInfo'],array('source_id'=>$data['source_id']));

        Validate::testNull($listProductInfo,'买款ID不存生产订单中');
        $mapEnumeration['listGoodsId']      = ArrayUtility::listField($listProductInfo,'goods_id');
        Validate::testNull($data['quantity'],'数量不能为空');
        Validate::testNull($data['weight'],'重量不能为零或者空');
        Validate::testNull($data['color_name'],'颜色不能为空');
        Validate::testNull($data['cost'],'成本工费不能为空');

        if($data['quantity'] == 0 || empty($data['quantity'])){
            
            throw   new ApplicationException('产品数量不能为零,且不能为空');
        }

        $data['color_id']    = empty($data['color_name']) ? '' :self::_getSpecValueId($data['color_name'],$mapEnumeration['mapSpecValue'],"颜色不正确",$mapEnumeration);

        $data['weight'] = sprintf('%.2f', $data['weight']);

        $mapGoodsId  = self::_getGoodsId($data,$mapEnumeration);
        Validate::testNull($mapGoodsId, '买款ID为' . $data['source_code'] . '颜色为' . $data['color_name'] . '的产品不存在');

        //获取买款ID下对应订单的产品
        $listGoodsId        = ArrayUtility::listField($mapGoodsId,'goods_id');
        $data['goods_id']   = $listGoodsId;
        
        //获取spu
        $spuGoodsInfo       = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
        $listSpuId          = array_unique(ArrayUtility::listField($spuGoodsInfo,'spu_id'));

        //判断spu唯一
        if(count($listSpuId) == 1){

            $mapProductInfo = Product_Info::getByMultiGoodsId($listGoodsId);
            $listProductIdA = ArrayUtility::listField($listProductInfo,'product_id');
            $listProductIdB = ArrayUtility::listField($mapProductInfo,'product_id');
            $listProductId  = array_intersect($listProductIdA , $listProductIdB);
            $data['list_product_id'] = $listProductId;
            return $data;
        }
     
        //根据买款id+颜色+三级分类判断唯一
        $data['category_id']    = $mapEnumeration['indexCategoryName'][$data['categoryLv3']]['category_id'];
        
        if(!empty($data['category_id'])){

            $listGoodsInfo      = Goods_Info::getByMultiId($listGoodsId);          
            $searchByCategoryId = ArrayUtility::searchBy($listGoodsInfo , array('category_id'=>$data['category_id']));
            $listGoodsId        = ArrayUtility::listField($searchByCategoryId,'goods_id');
            $spuGoodsInfo       = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
            $listSpuId          = array_unique(ArrayUtility::listField($spuGoodsInfo,'spu_id'));

            if(count($listSpuId) == 1){

                $mapProductInfo = Product_Info::getByMultiGoodsId($listGoodsId);
                $listProductIdA = ArrayUtility::listField($listProductInfo,'product_id');
                $listProductIdB = ArrayUtility::listField($mapProductInfo,'product_id');
                $listProductId  = array_intersect($listProductIdA , $listProductIdB);
                $data['list_product_id'] = $listProductId;

                return $data;
            }
        }

        Validate::testNull($data['product_sn'],'找不到对应的产品，请补充产品编号');
        $productInfo            = Product_Info::getBySn($data['product_sn']);
        Validate::testNull($productInfo,'产品编号为' . $data['product_sn'] . '的产品不存在');
        
        if(empty(ArrayUtility::searchBy($listProductInfo,array('product_id'=>$productInfo['product_id'])))){
        
            throw   new ApplicationException('产品编号为' . $data['product_sn'] . '的产品不存在');
        }
        $data['list_product_id'] = array($productInfo['product_id']);
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

        return Goods_Spec_Value_RelationShip::getListGoodsIdByValueList($specValueList, $data['style_id'], $data['category_id'],$mapEnumeration['listGoodsId']);

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
     * 输出到流 excel格式
     */
    static  public  function outputExcel ($taskInfo) {

        $tableHead = "产品图片,产品编号,SKU编号,SPU编号,买款ID,三级分类,款式,子款式,主料材质,颜色,规格重量,规格尺寸,到货件数,到货重量,入库重量,入库件数,退货件数,退货重量,工费";
        
        $tableHead          = explode(",",$tableHead);
        $order              = array();

        $listDraw           = array();
        $excel              = ExcelFile::create();
        $sheet              = $excel->getActiveSheet();
        $sheet->getRowDimension(1)->setRowHeight(-1);
        self::_saveExcelRow($sheet, 1, $tableHead);

        $maxWidth           = 0;
        $condition['produce_order_arrive_id']     = $taskInfo['produce_order_arrive_id'];
        $borrowInfo                 = Borrow_Info::getByBorrowId($taskInfo['produce_order_arrive_id']);
        $filePath                   = self::getFilePathByBorrowId($taskInfo['produce_order_arrive_id']);
        $stream                     = Config::get('path|PHP', 'refund_export').$filePath;
        $dir                        = pathinfo($stream, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            
            mkdir($dir, 0766, true);
        }

        $produceOrderId             = $taskInfo['produce_order_id'];
        //到货单中的产品
        $arriveProductInfo          = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($taskInfo['produce_order_arrive_id']);
        $indexProductId             = ArrayUtility::indexByField($arriveProductInfo,'product_id');
        $listProductId              = ArrayUtility::listField($arriveProductInfo,'product_id');

        $produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
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
            PageList::OPT_URL       => '/order/produce/arrive_detail.php',
            PageList::OPT_PERPAGE   => $perpage,
        ));

        $listOrderDetail    = Produce_Order_Product_List::listByCondition($condition, array(), $page->getOffset(), $perpage);

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

            $detail['arrive_weight']    = $indexProductId[$productId]['weight'];
            $detail['storage_weight']   = $indexProductId[$productId]['storage_weight'];
            $detail['arrive_quantity']  = $indexProductId[$productId]['quantity'];
            $detail['storage_quantity'] = $indexProductId[$productId]['storage_quantity'];

            $listIsArrive[] = $detail['is_arrive'];
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
            
            $refundQuantity             = $goodsInfo['arrive_quantity'] - $goodsInfo['storage_quantity'];
            $refundWeight               = $goodsInfo['arrive_weight'] - $goodsInfo['storage_weight'];
            
            if($goodsInfo['arrive_quantity'] == 0 || $goodsInfo['arrive_weight'] == 0 || $refundQuantity == 0 || $refundWeight == 0){
                
                continue;
            }
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
    static  public  function getFilePathByBorrowId ($arriveId) {

        $year       = date('Y');
        $month      = date('m');
        
        return  $year .'/' . $month .'/'. $arriveId. '.xlsx';
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
            'quantity'              => $info['arrive_quantity'],
            'weight'                => $info['arrive_weight'],
            'storage_weight'        => $info['storage_weight'],
            'storage_quantity'      => $info['storage_quantity'],
            'refund_quantity'       => $info['refund_quantity'],
            'refund_weight'         => $info['refund_weight'],
            'cost'                  => $info['product_cost'],
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
    
}   