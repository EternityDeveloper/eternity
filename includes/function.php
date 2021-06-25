<?php

function exportToCsv($fileName, $assocDataArray)
{
    ob_clean();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $fileName);    
    if(isset($assocDataArray['0'])){
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($assocDataArray['0']));
        foreach($assocDataArray AS $values){
            fputcsv($fp, $values);
        }
        fclose($fp);
    }
    ob_flush();
}

function createExcel($filename, $arrydata) {
	$excelfile = "xlsfile://temp_uploads/".$filename;  
	$fp = fopen($excelfile, "wb");  
	if (!is_resource($fp)) {  
		die("Error al crear $excelfile");  
	}  
	fwrite($fp, serialize($arrydata));  
	fclose($fp);
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
	header ("Cache-Control: no-cache, must-revalidate");  
	header ("Pragma: no-cache");  
	header ("Content-type: application/x-msexcel");  
	header ("Content-Disposition: attachment; filename=\"" . $filename . "\"" );
	readfile($excelfile);  
}
/*FUNCION PARSEA UN ARRAY EN STRING SCRIPT PARA SER IMPORTADO*/
function parserTags($tag="script",$array){
	$str="";
	
	if ($tag=="script"){
		$tag='<script type="text/javascript" src="%s"></script>'."\n";
	}
	if ($tag=="style"){
		$tag='<link type="text/css" href="%s" rel="stylesheet"/>'."\n";
	}
	
	if (isset($array)){
		foreach($array as $key => $val){
			$str.= sprintf($tag,$val) ;
		}	
	}
	
	return $str;
}

function validateField($_array,$field){
	$field_val="";
	if (is_array($_array)){
		if (isset($_array[$field])){
			$field_val=$_array[$field];
		}
	}
	if (is_object($_array)){
		if (isset($_array->{$field})){
			$field_val=$_array->{$field};
		}
	}	
	if ($field_val!=""){
		return true;	
	}	
 
	return false;
}
function zerofill($num,$zerofill = 5)
{
	return str_pad($num, $zerofill, '0', STR_PAD_LEFT);
}

function sendMailer($from,$name,$address,$body,$mensaje,$attachment=''){
 		require_once 'class/lib/phpMail/PHPMailerAutoload.php';	

		$mail = new PHPMailer(); 
		$mail->IsSMTP();
		$mail->Host       = "mail.memorial.com.do"; 
		//$mail->SMTPDebug  = 1;   
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "tls";               
		$mail->Host       = "mail.memorial.com.do";    
		$mail->Port       = 587;                  
		$mail->Username   = "noreply@memorial.com.do";
		$mail->Password   = "Memor01*";          
			 
		$mail->setFrom($from,$name); 
		$mail->addReplyTo('noreply@memorial.com.do', 'GPMEMORIAL :: AUTO');
	 
		foreach($address as $value){
			$mail->addAddress($value['email'], $value['name']);
		} 
		$mail->Subject = $body;
		$mail->msgHTML($mensaje); 
		$mail->AltBody = $body; 
		if ($attachment!=''){
			$mail->addAttachment($attachment);
		}
		
		if (!$mail->send()) {
			return false;
		} else {
			return true;
		}	
	}

?>
