<?php


class MotorTrack{
	private $token;
	private $_data;
	
	public function __construct($token){
		$this->token=$token;
	}
	 
	private function getDeviceList($token){
		$url="http://brainktracker.com/core/";
		$dat=array("token"=>$token);
		$fields=array("method"=>"deviceList","json"=>json_encode($dat));
		
		$headers=array(
			0=>'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			1=>'Connection:keep-alive',
			2=>'Accept-Language:es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
			3=>'Accept-Encoding:gzip, deflate',
			4=>'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
			5=>'Cache-Control: max-age=0'
		);
			
		$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
		$cookie="test.txt";
		$rs = curl_init($url);
		curl_setopt($rs, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rs, CURLOPT_POST, true);
		curl_setopt($rs, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($rs, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($rs, CURLOPT_HEADER, true);
		curl_setopt($rs, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($rs, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($rs, CURLOPT_FOLLOWLOCATION, 1);
		$raw=curl_exec($rs);
		$contentType = curl_getinfo($rs, CURLINFO_CONTENT_TYPE); 
		curl_close($rs);
	 
		$raw=explode("\n",$raw); 
		return $raw[13];
	}
	
	private function getReportStop($id,$token){
		$dat="filtro=1&ficha=".$id."&fecha_desde=2014-07-07&fecha_hasta=2014-07-07&hora_desde=00%3A00%3A00&hora_hasta=23%3A59%3A00&velocidad=50&tiempo=50token=".$token;
		$fields=array("method"=>"deviceList","json"=>json_encode($dat));
		
		$url="http://brainktracker.com/core/stopReport.php?".$dat;
	
		
		$headers=array(
			0=>'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			1=>'Connection:keep-alive',
			2=>'Accept-Language:es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
			3=>'Accept-Encoding:gzip, deflate',
			4=>'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
			5=>'Cache-Control: max-age=0'
		);
			
		$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
		$cookie="test.txt";
		$rs = curl_init($url);
		curl_setopt($rs, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rs, CURLOPT_POST, true);
	//	curl_setopt($rs, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($rs, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($rs, CURLOPT_HEADER, true);
		curl_setopt($rs, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($rs, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($rs, CURLOPT_FOLLOWLOCATION, 1);
		$raw=curl_exec($rs);
		$contentType = curl_getinfo($rs, CURLINFO_CONTENT_TYPE); 
		curl_close($rs);
		$raw=explode("\n",$raw);
		return $raw[11];
	}

}

?>