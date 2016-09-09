<?php
require_once ("../vendor/autoload.php");
set_time_limit(0);
ini_set('memory_limit', '4096M');

date_default_timezone_set('Asia/Shanghai');

$filename='appsflyer_info';
$objPHPExcel = new PHPExcel();
$title = "install log";
$objPHPExcel->getProperties()->setCreator("mobvista")
                             ->setTitle($title);
$headerInfo = array(  'timestamp'=>'timestamp', 'othertimestamp'=>'match_result.data.timestamp', 'uuid'=>'match_result.data.campaign', 'network'=>'network', 'mb_subid'=>'match_result.data.subid','ip'=>'match_result.data.ip', 'country'=>'query.mobvista_country', 'clickid'=>'query.mobvista_clickid', 'devid'=>'query.mobvista_devid', 'imei'=>'query.mobvista_imei', 'gaid'=>'query.mobvista_gaid', 'x_forward_ip'=>'forward_ip', 'defraud'=>'deduction', 'postback'=>'implode.postback');



$activeExcel = $objPHPExcel->setActiveSheetIndex(0);
// var_dump($activeExcel);exit;
$headerIndex = array_keys($headerInfo);
$infoIndex = array_values($headerInfo);
foreach ($headerIndex as  $key=>$header) {
	$activeExcel->setCellValue(chr($key + 65) . "1", $header);
}

$fp = fopen("../data/data.log", "r");
// $fp = fopen("../data/cdm_kpopwlpaper_th_click.log", "r");
$row = 2; // 从第二行开始写了。

echo "正在处理......";
$i = 1;
$a = 0;
while (!feof($fp)) {
  $clickStr = trim(fgets($fp, 4096));
  $arr = explode("\t", $clickStr);

  // if ($i == 100){
  //   break;
  // }
  // $i++;
  // continue;
  // if($i == 100){
  //   break;
  // }
  // echo count($arr), "\n"; continue;

  if(count($arr) < 5) {
    continue;
  }

  // $offerInfo = explode(' ', $arr[3]);
  // $uuid = $offerInfo[0];
  // 
  if (trim($arr[1]) == 'appsflyer') {
    $clickInfo = $arr[4];
    $offerInfo = explode(' ', $arr[3]);
    $network = $offerInfo[1];
  } else {
    $clickInfo = $arr[3];
    $offerInfo = explode(' ', $arr[2]);
    $network = $offerInfo[1];
  }
  

  // var_dump($clickInfo);exit;
  if (!empty(trim($clickInfo))){
    $info = json_decode($clickInfo, true);

    if (!is_array($info)) {
      echo $clickInfo;exit;
    }
    // print_r($info); exit;
    // $i = 0;
    foreach ($infoIndex as $key => $value) {
      if (strpos($value, '.')) {
        $dealFormat = explode('.', $value);
        if (array_key_exists($dealFormat[0], $info)){
       	  if(count($dealFormat) == 2){
          	$wirteVal = $info[$dealFormat[0]][$dealFormat[1]];
       	  } else {
       	  	$wirteVal = $info[$dealFormat[0]][$dealFormat[1]][$dealFormat[2]];
       	  }
        } else if (function_exists($dealFormat[0])) {
          // $wirteVal = $dealFormat[0]('Y-m-d H:i:s', $info[$dealFormat[1]] / 1000);
          if (isset($info[$dealFormat[1]]) && is_array($info[$dealFormat[1]])) {
            $wirteVal = $dealFormat[0](';', $info[$dealFormat[1]]);
          } else {
            $wirteVal = '';
          }
          
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

