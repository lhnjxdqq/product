<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_GET['sample_id'],'样本ID不能为空');

Sample_Storage_Info::update(array(
    'sample_storage_id' => $_GET['sample_id'],
    'status_id'         => Sample_Status::DELETED,
));
Utility::notice("删除成功");