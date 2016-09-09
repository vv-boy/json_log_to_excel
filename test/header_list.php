<?php
require_once ("../vendor/autoload.php");
set_time_limit(0);
ini_set('memory_limit', '2048M');

date_default_timezone_set('Asia/Shanghai');

$objPHPExcel = new PHPExcel();
$uuid = "test_uuid";
$title = "$uuid click log";
$objPHPExcel->getProperties()->setCreator($uuid)
                             ->setTitle($title);
$info = array (
  'timestamp' => 1469980847696,
  'campaign_id' => '486821',
  'channel_id' => '2573',
  'orig_campaign_id' => 0,
  'ua' => 'Mozilla/5.0 (Linux; Android 5.1; X9009 Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Mobile Safari/537.36 OPR/34.0.2044.101442',
  'referer' => 'http://clickadu.com/?zoneid=370312&pbk2=5ce2dcb3f3dd87040f29bf17317c012d6313519566268768663&r=%2Foc%2Fhan%2Ftomb&uuid=bb244eb4-dfbd-4af6-afaf-fb249eadefbe',
  'subid' => '2153',
  'country' => 'th',
  'ip' => '182.232.43.169',
  'forward_ip' => '182.232.43.169',
  'query' => 
  array (
    'transaction_id' => '1023217f282cca50b9d3fbdd56ab10',
    'offer_id' => '20241',
    'sub_id' => 'ho',
  ),
  'mode' => 'clickid',
  'clickid' => '579e20af76ff2bcf41e21973',
  'devid' => '',
  'options' => 
  array (
    'redirected' => 0,
  ),
  'price_info' => 
  array (
    'adv' => 0.84999999999999998,
    'chn' => 0.77000000000000002,
    'chn_mode' => 'cpa',
  ),
  'cookie' => 
  array (
    0 => 
    array (
      'campaign' => 'cdm_kpopwlpaper_th',
      'channel' => 'mobisummer',
      'clickid' => '579e20af76ff2bcf41e21973',
    ),
  ),
  'cookie_id' => '579e20af76ff2bcf41e21974',
  'restriction' => 0,
  'async_click_flag' => false,
  'inject_coded' => false,
  'deliver_ip' => '54.169.156.144',
  'referer_host' => 'clickadu.com',
  'ua_detail' => 
  array (
    'device' => 'X9009',
    'os' => 'Android 5.1.0',
    'ua' => 'Opera Mobile 34.0.2044',
  ),
  'host_id' => '14',
  'offline_realtime' => true,
  'offline_request_id' => '579e20b04b36e7a91da78863',
  'offline_log_level' => 'info',
);


$headerInfo = array('timestamp'=>'timestamp', 'clicktime'=>'time.timestamp', 'uuid'=>'uuid', 'network'=>'channel_id', 'country'=>'country', 'ip'=>'ip', 'device'=>'ua_detail.device', 'os'=>'ua_detail.os', 'ua'=>'ua_detail.ua', 'forward_ip'=>'forward_ip', 'clickid'=>'clickid');



$activeExcel = $objPHPExcel->setActiveSheetIndex(0);
// var_dump($activeExcel);exit;
$headerIndex = array_keys($headerInfo);
$infoIndex = array_values($headerInfo);
foreach ($headerIndex as  $key=>$header) {
	$activeExcel->setCellValue(chr($key + 65) . "1", $header);
}

$row = 2;
foreach ($infoIndex as $key => $value) {
	if (strpos($value, '.')) {
		$dealFormat = explode('.', $value);
		if (function_exists($dealFormat[0])) {
			$wirteVal = $dealFormat[0]('Y-m-d H:i:s', $info[$dealFormat[1]] / 1000);
		} else if (array_key_exists($dealFormat[0], $info)){
			$wirteVal = $info[$dealFormat[0]][$dealFormat[1]];
		}
	} else if(array_key_exists($value, $info)) {
		$wirteVal = $info[$value];
	} else {
		$wirteVal = $$value;
	}

	$activeExcel->setCellValue(chr($key + 65) . strval($row), $wirteVal);
}


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("../template/{$uuid}" . time(). ".xlsx");

