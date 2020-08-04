<?php

  if (!isset($protect)){
	exit;
  }

  /* Opcion Tabla de comisiones Asesores */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 1) ){
	  include ('view/pl_comiase_tbl.php'); 
	  exit; 
  }

  /* Opcion Tabla de Bono por Auxilio */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 2) ){
	  include ('view/pl_bonoaux_tbl.php'); 
	  exit; 
  }
  
  /* Opcion Tabla de Bono por Plan */
 if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 3) ){
	  include ('view/pl_bonoplan_tbl.php'); 
	  exit; 
  }
  
 /* Conceptos */
 if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 4) ){
	  include ('view/pl_conceptos_tbl.php'); 
	  exit; 
  }
  
 /* Comisiones Gerente */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 5) ){
	  include ('view/pl_comiger_tbl.php'); 
	  exit; 
  } 
  
   /* Opcion Tabla de Bono por Plan */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 6) ){
	  include ('view/pl_diferido_tbl.php'); 
	  exit; 
  }
  
 
  /* Tabla de Comisiones Cobros*/ 
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 7) ){
	  include ('view/tbl_comisiones_cobros.php'); 
	  exit; 
  } 
  /* Tipos de descuentos Cobros */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 8) ){
	  include ('view/tipo_descuentos_tbl.php'); 
	  exit; 
  } 
  
  /* Tipos de Incentivos Cobros */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 9) ){
	  include ('view/tbl_tipo_incentivo.php'); 
	  exit; 
  }  
?>