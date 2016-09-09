<?php 


$fp = fopen("../data/click_info.log", "r");

/**
 * 需要的信息。 clicktime timestamp  uuid channel_id  country ip  ua_detail forward_ip clickid
 */ 

$headerInfo = array("timestamp", "clicktime", "uuid","network","country","ip","device","os", "ua","forward_ip","clickid");


/*
  json格式的数据。
  array (
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
)
*/ 


while (!feof($fp)) {
	$clickStr = fgets($fp, 4096);
	$arr = explode("\t", $clickStr);
	$clickInfo = $arr[2];
	$uuid = explode(" ", $arr[1])[0];
	// var_dump($clickInfo);exit;
	if (!empty(trim($clickInfo))){
		$clickArr = json_decode($clickInfo, true);
		echo var_export($clickArr, 1);exit;
		// print_r($clickArr); exit;
		foreach ($clickArr as $key => $info) {

			$activeExcel->setCellValue(chr($key + 65) . strval($row), $info);
		}
		// 写下一行。
		$row ++;	
	}
}