<?php

class Api_Controller_Customer {

    static public function getAll () {

        return array(
            'CustomerList' => array(
              array(
                'customerId'=>1,
                'customerName'=>'dalei',
              ),
              array(
                'customerId'=>1,
                'customerName'=>'dalei',
              ),
              array(
                'customerId'=>1,
                'customerName'=>'dalei',
              ),
              array(
                'customerId'=>1,
                'customerName'=>'dalei',
              ),
            ),
        );
    }

}
