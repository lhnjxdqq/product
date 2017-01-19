<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

$mapSupplier    = ArrayUtility::indexByField(ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0)),'supplier_id');
$sampleType 	= Sample_Type::getSampleType();
$parentOwnType 	= Sample_Type::getOwnType();
$mapUser        = ArrayUtility::indexByField(ArrayUtility::searchBy(User_Info::listAll(),array('enable_status'=>1)),'user_id');

foreach($sampleType as $typeId=>$typeName){
    
    $mapSampleType[$typeId] = array(
        
        'sample_type_id'     => $typeId,
        'sample_type_name'   => $typeName,
    );
}

foreach($parentOwnType as $typeId=>$typeName){
    
    $mapOwnType[$typeId] = array(
        'sample_type_id'     => $typeId,
        'sample_type_name'   => $typeName,
    );
}

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapSupplier',$mapSupplier);
$template->assign('mapOwnType',$mapOwnType);
$template->assign('mapUser',$mapUser);
$template->assign('mapSampleType',$mapSampleType);
$template->display('sample/storage/import.tpl');
