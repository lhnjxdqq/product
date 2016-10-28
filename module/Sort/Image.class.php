<?php
class Sort_Image {

    // 模特图
    const   M = 1;
    
    //细节图
    const   D = 2;
    
    //正侧背概要图
    const   R = 3;

    //手机图
    const   P = 3;

    //工厂图
    const   F = 3;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getImageTypeList () {

        return  array(
            'M'  => '模特图',
            'D'  => '细节图',
            'R'  => '正侧背概要图',
            'P'  => '手机图',
            'F'  => '工厂图',
        );
    }
    
    /**
     * 获取排序
     *
     * @return array 排序
     */
    static public function sortImageType(){
        
        return array('R','M','D','P','F');
    }
    
    /**
     * 排序
     * 
     * @param array   $imageInfo    图片数据
     * 
     * return array   数据
     */
    static public function sortImage($imageInfo){
        
        if(empty($imageInfo)){
           
            return array();           
        }
        
        $sortType       = self::sortImageType();
        $imageTypeInfo  = array();
        
        foreach($sortType as $key => $type){

            $sortNumber      = array();
            $searchImage     = ArrayUtility::searchBy($imageInfo,array('image_type'=>$type));
            
            if(empty($searchImage)){
                
                continue;
            }
            foreach($searchImage as $row=>$info){
                
                $sortNumber[$row]   = $info['serial_number'];
            }
            array_multisort($sortNumber,SORT_ASC,SORT_NUMERIC ,$searchImage);

            $imageTypeInfo  = array_merge($imageTypeInfo, $searchImage);
        }
       
        return $imageTypeInfo; 
    }
}