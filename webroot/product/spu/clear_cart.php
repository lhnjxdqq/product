<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

Cart_Spu_Info::cleanByUserId($_SESSION['user_id']);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
    
    ),
));