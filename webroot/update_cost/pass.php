<?php

require dirname(__FILE__).'/../../init.inc.php';

Validate::testNull($_GET['update_cost_id'],'新报价单ID不能为空');

Update_Cost_Info::update(array(
    'update_cost_id'       => $_GET['update_cost_id'],
    'status_id'            => Update_Cost_Status::UPDATE,
    'auditor_user_id'      => $_SESSION['user_id'],
));
Utility::notice('审核通过,请稍后查看价格','/update_cost/index.php');