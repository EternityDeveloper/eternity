<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


//echo session_save_path();

	SystemHtml::getInstance()->addTagStyle("css/jquery-ui-1.8.16.custom.css");
	
	
//	SystemHtml::getInstance()->addTagStyle("css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/jquery.jOrgChart.css");
	SystemHtml::getInstance()->addTagStyle("css/custom.css");
//	SystemHtml::getInstance()->addTagStyle("css/prettify.css");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jOrgChart.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	SystemHtml::getInstance()->addTagStyle("css/base/jquery.ui.all.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<script>
    $(function() {
        $("#org").jOrgChart({
			chartElement : '#chart',
			dragAndDrop  : true
		});
		$("button").click(function(event){
		  event.stopPropagation();
		}); 

    });
</script>
<style>
.jOrgChart .title {
  font-size:20px;
  padding-top:10px;
}

.orgdesc
{
/*	background-color 	: #2CA70E;*/

	color 				: #000;
	width:200px;
	overflow:hidden; 
	background:white; 
	-moz-box-shadow: 0px 0px 0px #CCC;
    -webkit-box-shadow: 6px 3px 6px 1px #CCC;
     box-shadow:        0px 3px 6px 2px #CCC; 		
}

.orgdesc .desc_title{
	background-color:#2CA70E;
	text-align:center;
	font-size:14px;
	color:#FFF;
	padding: 2px 2px 2px 2px;
	font-weight: bolder;
}
.orgdesc .desc_descripcion{
	padding-left:15px;
	padding-top:10px;
	padding-bottom:10px;
	font-size:14px;
	font-family:Monospace;
	font-weight: bolder;
}

.option{
	margin-top:5px;
	background-color:#CCC;
			
}
</style>
<script>

function agregar_gerente(){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_estructurac/estruct_add":""},function(data){
		$("#dialog-global").attr("title","Agregar Gerente");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:500
		});
		
		
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"descripcion":$("#descripcion").val(),
					"submit":"true",
				}
				
				$.post("index.php?mod_estructurac/estruct_add",data,function(data){
				
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}


function agregar_director_divicion(id){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_estructurac/directore_add":"",'id_gerente':id},function(data){
		$("#dialog-global").attr("title","Agregar director de división");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:500
		});
		
		
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_directores":$("#id_directores").val(),
					"id_division":$("#id_division").val(),					
					"submit":"true",
				}
				
				$.post("index.php?mod_estructurac/directore_add",data,function(data){
					
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}



function agregar_gerentes_grupo(id,director_id){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_estructurac/subgerente_add":"",'id_gerente':id,'director_id':director_id},function(data){
		$("#dialog-global").attr("title","Agregar gerente de grupos de ventas");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:500
		});
		
		
		$("#bt_save").click(function(){

			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),
					"id_grupos":$("#id_grupos").val(),	
					"submit":"true",
					"id_division":$("#id_division").val()
				}
				
				$.post("index.php?mod_estructurac/subgerente_add",data,function(data){
					
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}



function agregar_asesor(id,director_id,gerente_grupo){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_estructurac/asesores_add":"",'id_gerente':id,'director_id':director_id,'gerente_grupo':gerente_grupo},function(data){
		$("#dialog-global").attr("title","Agregar asesor");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:500
		});
		
		
		$("#bt_save").click(function(){

			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),					
					"submit":"true",
					"id_division":$("#id_division").val(),
					"id_asesor":$("#id_asesor").val()
				}
				
				$.post("index.php?mod_estructurac/asesores_add",data,function(data){
					
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}

  
function _remove_gerente(id){
	var data={
		"id_gerente":id,
		"remove":"true"
	}
	   if (confirm("Esta seguro de elminar este item?")){	

		   $.post("index.php?mod_estructurac/estruct_add",data,function(data){
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json"); 
	   }
}
  
  
function _remove_director_division(id){
	var data={
		"id_director":id,
		"remove":"true"
	}
	   if (confirm("Esta seguro de elminar este item?")){	

		   $.post("index.php?mod_estructurac/directore_add",data,function(data){
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");  
	   }
}


function _remove_gerente_grupo(id){
	var data={
		"id_gerente_grupo":id,
		"remove":"true"
	}
   if (confirm("Esta seguro de elminar este item?")){	
	
	   $.get("index.php?mod_estructurac/subgerente_add",data,function(data){
				if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);	
					$("#dialog-global").dialog();
					window.location.reload();		
				}
			},"json"); 
   }
}




function _remove_asesor(id){
	var data={
		"id_asesor":id,
		"remove":"true"
	}
   if (confirm("Esta seguro de elminar este item?")){	
	   $.get("index.php?mod_estructurac/asesores_add",data,function(data){
				if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);	
					$("#dialog-global").dialog();
					window.location.reload();		
				}
			},"json"); 
   }
}

function _reload(){
  window.location.reload();
}

</script>
<h2>Estructura comercial</h2>

<div class="topbar">
   
 <ul id="org" style="display:none">
    <li width="200" class="title">
    
  		<div class="orgdesc">
        <div  class="desc_title"></div>	
        <div class="desc_descripcion">Estructura comercial Jardines Memorial</div>	
             <div class="option">   
                  <button type="button" class="positive" onclick="agregar_gerente()"><img src="images/apply2.png" alt=""/>Agregar</button>
             </div>            
    	</div>         
       <ul>
   
<?php 

$SQL="SELECT idgerentes,id_nit,CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre, 
	(SELECT COUNT(idgerentes) AS total FROM `sys_directores_division` WHERE `idgerentes` = sys_gerentes.idgerentes) as total_division
FROM `sys_gerentes` 
INNER JOIN sys_personas ON (sys_personas.`id_nit`=sys_gerentes.`sys_personas_id_nit`)
WHERE  sys_gerentes.status='1' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
//	print_r($row);
	$gerente_id=System::getInstance()->getEncrypt()->encrypt($row['idgerentes'],$protect->getSessionID());
?> 
         <li >
            <div class="orgdesc">
                <div  class="desc_title">Gerente General</div>	
                <div class="desc_descripcion"><?php echo $row['nombre']?></div>	
                 <div class="option">   
                      <button type="button" class="positive" onclick="agregar_director_divicion('<?php echo $gerente_id ?>')"><img src="images/apply2.png" alt=""/>Agregar</button>
                      <?php if ($row['total_division']<=0){?>
                       <button type="button" class="positive" onclick="_remove_gerente('<?php echo $gerente_id  ?>')"><img src="images/cross.png" alt=""/>Remover</button>
                       <?php } ?>
                 </div>
            </div>
<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row['total_division']>0)
{
?>            
       	 <ul>    
<?php 

$SQL="SELECT  
	 CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
	 iddirectores,
	 (SELECT COUNT(iddirectores) AS TOTAL FROM `sys_gerentes_grupos` WHERE 
	 `iddirectores`=sys_directores_division.iddirectores AND `idgerentes`='".$row['idgerentes']."') AS total_gerente_grupo
	FROM `sys_directores_division` 
INNER JOIN `sys_divisiones` ON (sys_divisiones.`iddivision`=sys_directores_division.`iddivision`)
INNER JOIN sys_personas ON (sys_personas.`id_nit`=sys_directores_division.`sys_personas_id_nit`)
WHERE `idgerentes` = '".$row['idgerentes']."' and sys_directores_division.status='1'";
	$rs_division=mysql_query($SQL);
	while($row_division=mysql_fetch_assoc($rs_division)){
		$director_divicion_id=System::getInstance()->getEncrypt()->encrypt($row_division['iddirectores'],$protect->getSessionID());
?>
		<li>
            <div class="orgdesc">
                <div  class="desc_title">Director de División</div>	
                <div class="desc_descripcion"><?php echo $row_division['nombre']?></div>	
                 <div class="option">   
                      <button type="button" class="positive" onclick="agregar_gerentes_grupo('<?php echo $gerente_id; ?>','<?php echo $director_divicion_id ?>')"><img src="images/apply2.png" alt=""/>Agregar</button>
                  <?php if ($row_division['total_gerente_grupo']<=0){?><button type="button" class="positive" onclick="_remove_director_division('<?php echo $director_divicion_id  ?>')"><img src="images/cross.png" alt=""/>Remover</button><?php } ?>
                 </div>
            </div>
<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row_division['total_gerente_grupo']>0)
{
?>   <ul>              
 <?php 

$SQL="SELECT
	CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
	idgerente_grupo,
	(SELECT COUNT(idgerente_grupo) AS total FROM `sys_asesor` WHERE `idgerente_grupo`=sys_gerentes_grupos.idgerente_grupo) as TOTAL_ASESOR
 FROM `sys_gerentes_grupos`
INNER JOIN sys_personas ON (sys_personas.`id_nit`=sys_gerentes_grupos.`id_nit`)
where `idgerentes` = '".$row['idgerentes']."' and iddirectores='".$row_division['iddirectores']."' AND  sys_gerentes_grupos.status='1' ";
	$rs_gg=mysql_query($SQL);
	while($row_gerente_g=mysql_fetch_assoc($rs_gg)){
		$idgerente_grupo=System::getInstance()->getEncrypt()->encrypt($row_gerente_g['idgerente_grupo'],$protect->getSessionID());
?>           
		<li>
            <div class="orgdesc">
                <div  class="desc_title">Gerente grupo de ventas</div>	
                <div class="desc_descripcion"><?php echo $row_gerente_g['nombre']?></div>	
                 <div class="option">   
                      <button type="button" class="positive" onclick="agregar_asesor('<?php echo $gerente_id; ?>','<?php echo $director_divicion_id ?>','<?php echo $idgerente_grupo;?>')"><img src="images/apply2.png" alt=""/>Agregar</button>
                       <?php if($row_gerente_g['TOTAL_ASESOR']<=0){?><button type="button" class="positive"  onclick="_remove_gerente_grupo('<?php echo $idgerente_grupo  ?>')"><img src="images/cross.png" alt=""/>Remover</button><?php }?>
                 </div>
            </div>

<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row_gerente_g['TOTAL_ASESOR']>0)
{
?>   <ul> 

 <?php 

$SQL="SELECT
CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
sys_gerentes_grupos_idgrupos  as asesor_id
 FROM `sys_asesor`
INNER JOIN sys_personas ON (sys_personas.`id_nit`=sys_asesor.`id_nit`)
WHERE 
`idgerente_grupo`='".$row_gerente_g['idgerente_grupo']."' and  sys_asesor.status='1'";
	$rs_asesor=mysql_query($SQL);
	while($row_asesor=mysql_fetch_assoc($rs_asesor)){
		$id_asesor=System::getInstance()->getEncrypt()->encrypt($row_asesor['asesor_id'],$protect->getSessionID());

?> 

        <li>
            <div class="orgdesc">
                <div  class="desc_title">Asesor de Familia</div>	
                <div class="desc_descripcion"><?php echo $row_asesor['nombre']?></div>	
                <div class="option">   
       <button type="button" class="positive"  onclick="_remove_asesor('<?php echo $id_asesor?>')"><img src="images/cross.png" alt=""/>Remover</button> 
                </div>
            </div>
		</li>
<?php } ?>    
    
	</ul>
<?php } ?>  


        </li>      

<?php } ?>          
  	
    </ul> 
    
<?php } ?>            
            
		 </li>
<?php } ?>
			
      	</ul>     
<?php }  ?>
   </li>
<?php } ?>          
    
       </ul>
       
       </li>
    
   </ul>            
</div>   
<div id="chart" class="orgChart"></div>

<div id="dialog-global" title="" style="display:block;background:#FFF">
