<?php

class Api_Controller_AuPrice {

    static public function getNewPrice () {

        $auPrice        = Au_Price_Log::getNewsPrice();
        $lastPrice      = Au_Price_Log::getLastPrice();
        return array(
            'diffPrice'  => ($auPrice - $lastPrice),
            'auNewPrice' => $auPrice,
        );
    }

    /**
     * 根据时间获取金价
     *
     */
     static public function getAuPriceByDate ($date) {

         if (empty($date)){

            return array(
                'auPrice'=>'0.00',
            );
         }
         $auPriceLog    = Au_Price_Log::getByDate($date);
         if (empty($auPriceLog)) {

             $result['auPrice'] = '0.00';
         } else {

             $result['auPrice'] = $auPriceLog['au_price'];
         }
         return $result;

     }

}
