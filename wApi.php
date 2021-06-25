<?php ob_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8'); 
session_start();
$_PATH="";
include("includes/protect.php");
 
if (isset($_REQUEST['action'])){
	

	if ($_REQUEST['action']=="doLoginAPI"){ 
		$ret=$protect->doLoginAPI(isset($_REQUEST['user'])?$_REQUEST['user']:'',isset($_REQUEST['pwd'])?$_REQUEST['pwd']:'');
		echo json_encode($ret);
		exit;
	}
	if ($_REQUEST['action']=="doRequestPrint"){  
		$ret=$protect->checkToken(isset($_REQUEST['token'])?$_REQUEST['token']:'');
		if ($ret['valid']==1){
			SystemHtml::getInstance()->includeClass("archivo","Archivo"); 
			$_archivo= new Archivo($protect->getDBLink()); 
			$result=$_archivo->getPrintContrato($ret['data']['id_nit']);	
	 
			echo json_encode($result);
		}else{
			echo json_encode($ret);	
		}
		exit;
	}
	if ($_REQUEST['action']=="doPrintedDocument"){  
		$ret=$protect->checkToken(isset($_REQUEST['token'])?$_REQUEST['token']:'');
		if ($ret['valid']==1){
			SystemHtml::getInstance()->includeClass("archivo","Archivo"); 
			$_archivo= new Archivo($protect->getDBLink()); 
			$result=$_archivo->doChangePoolEstatus($_REQUEST['pool_id'],"");		
			echo json_encode($result);
		}else{
			echo json_encode($ret);	
		}
		exit;
	}	
	
	
		
	
	
}
 
if ($protect->isLogin()){
 
 


}





?>