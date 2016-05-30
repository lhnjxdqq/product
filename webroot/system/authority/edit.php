<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['authority_id'])) {

    Utility::notice('authority_id is missing');
}

$authorityId    = (int) $_GET['authority_id'];

$data['listAuthorityLv1']   = Authority_Info::listByCondition(array('parent_id'=>0));
$data['authorityInfo']      = Authority_Info::getById($authorityId);
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/authority/edit.tpl');