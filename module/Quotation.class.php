<?php
/**
 * 报价单
 */
class   Quotation {
    
    /**
     * 验证报价单
     *
     * @param   array   $data   数据 
     * @param   array   $data   枚举数据
     */
    static public function testQuotation (array $data, array $mapEnumeration) {

        Validate::testNull($data['sku_code'],'买款ID不能为空');
        Validate::testNull($data['categoryLv3'],'三级分类不能为空');
        Validate::testNull($data['material_main_name'],'主料材质不能为空');
        Validate::testNull($data['size_name'],'规格尺寸不能为空');
        Validate::testNull($data['weight_name'],'规格重量不能为空');
        echo "<pre>";
        $categoryInfo       = ArrayUtility::searchBy($mapEnumeration['mapCategory'], array("category_name"=>$data['categoryLv3'],'category_level'=>2));
        
        if(empty($categoryInfo)){
            
            throw   new ApplicationException('产品分类不存在');
        }
        $indexCategoryName      = ArrayUtility::indexByField($categoryInfo,'category_name','goods_type_id');
        $goodsType              = $indexCategoryName[$data['categoryLv3']];
        $listTypeSpecValue      = ArrayUtility::searchBy($mapEnumeration['mapTypeSpecValue'], array('goods_type_id'=>$goodsType));
        $mapMatetialSpecValue   = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['material']));
        $mapWeightSpecValue     = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['weight']));
        $mapColorSpecValue      = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$mapEnumeration['mapIndexSpecAlias']['color']));
        
        foreach($listTypeSpecValue as $key=>$val){
            
            if(in_array($val['spec_id'],$mapEnumeration['mapSizeId'])){
                
                $sizeSpecId = $val['spec_id'];
                break;
            }
        }
        $mapSizeSpecValue       = ArrayUtility::searchBy($listTypeSpecValue,array("spec_id"=>$sizeSpecId));
       
        $data['material_id'] = self::_getSpecValueId($data['material_main_name'],$mapMatetialSpecValue,"主料材质不正确",$mapEnumeration);
        $data['weight_id']   = self::_getSpecValueId($data['weight_name'],$mapWeightSpecValue,"规格重量不正确",$mapEnumeration);
        //var_dump($mapEnumeration['mapSpecValue']);
        //die;
        
        return $data;
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
        //var_dump($mapGoodsTypeValue);
        $specValueInfo      = ArrayUtility::searchBy($mapEnumeration['mapSpecValue'],array("spec_value_data"=>$data));
        
        if(empty($specValueInfo)){

            throw   new ApplicationException($message);
        }
        $indexSpecValue = ArrayUtility::indexByField($specValueInfo,'spec_value_data','spec_value_id');
        $specGoodsValueInfo = ArrayUtility::searchBy($mapGoodsTypeValue,array("spec_value_id"=>'"'.$indexSpecValue[$data].'"'));
        var_dump($specGoodsValueInfo);
        
    }
}