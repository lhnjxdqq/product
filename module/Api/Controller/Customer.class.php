<?php

class Api_Controller_Customer {

    static public function getAll () {

      $listCustomerInfo = ArrayUtility::searchBy(Customer_Info::listAll(), array('delete_status'=>Customer_DeleteStatus::NORMAL,));
      $data             = array();
      $CustomerList     = array();
      foreach ($listCustomerInfo as $customerInfo) {

        $CustomerList[] = array(
          'customerId'          => $customerInfo['customer_id'],
          'customerName'        => $customerInfo['customer_name'],
          'serviceNumber'       => $customerInfo['service_number'],
          'qrCodeImageKey'      => $customerInfo['qr_code_image_key'],
        );

      }

      $data['CustomerList'] = $CustomerList;
      return $data;

    }

}
