<?php

class Api_Controller_Customer {

    static public function getAll () {

      $listCustomerInfo = ArrayUtility::searchBy(Customer_Info::listAll(), array('delete_status'=>Customer_DeleteStatus::NORMAL,));
      $data             = array();
      $CustomerList     = array();
       
      foreach ($listCustomerInfo as $customerInfo) {
      
        $imageUrl  = !empty($customerInfo['qr_code_image_key'])
                ? AliyunOSS::getInstance('images-spu')->url($customerInfo['qr_code_image_key'])
                : '';
                
        $CustomerList[] = array(
          'customerId'          => $customerInfo['customer_id'],
          'customerName'        => $customerInfo['customer_name'],
          'serviceNumber'       => $customerInfo['service_number'],
          'qrCodeImageKey'      => $imageUrl,
        );

      }

      $data['CustomerList'] = $CustomerList;
      return $data;

    }

}
