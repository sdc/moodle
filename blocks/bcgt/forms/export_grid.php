<?php
//
//set_time_limit(0);
//require_once '../../../config.php';
//require_once $CFG->dirroot . '/blocks/bcgt/lib.php';
//
//require_login();
//
//if (!isset($_GET['qualID'])) exit;
//
//$qualID = $_GET['qualID'];
//
//$loadParams = new stdClass();
//$loadParams->loadLevel = \Qualification::LOADLEVELALL;
//$loadParams->loadAward = true;
//$loadParams->loadTargets = true;
//$qualification = Qualification::get_qualification_class_id($qualID, $loadParams);
//
//if ($qualification && method_exists($qualification, 'export_grid')){
//    
//    $name = preg_replace("/[^a-z 0-9]/i", "", $qualification->get_display_name());
//    
//    ob_clean();
//    header("Pragma: public");
//    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
//    header('Content-Disposition: attachment; filename="'.$name.'.xlsx"');     
//    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//    header("Cache-Control: private", false);
//    
//    require_once $CFG->dirroot . '/blocks/bcgt/lib/PHPExcel/Classes/PHPExcel.php';
//    
//    $qualification->export_grid();
//    
//} else {
//    echo "Grids of this qualification family cannot yet be exported.";
//}
//exit;