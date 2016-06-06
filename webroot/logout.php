<?php
require_once dirname(__FILE__) . '/../init.inc.php';

Common_Auth::logout();

Utility::redirect('/login.php');