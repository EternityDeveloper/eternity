<?php 
//if (!isset($protect)){
//	exit;	
//}

SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
//if (!isset($_REQUEST['id'])){
//	exit;	
//}
echo "antes de todo";
$_contratos=new Contratos($protect->getDBLink());
  $id = json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$rt=  $_contratos->getBeneficiarios($id->serie_contrato,
					$id->no_contrato);
        return $rt;
        echo "esto no funciona";
