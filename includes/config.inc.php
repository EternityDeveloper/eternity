<?php 
include($_PATH."class/lib/class.SystemHtml.php");
include($_PATH."class/lib/class.SysLogs.php");
include($_PATH."class/lib/class.SystemCache.php");
include($_PATH."class/lib/excel/excel.php");

define('VERSION', "1.1");

define('DB_SERVER', "localhost");
define('DB_USER', "traslado");
define('DB_PWD', "ASxc2018#"); 
define('DB_DATABASE', "dbeternity"); 

/*
define('DB_SERVER', "192.168.0.25");
define('DB_USER', "root");
define('DB_PWD', "123456"); 
define('DB_DATABASE', "produccion"); 
*/
 
/* 
define('DB_SERVER', "192.168.1.254");
define('DB_USER', "root");
define('DB_PWD', "m3m0rial01*"); 
define('DB_DATABASE', "produccion"); */


/*define('DB_SERVER', "localhost");
define('DB_USER', "root");
define('DB_PWD', "");
define('DB_DATABASE', "produccion"); */


/*DATOS DE CONFIGURACION DE RECIBOS*/
define('RECIBO_CAJA', "RBC",true);
define('FACTURA_CONSUMO',"FC",true);
define('FACTURA_FISCAL', "FF",true);
define('NOTA_CREDITO', "NC",true);
define('NOTA_DEBITO', "ND",true);
define('RECIBO_VIRTUAL', "RBCV",true);


/*DATOS DE LA BASE DE DATOS*/
/*define('SIAD_DB_SERVER', "192.168.0.4");
define('SIAD_DB_USER', "sysdba");
define('SIAD_DB_PWD', "masterkey");
define('SIAD_DB_DATABASE', "d:/infosis/database/mfsbase.ib");*/
define('SIAD_DB_SERVER', "127.0.0.1");
define('SIAD_DB_USER', "sysdba");
define('SIAD_DB_PWD', "masterkey");
define('SIAD_DB_DATABASE', "127.0.0.1:d:\infosis\database\mfsbase.ib");
//-------------------------------------/
 
 
define('XSAM_HOST', "m2m.eclipse.org");
define('XSAM_PORT', "1883");
define('SAM_SERVER_NAME', "m3morial/comunicator");

define('SAM_HOST', "SAM_HOST");
define('SAM_PORT', "SAM_PORT"); 


//database name
define('DB_TABLE_ROLE', "sys_agentes");

/*SI SE INSTALA DENTRO UNA CARPETA QUE NO ES EL ROOT FOLDER ENTONCES SE AGREGA EL NOMBRE AQUI*/
define('_PATH_DEV', "eternity");

define('_PAGE_TITLE_', "Eternity");

/*Clase que maneja los tags html que seran insertado en la pagina*/
SystemHtml::getInstance()->addTagScript("script/jquery/jquery-1.9.1.js");
//SystemHtml::getInstance()->addTagScript("script/jquery-1.10.2.min.js");

SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
SystemHtml::getInstance()->addTagStyle("css/style.css");

SystemHtml::getInstance()->addTagStyle("css/bar_menu.css");

SystemHtml::getInstance()->addTagScript("script/select2.min.js"); 
SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js"); 
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");
SystemHtml::getInstance()->addTagScript("script/tinyeditor.js"); 
SystemHtml::getInstance()->addTagStyle("css/tinyeditor.css"); 
 
 
?>
