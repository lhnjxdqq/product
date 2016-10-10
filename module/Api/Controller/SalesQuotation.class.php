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
            
            Sales_Quotation_Task::update(array(
                'task_id'   => $info['task_id'],
                'is_push'   => Sales_Quotation_IsPush::FINISH,
            ));
            $filePath[]     = $stream.$info['log_file'];
        }
        return $filePath;
    }

}
