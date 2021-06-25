<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

	if (isset($_REQUEST['form_submit'])){
		if ($_REQUEST['form_submit']=="1"){
		 	SystemHtml::getInstance()->includeClass("client","PersonalData");
			
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			//print_r($_REQUEST); 
			$person->create();
			echo json_encode($person->getMessages());
	 
			exit;
		}
	}
	
	if (isset($_REQUEST['form_identifaction'])){
		if ($_REQUEST['form_identifaction']=="1"){
			$id_document=System::getInstance()->Decrypt($_REQUEST['tipo_documento']);
			 
		 	SystemHtml::getInstance()->includeClass("client","PersonalData");
			
			$person = new PersonalData($protect->getDBLink());
			
			$return=array(	
						"existe"=>$person->existClientByDocument($_REQUEST['numero_documento'],$id_document),
						"mensaje"=>"Este identficador de cliente existe, el sistema no acepta duplicados!",
						"typeError"=>101,
						"id_nit"=>System::getInstance()->Encrypt($_REQUEST['numero_documento'])
					);
			
			echo json_encode($return);
			
			exit;
		}
	}	


	SystemHtml::getInstance()->addTagStyle("css/south-street/jquery-ui-1.10.3.custom.css");

	/*Agrego el script para que el componente direcciones funcione*/
	SystemHtml::getInstance()->addTagScript("script/comp_direcciones.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");
	



?>
<style>
.fsPage{
	width:800px;
	padding-left:0px;
	margin-left:3px;
}

h2 button{
	float:right;
}

.ui-autocomplete {
	max-height: 100px;
	overflow-y: auto;
	overflow-x: hidden;
}

.validdate
{
    background:url('images/apply2.png') no-repeat right center;
}
.invalidate{
	background:url('images/agt_action_fail1.png') no-repeat right center;
}
</style>
<script>


$(function() {
 createFasterSearch(0);
 
 	$("#tabs").tabs({disabled: [1,2,3,4]});
	
    $("#client_form").validate({
			rules: {
				numero_documento: {
					required: true,
					minlength: 7
				}
			},
			messages : {
				numero_documento : {
					required: "Este campo es obligatorio",
					minlength: "Debes de digitar un minimo de 7 caracteres"	
				}	
				
			}
		
		});
	
	$.validator.messages.required = "Campo obligatorio.";
 
 	
	$("#procesar").click(function(){
	
		if ($("#client_form").valid()){
			$.post("./?mod_client/client_add",$("#client_form").serializeArray(),function(data){
				if (data.typeError=="100"){
					alert(data.mensaje);
					window.location.href="./?mod_client/client_edit&id="+data.nit;
				}else{
					alert(data.mensaje + " error "+data.typeError);
				} 
			},"json");
		}
	});
 	
 
 	$("#numero_documento").change(function(){
			var type_document=$('#id_documento option:selected').text();
			var valid_field=true;
			if (type_document.trim()=="CEDULA"){
				valid_field=valida_cedula($("#numero_documento").val());
			}
			if (($("#numero_documento").val().length>=7) && (valid_field)){
				$.get("./?mod_client/client_add",{form_identifaction:"1","numero_documento":$("#numero_documento").val(),"id_documento":$("#id_documento").val()},function(data){			
					if (data.existe==1){
						alert(data.mensaje);	
						$("#numero_documento").val("");
						$("#numero_documento").removeClass('validdate');
						$("#numero_documento").addClass('invalidate');
					}else{
						$("#numero_documento").removeClass('invalidate');
						$("#numero_documento").addClass('validdate');
					}
				},"json");	
			}else{
				$("#numero_documento").val('');
				$("#numero_documento").focus();
				alert('Digite un numero de identificacion valido!');	
			}
	});
	
 
 	$("#addphone").click(function(){	
		$.post("./?mod_component/comp_telefonos",{},function(data){
			$("#phoneFields").append(data);
		});
	});
 
   $("#phoneFields").on('click','.remCF',function(){
        $(this).parent().parent().parent().remove();
    });
	
	
	
 	$("#add_referencia").click(function(){
		$.post("./?mod_component/comp_referencia",{},function(data){
			$("#referencia_fields").append(data);
		});
		
	});
 
    $("#referencia_fields").on('click','.remCF',function(){
        $(this).parent().parent().parent().remove();
    });	
	
	
	 $("#add_email").click(function(){
		$.post("./?mod_component/comp_emails",{},function(data){
			$("#email_content").append(data);
		});
		
	});
 
    $("#email_content").on('click','.remCF',function(){
        $(this).parent().parent().parent().remove();
    });	
	
	
	$("#fecha_nacimiento").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:2050',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
            dateFormat: 'dd-mm-yy',  
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
            dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
                
		});
			
 
});


function addNewDirection(){
	counter_direction=counter_direction+1
	$.post("./?mod_component/comp_direcciones",{address_number:counter_direction},function(data){
		$("#list_direcciones").append(data);
		createFasterSearch(counter_direction);
		effectShadow("master_direction_"+counter_direction);
	})
}

</script> 
<div class="form-errors"></div>
<div class="fsPage">
<h2>Nuevo cliente </h2>
  <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Datos personales</a></li>
    <li><a href="#tabs-2" style="">Direccion</a></li>
    <li><a href="#tabs-3">Telefono / Email</a></li>
	<li><a href="#tabs-4">Contactos</a></li>
	<li><a href="#tabs-5">Referencias Personales</a></li>
  </ul>
  <div id="tabs-1">
  <form method="post"  action="" id="client_form" class="fsForm">
    <table width="100%" border="0" cellpadding="5" cellspacing="5" >

      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Genero<span>*</span></label></td>
      </tr>
      <tr>
        <td><input type="hidden" name="form_submit" id="form_submit" value="1" />
		<select name="id_documento" id="id_documento" class="required">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_documento'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
            <?php } ?>
        </select></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="numero_documento" name="numero_documento" size="20" value="" class="required" />
        </span></td>
        <td><p>
        <label>
          <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="id_genero"   />
          Masculino</label>
        <label>
          <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="id_genero"   />
          Femenino</label>
      </p></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer nombre<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Tercer nombre</label></td>
      </tr>
      <tr>
        <td><input type="text" id="primer_nombre" name="primer_nombre" size="20" value="" class="required" /></td>
        <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="" /></td>
        <td><input type="text" id="tercer_nombre" name="tercer_nombre" size="20" value="" /></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer apellido <span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo apellido</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Apellido de casada </label></td>
      </tr>
      <tr>
        <td><input type="text" id="primer_apellido" name="primer_apellido" size="20" value="" class="required" /></td>
        <td><input type="text" id="segundo_apellido" name="segundo_apellido" size="20" value=""></td>
        <td><input type="text" id="apellido_conyuge" name="apellido_conyuge" size="20" value=""></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Fecha nacimiento<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Lugar de nacimiento<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Estado civil</label></td>
      </tr>
      <tr>
        <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value="" class="required" placeholder="Dia - Mes - A&ntilde;o" /></td>
        <td><select name="lugar_nacimiento" id="lugar_nacimiento" class="required">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `paises` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idPaises'],$protect->getSessionID());
?>
          <option <?php echo $data_p['lugar_nacimiento']==$row['Pais']?'selected':''?>><?php echo $row['Pais']?></option>
          <?php } ?>
        </select></td>
        <td><select name="id_estado_civil" id="id_estado_civil" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_estado_civil` WHERE status_2='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_estado_civil'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo $row['nombre']?></option>
            <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td ><label class="fsLabel fsRequiredLabel" for="label">Profesi&oacute;n y oficio</label></td>
        <td ><label class="fsLabel fsRequiredLabel" for="label">Tipo Cliente</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Religion</label></td>
      </tr>
      <tr>
        <td ><select name="id_profecion" id="id_profecion" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_profeciones` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_profecion'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo ($row['descripcion']);?></option>
            <?php } ?>
        </select></td>
        <td ><select name="sys_clasificacion_persona" id="sys_clasificacion_persona" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_clasificacion_persona` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_clasificacion'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
            <?php } ?>
        </select></td>
        <td><select name="id_religion" id="id_religion" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_religiones` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_religion'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
            <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="firstname">Numero de hijos</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Cliente Local/Extrangero<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Nacionalidad<span>*</span></label></td>
      </tr>
      <tr>
        <td><input type="text" id="numero_hijos" name="numero_hijos" size="20" value="" /></td>
        <td><p>
        <label>
          <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="idtipo_cliente"   />
          Local</label>
        <label>
          <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="idtipo_cliente"  />
          Extranjero</label>
      </p></td>
        <td><select name="id_nacionalidad" id="id_nacionalidad" class="required">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_nacionalidad` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_nacionalidad'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"><?php echo ($row['Descripcion'])?></option>
            <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td colspan="3" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
        </tr>
    </table>

</form>	
 </div>
<div id="tabs-2">
   <form method="post"  action="" id="form_address" class="fsForm">
 
    <table width="100%" border="1">
    <tr>
      <td colspan="3"><button type="button" onclick="addNewDirection()">Agregar direccion</button></td>
    </tr>

    <tr>
      <td colspan="3" id="list_direcciones"><?php 
	  
	  /* Agrego los campos de direcciones */
	  $_REQUEST['address_number']=0;
	  include("modulos/component/comp_direcciones.php");?>&nbsp;</td>
      </tr>
    <tr>
      <td colspan="3" ><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
    </tr>
    </table>
    </form>
</div>
	
  <div id="tabs-3">
    <form method="post"  action="" id="form_email_phone" class="fsForm">

    <table width="100%" border="1">
    <tr>
      <td colspan="3"> <button type="button" id="add_email">Agregar correo </button> </td>
      </tr>
    <tr>
      <td colspan="3" id="email_content"><?php //include("modulos/component/comp_emails.php")?></td></td>
      </tr>
    <tr>
      <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
      <td>    
    </tr>
    <tr>
    </table>
    </form>
	
    </div>
	
	
  <div id="tabs-4">
     <form method="post"  action="" id="client_form" class="fsForm">

    <table width="100%" border="1">
    <tr>
      <td colspan="3"> <button type="button" id="add_email">Agregar correo </button> </td>
      </tr>
    <tr>
      <td colspan="3" id="email_content"><?php //include("modulos/component/comp_emails.php")?></td></td>
      </tr>
    <tr>
      <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
      <td>    
    </tr>
    <tr>
    </table>
    </form>
    </div>	
 <div id="tabs-5">
   <form method="post"  action="" id="form_reference" class="fsForm">

    <table width="100%" border="1">
		<tr>
		<td colspan="3"> <button type="button" id="add_referencia">Agregar referencias</button></td>
      </tr>
    <tr>
      <td colspan="3" id="referencia_fields">
	  <?php //include("modulos/component/comp_referencia.php")?>	  </td>
    <tr>
      <td colspan="3" align="center"  ><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
    </table>
    </form>
    </div>		
</div>
  
  
</div>
<?php SystemHtml::getInstance()->addModule("footer");?>