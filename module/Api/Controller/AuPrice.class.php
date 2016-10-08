<?php

class Api_Controller_AuPrice {

    static public function getNewPrice () {

        $auPrice   = Au_Price_Log::getNewsPrice();
        return array(
            'auNewPrice' => $auPrice,
        );
    }

}
