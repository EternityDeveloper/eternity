<?php

  if (!isset($protect)){
	exit;
  }

  /* Opcion GENERACION DE COMISIONES */ 
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 1) ){
	  
	  include ('asesores/pl_genera_calculos.php');    
	  exit;
  }
    
  /* Opcion BONO POR PLAN */
   if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 2) ){
	  include ('asesores/pl_bonopor_plan.php'); 
	  exit; 
  }
  
  /* Opcion BONO POR AUXILIO */
   if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 3) ){
	  include ('asesores/pl_bonopor_auxilio.php'); 
	  exit; 
  }  
  
  /* Opcion MANTENIMIENTO DE PLANILLA */
   if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 4) ){
	  
	  include ('asesores/man_planilla_ase.php'); 
	  exit; 
  }  
  
 
 /* Opcion IMPRESION DE PLANILLA */
   if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 5) ){
	  include ('asesores/pl_impresion_planilla.php'); 
	  exit; 
  }  
 /* Diferidos */ 
 if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 6) ){
	  
	  include ('asesores/pl_genera_diferidos.php');    
	  exit;
  } 
  

?>