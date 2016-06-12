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
        return $data;
    }
    
    /**
     * 获取枚举值的ID
     *
     * @param   string   $data      枚举值
     * @param   array   $fieldName  数据
     * @param   string  $idField    对应ID字段名
     * @param   string  $nameField  对应name字段名
     * @param   string  $message    对应错误提示
     * @return  string              返回枚举值ID
     */
    static private  function _getFieldId ($data, array $mapEnumeration, $idField, $nameField, $message) {

        if (empty($data)) {

            $defaultValue   = ArrayUtility::searchBy($mapEnumeration, array('is_default'=>1));
            $defaultInfo    = reset($defaultValue);
            $dafaultid      = $defaultInfo[$idField];
            
            return  $dafaultid;
            
        }
        $mapIndex           = ArrayUtility::indexByField($mapEnumeration, $nameField, $idField);

        Validate::isExist($data,array_keys($mapIndex),$message);

        return $mapIndex[$data];
        
    }
}