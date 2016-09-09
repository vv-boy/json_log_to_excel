<?php

require_once ("../vendor/autoload.php");
set_time_limit(0);
ini_set('memory_limit', '2048M');
// use PHPExcel\Spreadsheet;
// 转换成东八区的时间
date_default_timezone_set('Asia/Shanghai');

$objPHPExcel = new PHPExcel();
$uuid = "test_uuid";
$title = "$uuid click log";
$objPHPExcel->getProperties()->setCreator($uuid)
                             ->setTitle($title);

/**
 * 写头部信息。 clicktime timestamp  uuid channel_id  country ip  ua_detail forward_ip clickid
 * @var array
 */
$headerInfo = array("timestamp", "clicktime", "uuid","network","country","ip","device","os", "ua","forward_ip","clickid");

$activeExcel = $objPHPExcel->setActiveSheetIndex(0);
// var_dump($activeExcel);exit;
foreach ($headerInfo as  $key=>$header) {
	$activeExcel->setCellValue(chr($key + 65) . "1", $header);
}


/*
写入excel
 */
$fp = fopen("../data/click_info.log", "r");
// $fp = fopen("../data/cdm_kpopwlpaper_th_click.log", "r");
$row = 2; // 从第二行开始写了。

echo "正在处理......";
while (!feof($fp)) {
	$clickStr = fgets($fp, 4096);
	$arr = explode("\t", $clickStr);

	if(count($arr) < 3) {
		continue;
	}

	$clickInfo = $arr[2];
	$uuid = explode(" ", $arr[1])[0];

	// var_dump($clickInfo);exit;
	if (!empty(trim($clickInfo))){
		$clickArr = json_decode($clickInfo, true);
		// print_r($clickArr); exit;
		// $i = 0;
		$activeExcel->setCellValue(chr(0 + 65) . strval($row), intval($clickArr['timestamp'] / 1000));
		$activeExcel->setCellValue(chr(1 + 65) . strval($row), date('Y-m-d H:i:s', $clickArr['timestamp'] / 1000));
		$activeExcel->setCellValue(chr(2 + 65) . strval($row), $uuid);
		$activeExcel->setCellValue(chr(3 + 65) . strval($row), $clickArr['channel_id']);
		$activeExcel->setCellValue(chr(4 + 65) . strval($row), $clickArr['country'] );
		$activeExcel->setCellValue(chr(5 + 65) . strval($row), $clickArr['ip'] );
		$activeExcel->setCellValue(chr(6 + 65) . strval($row), $clickArr['ua_detail']['device'] );
		$activeExcel->setCellValue(chr(7 + 65) . strval($row), $clickArr['ua_detail']['os'] );
		$activeExcel->setCellValue(chr(8 + 65) . strval($row), $clickArr['ua_detail']['ua'] );
		$activeExcel->setCellValue(chr(9 + 65) . strval($row), $clickArr['forward_ip']);
		$activeExcel->setCellValue(chr(10 + 65) . strval($row), $clickArr['clickid']);


		/*foreach ($clickArr as $key => $info) {
			$activeExcel->setCellValue(chr($i + 65) . strval($row), $info);
			$i++;
		}*/
		// 写下一行。
		$row ++;
			
	}
}
echo "处理完,共 {$row-1} 行数据\n"; 	

/*while (!feof($fp)) {
	$clickInfo = fgets($fp);
	if (!empty(trim($clickInfo))){
		$clickArr = array_values(json_decode($clickInfo, true));
		foreach ($clickArr as $key => $info) {

			$activeExcel->setCellValue(chr($key + 65) . strval($row), $info);
		}
		// 写下一行。
		$row ++;	
	}
}*/

fclose($fp);
echo "写入excel, 请稍等......\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("../template/{$uuid}" . time(). ".xlsx");

echo "处理完成！\n";