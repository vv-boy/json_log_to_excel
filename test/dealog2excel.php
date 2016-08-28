<?php

require_once ("../vendor/autoload.php");

// use PHPExcel\Spreadsheet;

$objPHPExcel = new PHPExcel();
$uuid = "test_uuid";
$title = "$uuid click log";
$objPHPExcel->getProperties()->setCreator($uuid)
                             ->setTitle($title);

/**
 * 写头部信息。
 * @var array
 */
$headerInfo = array("timestamp", "_type");

$activeExcel = $objPHPExcel->setActiveSheetIndex(0);
// var_dump($activeExcel);exit;
foreach ($headerInfo as  $key=>$header) {
	$activeExcel->setCellValue(chr($key + 65) . "1", $header);
}


/*
写入excel
 */

$fp = fopen("../data/click_info.log", "r");
$row = 2; // 从第二行开始写了。

while (!feof($fp)) {
	$clickInfo = fgets($fp);
	if (!empty(trim($clickInfo))){
		$clickArr = array_values(json_decode($clickInfo, true));
		foreach ($clickArr as $key => $info) {

			$activeExcel->setCellValue(chr($key + 65) . strval($row), $info);
		}
		// 写下一行。
		$row ++;	
	}
}

fclose($fp);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("../template/1.xlsx");