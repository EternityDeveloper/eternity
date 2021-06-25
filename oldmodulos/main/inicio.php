<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	//SystemHtml::getInstance()->addModule("main/topmenu");

?>
Modulo de inicio


<?php SystemHtml::getInstance()->addModule("footer");?>