<?php
require_once ("../vendor/autoload.php");
set_time_limit(0);
ini_set('memory_limit', '4096M');

date_default_timezone_set('Asia/Shanghai');

$filename='hwp_complaints';
$objPHPExcel = new PHPExcel();
$title = "click log";
$objPHPExcel->getProperties()->setCreator("mobvista")
                             ->setTitle($title);

$headerInfo = array( 'clickid'=>'clickid', 'timestamp'=>'timestamp', 'clicktime'=>'time.timestamp', 'uuid'=>'uuid', 'network'=>'channel_id', 'mb_subid'=>'subid','referer'=>'referer', 'country'=>'country', 'ip'=>'ip', 'device'=>'ua_detail.device', 'os'=>'ua_detail.os', 'ua'=>'ua_detail.ua', 'forward_ip'=>'forward_ip');



$activeExcel = $objPHPExcel->setActiveSheetIndex(0);
// var_dump($activeExcel);exit;
$headerIndex = array_keys($headerInfo);
$infoIndex = array_values($headerInfo);
foreach ($headerIndex as  $key=>$header) {
	$activeExcel->setCellValue(chr($key + 65) . "1", $header);
}

$fp = fopen("../data/hwp_complaints.log", "r");
// $fp = fopen("../data/cdm_kpopwlpaper_th_click.log", "r");
$row = 2; // 从第二行开始写了。

echo "正在处理......";
while (!feof($fp)) {
  $clickStr = fgets($fp, 4096);
  $arr = explode("\t", $clickStr);

  if(count($arr) < 3) {
    continue;
  }

  $offerInfo = explode(' ', $arr[1]);
  $uuid = $offerInfo[0];
  $clickInfo = $arr[2];

  // var_dump($clickInfo);exit;
  if (!empty(trim($clickInfo))){
    $info = json_decode($clickInfo, true);
    // print_r($info); exit;
    // $i = 0;
    foreach ($infoIndex as $key => $value) {
      if (strpos($value, '.')) {
        $dealFormat = explode('.', $value);
        if (array_key_exists($dealFormat[0], $info)){
          $wirteVal = $info[$dealFormat[0]][$dealFormat[1]];
        } else if (function_exists($dealFormat[0])) {
          $wirteVal = $dealFormat[0]('Y-m-d H:i:s', $info[$dealFormat[1]] / 1000);
        } 
      } else if(array_key_exists($value, $info)) {
        $wirteVal = $info[$value];
      } else {
        $wirteVal = $$value;
      }

      $activeExcel->setCellValue(chr($key + 65) . strval($row), $wirteVal);
    }
    $row ++;
  }
}
$row--;
echo "处理完,共 {$row}行数据\n";  

fclose($fp);
echo "写入excel, 请稍等......\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("../template/{$filename}" . time(). ".xlsx");

echo "处理完成！\n";


