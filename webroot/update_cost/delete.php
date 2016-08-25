<?php

require_once dirname(__FILE__).'/../../init.inc.php';

Validate::testNull($_GET['update_cost_id'],'Id不能为空');

Update_Cost_Info::update(array(
    'update_cost_id'       => $_GET['update_cost_id'],
    'status_id'            => Update_Cost_Status::DELETED,
));

Utility::notice('删除成功','/update_cost/index.php');