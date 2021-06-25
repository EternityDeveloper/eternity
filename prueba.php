<?php
/*POWER BY JOSE GREGORIO RAMOS 1(809)-481-6599*/

$headers=array(
	0=>'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	1=>'Host:www.proveyourworth.net',
	2=>'Connection:keep-alive',
	3=>'Accept-Language:es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
	4=>'Accept-Encoding:gzip, deflate',
	5=>'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
	6=>'Cache-Control: max-age=0',
	7=>'X-Post-Back-Fields:image=fd.jpg,code=dd,resume=dd',
	8=>'X-Please-Also-Provide: email=jramos@oscgre.com,name=jose'
);
$payload="http://www.proveyourworth.net/level3/payload";
$public_url="http://www.proveyourworth.net/level3/reaper";

$DATA_RQ=array(
	'X-Post-Back-aboutme'=>urlencode('I am a responsible person, Proactive, self-educated, tenacious, persistent and innovative. Consider to be good for this, because I am passionate about everything related to technology, I worry and take care to be aware of all the innovations that arise in this environment and stay ahead, so you can be up to the best in my area which is the programming.'),
	'X-Post-Back-email'=>urlencode('jramos@oscgre.com'),
	'X-Post-Back-name'=>urlencode('Jose Gregorio Ramos'),
	'X-Post-Back-code'=>urlencode(getDataFromFile()),
	'X-Post-Back-image'=>'@'.realpath(dirname(__FILE__)).'\\'."download.jpg",
	'X-Post-Back-resumen'=>'@'.realpath(dirname(__FILE__)).'\\'.'curriculum_Jose_Gregorio_Ramos.pdf'
);


if (create_login_request("oscgre",$headers)){
	echo "[-] Procesing image\n";
	$name=getImageSign($payload,$headers);
	$DATA_RQ['image']='@'.realpath(dirname(__FILE__)).'\\'.$name;
	 
	echo "[-] Procesing complete\n";
	echo "[-] Push data to server!\n";

	foreach($DATA_RQ as $key =>$val){
		$field.=$key."=". urlencode($val).",";	
	}	
	print_r(post($public_url,$headers,$DATA_RQ));
	echo "[-] End Push";
}


function create_login_request($username,$headers){
	$data= post("http://www.proveyourworth.net/level3/",$headers,array());
	$result = preg_match('/<input type="hidden" name="statefulhash" value="(.*?)"/', $data, $match);
	$value = $match[1];
	if ($value!=""){
		$login=array(
			"statefulhash"=>$value,
			"username"=>$username
		);
		
		$data=get("http://www.proveyourworth.net/level3/activate",$headers,$login);
		//print_r($data);
		return true;
	}else{
		echo "Fail login";	
		return false;
	}
}
 
function get($url,$headers,$fields){
	
	foreach($fields as $key =>$val){
		$field.=$key."=". urlencode($val)."&";	
	}
	$field=substr($field,0,strlen($field)-1);
	$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
	$cookie="test.txt";
    $rs = curl_init($url."?".$field);
	curl_setopt($rs, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($rs, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($rs, CURLOPT_HEADER, true);
	curl_setopt($rs, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($rs, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($rs, CURLOPT_FOLLOWLOCATION, 1);
    $raw=curl_exec($rs);
    curl_close($rs);
    return $raw;
}
function getDataFromFile(){
	$f = realpath(dirname(__FILE__)).'\\'."prueba.php";
	$size = filesize($f); 
	$fH = fopen($f,"r");  
	$data = fread($fH,$size); 
	fclose($fH);
	return $data; 	
}
function post($url,$headers,$fields){
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
    return $raw;
}
function getImageSign($url,$headers){
	$raw=download_image($url,$headers);
	$img=imagecreatefromstring($raw['body']);
	$color = imagecolorallocate($img, 255, 255, 255);
	imagestring($img, 20, 0, 20, 'Jose Gregorio Ramos', $color);
	imagestring($img, 20, 0, 40, 'Cel Number 1(809)-481-6599!', $color);
	
	$name=trim($raw['image_name']);
	imagejpeg($img,$name, 50);
	return $name;
}
function download_image($url,$headers){
	$user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
	$cookie="test.txt";
    $rs = curl_init($url);
	curl_setopt($rs, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($rs, CURLOPT_HEADER, true);
	curl_setopt($rs, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($rs, CURLOPT_COOKIEJAR, $cookie);	
    curl_setopt($rs, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rs, CURLOPT_BINARYTRANSFER,1);
	curl_setopt($rs, CURLOPT_FOLLOWLOCATION, 1);
    $raw=curl_exec($rs);
  	$contentType = curl_getinfo($rs, CURLINFO_CONTENT_TYPE);	
    curl_close($rs);
	list($header, $body) = explode("\r\n\r\n", $raw, 2);
	//echo $header;
  	preg_match('/Content-Disposition: .*filename=([^ ]+)/',$header, $matches);
	$matches=explode("\n",$matches[1]);
    return array("body"=>$body,"image_name"=>$matches[0]);
}

?>