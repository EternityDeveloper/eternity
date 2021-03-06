<?php 
include("config.inc.php"); 
include("function.php"); 
include($_PATH."class/lib/class.ObjectSQL.php"); 
include($_PATH."class/lib/Database.class.php"); 
include($_PATH."class/lib/class.userAccess.php");
include($_PATH."class/lib/class.System.php");
include($_PATH."class/lib/class.Security.php");
include($_PATH."class/lib/excel/class_excel.php");
include($_PATH."class/lib/class.MVFactura.php");
include($_PATH."class/lib/class.STCSession.php");
include($_PATH."class/lib/class.Siadcom.php"); 
include($_PATH."class/lib/class.ExportXLS.php"); 

include($_PATH."class/lib/SAM/php_sam.php");
include($_PATH."class/lib/class.MQTTConection.php");

$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 
$protect = new UserAccess($db);
$log = new SysLog($db);
$mvf= new MVFactura($db);
$stc= new STCSession();
 

if (isset($_GET['logoff'])){
	unset($_SESSION['MODE_EDIT']);
	$protect->logoff();
	header("location:./");
	exit;
}

/*ACTIVAR MODO EDITOR PERMITE VER LOS CONTRATOS 
COMO ESTAN CONSTITUIDOS HASTA ANTES DE LA FECHA DE CORTE 2015-03-04 QUE ES EN LA FECHA
EN QUE EL SISTEMA ENTRO EN VIGENCIA*/
if (isset($_GET['modeEdit'])){ 
	$_SESSION['MODE_EDIT']=array('FECHA'=>'2015-03-03'); 
	exit;
}



if (isset($_POST['action'])){
	if ($_POST['action']=="login"){
		$protect->login(isset($_POST['user'])?$_POST['user']:'',isset($_POST['pwd'])?$_POST['pwd']:'');
		if ($protect->isLogin()){
			header("location: ./");
		}else{
			header("location: ./?error=1");
		}
		exit;
	}
}


?>