<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

Cart_Spu_Info::cleanByUserId($_SESSION['user_id']);
Utility::notice('清除成功');