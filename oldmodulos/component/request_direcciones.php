<?php

if (!isset($protect)){
	exit;
}




if (!isset($_REQUEST['action'])){
	exit;
}


switch($_REQUEST['action']){

	case "finder":
		if (!isset($_REQUEST['address'])){
			exit;
		}
		if (!isset($_REQUEST['number'])){
			exit;
		}	
		$number=$_REQUEST['number'];
			
		$find=mysql_escape_string($_REQUEST['address']);
		$SQL="SELECT 
		    sys_provincia.idprovincia,
			sys_municipio.idmunicipio,
			sys_ciudad.idciudad, 
			sys_sector.idsector,
			CONCAT(sys_provincia.descripcion,' / ',sys_municipio.descripcion,' / ',sys_ciudad.descripcion,' / ', sys_sector.descripcion) AS descripcion
		 FROM `sys_sector`
		INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_sector.`idciudad`)
		INNER JOIN `sys_municipio` ON (sys_municipio.`idmunicipio`=sys_ciudad.`idmunicipio`)
		INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_municipio.`idprovincia`)
		WHERE sys_sector.descripcion LIKE '%".$find."%' LIMIT 15 ";
		
		$data=array();
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			
			$idprovincia=System::getInstance()->getEncrypt()->encrypt($row['idprovincia'],$protect->getSessionID());
			$idmunicipio=System::getInstance()->getEncrypt()->encrypt($row['idmunicipio'],$protect->getSessionID());
			$idciudad=System::getInstance()->getEncrypt()->encrypt($row['idciudad'],$protect->getSessionID());
			$sectorID=System::getInstance()->getEncrypt()->encrypt($row['idsector'],$protect->getSessionID());
			
			$ubicacion=$idprovincia." / ".$idmunicipio." / ".$idciudad." / ".$sectorID." / ".$number." / ". $_REQUEST['component'];
			
			array_push($data,array("id"=>$ubicacion,"text"=>utf8_encode($row['descripcion'])));
		
		}
		echo json_encode(array("results"=>$data));
	break;
	case "loadmunicipio":
		if (!isset($_REQUEST['number'])){
			exit;
		}
		if (!isset($_REQUEST['id_field'])){
			exit;
		}
		$number=$_REQUEST['number'];
		$id_provincia=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_field'],$protect->getSessionID());
		
		?>
		<select name="municipio_id[]" id="municipio_id[]" class="required" onchange="loadAddressField(this.value,'loadciudad','<?php echo $number?>','ciudad_charge','<?php echo $_REQUEST['component'] ?>');">
				  <option value="">Seleccione</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_municipio` WHERE status_2='A' and idprovincia='". mysql_escape_string($id_provincia) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idmunicipio'],$protect->getSessionID());
		?>
				  <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
				  <?php } ?>
				</select>		
		<?php
		
	break;
	break;
	case "loadciudad":
		if (!isset($_REQUEST['number'])){
			exit;
		}
		if (!isset($_REQUEST['id_field'])){
			exit;
		}
	
		$id_municipio=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_field'],$protect->getSessionID());
		$number=$_REQUEST['number'];
		?>
		<select name="cuidad_id[]" id="cuidad_id[]" class="required"  onchange="loadAddressField(this.value,'loadsector','<?php echo $number?>','sector_charge','<?php echo $_REQUEST['component'] ?>');">
				  <option value="">Seleccione</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_ciudad` WHERE status_2='A' and idmunicipio='". mysql_escape_string($id_municipio) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idciudad'],$protect->getSessionID());
		?>
			<option value="<?php echo $encriptID?>"><?php echo $row['Descripcion']?></option>
				  <?php } ?>
		</select>		
		<?php
		
	break;	
	case "loadsector":
		if (!isset($_REQUEST['number'])){
			exit;
		}
		if (!isset($_REQUEST['id_field'])){
			exit;
		}
	
		$id_ciudad=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_field'],$protect->getSessionID());
		$number=$_REQUEST['number'];
		?>
		<select name="sector_id[]" id="sector_id[]" class="required">
				  <option value="">Seleccione</option>
				  <option value="-2">Agregar nuevo</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_sector` WHERE status='A' and idciudad='". mysql_escape_string($id_ciudad) ."' ORDER BY descripcion";
 		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idsector'],$protect->getSessionID());
		?>
				  <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
				  <?php } ?>
				</select>		
		<?php
		
	break;	
		
}	
?>