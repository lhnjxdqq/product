<?php
/**
 * ���۵�
 */
class   Quotation {
    
    /**
     * ��֤���۵�
     *
     * @param   array   $data   ���� 
     * @param   array   $data   ö������
     */
    static public function testQuotation (array $data, array $mapEnumeration) {

        Validate::testNull($data['sku_code'],'���ID����Ϊ��');
        Validate::testNull($data['categoryLv3'],'�������಻��Ϊ��');
        Validate::testNull($data['material_main_name'],'���ϲ��ʲ���Ϊ��');
        Validate::testNull($data['size_name'],'���ߴ粻��Ϊ��');
        Validate::testNull($data['weight_name'],'�����������Ϊ��');
        return $data;
    }
    
    /**
     * ��ȡö��ֵ��ID
     *
     * @param   string   $data      ö��ֵ
     * @param   array   $fieldName  ����
     * @param   string  $idField    ��ӦID�ֶ���
     * @param   string  $nameField  ��Ӧname�ֶ���
     * @param   string  $message    ��Ӧ������ʾ
     * @return  string              ����ö��ֵID
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