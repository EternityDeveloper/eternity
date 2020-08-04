<?php
/* esto es por si alguien accede a este link directamente*/
echo "va entrando";
if (!isset($protect)){
	exit;
}
 

SystemHtml::getInstance()->includeClass("estructurac","Asesores");  

if (isset($_REQUEST['getComercialParent'])){
	$_asesor=new Asesores($protect->getDBLink());
 
	if (isset($_REQUEST[code])){
		//$_SESSION['CONTRATO_DATA']['beneficiario1']=$_REQUEST['data'];
		
		$rs=$_asesor->getComercialParentData($_REQUEST[code]);
	 
		echo json_encode($rs);
	}

	exit;
}


if (isset($_REQUEST['showlist'])){
	

	if (isset($_REQUEST[sSearch])){
		if (trim($_REQUEST[sSearch])!=""){
			$_REQUEST[sSearch]=mysql_escape_string($_REQUEST[sSearch]);
			$QUERY=" WHERE (sys_personas.id_nit LIKE '%".$_REQUEST[sSearch]."%' 
					OR CONCAT(sys_personas.primer_nombre,' ' ,sys_personas.segundo_nombre) LIKE '%".$_REQUEST[sSearch]."%' 
					OR CONCAT(sys_personas.primer_apellido,' ' ,sys_personas.segundo_apellido) LIKE '%".$_REQUEST[sSearch]."%'
					OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ' ,sys_personas.primer_apellido,' ' ,sys_personas.segundo_apellido) LIKE '%".$_REQUEST[sSearch]."%' 	
					OR CONCAT(sys_personas.primer_nombre,sys_personas.segundo_nombre,sys_personas.primer_apellido ,sys_personas.segundo_apellido) LIKE '%". str_replace(' ','',$_REQUEST[sSearch]) ."%' 	
 
 					OR asesores_g_d_gg_view.id_comercial LIKE '%".$_REQUEST[sSearch]."%' 
					OR asesores_g_d_gg_view.tabla LIKE '%".$_REQUEST[sSearch]."%' )  ";
		}
	}
	if (isset($_REQUEST[list_type])){
		$list_type=json_decode($_REQUEST[list_type]);
		/* 1 = QUE LISTE SOLO LOS GERENTES DE VENTAS*/
		if (isset($list_type->filter_gerente_ventas)=="1"){
			 if ($list_type->filter_gerente_ventas=="1"){
				$QUERY.=" AND asesores_g_d_gg_view.tabla='Gerente de ventas' ";
			 }
		}
		if (isset($list_type->filter_show_my_asesores)=="1"){
			 if ($list_type->filter_show_my_asesores=="1"){ 
				$QUERY.=" and asesores_g_d_gg_view.id_comercial_gerente='".UserAccess::getInstance()->getComercialID()."' 
				and asesores_g_d_gg_view.id_comercial!='".UserAccess::getInstance()->getComercialID()."' ";
			 }
		}
		if (isset($list_type->filter_show_all_asesores)=="1"){
			 if ($list_type->filter_show_all_asesores=="1"){ 
				$QUERY.=" and asesores_g_d_gg_view.tabla='Asesor de Familia' ";
			 }
		}		
	}
	
	
	//$QUERY.=" and asesores_g_d_gg_view.tabla='Asesor de Familia' ";

	$SQL="SELECT count(*) as total FROM view_estructura_comercial as asesores_g_d_gg_view
	INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) ";
	$SQL.= $QUERY;
	  
	$rs=mysql_query($SQL);
	$row=mysql_fetch_assoc($rs);
	$total_row=$row[total];
	
	
	$SQL="SELECT 
			asesores_g_d_gg_view.*,CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre) AS primer_nombre,
			CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS primer_apellido  
		FROM view_estructura_comercial  as asesores_g_d_gg_view
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) ";
	$SQL.= $QUERY;
	
   
   	
	$SQL.=" limit ".$_REQUEST[iDisplayStart].",".$_REQUEST[iDisplayLength]."";
  
        	
	$rs=mysql_query($SQL);
	$data=array(
		'sEcho'=>$_REQUEST[sEcho],
		'iTotalRecords'=>10,
		'iTotalDisplayRecords'=>$total_row,
		'aaData' =>array()
	);
	while($row=mysql_fetch_assoc($rs)){
		$ecryt=System::getInstance()->Encrypt($row[id_nit]);
		$row["option"]='<a href="#" class="rs_add_link" id="'.$ecryt.'"><img src="images/netvibes.png" width="27" height="28" /></a>';
		array_push($data[aaData],$row);		
	}
	
	echo json_encode($data);

	exit;	
}


?>
<div class="modal fade" id="modal_listado_asesor" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:730px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Estructura comercial</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><button type="button" class="positive" id="crear_asesor">Crear</button></td>
          </tr>
          <tr>
            <td><table border="0" class="table table-hover" id="tb_estructura_comercial" style="font-size:12px;" >
              <thead>
                <tr>
                  <th>Codigo</th>
                  <th>Nombre </th>
                  <th>Apellido</th>
                  <th>Cargo</th>
                </tr>
              </thead>
              <tbody>
                <?php 
 
if (isset($_REQUEST['list_type'])){
	$list_type=json_decode($_REQUEST['list_type']);
	$QUERY.=" WHERE 1=1 ";
	
	if (!$protect->getIfAccessPageById(154)){ 
		/* 1 = QUE LISTE SOLO LOS GERENTES DE VENTAS*/
		if (isset($list_type->filter_gerente_ventas)=="1"){
			 if ($list_type->filter_gerente_ventas=="1"){
				$QUERY.=" AND asesores_g_d_gg_view.tabla='Gerente de ventas' ";
			 }
		}
		if (isset($list_type->filter_show_my_asesores)=="1"){
			 if ($list_type->filter_show_my_asesores=="1"){ 
				$QUERY.=" and asesores_g_d_gg_view.id_comercial_gerente='".UserAccess::getInstance()->getComercialID()."' 
				and asesores_g_d_gg_view.id_comercial !='".UserAccess::getInstance()->getComercialID()."' and asesores_g_d_gg_view.tabla='Asesor de Familia' ";
			 }
		}
		if (isset($list_type->filter_show_all_asesores)=="1"){
			 if ($list_type->filter_show_all_asesores=="1"){ 
				$QUERY.=" and asesores_g_d_gg_view.tabla='Asesor de Familia' ";
			 }
		}		
	}
}
 
	$SQL="SELECT asesores_g_d_gg_view.*,
	CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre) AS primer_nombre,
	CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS primer_apellido,
	(SELECT 
CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',
sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS nombre  
FROM sys_gerentes_grupos	
INNER JOIN sys_personas ON (sys_personas.id_nit=sys_gerentes_grupos.id_nit)
WHERE sys_gerentes_grupos.codigo_gerente_grupo=asesores_g_d_gg_view.id_comercial_gerente) as nombre_gerente  
	FROM view_estructura_comercial  as asesores_g_d_gg_view
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) ";
	
	if ($protect->getIfAccessPageById(154)){ 
		$SQL.=" WHERE  asesores_g_d_gg_view.tabla='Asesor de Familia' ";
	}else{
		$SQL.= $QUERY;	
	}
 	 
	$rs=mysql_query($SQL); 
	while($row=mysql_fetch_assoc($rs)){  
	 	
		$id=System::getInstance()->Encrypt(json_encode(array(
			"nombre"=>$row[primer_nombre]." ".$row[primer_apellido], 
			"id_nit"=>$row[id_nit],
			"id_comercial"=>$row[id_comercial]
		)));				
?>
                <tr style="cursor:pointer" class="list_ase_cx" name="<?php echo base64_encode($row[primer_nombre]." ".$row[primer_apellido])?>" name_gerente="<?php echo base64_encode($row[nombre_gerente])?>"  id="<?php echo $id;?>" id_nit="<?php echo System::getInstance()->Encrypt($row[id_nit]);?>">
                  <th><?php echo $row[id_comercial]?></th>
                  <th><?php echo $row[primer_nombre]?></th>
                  <th><?php echo $row[primer_apellido]?></th>
                  <th><?php echo $row[tabla]?></th>
                </tr>
                <?php }  ?>
              </tbody>
            </table></td>
          </tr>
        </table>
      </div>
       
    </div>:
  </div>
</div>
