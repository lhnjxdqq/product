<?php
/**
 * 报价单
 */
class   Quotation {
    
    /**
     * Excel导出缓冲区尺寸 (记录条数)
     */
    const   BUFFER_SIZE_EXCEL   = 1000;

    static private $_unusualCharacter = array(" ", "\n", "（", "）");
    
    /**
     * 验证报价单
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     * @param   string  $isSkuCode        是否忽略买款ID重复
     *
     * @return  array                     验证后的数据
     */
    static public function testQuotation (array $data, array $mapEnumeration, $isSkuCode = true, $supplierId) {

        Validate::testNull($data['sku_code'],'买款ID不能为空');
        Validate::testNull($data['categoryLv3'],'三级分类不能为空');
        Validate::testNull($data['material_main_name'],'主料材质不能为空');
        Validate::testNull($data['weight_name'],'规格重量不能为空');

        foreach (self::$_unusualCharacter as $unusalCharacter) {

            if (false !== strpos($data['sku_code'], $unusalCharacter)) {

                throw new ApplicationException('买款ID中有特殊字符');
            }
        }
        
        $data['weight_name'] = sprintf('%.2f', $data['weight_name']);
        
        $categoryInfo       = ArrayUtility::searchBy($mapEnumeration['mapCategory'], array("category_name"=>$data['categoryLv3'],'category_level'=>2));
        
        if(!$isSkuCode){
        
            $sourceInfo         = Source_Info::getBySourceCode($data['sku_code']);
            
            if(!empty($sourceInfo)){
                
                $searchSupplier     = ArrayUtility::searchBy($sourceInfo,array("supplier_id"=>$supplierId));

                if(!empty($searchSupplier)){
                 
                    throw   new ApplicationException('买款ID已经存在');   
                }
            }
        }
        
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
        
        if(!empty($data['size_name'])) {
                
            $listSize            = array_unique(explode(",",$data['size_name']));

            foreach($listSize as $key=>$val){
                
                $data['size'][] = self::_getSpecValueId($val,$mapSizeSpecValue,"规格尺寸不正确",$mapEnumeration);
            }
            $data['size'] = array_unique($data['size']);
        }
        
        foreach($data['price'] as $key=>$val){
            
            if(!empty($val)){
            
                $colorId = self::_getSpecValueId($key,$mapColorSpecValue,$key."颜色工费不正确",$mapEnumeration);
                if(!is_numeric($val)){
                
                    throw   new ApplicationException($key."颜色工费不正确");
                }
                $data['cost'][$colorId] = $val;
            }
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
        
        return $data;
    }
    
    /**
     *  导入报价单
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     * @param   string  $isSkuCode        是否忽略买款ID重复
     *
     * @return  array                     验证后的数据
     */
    static public function createQuotation(array $data, array $mapEnumeration,  $isSkuCode = true, $supplierId) {

        $data = self::testQuotation($data, $mapEnumeration, $isSkuCode, $supplierId);
        $content['suppplier_id'] = $supplierId;
        
        foreach($data['cost'] as $colorId=>$price) {
            
            if(!empty($data['size_name'])) {
                
                foreach($data['size'] as $key=>$sizeId){
                    
                    $goodsInfo[] = self::_createGoods($data,$sizeId,$colorId,$mapEnumeration,$supplierId);
                }
                
            }else{
                
                    $goodsInfo[] = self::_createGoods($data,$sizeId,$colorId,$mapEnumeration,$supplierId);
            }
        }
        //生成SPU SKU关系
        $goodsId = ArrayUtility::listField($goodsInfo, 'goods_id');
        $spuGoodsInfo           = Spu_Goods_RelationShip::getByMultiGoodsId($goodsId);
        if(!empty($data['list_spu_id'])){
            
            foreach($data['list_spu_id'] as $spuId){
 
                foreach($goodsInfo as $key=>$info) {
                    
                    $content =array(
                        'spu_id'            => $spuId,
                        'goods_id'          => $info['goods_id'],
                        'spu_goods_name'    => $info['goods_size'].$info['goods_color'],
                    );
                    Spu_Goods_RelationShip::create($content);
                } 
            }
            return ;
        }
        if(empty($spuGoodsInfo)){
            
            $indexCategoryId    = ArrayUtility::indexByField($mapEnumeration['mapCategory'], 'category_id');
            
            $spu['spu_sn']      = Spu_Info::createSpuSn($indexCategoryId[$data['category_id']]['category_sn']);
            $spu['spu_remark']  = $data['remark'];
            $spu['spu_name']    = $data['weight_name']."g".$data['material_main_name'].$data['categoryLv3'].$data['style_two_level'];

            $spuGoodsInfo['spu_id']['spu_id'] = Spu_Info::create($spu);
        }
        foreach($spuGoodsInfo as $mapSpuGoods=>$spuInfo) {
            
            foreach($goodsInfo as $key=>$info) {
                
                $content =array(
                    'spu_id'            => $spuInfo['spu_id'],
                    'goods_id'          => $info['goods_id'],
                    'spu_goods_name'    => $info['goods_size'].$info['goods_color'],
                );
                Spu_Goods_RelationShip::create($content);
            }
        }
        Common_Product::createImportSpuData($spuGoodsInfo['spu_id']['spu_id']);
    }
    
    /**
     *  修改新报价导入报价单
     *
     * @param   array   $data             数据 
     * @param   array   $mapEnumeration   枚举数据
     * @param   string  $isSkuCode        是否忽略买款ID重复
     *
     * @return  array                     验证后的数据
     */
    static public function updateCostcreateQuotation(array $data, array $mapEnumeration,  $isSkuCode = true, $supplierId) {

        $content['suppplier_id'] = $supplierId;
        
        foreach($data['cost'] as $colorId=>$price) {
            
            if(!empty($data['size_name'])) {
                
                foreach($data['size'] as $key=>$sizeId){
                    
                    $goodsInfo[] = self::_createGoods($data,$sizeId,$colorId,$mapEnumeration,$supplierId);
                }
                
            }else{
                
                    $goodsInfo[] = self::_createGoods($data,$sizeId,$colorId,$mapEnumeration,$supplierId);
            }
        }
        //生成SPU SKU关系
        $goodsId = ArrayUtility::listField($goodsInfo, 'goods_id');
        $spuGoodsInfo           = Spu_Goods_RelationShip::getByMultiGoodsId($goodsId);
        if(!empty($data['list_spu_id'])){
            
            foreach($data['list_spu_id'] as $spuId){
 
                foreach($goodsInfo as $key=>$info) {
                    
                    $content =array(
                        'spu_id'            => $spuId,
                        'goods_id'          => $info['goods_id'],
                        'spu_goods_name'    => $info['goods_size'].$info['goods_color'],
                    );
                    Spu_Goods_RelationShip::create($content);
                } 
            }
            return ;
        }
        if(empty($spuGoodsInfo)){
            
            $indexCategoryId    = ArrayUtility::indexByField($mapEnumeration['mapCategory'], 'category_id');
            
            $spu['spu_sn']      = Spu_Info::createSpuSn($indexCategoryId[$data['category_id']]['category_sn']);
            $spu['spu_remark']  = $data['remark'];
            $spu['spu_name']    = $data['weight_name']."g".$data['material_main_name'].$data['categoryLv3'].$data['style_two_level'];

            $spuGoodsInfo['spu_id']['spu_id'] = Spu_Info::create($spu);
        }
        foreach($spuGoodsInfo as $mapSpuGoods=>$spuInfo) {
            
            foreach($goodsInfo as $key=>$info) {
                
                $content =array(
                    'spu_id'            => $spuInfo['spu_id'],
                    'goods_id'          => $info['goods_id'],
                    'spu_goods_name'    => $info['goods_size'].$info['goods_color'],
                );
                Spu_Goods_RelationShip::create($content);
            }
        }
        Common_Product::createImportSpuData($spuGoodsInfo['spu_id']['spu_id']);
    }
    
    /**
     * 添加产品
     *  
     * @param   array   $data             数据 
     * @param   string  $sizeId           尺寸ID
     * @param   array   $colotId          颜色ID
     * @param   array   $mapEnumeration   枚举数据
     * @param   string  $isSkuCode        是否忽略买款ID重复
     *
     * @return  string                    SKU ID
     */
    static private function _createGoods(array $data,$sizeId = null,$colorId,array $mapEnumeration,$supplierId) {

        $sourceInfo                 = Source_Info::listByCondition(array(
            'source_code'   => $data['sku_code'],
            'supplier_id'   => $supplierId,
        ));
        $sourceId                   = $sourceInfo ? current($sourceInfo)['source_id'] : Source_Info::create(array(
            'source_code' => $data['sku_code'],
            'supplier_id' => $supplierId,
        ));
        
        $content['product_name']    = self::getProductName($data,$sizeId,$colorId,$mapEnumeration);
        $content['product_cost']    = $data['cost'][$colorId];
        $indexCategoryId = ArrayUtility::indexByField($mapEnumeration['mapCategory'], 'category_id');
        $productData    = array(
            'product_sn'        => Product_Info::createProductSn($indexCategoryId[$data['category_id']]['category_sn']),
            'product_name'      => $content['product_name'],
            'product_cost'      => sprintf('%.2f', $content['product_cost']),
            'source_id'         => $sourceId,
            'product_remark'    => $data['remark'],
        );    
        $specInfo = array(
        
            $data['size_spce_id']                               => $sizeId,
            $mapEnumeration['mapIndexSpecAlias']['material']    => $data['material_id'],
            $mapEnumeration['mapIndexSpecAlias']['weight']      => $data['weight_id'],
            $mapEnumeration['mapIndexSpecAlias']['color']       => $colorId,    
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
        
        $goodsId = Goods_Spec_Value_RelationShip::validateGoods($specValueList, $data['style_id'], $data['category_id']);

        if ($goodsId) {

            $productData['goods_id']    = $goodsId;

        } else {

            // 先新增一个商品
            $goodsData  = array(
                'goods_sn'      => Goods_Info::createGoodsSn($indexCategoryId[$data['category_id']]['category_sn']),
                'goods_name'    => $content['product_name'],
                'goods_type_id' => $data['goods_type_id'],
                'category_id'   => $data['category_id'],
                'self_cost'     => $productData['product_cost']+PLUS_COST,
                'sale_cost'     => $productData['product_cost']+PLUS_COST,
                'style_id'      => $data['style_id'] ? $data['style_id'] : 0,
                'goods_remark'  => $data['remark'],
            );
            
            // 记录商品的规格 和 规格值
            $goodsId                    = Goods_Info::create($goodsData);

            foreach ($specValueList as $specValue) {
                Goods_Spec_Value_Relationship::create(array(
                    'goods_id'      => $goodsId,
                    'spec_id'       => $specValue['spec_id'],
                    'spec_value_id' => $specValue['spec_value_id'],
                ));
            }
            Common_Product::createImportGoodsData($goodsId);
            // 新增产品
            $productData['goods_id']    = $goodsId;
        }
        
        $productId                  = Product_Info::create($productData);
     
        Cost_Update_Log_Info::create(array(
            'product_id'        => $productId,
            'cost'              => $productData['product_cost'],
            'update_means'      => Cost_Update_Log_UpdateMeans::BATCH,
        ));

        $goodsInfo                  = array(  
                'goods_id'   => $goodsId,
                'goods_size' => self::_getGoodsSize($data,$sizeId,$mapEnumeration),
                'goods_color'=> self::_getGoodsColor($colorId,$mapEnumeration),
            );
        return $goodsInfo;

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
     * 获取产品名称
     *  
     *  @param    array     $data           产品数据
     *  @param    stying    $sizeId         尺寸ID
     *  @param    stying    $colorID        颜色ID
     *  @param    array     $mapEnumeration 枚举数据
     *  
     *  @return   string                    产品名称
     */
    static public  function  getProductName ($data, $sizeId = NUll, $colorId,$mapEnumeration) {
        
        $goodsTypeValueByWeight = ArrayUtility::searchBy($mapEnumeration['mapTypeSpecValue'],array('goods_type_id'=>$data['goods_type_id'],'spec_value_id'=>$data['weight_id']));
       
        $indexWeightSpecId        = ArrayUtility::indexByField($goodsTypeValueByWeight,'spec_value_id');
        $specWeightId             = $indexWeightSpecId[$data['weight_id']]['spec_id'];
        $goodsWeightValue         = ArrayUtility::searchBy($mapEnumeration['mapSpecInfo'],array('spec_id'=>$specWeightId));
        $indexColorSpecId         = ArrayUtility::indexByField($goodsWeightValue,'spec_id');
        $weightSpecUnit           = $indexColorSpecId[$specWeightId]['spec_unit'];

        $sizeName           = self::_getGoodsSize($data, $sizeId ,$mapEnumeration);
        $specColorName          = self::_getGoodsColor($colorId, $mapEnumeration);          

        
        return $data['weight_name'].$weightSpecUnit.$data['product_name'].$sizeName.$specColorName.$data['style_two_level'];
    }
    
    /**
     * 获取颜色
     *
     * @param    stying    $sizeId         颜色ID
     * @param    array     $mapEnumeration 枚举数据 
     */
     static  private function _getGoodsColor($colorId,array $mapEnumeration){
        
        if(empty($colorId)){
            
            return ;
        }
        $goodsTypeValueByColor  = ArrayUtility::searchBy($mapEnumeration['mapSpecValue'],array('spec_value_id'=>$colorId));
        $indexSpecColorId       = ArrayUtility::indexByField($goodsTypeValueByColor,'spec_value_id');
        $specColorName          = $indexSpecColorId[$colorId]['spec_value_data'];    

        return $specColorName;

     }
     
    /**
     * 获取规格尺寸
     *
     * @param    array     $data           产品数据
     * @param    stying    $sizeId         尺寸ID
     * @param    array     $mapEnumeration 枚举数据
     */
     static private function _getGoodsSize ($data, $sizeId = NUll,$mapEnumeration) {
         
        if(empty($sizeId)){
            
            return ;
        }
        $goodsTypeValueBySize   = ArrayUtility::searchBy($mapEnumeration['mapTypeSpecValue'],array('goods_type_id'=>$data['goods_type_id'],'spec_value_id'=>$sizeId));
        $goodsTypeIndexSizeSpecId = ArrayUtility::indexByField($goodsTypeValueBySize,'spec_value_id');
        $specSizeId             = $goodsTypeIndexSizeSpecId[$sizeId]['spec_id'];
        $goodsSizeValue         = ArrayUtility::searchBy($mapEnumeration['mapSpecInfo'],array('spec_id'=>$specSizeId));
        $indexSizeSpecId        = ArrayUtility::indexByField($goodsSizeValue,'spec_id');
        $sizeSpecUnit           = $indexSizeSpecId[$specSizeId]['spec_unit'];
        $ValueBySize            = ArrayUtility::searchBy($mapEnumeration['mapSpecValue'],array('spec_value_id'=>$sizeId));
        $indexSpecSizeId        = ArrayUtility::indexByField($ValueBySize,'spec_value_id');
        $specSizeName           = $indexSpecSizeId[$sizeId]['spec_value_data'];
        
        return $specSizeName.$sizeSpecUnit;
    }
    
    /**
     *  获取不同颜色商品下的工费
     */
    static public function getGoodsCost($colorName,$specValueData,$mapAllGoodsInfo,$goodsId){
            
        if ($specValueData == $colorName) {

            return $mapAllGoodsInfo[$goodsId]['sale_cost'];
        }
        return 0;
    }
    
    static  public  function getExportExcelFileByHashCode ($code) {

        return  Config::get('path|PHP', 'sales_quotation_export') . $code . '.xlsx';
    }
    
    /**
     * 输出到流 excel格式
     */
     
    static  public  function outputExcel ($salesQuotationInfo, $stream) {
              
        $tableHead = "SPU编号,SPU名称,商品图片,买款ID,三级分类,主料材质,规格尺寸,规格重量,工费,备注";
        $colorPriceHead = "K红,K白,K黄,红白,红黄,黄白,三色";
        
        $tableHead          = explode(",",$tableHead);
        $colorPriceHead     = explode(",",$colorPriceHead);

        $order              = array();
        $condition          = array(
            'sales_quotation_id'    => $salesQuotationInfo['sales_quotation_id'],
        );
        $group              = 'spu_id';
        $listColorName      = explode(",","K红,K白,K黄,红白,红黄,黄白,三色");
        $colorSpecValueInfo = Spec_Value_Info::getByMultiValueData ($listColorName);
        $indexColorName     = ArrayUtility::indexByField($colorSpecValueInfo,'spec_value_data','spec_value_id');
        
        for ($offsetBuffer = 0;$offsetBuffer < $salesQuotationInfo['spu_num'];$offsetBuffer += self::BUFFER_SIZE_EXCEL) {
 
            $listDraw           = array();
            $excel              = ExcelFile::create();
            $sheet              = $excel->getActiveSheet();
            $sheet->mergeCells('A1:A2');
            $sheet->mergeCells('B1:B2');
            $sheet->mergeCells('C1:C2');
            $sheet->mergeCells('D1:D2');
            $sheet->mergeCells('E1:E2');
            $sheet->mergeCells('F1:F2');
            $sheet->mergeCells('G1:G2');
            $sheet->mergeCells('H1:H2');
            self::_saveExcelRow($sheet, 1, $tableHead);
            self::_saveExcelRow($sheet, 2, $colorPriceHead);
 
            $maxWidth           = 0;
    
            $listSalesQutationSpuInfo    = Sales_Quotation_Spu_Info::listByCondition($condition, $order, $group, $offsetBuffer, self::BUFFER_SIZE_EXCEL);

            //获取SQU组合
            $listSpuId       = ArrayUtility::listField($listSalesQutationSpuInfo,"spu_id");
            $listSpuInfo     = Spu_Info::getByMultiId($listSpuId);
          
            //获取SPU图片
            $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
            $mapSpuImages   = ArrayUtility::indexByField($listSpuImages, 'spu_id');
            foreach ($mapSpuImages as $spuId => $spuImage) {

                $mapSpuImages[$spuId]['image_url']  = AliyunOSS::getInstance('images-spu')->url($spuImage['image_key']);
            }

            $listSpecInfo       = Spec_Info::listAll();
            $listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
            $mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

            $listSpecValueInfo  = Spec_Value_Info::listAll();
            $listSpecValueInfo  = ArrayUtility::searchBy($listSpecValueInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
            $mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
            //获取规格尺寸和主料材质的属性ID
            $specMaterialInfo     = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'material')),'spec_alias','spec_id');
            $specSizeInfo         = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'size')),'spec_alias','spec_id');
            $specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
            $specMaterialId       = $specMaterialInfo['material'];
            $specSizeId           = $specSizeInfo['size'];
            $specColorId          = $specColorInfo['color'];

            // 查询SPU下的商品
            $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
            $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
            $listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

            // 查所当前所有SPU的商品 商品信息 规格和规格值
            $allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
            $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
            $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
            $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

            // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
            $mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
            $listGoodsId    = array_values($mapSpuGoods);
            $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
            $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

            // 根据商品查询品类
            $listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
            $listCategory   = Category_Info::getByMultiId($listCategoryId);
            $mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');
            $mapProductInfo = Product_Info::getByMultiGoodsId($listGoodsId);
            $groupSkuSourceId   = ArrayUtility::groupByField($mapProductInfo,'goods_id','source_id');
            $listSourceId   = ArrayUtility::listField($mapProductInfo,'source_id');
            $mapSourceInfo  = Source_Info::getByMultiId($listSourceId);
            $indexSourceInfo= ArrayUtility::indexByField($mapSourceInfo,'source_id','source_code');
            $groupSkuSourceId   = ArrayUtility::groupByField($mapProductInfo,'goods_id','source_id');
            $groupProductIdSourceId = array();
            foreach($groupSkuSourceId as $productId => $sourceIdInfo){

                $groupProductIdSourceId[$productId]    = array();
                foreach($sourceIdInfo as $key=>$sourceId){

                    $groupProductIdSourceId[$productId][] = $indexSourceInfo[$sourceId];   
                }
            }

            // 根据商品查询规格重量
            $listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

            $mapSpecValue   = array();
            $mapMaterialValue = array();
            $mapSizeValue = array();
            foreach ($listSpecValue as $specValue) {

                $specName       = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
                $specValueData  = $mapSpecValueInfo[$specValue['spec_value_id']]['spec_value_data'];
                if ($specName == '规格重量') {

                    $mapWeightValue[$specValue['goods_id']] = $specValueData;
                }
            }
            
            $mapColorInfo           = array();
            $sourceId          = array();
            foreach ($groupSpuGoods as $spuId => $spuGoods) {

                $spuCost    = array();
                foreach ($spuGoods as $goods) {

                    $goodsId        = $goods['goods_id'];
                    $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];
                    $listSourceId   = $groupProductIdSourceId[$goods['goods_id']];
                    if(!empty($listSourceId)){

                        $sourceId[$spuId][]= implode(',',$listSourceId);
                    }
                    foreach ($goodsSpecValue as $key => $val) {

                        $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                        if($val['spec_id']  == $specMaterialId){
                            
                            $mapMaterialValue[$spuId][]  = $specValueData;
                        }
                        if($val['spec_id']  == $specSizeId){
                            
                            $mapSizeValue[$spuId][]  = $specValueData;
                        }
                        if($val['spec_id'] == $specColorId) {

                            $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
                        }
                    }
                }
                
                if(!empty($sourceId[$spuId])){

                    $sourceId[$spuId]      = implode(",",$sourceId[$spuId]);   
                }
                $mapSizeValue[$spuId]     = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
                $mapMaterialValue[$spuId] = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";

                foreach($mapColor as $spuIdKey => $colorInfo){

                    foreach($colorInfo as $colorId => $cost){

                        rsort($cost);
                        $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
                    }
                }
             
  
            }         
            //获取颜色属性Id列表
            $listSpecValueColotId   = array();
            
            foreach($mapColorInfo as $spuId=>$colorCost){
                
                foreach($colorCost as $specColorId=>$cost){
                    
                    $listSpecValueColotId[$specColorId] = $specColorId;
                }
            }
            
            $countColor         = count($listSpecValueColotId);
            $mapColorValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueColotId);
            //确定颜色表头
            $mapSpecColorId     = ArrayUtility::indexByField($mapColorValueInfo,'spec_value_id', 'spec_value_data');
                        
            $col = 0;
            foreach($mapSpecColorId as $colorId=>$colorName){
            
                $row = PHPExcel_Cell::stringFromColumnIndex(ord(I)+$col-ord(A)).'2';
               
                $sheet->setCellValue($row, $colorName);
                $col++;
            }

            $colorCosrCol       = PHPExcel_Cell::stringFromColumnIndex(ord(I)+($col-1)-ord(A))."1";
            $sheet->mergeCells("I1:".$colorCosrCol);
            $remarkCol  = PHPExcel_Cell::stringFromColumnIndex(ord(I)+$col-ord(A));
            $remarkCol1 = $remarkCol."1"; 
            $remarkCol2 = $remarkCol."2"; 
            $sheet->mergeCells($remarkCol1 .':'. $remarkCol2);
            $sheet->setCellValue($remarkCol.'1', "备注");
    
            $mapEnumeration = array(
                'sales_quotation_id'=> $salesQuotationInfo['sales_quotation_id'],
                'mapSpuGoods'       => $mapSpuGoods,
                'mapSizeValue'      => $mapSizeValue,
                'mapMaterialValue'  => $mapMaterialValue,
                'mapWeightValue'    => $mapWeightValue,
                'mapGoodsInfo'      => $mapGoodsInfo,
                'mapCategory'       => $mapCategory,
                'indexColorName'    => $indexColorName,
                'mapSpecColorId'    => $mapSpecColorId,
                'sourceId'          => $sourceId,    
            );
            
            foreach ($listSpuInfo as $offsetInfo => $info) {
                
                $row        = self::_getExcelRow($info,$mapEnumeration);

                $numberRow  = $offsetInfo + 3;
                self::_saveExcelRow($sheet, $numberRow, array_values($row));
                $draw       = self::_appendExcelImage($sheet, $numberRow, $row, $mapSpuImages[$info['spu_id']]['image_url']);

                if ($draw instanceof PHPExcel_Worksheet_MemoryDrawing) {
                    
                    $imageWidth = $draw->getWidth();
                    $maxWidth   = $maxWidth < $imageWidth   ? $imageWidth   : $maxWidth; 
                    $sheet->getRowDimension($numberRow)->setRowHeight($draw->getHeight() * (3 / 4));
                    $listDraw[] = $draw;
                }
            }

            if ($maxWidth > 0) {
            
                $sheet->getColumnDimension('C')->setWidth($maxWidth / 7.2);
            }
            

                $writer   = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                $writer->save($stream);
            }
    }
    
    /**
     *
     */
    static private function _getExcelRow($info,$mapEnumeration) {

        $goodsId    = $mapEnumeration['mapSpuGoods'][$info['spu_id']];

        if (!$goodsId) {
            
            $categoryName = '';
            $weightName   = '';
        } else {

            $categoryId   = $mapEnumeration['mapGoodsInfo'][$goodsId]['category_id'];
            $categoryName = $mapEnumeration['mapCategory'][$categoryId]['category_name'];
            $materialName = !empty($mapEnumeration['mapMaterialValue'][$info['spu_id']]) ? implode(",",$mapEnumeration['mapMaterialValue'][$info['spu_id']]): '';
            $sizeName     = !empty($mapEnumeration['mapSizeValue'][$info['spu_id']]) ? implode(",",$mapEnumeration['mapSizeValue'][$info['spu_id']]) : '';
            $weightName   = $mapEnumeration['mapWeightValue'][$goodsId];
                
        }
        $condition = array(
            'spu_id'                => $info['spu_id'],
            'sales_quotation_id'    => $mapEnumeration['sales_quotation_id'],
        );
        $order    = array();
        $salesQuotationSquInfo = Sales_Quotation_Spu_Info::listByCondition($condition, $order, 0, 0, 100);
        $indexColorId          = ArrayUtility::indexByField($salesQuotationSquInfo, 'color_id', 'cost');
        $indexSalesQuotationId = ArrayUtility::indexByField($salesQuotationSquInfo, 'spu_id', 'sales_quotation_remark');

        foreach($mapEnumeration['mapSpecColorId'] as $colorId=>$colorName){
            
            $color[$colorId]    = !empty($indexColorId[$colorId]) ? $indexColorId[$colorId] : "-";
        }
        $spuInfo                = array(
        
            'spu_sn'                => $info['spu_sn'],
            'spu_name'              => $info['spu_name'],
            'image'                 => '',
            'score_id'              => $mapEnumeration['sourceId'][$info['spu_id']],
            'category_name'         => $categoryName,
            'material_name'         => $materialName,
            'size_name'             => $sizeName,
            'weight_name'           => $weightName,
        );
        $spuInfo = array_merge($spuInfo,$color);
        $spuInfo['remark']      = $indexSalesQuotationId[$info['spu_id']];
        
        return $spuInfo;
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
    
    /**
     * 获取已存在的异步导出文件
     *
     * @return  array   已存在的文件列表
     */
    static  public  function listExistsExportFile () {

        $listFile   = glob(Config::get('path|PHP', 'sales_quotation_export') . '*.xlsx');
        $mapFile    = array();

        foreach ($listFile as $filePath) {

            $mapFile[basename($filePath, '.xlsx')]   = $filePath;
        }

        return      $mapFile;
    }
    
    /**
     * 保存成本报价信息
     */
    static  public function updateCostStore(array $data, array $mapEnumeration,  $isSkuCode = true, $updateCostId, $supplierId){
 
        $data = self::testQuotation($data, $mapEnumeration, $isSkuCode, $supplierId);
        $sourceInfo  = Source_Info::getBySourceCodeAndSupplierId($data['sku_code'],$supplierId);
        $sourceId    = $sourceInfo['source_id'];
        if(!empty($sourceId)){
            
            $productInfo    = Product_Info::getByMultiSourceId(array($sourceId));
            $listProductId  = ArrayUtility::listField($productInfo,'product_id');
            if($listProductId){
                $listProductId = implode(',',$listProductId);
            }
        }
        Update_Cost_Source_Info::create(array(
            'update_cost_id'            => $updateCostId,
            'source_code'               => $data['sku_code'],
            'json_data'                 => json_encode($data),
            'relationship_product_id'   => $listProductId,
        ));
    }
    
    /**
     * 生成销售报价单信息日志
     */
    static public function salesQuotationLogFile($salesQuotationId) {
        
        //获取报价单信息
        $salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);

        $indexCartColorId   = array();

        //获取客户列表
        $listCustomer       = Customer_Info::listAll();
        $indexCustomerId    = ArrayUtility::indexByField($listCustomer,'customer_id');

        $orderBy                = array();
        $perpage                = self::BUFFER_SIZE_EXCEL;
        $condition['sales_quotation_id']   = $salesQuotationId;
        $group              = 'spu_id'; 
        $countSpu   = Sales_Quotation_Spu_Info::countBySalesQuotationId($salesQuotationId);

        $page           = new PageList(array(
            PageList::OPT_TOTAL     => $countSpu,
            PageList::OPT_URL       => '/sales_quotation/detail.php',
            PageList::OPT_PERPAGE   => $perpage,
        ));
        $listQuotationSpuInfo    = Sales_Quotation_Spu_Info::listByCondition($condition, $orderBy, $group, $page->getOffset(), $perpage);
        $listSpuId               = ArrayUtility::listField($listQuotationSpuInfo,'spu_id');

        //获取颜色工费和备注
        $indexCartColorId   = array();
        $mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationIdAndMuitlSpuId($salesQuotationId , $listSpuId);
        $spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');

        foreach($spuInfo as $spuId=>$info){
            
            $indexCartColorId[$spuId]['color'] = ArrayUtility::indexByField($info, 'color_id', 'cost');
            $indexCartColorId[$spuId]['sales_quotation_remark'] = ArrayUtility::indexByField($info, 'spu_id', 'sales_quotation_remark');
        }

        $listSpuInfo     = Spu_Info::getByMultiId($listSpuId);
        //获取SPU数量
        $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
        $mapSpuImages   = ArrayUtility::indexByField($listSpuImages, 'spu_id');
        foreach ($mapSpuImages as $spuId => $spuImage) {

            //$mapSpuImages[$spuId]['image_url']  = AliyunOSS::getInstance('images-spu')->url(imagesSKU);
            $mapSpuImages[$spuId]['image_url']  = $spuImage['image_key'] ? 'imagesSPU/'.$spuImage['image_key'].'.jpg' : '';
        }

        //属性列表
        $listSpecInfo       = Spec_Info::listAll();
        $listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
        $mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

        //获取属性值
        $listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
        $mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

        // 查询SPU下的商品
        $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
        $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
        $listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

        // 查所当前所有SPU的商品 商品信息 规格和规格值
        $allGoodsInfo           = ArrayUtility::searchBy(Goods_Info::getByMultiId($listAllGoodsId), array('online_status'=>Goods_OnlineStatus::ONLINE, 'delete_status'=>Goods_DeleteStatus::NORMAL));
        $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
        $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
        $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');
        
        // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
        $mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
        $listGoodsId    = array_values($mapSpuGoods);
        $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

        $listGoodsImages            = Goods_Images_RelationShip::getByMultiGoodsId($listGoodsId);
        $mapGoodsImages             = ArrayUtility::indexByField($listGoodsImages, 'goods_id');

        // 根据商品查询品类
        $listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
        $listCategory   = Category_Info::getByMultiId($listCategoryId);
        $mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');
        $mapProductInfo = Product_Info::getByMultiGoodsId($listGoodsId);
        $listSourceId   = ArrayUtility::listField($mapProductInfo,'source_id');
        $mapSourceInfo  = Source_Info::getByMultiId($listSourceId);
        $indexSourceInfo= ArrayUtility::indexByField($mapSourceInfo,'source_id','source_code');
        $groupSkuSourceId   = ArrayUtility::groupByField($mapProductInfo,'goods_id','source_id');
        $groupProductIdSourceId = array();
        foreach($groupSkuSourceId as $productId => $sourceIdInfo){
            
            $groupProductIdSourceId[$productId]    = array();
            foreach($sourceIdInfo as $key=>$sourceId){

                $groupProductIdSourceId[$productId][] = $indexSourceInfo[$sourceId];   
            }
        }
        // 根据商品查询规格重量
        $listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

        //获取规格尺寸和主料材质的属性ID
        $specMaterialInfo     = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'material')),'spec_alias','spec_id');
        $specWeightInfo       = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'weight')),'spec_alias','spec_id');
        $specSizeInfo         = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'size')),'spec_alias','spec_id');
        $specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
        $specMaterialId       = $specMaterialInfo['material'];
        $specSizeId           = $specSizeInfo['size'];
        $specWeightId         = $specWeightInfo['weight'];
        $specColorId          = $specColorInfo['color'];

        $mapSpecValue   = array();
        $mapMaterialValue = array();
        $mapSizeValue = array();

        $spuCost    = array();
        $mapSpuSalerCostByColor = array();
        $sourceId   = array();
        foreach ($groupSpuGoods as $spuId => $spuGoods) {

            $mapColor   = array();
            foreach ($spuGoods as $goods) {

                $goodsId        = $goods['goods_id'];
                $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];
                $listSourceId   = $groupProductIdSourceId[$goods['goods_id']];

                foreach ($goodsSpecValue as $key => $val) {

                    $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                    if($val['spec_id']  == $specMaterialId) {
                        
                        $mapGoodsValue[$val['goods_id']]['spec_material']  = $specValueData;
                        $mapGoodsValueMaterialId[$val['goods_id']]         = $val['spec_value_id'];
                    }
                    if($val['spec_id']  == $specWeightId) {
                        
                        $mapGoodsValue[$val['goods_id']]['spec_weight']    = $specValueData;
                        $mapGoodsValueWeightId[$val['goods_id']]           = $val['spec_value_id'];
                    }
                    if($val['spec_id']  == $specSizeId) {
                        
                        $mapGoodsValue[$val['goods_id']]['spec_size']  = $specValueData;
                        $mapGoodsValueSizeId[$val['goods_id']]         = $val['spec_value_id'];
                    }
                    if($val['spec_id']  == $specColorId) {
                        
                        $mapGoodsValue[$val['goods_id']]['spec_color']          = $specValueData;
                        $mapGoodsValueColorId[$val['goods_id']]['color_id']     = $val['spec_value_id'];
                        $mapColor[$spuId][$val['spec_value_id']][]              = $mapAllGoodsInfo[$goodsId]['sale_cost'];
                    }
                
                }
            }

            foreach($mapColor as $spuIdKey => $colorInfo){

                foreach($colorInfo as $colorId => $cost){
                    
                    rsort($cost);
                    $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
                }
            }
        }
        //获取颜色属性Id列表
        $listSpecValueColotId   = array();

        foreach($mapColorInfo as $spuId=>$colorCost){
            
            foreach($colorCost as $specColorId=>$cost){
                
                $listSpecValueColotId[$specColorId] = $specColorId;
            }
        }
        $countColor         = count($listSpecValueColotId);
        $mapColorValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueColotId);
        $mapSpecColorId     = ArrayUtility::indexByField($mapColorValueInfo,'spec_value_id', 'spec_value_data');

        // 每个SPU下有哪些goodsId
        $groupSpuGoodsId    = array();
        foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

            $groupSpuGoodsId[$spuId] = ArrayUtility::listField($spuGoodsList, 'goods_id');
        }
        
        // 整合数据, 方便前台输出
        foreach ($listSpuInfo as $key => $spuInfo) {

            // 品类名 && 规格重量
     
            $listSpuInfo[$key]['color'] = array();
        
            foreach($mapSpecColorId as $colorId=>$colorName){
                
                if($indexCartColorId[$spuInfo['spu_id']]['color'][$colorId]){
                    
                    $listSpuInfo[$key]['color'][$colorId] = !empty($mapColorInfo[$spuInfo['spu_id']][$colorId]) ? $indexCartColorId[$spuInfo['spu_id']]['color'][$colorId] : "-";
                    $listSpuInfo[$key]['spu_remark'] = $indexCartColorId[$spuInfo['spu_id']]['sales_quotation_remark'][$spuInfo['spu_id']];
                }
                        
            }
            $listSpuInfo[$key]['image_path'] = $mapSpuImages[$spuInfo['spu_id']]['image_url'];
            
            $listSpuInfo[$key]['goods']     = array();

            foreach($groupSpuGoods[$spuInfo['spu_id']] as $row => $info){

                $goodsId                    = $info['goods_id'];
                $colorCost                  = (float) $listSpuInfo[$key]['color'][$mapGoodsValueColorId[$goodsId]['color_id']];
                if((empty($colorCost) || !is_numeric($colorCost)) && $colorCost == 0){
                    
                    continue;
                }
                $listSpuInfo[$key]['goods'][$goodsId]['goods_id']       = $goodsId;
                $listSpuInfo[$key]['goods'][$goodsId]['goods_sn']       = $mapAllGoodsInfo[$goodsId]['goods_sn'];
                $listSpuInfo[$key]['goods'][$goodsId]['goods_name']     = $mapAllGoodsInfo[$goodsId]['goods_name'];
                $listSpuInfo[$key]['goods'][$goodsId]['sale_cost']      = $listSpuInfo[$key]['color'][$mapGoodsValueColorId[$goodsId]['color_id']];
                $listSpuInfo[$key]['goods'][$goodsId]['style_id']       = $mapAllGoodsInfo[$goodsId]['style_id'];
                $listSpuInfo[$key]['goods'][$goodsId]['category_id']    = $mapAllGoodsInfo[$goodsId]['category_id'];
                
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['material']['spec_value_id']     = $mapGoodsValueMaterialId[$goodsId];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['material']['spec_value_data']   = $mapGoodsValue[$goodsId]['spec_material'];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['size']['spec_value_id']         = $mapGoodsValueSizeId[$goodsId];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['size']['spec_value_data']       = $mapGoodsValue[$goodsId]['spec_size'];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['color']['spec_value_id']        = $mapGoodsValueColorId[$goodsId]['color_id'];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['color']['spec_value_data']      = $mapGoodsValue[$goodsId]['spec_color'];;
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['weight']['spec_value_id']       = $mapGoodsValueWeightId[$goodsId];
                $listSpuInfo[$key]['goods'][$goodsId]['spec']['weight']['spec_value_data']     = $mapGoodsValue[$goodsId]['spec_weight'];
                $imageKey   = $mapGoodsImages[$goodsId]['image_key'];
                //$listSpuInfo[$key]['goods'][$goodsId]['image_path']     = $imageKey ? AliyunOSS::getInstance('images-sku')->url($imageKey) : '';
                $listSpuInfo[$key]['goods'][$goodsId]['image_path']     = $imageKey ? 'imagesSKU/'.$spuImage['image_key'].'.jpg' : '';
            }
            unset($listSpuInfo[$key]['color']);
            
        }
        $listSpuInfo['quotation']['customer_id']            = $salesQuotationInfo['customer_id'];
        $listSpuInfo['quotation']['sales_quotation_id']     = $salesQuotationInfo['sales_quotation_id'];
        $listSpuInfo['quotation']['sales_quotation_name']   = $salesQuotationInfo['sales_quotation_name'];

        $path   = self::getFilePathBySalesQuotationId();
        $stream                     = Config::get('path|PHP', 'sales_quotation_product').$path;

        if (!is_dir($stream)) {
            
            mkdir($stream, 0766, true);
        }
        $filePath   = $stream.$salesQuotationInfo['sales_quotation_id'] . '.log';
        if(file_put_contents($filePath,json_encode($listSpuInfo))){
            
            return $path.$salesQuotationInfo['sales_quotation_id'] . '.log';
        }
    }
    
    /**
     * 获取文件储存位置
     *
     * @return  array   已存在的文件列表
     */
    static  public  function getFilePathBySalesQuotationId () {

        $year       = date('Y',time());
        $month      = date('m',time());
        
        return   $year .'/' . $month .'/';
    }
}
