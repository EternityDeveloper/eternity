<?php

  if (!isset($protect)){
	exit;
  }
  
  /* Opcion GENERACION DE COMISIONES */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 1) ){
	  include ('gerentes/comision_gerentes.php'); 
	  exit; 
  }
  
  /*Mantenimiento a Planilla de Gerentes*/
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 2) ){
	  include ('gerentes/man_planilla_ger.php'); 
	  exit; 
  } 
  
  /* Opcion EMISION DE PLANILLA GERENTES */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 3) ){
	  include ('gerentes/planilla_gerentes.php'); 
	  exit; 
  }

?>