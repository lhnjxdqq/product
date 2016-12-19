<?php
/**
 * 借版
 */
class   Borrow {
        
    /**
     * Excel导出缓冲区尺寸 (记录条数)
     */
    const   BUFFER_SIZE_EXCEL   = 1000;

    /**
     * 输出到流 excel格式
     */
     
    static  public  function outputExcel ($taskInfo) {

        $tableHead = "SKU编号,买款ID,产品图片,三级分类,主料材质,规格重量,规格尺寸,颜色,基础销售工费,进货工费,备注,样板类型,状态";
        
        $tableHead          = explode(",",$tableHead);
        $order              = array();

        $listDraw           = array();
        $excel              = ExcelFile::create();
        $sheet              = $excel->getActiveSheet();
        $sheet->getRowDimension(1)->setRowHeight(-1);
        self::_saveExcelRow($sheet, 1, $tableHead);

        $maxWidth           = 0;
        $condition['borrow_id']     = $taskInfo['borrow_id'];
        $borrowInfo                 = Borrow_Info::getByBorrowId($taskInfo['borrow_id']);
        $stream                     = self::getFilePathByBorrowId($taskInfo['borrow_id']);
        $dir                        = pathinfo($stream, PATHINFO_DIRNAME);
        if (!is_dir($dir)) {
            
            mkdir($dir, 0766, true);
        }
        $borrowStatus               = Borrow_Status::getBorrowStatus();
        foreach($borrowStatus as $statusId => $statusName){
            $borrowStatusInfo[$statusId]['status_id']       = $statusId;
            $borrowStatusInfo[$statusId]['status_name']     = $statusName;
        }
        $orderBy                    = array();
        $countGoods                 = Borrow_Goods_Info::countByCondition($condition);
        $listBorrowGoodsInfo        = Borrow_Goods_Info::listByCondition($condition);
        $listGoodsId                = ArrayUtility::listField($listBorrowGoodsInfo,'goods_id');
        $listSampleType = Sample_Type::getSampleType();

        foreach($listSampleType as $key=>$val){
            $sampleType[$key]['type_name']    =  $val;
        }

        $condition['list_goods_id'] = $listGoodsId;         
        $listgoodsInfo              = Goods_List::listByCondition($condition);

        $listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
        $listSourceId   = ArrayUtility::listField($listGoodsProductInfo,'source_id');
        $mapSourceInfo  = Source_Info::getByMultiId($listSourceId);
        $indexSourceInfo= ArrayUtility::indexByField($mapSourceInfo,'source_id','source_code');
        $groupSkuSourceId   = ArrayUtility::groupByField($listGoodsProductInfo,'goods_id','source_id');
        $groupProductIdSourceId = array();
        foreach($groupSkuSourceId as $productId => $sourceIdInfo){
            
            $groupProductIdSourceId[$productId]    = array();
            foreach($sourceIdInfo as $key=>$sourceId){

                $groupProductIdSourceId[$productId][] = $indexSourceInfo[$sourceId];   
            }
        }
        $listCategoryInfo           = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
        $mapCategoryInfo            = ArrayUtility::indexByField($listCategoryInfo, 'category_id');
        $listCategoryInfoLv3        = ArrayUtility::searchBy($listCategoryInfo, array('category_level'=>2));
        $mapCategoryInfoLv3         = ArrayUtility::indexByField($listCategoryInfoLv3, 'category_id');

        $listSupplierInfo           = Supplier_Info::listAll();
        $mapSupplierInfo            = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

        $listStyleInfo              = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
        $groupStyleInfo             = ArrayUtility::groupByField($listStyleInfo, 'parent_id');

        $weightSpecInfo             = Spec_Info::getByAlias('weight');
        $listWeightSpecValue        = Goods_Type_Spec_Value_Relationship::getBySpecId($weightSpecInfo['spec_id']);
        $listWeightSpecValueId      = array_unique(ArrayUtility::listField($listWeightSpecValue, 'spec_value_id'));
        $listWeightSpecValueInfo    = Spec_Value_Info::getByMulitId($listWeightSpecValueId);
        $mapWeightSpecValueInfo     = ArrayUtility::indexByField($listWeightSpecValueInfo, 'spec_value_id');

        $sizeSpecInfo               = Spec_Info::getByAlias('size');
        $listSizeSpecValue          = Goods_Type_Spec_Value_Relationship::getBySpecId($sizeSpecInfo['spec_id']);
        $listSizeSpecValueId        = array_unique(ArrayUtility::listField($listSizeSpecValue, 'spec_value_id'));
        $listSizeSpecValueInfo      = Spec_Value_Info::getByMulitId($listSizeSpecValueId);
        $mapSizeSpecValueInfo       = ArrayUtility::indexByField($listSizeSpecValueInfo, 'spec_value_id');

        $colorSpecInfo              = Spec_Info::getByAlias('color');
        $listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
        $listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
        $listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
        $mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

        $materialSpecInfo           = Spec_Info::getByAlias('material');
        $listMaterialSpecValue      = Goods_Type_Spec_Value_Relationship::getBySpecId($materialSpecInfo['spec_id']);
        $listMaterialSpecValueId    = array_unique(ArrayUtility::listField($listMaterialSpecValue, 'spec_value_id'));
        $listMaterialSpecValueInfo  = Spec_Value_Info::getByMulitId($listMaterialSpecValueId);
        $mapMaterialSpecValueInfo   = ArrayUtility::indexByField($listMaterialSpecValueInfo, 'spec_value_id');

        $listGoodsInfo              = $countGoods <= 0 ? array() : Goods_List::listByCondition($condition);
        $listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
        $mapGoodsImages             = ArrayUtility::indexByField($listGoodsImages, 'goods_id');
        $listGoodsProductInfo       = Product_Info::getByMultiGoodsId($listGoodsId);
        $groupGoodsProductInfo      = ArrayUtility::groupByField($listGoodsProductInfo, 'goods_id');
        $mapGoodsProductMinCost     = array();
        foreach ($groupGoodsProductInfo as $goodsId => $goodsProductList) {

            $goodsProductList   = ArrayUtility::sortMultiArrayByField($goodsProductList, 'product_cost');
            $goodsProductInfo   = current($goodsProductList);
            $mapGoodsProductMinCost[$goodsId]   = $goodsProductInfo['product_cost'];
        }

        $listMaterialValueId        = ArrayUtility::listField($listGoodsInfo, 'material_value_id');
        $listSizeValueId            = ArrayUtility::listField($listGoodsInfo, 'size_value_id');
        $listColorValueId           = ArrayUtility::listField($listGoodsInfo, 'color_value_id');
        $listWeightValueId          = ArrayUtility::listField($listGoodsInfo, 'weight_value_id');
        $listSpecValueId            = array_unique(array_merge(
            $listMaterialValueId,
            $listSizeValueId,
            $listColorValueId,
            $listWeightValueId
        ));
        $listSpecValueInfo          = Spec_Value_Info::getByMulitId($listSpecValueId);
        $mapSpecValueInfo           = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
        $mapSampleInfo              = Sample_Info::getByMultiId($listGoodsId);
        $indexGoodsIdType           = ArrayUtility::indexByField($mapSampleInfo,'goods_id','sample_type');

        $mapEnumeration = array(
            'mapCategoryInfo'            => $mapCategoryInfo,
            'mapCategoryInfoLv3'         => $mapCategoryInfoLv3,
            'mapSupplierInfo'            => $mapSupplierInfo,
            'groupStyleInfo'             => $groupStyleInfo,
            'mapWeightSpecValueInfo'     => $mapWeightSpecValueInfo,
            'mapSizeSpecValueInfo'       => $mapSizeSpecValueInfo,
            'mapColorSpecValueInfo'      => $mapColorSpecValueInfo,
            'mapMaterialSpecValueInfo'   => $mapMaterialSpecValueInfo,
            'mapSpecValueInfo'           => $mapSpecValueInfo,
            'listGoodsInfo'              => $listGoodsInfo,
            'sampleType'                 => $sampleType,
            'borrowStatusInfo'           => $borrowStatusInfo,
        );
        
        foreach ($listGoodsInfo as $offsetInfo => $goodsInfo) {
            
            $goodsId    = $goodsInfo['goods_id'];
            $imageKey   = $mapGoodsImages[$goodsId]['image_key'];
            $goodsInfo['image_url']     = $imageKey
                ? AliyunOSS::getInstance('images-sku')->url($imageKey)
                : '';
            $goodsInfo['product_cost']  = $mapGoodsProductMinCost[$goodsId];
            $goodsInfo['source']        = implode(',', $groupProductIdSourceId[$goodsId]);
            $goodsInfo['sample_type']   = $indexGoodsIdType[$goodsId];

            $row        = self::_getExcelRow($goodsInfo,$mapEnumeration,$borrowInfo);

            $numberRow  = $offsetInfo + 2;
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
        
            $sheet->getColumnDimension('C')->setWidth($maxWidth / 7.2);
        }
        $writer   = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save($stream);

        return $stream;
        
    }
    
    /**
      * 获取文件路径
      *
      */
    static  public  function getFilePathByBorrowId ($borrowId) {

        $year       = date('Y');
        $month      = date('m');
        
        return   Config::get('path|PHP', 'borrow_export') . $year .'/' . $month .'/'. $borrowId. '.xlsx';
    }
    
    /**
     *
     */
    static private function _getExcelRow(array $info,array $mapEnumeration,array $borrowInfo) {

        return  array(
            'goods_sn'      => $info['goods_sn'],
            'source'        => $info['source'],
            'image'         => '',
            'category'      => $mapEnumeration['mapCategoryInfo'][$info['category_id']]['category_name'],
            'material'      => $mapEnumeration['mapSpecValueInfo'][$info['material_value_id']]['spec_value_data'],
            'weight'        => $mapEnumeration['mapSpecValueInfo'][$info['weight_value_id']]['spec_value_data'],
            'size'          => $mapEnumeration['mapSpecValueInfo'][$info['size_value_id']]['spec_value_data'],
            'color'         => $mapEnumeration['mapSpecValueInfo'][$info['color_value_id']]['spec_value_data'],
            'sale_cost'     => $info['sale_cost'],
            'product_cost'  => $info['product_cost'],
            'remark'        => $info['goods_remark'],
            'sample_type'   => $mapEnumeration['sampleType'][$info['sample_type']]['type_name'],
            'status'        => $mapEnumeration['borrowStatusInfo'][$borrowInfo['status_id']]['status_name'],
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

        $coordinate = $sheet->getCellByColumnAndRow(2, $numberRow)->getCoordinate();
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