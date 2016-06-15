<?php
/**
 * 报价单
 */
class   Quotation {
    
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
        
        $data['weight_name'] = sprintf('%.2f', $data['weight_name']);
        
        $categoryInfo       = ArrayUtility::searchBy($mapEnumeration['mapCategory'], array("category_name"=>$data['categoryLv3'],'category_level'=>2));
        
        if(!$isSkuCode){
        
            $sourceInfo         = Source_Info::getBySourceCode($data['sku_code']);
            if(!empty($sourceInfo)){
                
                throw   new ApplicationException('买款ID已经存在');
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
        if(empty($spuGoodsInfo)){
            
            $indexCategoryId    = ArrayUtility::indexByField($mapEnumeration['mapCategory'], 'category_id');
            
            $spu['spu_sn']      = Spu_Info::createSpuSn($indexCategoryId[$data['category_id']]['category_sn']);
            $spu['spu_remark']  = $data['remark'];
            $spu['spu_name']    = $data['weight_name']."g".$data['material_main_name'].$data['categoryLv3'];

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
                'self_cost'     => $productData['product_cost']+2,
                'sale_cost'     => $productData['product_cost']+2,
                'style_id'      => $data['style_id'] ? $data['style_id'] : 0,
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
            // 新增产品
            $productData['goods_id']    = $goodsId;
        }
        
        $productId                  = Product_Info::create($productData);
        
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

        
        return $data['weight_name'].$weightSpecUnit.$data['product_name'].$sizeName.$specColorName;
    }
    
    /**
     * 获取颜色
     *
     * @param    stying    $sizeId         颜色ID
     * @param    array     $mapEnumeration 枚举数据 
     */
     static  public function _getGoodsColor($colorId,array $mapEnumeration){
        
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
     static public function _getGoodsSize ($data, $sizeId = NUll,$mapEnumeration) {
         
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
}