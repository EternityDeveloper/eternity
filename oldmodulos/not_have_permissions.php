<?php 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");

$permiso=$protect->getIDPermiso($_url);

$perm="";
if (count($permiso)>0){
	$perm=$permiso['id_pantalla'];
}
?><strong>No tiene permisos para acceder a esta pagina Codigo (<?php echo $perm;?>)</strong>