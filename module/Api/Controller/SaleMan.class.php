<?php

class Api_Controller_SaleMan {

    static public function getAll () {

      $listSalePerson = ArrayUtility::searchBy(Salesperson_Info::listAll(), array('delete_status'=>0));
      $data           = array();
      $SaleManList    = array();

      foreach ($listSalePerson as $salePerson) {

        $SaleManList[] = array(
            'salemanId'=>$salePerson['salesperson_id'],
            'salemanName'=>$salePerson['salesperson_name'],
            'salemanTel'=>$salePerson['telephone'],
            'createTime'=>$salePerson['create_time'],
            'updateTime'=>$salePerson['update_time'],
        );
      }
      
      $data['SaleManList'] = $SaleManList;
      return $data;

    }

}
