<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


 

SystemHtml::getInstance()->includeClass("client","PersonalData");
			
			
$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());

$person= new PersonalData($protect->getDBLink());

/* VERIFICO SI EL CLIENTE EXISTE  */
if (!$person->existClient($client_id)){
	header("location:index.php?mod_client/client_list");
	exit;
}

	/*Actualiza los datos personales de un cliente*/
	if (isset($_REQUEST['form_submit'])){
		if ($_REQUEST['form_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$person->updatePersonalData();
			echo json_encode($person->getMessages());
			
			exit;
		}
	}
	
	

	if (isset($_REQUEST['doFiscal']) && 
							validateField($_REQUEST,'id') &&
							validateField($_REQUEST,'factura_fiscal') ){
		
		$person= new PersonalData($protect->getDBLink(),$_REQUEST);
		$person->doFacturarFiscal($_REQUEST['factura_fiscal']=="false"?0:1); 
		echo json_encode($person->getMessages());						
		exit;
	}
	
    /*Agrega un contacto a un cliente*/
	if (isset($_REQUEST['form_contactos_submit'])){
		if ($_REQUEST['form_contactos_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$person->addContacto($client_id);
			
			//print_r($_GET);
			echo json_encode($person->getMessages());
			
			exit;
		}
	}		
	
	/*Agrega una direccion a un cliente*/
	if (isset($_REQUEST['form_address_submit'])){
		if ($_REQUEST['form_address_submit']=="1"){
 
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			 
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],UserAccess::getSessionID());
		 
			if ($contact_id<0){
				$contact_id=0;
			}
			
			$person->addAddress($client_id,$contact_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	
	/*Agrega una direccion a un cliente*/
	if (isset($_REQUEST['form_contact_address_submit'])){
		if ($_REQUEST['form_contact_address_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],UserAccess::getSessionID());
		 
			if ($contact_id>0){
				$person->addAddress($client_id,$contact_id);
				echo json_encode($person->getMessages());
			}
			exit;
		}
	}	

	/*Agrega una direccion a un cliente*/
	if (isset($_REQUEST['form_phone_submit'])){
		if ($_REQUEST['form_phone_submit']=="1"){
		 	
		//	$_REQUEST['telefonos_tipo']=$_REQUEST['tipo'];
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],UserAccess::getSessionID());
		 
			if ($contact_id<0){
				$contact_id=0;
			}			
			
			$person->addPhone($client_id,$contact_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	
 	/*Agrega un email a un cliente*/
	if (isset($_REQUEST['form_email_submit'])){
		if ($_REQUEST['form_email_submit']=="1"){
		 	
		//	$_REQUEST['telefonos_tipo']=$_REQUEST['tipo'];
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],UserAccess::getSessionID());
		 
			if ($contact_id<0){
				$contact_id=0;
			}				
			$person->addEmail($client_id,$contact_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}
		/*Agrega una referencia personal a un cliente */
	if (isset($_REQUEST['form_referencia_submit'])){
		if ($_REQUEST['form_referencia_submit']=="1"){
		//	$_REQUEST['telefonos_tipo']=$_REQUEST['tipo'];
	 
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$person->addReference($client_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	
	/*Agrega un referido a una persona */
	if (isset($_REQUEST['form_referidos_submit'])){
		if ($_REQUEST['form_referidos_submit']=="1"){
	 
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$person->addReferido($client_id);
			 
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
		 
	/*Actualiza el estatus de las direcciones la desabilita */
	if (isset($_REQUEST['adress_submit'])){
		if ($_REQUEST['adress_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink());
			
			//print_r($_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$id_tipo_direccion=System::getInstance()->getEncrypt()->decrypt($_REQUEST['tipo_direccion'],UserAccess::getSessionID());
			$estatus=System::getInstance()->getEncrypt()->decrypt($_REQUEST['estatus'],UserAccess::getSessionID());
			
			$contact_id=0;
			if (isset($_REQUEST['contact_id'])){
				$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],$protect->getSessionID());
			} 
			
			$direccion_id=0;
			if (isset($_REQUEST['direccion_id'])){
				$direccion_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['direccion_id'],$protect->getSessionID());
			} 			
			
			$person->updateEstatusAddress($client_id,$id_tipo_direccion,$estatus,$contact_id,$direccion_id);
		
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	
	/*Actualiza los datos de un telefono */
	if (isset($_REQUEST['phone_submit'])){
		if ($_REQUEST['phone_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink());
			
			//print_r($_REQUEST);
			$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
			$id_tipo_direccion=System::getInstance()->Decrypt($_REQUEST['tipo']);
			$id_tipo_direccion_old=System::getInstance()->Decrypt($_REQUEST['old_tipo']);
			$estatus=System::getInstance()->Decrypt($_REQUEST['estatus']);
			
			$area_code=$_REQUEST['telefono_area'];
			$number=$_REQUEST['telefonos'];
			$extension=$_REQUEST['telefono_extension'];
			
			$phone_id=0;
			if (isset($_REQUEST['phone_id'])){
				$phone_id=System::getInstance()->Decrypt($_REQUEST['phone_id']);
			}
			//updatePhone($client_id,$id_tipo,$estatus,$area_code,$number,$extension=0,$phone_id=0)
			
			$person->updatePhone($client_id,$id_tipo_direccion,$id_tipo_direccion_old,$estatus,$area_code,$number,$extension,$phone_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}		
	/*Actualiza el estatus de los emails */
	if (isset($_REQUEST['email_submit'])){
		if ($_REQUEST['email_submit']=="1"){
			 
			$person= new PersonalData($protect->getDBLink());
			
			$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
			$id_tipo_email=System::getInstance()->Decrypt($_REQUEST['tipo']);
			$id_tipo_email_old=System::getInstance()->Decrypt($_REQUEST['old_tipo']);
			$estatus=System::getInstance()->Decrypt($_REQUEST['estatus']);		
			$email=$_REQUEST['email_direccion'];
			$observacion=$_REQUEST['email_descripcion'];	
			
			$email_id=0;
			if (isset($_REQUEST['email_id'])){
				$email_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['email_id'],$protect->getSessionID());
			}
			
			//$person->updateEstatusEmail($client_id,$id_tipo_email,$estatus,$email_id);
			$person->updateEmail($client_id,$id_tipo_email,$id_tipo_email_old,$estatus,$email,$observacion,$email_id);
		 
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	/*Actualiza el estatus de las referencias personales */
	if (isset($_REQUEST['ref_submit'])){
		if ($_REQUEST['ref_submit']=="1"){

			$person= new PersonalData($protect->getDBLink());
			
			$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
			$id_tipo=System::getInstance()->Decrypt($_REQUEST['tipo']);
			$id_tipo_old=System::getInstance()->Decrypt($_REQUEST['tipo_old']);
			$estatus=System::getInstance()->Decrypt($_REQUEST['estatus']);	
					
			$nombre=$_REQUEST['referencia_nombre'];
			$telefono=$_REQUEST['referencia_telefono_one'];
			$telefono2=$_REQUEST['referencia_telefono_two'];
			$observacion=$_REQUEST['referencia_descripcion'];
			
			$refer_id=0;
			if (isset($_REQUEST['refer_id'])){
				$refer_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['refer_id'],$protect->getSessionID());
			}		
		
			$person->updateReferencia($client_id,$id_tipo,$id_tipo_old,$nombre,$telefono,$telefono2,$observacion,$estatus,$refer_id);
		
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
	
	/*Actualiza el estatus de los REFERIDOS */
	if (isset($_REQUEST['referidos_submit'])){
		if ($_REQUEST['referidos_submit']=="1"){

			$person= new PersonalData($protect->getDBLink());
			
			$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
			$id_tipo=System::getInstance()->Decrypt($_REQUEST['tipo']);
			$id_tipo_old=System::getInstance()->Decrypt($_REQUEST['tipo_old']);
			$estatus=System::getInstance()->Decrypt($_REQUEST['estatus']);			
		 	$nombre1=$_REQUEST['nombre1'];
			$nombre2=$_REQUEST['nombre2'];
			$apellido1=$_REQUEST['apellido1'];
			$apellido2=$_REQUEST['apellido2'];
			$telefono=$_REQUEST['telefono'];
			$celular=$_REQUEST['movil'];
			$descripcion=$_REQUEST['descripcion'];
		 
		 
			$person->updateReferidos($client_id,$id_tipo,$id_tipo_old,$estatus,$nombre1,$nombre2,$apellido1,$apellido2,$telefono,$celular,$descripcion);
		
			echo json_encode($person->getMessages());
			
			exit;
		}
	}		
	
 
	if (isset($_REQUEST['form_identifaction'])){
		if ($_REQUEST['form_identifaction']=="1"){
		 	SystemHtml::getInstance()->includeClass("client","PersonalData");
			
			$person= new PersonalData($protect->getDBLink());

			echo json_encode(array("existe"=>$person->existClient($_REQUEST['numero_documento']),"mensaje"=>"Este identficador de cliente existe, el sistema no acepta duplicados!","typeError"=>101));
			
			exit;
		}
	}	
 
	/*Actualiza el estatus de los telefonos */
	if (isset($_REQUEST['remove_contacto_submit'])){
		if ($_REQUEST['remove_contacto_submit']=="1"){
		 	
			$person= new PersonalData($protect->getDBLink());
			
			//print_r($_REQUEST);
			$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],UserAccess::getSessionID());
			$tipo=System::getInstance()->getEncrypt()->decrypt($_REQUEST['tipo'],UserAccess::getSessionID());
			$estatus=System::getInstance()->getEncrypt()->decrypt($_REQUEST['estatus'],UserAccess::getSessionID());
			
			
			$contacto_id=0;
			if (isset($_REQUEST['contacto_id'])){
				$contacto_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contacto_id'],$protect->getSessionID());
			}
			
			$person->updateEstatusContacto($client_id,$tipo,$estatus,$contacto_id);
		
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}	
 
	/*Agrega una empresa a un cliente*/
	if (isset($_REQUEST['submit_empresa'])){
		if ($_REQUEST['submit_empresa']=="1"){
 
			$person= new PersonalData($protect->getDBLink(),$_REQUEST);
			
			$client_id=System::getInstance()->Decrypt($_REQUEST['id']);
	 		  
			$person->addEmpresa($client_id);
			
			echo json_encode($person->getMessages());
			
			exit;
		}
	}

	
	SystemHtml::getInstance()->addTagStyle("css/south-street/jquery-ui-1.10.3.custom.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");

	SystemHtml::getInstance()->addTagScript("script/Class.js");
	/*Agrego el script para que el componente direcciones funcione*/
//	SystemHtml::getInstance()->addTagScript("script/comp_direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	
	SystemHtml::getInstance()->addTagScript("script/personalData.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");
	

	$data_p=$person->getClientData($client_id);
 
 
	$_permisos=$protect->getPermisosByPage(System::getInstance()->getCurrentModulo());
 
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


var client_data;
var addressList =null;
var phoneList =null;
var emailList =null;
var reference =null;
var contactosList=[];
var empresa=null;

$(function() {

	$.validator.messages.required = "Campo obligatorio.";
	
	client_data = new PersonalData("tabs-1",'form_personal_data','<?php echo $_REQUEST['id']?>');
	client_data.loadCliente();
	
	empresa = new Empresa("tabs-7",'form_empresa','<?php echo $_REQUEST['id']?>');
	empresa.loadEmpresa();
	empresa.addListener("onCreateEmpresa",function(dt){
		window.location.reload();
	});
	
	/*CUANDO SE ACTUALISE LA PAGINA ENTONCES CARGA EL TAB EN EL 
	QUE REALIZARON LA ACTUALIZACION Y NO SE MUEVA HACIA EL PRIMERO*/
	$("#tabs").tabs({
		beforeActivate: function(event, ui) {
				var hash = ui.newTab.children("li a").attr("href");
				window.location.hash = hash;
		}	
	});	
	/* --------------------------------------------------------------*/
  
	addressList = new PersonAddress('personal_address_','<?php echo $_REQUEST['id']?>','list_direcciones');
	addressList.loadAddress();
	addressList.addListener("AddressChangeEstatus",function(){
		window.location.reload();
	});
	addressList.addListener("doEditAddress",function(info){
		addressList.viewAddress("editaddress",info.contact_id,info.index);	
	});
	addressList.addListener("AddressSave",function(form_data){
		$.post("./?mod_client/client_edit",form_data,function(data){
			if (data.typeError=="100"){
				addressList.loadAddress();
				alert(data.mensaje); 
			}else{
				alert(data.mensaje + " error "+data.typeError);
			} 
		},"json");
	});
	
 
	phoneList= new PersonPhone('personal_phone_','<?php echo $_REQUEST['id']?>','phoneFields');
	phoneList.loadPhone();
	phoneList.addListener("onCreatePhone",function(data){
		window.location.hash="tabs-3";
		window.location.reload();
	});
 
	emailList = new PersonEmail('personal_email_','<?php echo $_REQUEST['id']?>','email_content');
	emailList.loadEMail();
 
 
	reference= new PersonReference('personal_ref_','<?php echo $_REQUEST['id']?>','referencia_fields');
	reference.loadRef();

  
	contactosList = new PersonContactos('personal_contact_<?php echo $key;?>','<?php echo $_REQUEST['id']?>','contactos_field');
	contactosList.loadContact();
 

	$("#addphone").click(function(){	
		phoneList.addNewPhone();
	});
	
	 $("#add_email").click(function(){
		 emailList.addNewEmail();		
	});
	
 	$("#add_referencia").click(function(){
		 reference.addNewRef();	
	});
	
 	$("#add_contactos").click(function(){
		 contactosList.addNewContact();	
	});
	
});


function addNewDirection(){
	addressList.addNewAddress();	
}
function saveAddress(formName){
	if ($("#"+formName).valid()){
		$.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
			if (data.typeError=="100"){
				window.location.hash="tabs-2"
				alert(data.mensaje);
				window.location.reload();
			}else{
				alert(data.mensaje + " error "+data.typeError);
			} 

		},"json");
	}
}
function savePhone(formName){
	
	if ($("#"+formName).valid()){
		$.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
			if (data.typeError=="100"){
				window.location.hash="tabs-3"
				alert(data.mensaje);
				window.location.reload();
			}else{
				alert(data.mensaje + " error "+data.typeError);
			} 
 
		},"json");
	}
}

 

function addNewContactAddress(contact_id){
  contactosList.addNewAddress(contact_id);	
}
function addNewContactPhone(contact_id){
  contactosList.addNewPhone(contact_id);	
}
function addNewContactEmail(contact_id){
  contactosList.addNewEmail(contact_id);	
}

function saveContactAddress(formName,index,contact_id){

	if ($("#"+formName).valid()){
		
		$.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
			if (data.typeError=="100"){
				alert(data.mensaje);
				window.location.reload();
			}else{
				alert(data.mensaje + " error "+data.typeError);
			}
 
		},"json");
	}
}

function addNewContacto(formName){
	
}

function viewAddress(index,contact_id){
	addressList.viewAddress("editaddress",contact_id,index);	
}

function viewPhone(index,contact_id){
	phoneList.viewPhone("editphone",contact_id,index);
}
 
function viewContact(index,contact_id){
	contactosList.viewContact("editcontact",contact_id,index);
}

function saveContact(formName){
	if ($("#"+formName).valid()){
		$.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
			if (data.typeError=="100"){
				alert(data.mensaje);
				window.location.reload();
			}else{
				alert(data.mensaje + " error "+data.typeError);
			} 

		},"json");
	}
}



</script> 
<style>
.st_contactos{
	
}
</style>

<div class="form-errors"></div>
<div class="fsPage">
<h2>Editar cliente </h2>
  <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Datos personales</a></li>
    <li><a href="#tabs-7">Empresa</a></li>
    <li><a href="#tabs-2">Direccion</a></li>
    <li><a href="#tabs-3">Telefono </a></li>
	<li><a href="#tabs-4">Email</a></li>
	<li><a href="#tabs-5">Referencias Personales</a></li>
	<li><a href="#tabs-6">Contatos</a></li>
  </ul>

 <div id="tabs-1">
  	
 </div>

<div id="tabs-2">
  <table width="100%" border="1">
    <tr>
<td colspan="3" align="right"><?php if (trim($_permisos['Cambios'])=="1"){ ?>
<button type="button" onclick="addNewDirection()">Agregar direccion</button> <?php } ?></td>
    </tr>

    <tr>
      <td colspan="3" >
	  <div id="list_direcciones"> 
	  </div>
     </td>
    </tr>
  </table>
</div>

<div id="tabs-3">
  <table width="100%" border="1">
    <tr>
 <td colspan="3" align="right"><?php if (trim($_permisos['Cambios'])=="1"){ ?>
<button type="button" id="addphone">Agregar telefono</button><?php } ?></td>
      </tr>
    <tr>
      <td colspan="3">
	  <div  id="phoneFields"></div>
	  </td>
    </tr>
  </table>
</div>

<div id="tabs-4">
<table width="100%" border="1">
  <tr>
      <td colspan="3" align="right"><?php if (trim($_permisos['Cambios'])=="1"){ ?>
<button type="button" id="add_email">Agregar correo </button><?php }  ?></td>
      </tr>
    <tr>
      <td colspan="3" >
	   
	  <div id="email_content"> </div> </td></td>
  </tr>
</table>

</div>

<div id="tabs-5">
<table width="100%" border="1">
  <tr>
<td colspan="3" align="right">	<?php if (trim($_permisos['Cambios'])=="1"){ ?>
<button type="button" id="add_referencia">Agregar referencias</button><?php } ?></td>
      </tr>
    <tr>
      <td colspan="3">

	  <div id="referencia_fields">
	  
	  </div> </td>
  </tr>
</table>
</div>
<div id="tabs-6">
  <table width="100%" border="1">
    <tr>
    <td colspan="3" align="right"><?php if (trim($_permisos['Cambios'])=="1"){ ?>
      <button type="button" id="add_contactos">Agregar Contactos</button>
      <?php } ?></td>
  </tr>
  <tr>
    <td colspan="3">
      <div id="contactos_field">
 
      </div>
     </td>
  </tr>
</table>
 
</div> 
<div id="tabs-7">
  
 
</div> 

</div>

</div>
<div id="editaddress" title="" style="display:block;background:#FFF"></div>
<div id="editphone" title="" style="display:block;background:#FFF"></div>
<div id="editemail" title="" style="display:block;background:#FFF"></div>
<div id="editrefe" title="" style="display:block;background:#FFF"></div>
<div id="editcontact" title="" style="display:block;background:#FFF"></div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>