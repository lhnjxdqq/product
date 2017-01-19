<?php
/**
 * 样本清单
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$condition  = empty($_GET) ? array() : $_GET ;
$orderBy    = array(
    'create_time' => 'DESC',
);

$mapSalesperson = ArrayUtility::indexByField(ArrayUtility::searchBy(Salesperson_Info::listAll(),array('delete_status'=>0)),'salesperson_id');
$mapSupplier    = ArrayUtility::indexByField(ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0)),'supplier_id');
$mapUser        = ArrayUtility::indexByField(ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1)),'user_id');
$sampleStatus   = Sample_Status::getSampleStatus();
$sampleType = Sample_Type::getSampleType();

foreach($sampleType as $typeId=>$typeName){
    
    $mapSampleType[$typeId] = array(
        
        'sample_type_id'     => $typeId,
        'sample_type_name'   => $typeName,
    );
}

$condition['creare_time_start']    = isset($_GET['creare_time_start']) ? $_GET['creare_time_start'] : date('Y-m-d', strtotime('-30 day'));
$condition['create_time_end']      = isset($_GET['create_time_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['create_time_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);

$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countOrder     = Sample_Storage_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countOrder,
    PageList::OPT_URL       => '/sample/storage/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSampleStorageInfo          = Sample_Storage_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('listSampleStorageInfo',$listSampleStorageInfo);
$template->assign('condition',$condition);
$template->assign('mapSampleType',$mapSampleType);
$template->assign('mapSupplier',$mapSupplier);
$template->assign('mapUser',$mapUser);
$template->assign('sampleStatus',$sampleStatus);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sample/storage/index.tpl');