<?php

class Api_Controller_SalesQuotation {

    static public function  getSalesQuotationFile() {

        $taskInfo   = Sales_Quotation_Task::getByIsPush(Sales_Quotation_IsPush::YES);
        if(empty($taskInfo)){
         
            return array();
        }
        $filePath   = array();
        $stream     = Config::get('path|PHP', 'sales_quotation_product');

        foreach($taskInfo as $info){

            $filePath[]     = $stream.$info['log_file'];
        }
        return $filePath;
    }
    
    /**
     * 根据销售订单ID获取路径
     *
     * @param $salesOrderId   销售订单ID
     * @return                销售订单路径
     */
    static public function getLogPathBySalesOrderId($salesOrderId){
        
        $result         = array();
        $salesQuotationInfo = Sales_Quotation_Task::getBySalesQuotationId($salesOrderId);
        $stream     = Config::get('path|PHP', 'sales_quotation_product');
        
        if(!empty($salesQuotationInfo)){
            
            $result['log_file'] = $stream.$salesQuotationInfo['log_file'];
        }
        return $result;
    }
     
    static public function success($salesQuotationId) {
    
        $taskInfo   = Sales_Quotation_Task::getBySalesQuotationId($salesQuotationId);
    }

}
