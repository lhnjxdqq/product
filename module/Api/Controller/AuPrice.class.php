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

}
