<?php
/**
 * 样板审核通过
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['sample_storage_id'],'样板ID不能为空');

Sample_Storage_Info::update(array(
    'sample_storage_id' => $_GET['sample_storage_id'],
    'status_id'         => Sample_Status::WAIT_UPDATE,
    'examine_user'      => $_SESSION['user_id'],
    'examine_time'      => date("Y-m-d"),
));
Utility::notice('审核通过','/sample/storage/index.php');
