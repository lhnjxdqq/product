<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

Validate::testNull($_POST['factory_id'], "供应商不能为空");
$listFactory   = Factory_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listFactory,array('factory_id'=>$_POST['factory_id'])),"所选供应商不存在");

$filePath           = $_FILES['quotation']['tmp_name'];

if($_FILES['quotation']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}
$objPHPExcel        = ExcelFile::load($filePath);
$sheet              = $objPHPExcel->getSheet(0); 
$highestRow         = $sheet->getHighestRow();
$highestColumn      = $sheet->getHighestColumn();


$csvHead        = array(
    '买款ID'          => 'product_name',
    '产品名称'          => 'product_sn',
    '三级分类'          => 'categoryLv1',
    '主料材质'          => 'categoryLv2',
    '规格尺寸'          => 'categoryLv3',
    '规格重量'          => 'material_name',
    '进货工费'          => 'weight',
);
for($line=1;$line<=$highestRow;$line++){
    
    for($list='A';$list<=$highestColumn;$list++){
        
        if($line == 1 || $line == 2){
        
            $str[] =$objPHPExcel->getActiveSheet()->getCell("$list$line")->getValue();
            
            if($line == 2){
                
                $format = array();

                foreach ($str as $offsetCell => $head) {

                    if (isset($csvHead[$head])) {

                        $format[$offsetCell]    = $csvHead[$head];
                
                    }
                }

                if (count($format) != count($csvHead)) {

                    throw   new ApplicationException('无法识别表头');
                }

                continue;
            }
        }
    } 
        
}  var_dump($format);die;
$template       = Template::getInstance();