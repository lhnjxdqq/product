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

        $listProductInfo                    = ArrayUtility::searchBy($mapEnumeration['listMapProudctInfo'],array('source_id'=>$data['source_id']));
        
        Validate::testNull($listProductInfo,'买款ID不存生产系统中');
        $mapEnumeration['listGoodsId']      = ArrayUtility::listField($listProductInfo,'goods_id');

        Validate::testNull($data['categoryLv3'],'三级分类不能为空');
        Validate::testNull($data['material_main_name'],'主料材质不能为空');
        Validate::testNull($data['weight_name'],'规格重量不能为空');
        Validate::testNull($data['weight'],'重量不能为零或者空');
        
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
        
        return Goods_Spec_Value_RelationShip::getGoodsIdByValueList($specValueList, $data['style_id'], $data['category_id'],$mapEnumeration['listGoodsId']);

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
    
    
}   