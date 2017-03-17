<?php
class Spu_Attribute {

    /**
     * spu属性数据整理
     *
     * @param $attrInfo array  属性数据
     * @param $mapSpuSn array  spu数据
     */
    static public function createSpuAttr($attrInfo = array(),$mapSpuSn = array()){
        
        if(empty($attrInfo) || empty($mapSpuSn)){
            
            return ;
        }

        if(empty($attrInfo['spuSn']) || empty($mapSpuSn[$attrInfo['spuSn']])){
            
            return ;
        }
        $spuId  = $mapSpuSn[$attrInfo['spuSn']];     
        
        self::_brandAndStyleAndKeywords($attrInfo,$spuId);//品牌、风格、关键词
        self::_technicAttr($attrInfo,$spuId);//工艺
        self::_elementAttr($attrInfo,$spuId);//元素
        self::_shapeAttr($attrInfo,$spuId);//形状
        self::_mainstoneAttr($attrInfo,$spuId);//主石
        return ;
    }
    
    /**
     * 修改spu元素信息
     *
     * @param $attrInfo array  数据
     * @param $spuId    int    spuId
     */
    static private function _elementAttr (array $attrInfo, $spuId)  {
        
        Spu_Element_Relationship::delBySpuId($spuId);
        
        if(empty($attrInfo['elementId'])){
            
            return ;
        }
        $element    = explode(",",$attrInfo['elementId']);
        
        foreach($element as $elementId){
            Spu_Element_Relationship::create(array(
                'element_id'    => $elementId,
                'spu_id'        => $spuId,
            ));
        }
    }
    
    /**
     * 修改spu工艺信息
     *
     * @param $attrInfo array  数据
     * @param $spuId    int    spuId
     */
    static private function _technicAttr (array $attrInfo, $spuId)  {
        
        Spu_Technic_Relationship::delBySpuId($spuId);
        
        if(empty($attrInfo['technicId'])){
            
            return ;
        }
        $technic    = explode(",",$attrInfo['technicId']);
        
        foreach($technic as $technicId){
            Spu_Technic_Relationship::create(array(
                'technic_id'    => $technicId,
                'spu_id'        => $spuId,
            ));
        }
    }
    
    /**
     * 修改spu形状信息
     *
     * @param $attrInfo array  数据
     * @param $spuId    int    spuId
     */
    static private function _shapeAttr (array $attrInfo, $spuId)  {
        
        Spu_Shape_Relationship::delBySpuId($spuId);
        
        if(empty($attrInfo['shapeId'])){
            
            return ;
        }
        $shape    = explode(",",$attrInfo['shapeId']);
        
        foreach($shape as $shapeId){
            Spu_Shape_Relationship::create(array(
                'shape_id'      => $shapeId,
                'spu_id'        => $spuId,
            ));
        }
    }
    
    /**
     * 修改spu主石信息
     *
     * @param $attrInfo array  数据
     * @param $spuId    int    spuId
     */
    static private function _mainstoneAttr (array $attrInfo, $spuId)  {
        
        Spu_Mainstone_Relationship::delBySpuId($spuId);
        
        if(empty($attrInfo['mainstoneId'])){
            
            return ;
        }
        $mainstone    = explode(",",$attrInfo['mainstoneId']);
        echo "正在初始化买款Id为 ".$attrInfo['spuSn']." 的数据\n";
        
        foreach($mainstone as $mainstoneId){
            Spu_Mainstone_Relationship::create(array(
                'mainstone_id'      => $mainstoneId,
                'spu_id'            => $spuId,
            ));
        }
    }
    
    /**
     * 修改spu品牌、风格和关键词
     *
     * @param $attrInfo array  数据
     * @param $spuId    int    spuId
     */
    
    static private function _brandAndStyleAndKeywords (array $attrInfo, $spuId) {
        
        Spu_Info::update(array(
            'spu_id'    => $spuId,
            'brand_id'  => $attrInfo['brandId'],
            'style_id'  => $attrInfo['mannerLv2Id'],
            'keywords'  => $attrInfo['keywords'],
        ));
    }
}