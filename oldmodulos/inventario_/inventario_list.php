<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['view_list_parcela'])){
	include("view/view_listado_parcela.php");
	exit;
} 

if (isset($_REQUEST['view_liberar_parcela'])){
	include("view/view_liberar_parcela.php");
	exit;
} 
if (isset($_REQUEST['view_agregar_producto'])){
	include("view/view_add_producto.php");
	exit;
} 

if (isset($_REQUEST['view_cambio_producto'])){
	include("view/view_cambio_producto.php");
	exit;
}

/*VISTA REMOVER PRODUCTO*/
if (isset($_REQUEST['view_remover_producto'])){
	include("view/view_remover_producto.php");
	exit;
} 
if (isset($_REQUEST['view_simple_inventario'])){
	include("view/simple_inventario_list.php");
	exit;
}


if (isset($_REQUEST['view_custom_inventario'])){
	include("view/custom_inventario_list.php");
	exit;
}

	if (isset($_REQUEST['doLiberarParcela']) && isset($_REQUEST['producto'])){
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		SystemHtml::getInstance()->includeClass("inventario","Inventario");  
		$inv= new Inventario($protect->getDBLink());  
		$prod=(array)$producto; 
		$prod['_estatus']=2; //ESTATUS REMOVIDO
		$valid=$inv->liberar_parecela_contrato($prod,$_REQUEST['comentario']);
		echo json_encode($valid);
 		exit;
	}


	if (isset($_REQUEST['doAgregarProducto']) && isset($_REQUEST['contrato']) && isset($_REQUEST['producto'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
		$producto_nuevo=STCSession::GI()->isSubmit("producto");
		$_contratos=new Contratos($protect->getDBLink()); 
		///print_r($producto);
		echo json_encode($_contratos->asignarProducto($contrato,$producto,$_REQUEST['comentario']));
 		exit;
	}

	if (isset($_REQUEST['doProcesarCambio']) && isset($_REQUEST['contrato']) && isset($_REQUEST['id_nit'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		$producto_nuevo=STCSession::GI()->isSubmit("producto");
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->cambioProducto($producto,$producto_nuevo,$_REQUEST['comentario']));
 		exit;
	}

	if (isset($_REQUEST['doRemoverProducto']) && isset($_REQUEST['producto'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		$producto_nuevo=STCSession::GI()->isSubmit("producto");
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->removerParcela($producto,$_REQUEST['comentario']));
 		exit;
	}
	
	if (isset($_REQUEST['doRealizarCambio']) && isset($_REQUEST['producto'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		$producto_nuevo=STCSession::GI()->isSubmit("producto");
		$_contratos=new Contratos($protect->getDBLink()); 
		echo json_encode($_contratos->cambioProducto($producto,$producto_nuevo,$_REQUEST['comentario']));
 		exit;
	}
	
	/*SELECCIONAR UN PRODUCTO */
	if (isset($_REQUEST['doSelect']) && isset($_REQUEST['producto'])){
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));
		STCSession::GI()->setSubmit("producto",$producto);
		echo json_encode($producto);
		exit;
	}

	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['dt_list_custom'])){
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
		$inv= new Inventario($protect->getDBLink()); 
		echo json_encode($inv->getListWithReserva($_REQUEST));
		exit;
	}



	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['dt_list'])){
		SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
		$inv= new Inventario($protect->getDBLink());
		echo json_encode($inv->getList($_REQUEST));
		exit;
	}

	
	if (isset($_REQUEST['action']) &&  isset($_REQUEST['items']) && isset($_REQUEST['cmd'])
		&& isset($_REQUEST['token'])){
			
		SystemHtml::getInstance()->includeClass("contratos","Carrito"); 
		$car= new Carrito($protect->getDBLink());
		$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['items']));
	 	/*ALMACENO LOS TOKEN EN UN LISTADO DE ITEM*/
		$car->setToken($_REQUEST['token']);
		$car->saveItem($_REQUEST['token']);
		
		$data=array("error"=>true,"mensaje"=>"","total_reserva"=>0);
		
		if ($_REQUEST['cmd']=="remove"){
			$data['error']=false; 
			$car->removeProducto($producto);
			$data['mensaje']="Item removido";
		} 
		if ($_REQUEST['cmd']=="add"){
			$data['error']=false; 
			$inf=$car->addProducto($producto);
			$data['total_reserva']=$inf['total_reserva'];
			switch($inf['valid']){
				case 1:
					$data['mensaje']="Item agregado";
				break;
				case 2:
					$data['mensaje']="Item existe!";
				break;	
			}
		}	
		
		
		//print_r($producto);
		echo json_encode($data);
		exit;
		
		exit;
		
	}
 

	/* AGREGAR LOTES */
	if (isset($_REQUEST['submit_lotes_add'])){
		if ($_REQUEST['submit_lotes_add']=="1"){
			//System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
			$jardin=json_decode(System::getInstance()->Request("jardin"));
			$fase=json_decode(System::getInstance()->Request("fase"));
			
		//	echo $_REQUEST['bloque'];
		//	print_r($_REQUEST);
			$retur=array("mensaje"=>"Operacion agregada correctamente!","error"=>false);


			if ($_REQUEST['sbloque']=="1"){
				$b_from=$_REQUEST['bloque_from'];
				$b_to=$_REQUEST['bloque_to'];
			}else{
				$b_from=$_REQUEST['bloque'];
				$b_to=$_REQUEST['bloque'];
			}
			
							
			$l_from=$_REQUEST['lotes_from'];
			$l_to=$_REQUEST['lotes_to'];
			
			$osa_code=array(
							'A','B','C','D','E','F','G',
							'H','I','J','K','L','M','N','O',
							'P','Q','R','S','T','U','V','W',
							'X','Z'
						);
			 
			
			for ($b=$b_from;$b<=$b_to;$b++){ 
				for ($l=$l_from;$l<=$l_to;$l++){
					 
					$obj= new ObjectSQL();
					$obj->id_jardin=$jardin->id_jardin;
					$obj->id_fases=$fase->id_fases;
					$obj->cavidades=$_REQUEST['cavidades'];
					$obj->osarios=$_REQUEST['osarios'];
					/*CODIGO SI TIENE*/
					$obj->osarios=$_REQUEST['osarios'];
					$obj->bloque=$b;
					$obj->lote=$l;
					$obj->estatus="1";
					$obj->EM_ID=System::getInstance()->Decrypt($_REQUEST['empresa']);
							
					if ($obj->osarios>0){
						$cantidad=$obj->osarios;
						for($l=0;$l<$cantidad;$l++){
							$obj->osarios=1;
							$obj->osario=$osa_code[$l];
							$SQL=$obj->getSQL("insert","inventario_jardines"); 
							mysql_query($SQL); 
						}
					}else{  
						$SQL=$obj->getSQL("insert","inventario_jardines");
						mysql_query($SQL); 
					} 
					
				}
			}
			
			
			echo json_encode($retur);
			exit;	
		}
	}

	/* EDITAR UN LOTE */
	if (isset($_REQUEST['submit_lote_edit'])){
		if ($_REQUEST['submit_lote_edit']=="1"){
			$retur=array("mensaje"=>"Registro actualizado correctamente!","error"=>false);


			$jardin=json_decode(System::getInstance()->Request("id"));			
			
			$obj= new ObjectSQL();
		//	$obj->id_jardin=$jardin->id_jardin;
		//	$obj->id_fases=$jardin->id_fases;
			$obj->cavidades=$_REQUEST['cavidades'];
			$obj->osarios=$_REQUEST['osarios'];
			//$obj->estatus="1";
			$SQL=$obj->getSQL("update","inventario_jardines"," where id_jardin='". mysql_escape_string($jardin->id_jardin) ."' AND id_fases='". mysql_escape_string($jardin->id_fases) ."' AND lote='". mysql_escape_string($jardin->lote) ."' ");
			
			mysql_query($SQL);
			
			echo json_encode($retur);
			exit;
		}
	}




	/* Editar LOTES */
	if (isset($_REQUEST['submit_group_lotes_edit'])){
		if ($_REQUEST['submit_group_lotes_edit']=="1"){
			//System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
			$jardin=json_decode(System::getInstance()->Request("jardin"));
			$fase=json_decode(System::getInstance()->Request("fase"));
			
		//	echo $_REQUEST['bloque'];
		//	print_r($_REQUEST);
			$retur=array("mensaje"=>"Operacion realizada correctamente!","error"=>false);


			if ($_REQUEST['sbloque']=="1"){
				$b_from=$_REQUEST['bloque_from'];
				$b_to=$_REQUEST['bloque_to'];
			}else{
				$b_from=$_REQUEST['bloque'];
				$b_to=$_REQUEST['bloque'];
			}
			
							
			$l_from=$_REQUEST['lotes_from'];
			$l_to=$_REQUEST['lotes_to'];

			for ($b=$b_from;$b<=$b_to;$b++){
				
				for ($l=$l_from;$l<=$l_to;$l++){
					 
					$obj= new ObjectSQL();
			//		$obj->id_jardin=$jardin->id_jardin;
			//		$obj->id_fases=$fase->id_fases;
					$obj->cavidades=$_REQUEST['cavidades'];
					$obj->osarios=$_REQUEST['osarios'];
			//		$obj->bloque=$b;
			//		$obj->lote=$l;
				//	$obj->estatus="1";
					$SQL=$obj->getSQL("update","inventario_jardines"," where id_jardin='". mysql_escape_string($jardin->id_jardin) ."' AND id_fases='". mysql_escape_string($fase->id_fases) ."' AND lote='". mysql_escape_string($l) ."' AND bloque='". mysql_escape_string($b) ."'");
					mysql_query($SQL);
			
				}
			}
			
			echo json_encode($retur);
			exit;	
		}
	}

	//SystemHtml::getInstance()->addTagStyle("css/jquery-ui-1.8.16.custom.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reservas.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.inventario.js","inventario");
	

	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.beneficiario.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/personalData.js");
		
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js");

	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 

	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");
 
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<script>

$(document).ready(function(){
	$("#role_list").dataTable({
							"bFilter": true,
							"bInfo": false,
							"bLengthChange": false,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_inventario/inventario_list&dt_list=1",
							"sServerMethod": "POST",
							"aoColumns": [
									{ "mData": "nombre_jardin" },
									{ "mData": "nombre_fase" },
									{ "mData": "bloque" },
									{ "mData": "lotes" },
									{ "mData": "descripcion" },
									{ "mData": "contrato" },
									{ "mData": "cavidades" },
									{ "mData": "osarios" },
									{ "mData": "serie_recibo_no" },
									{ "mData": "reserva_descrip" },
									{ "mData": "fecha_reserva" },
									{ "mData": "fecha_fin" },
									{ "mData": "bt_reserva" },
									{ "mData": "bt_editar" }
								],
							"oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								}
							});
							
	$('<button id="refresh" class="greenButton">Buscar</button>').appendTo('div.dataTables_filter');
	
	$('<button type="button" class="greenButton" name="pbutton"  id="pbutton" onclick="openDialogNewLote()" >Agregar producto </button>').appendTo('div.dataTables_filter');
	
	$('<button type="button" class="greenButton" name="pbutton"  id="pbutton" onclick="openDialogEditGroupLote()" >Modificar producto </button>').appendTo('div.dataTables_filter');
	
	
 
});


function openDialogNewLote(){
	$('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/inv_lotes_add",function(data){
		$('#inventario_page').hideLoading();	
	//	var dialog=createNewDialog("Agregar lote de jardines",data);
		var cl = new Empresa("content_dialog");
		var dialog=cl.createDialog("content_dialog","Agregar lote de jardines",data,500);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true 
					},
					osarios: {
						required: true 
					},
					cavidades: {
						required: true 
					},
					bloque: {
						required: true,
						number: true
					},
					bloque_from: {
						required: true,
						number: true
					},
					bloque_to: {
						required: true,
						number: true
					},					
					lotes_from: {
						required: true 
					},
					lotes_to: {
						required: true 
					},
					empresa: {
						required: true 
					}
					
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio"
					},
					osarios : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					cavidades : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},					
					bloque : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					bloque_from : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					bloque_to : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					lotes_from : {
						required: "Este campo es obligatorio"
					},
					lotes_to : {
						required: "Este campo es obligatorio" 
					},
					empresa: {
						required: "Este campo es obligatorio" 
					}
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			 $('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
			$.get("./?mod_inventario/inventario_list",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data);
				$('#inventario_page').hideLoading();	
			 	if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);
					window.location.reload();	
				}
			},"json");
		  }
		});
		
		
	});
	
}

function change_bloque(val){
	switch(val){
		case "0":
			$("#bloques_1").hide();
			$("#bloques_2").show();	
		break;
		case "1":
			$("#bloques_1").show();
			$("#bloques_2").hide();	
		break;	
	}

}


function openDialogEditGroupLote(){
	$('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/inv_lotes_grupo_edit",function(data){
		$('#inventario_page').hideLoading();	
		var dialog=createNewDialog("Editar lotes de jardines",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true 
					},
					osarios: {
						required: true 
					},
					cavidades: {
						required: true 
					},
					bloque: {
						required: true,
						number: true
					},
					bloque_from: {
						required: true,
						number: true
					},
					bloque_to: {
						required: true,
						number: true
					},					
					lotes_from: {
						required: true 
					},
					lotes_to: {
						required: true 
					}
					
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio"
					},
					osarios : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					cavidades : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},					
					bloque : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					bloque_from : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					bloque_to : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					lotes_from : {
						required: "Este campo es obligatorio"
					},
					lotes_to : {
						required: "Este campo es obligatorio" 
					}
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			 $('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_inventario/inventario_list",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data);
				$('#inventario_page').hideLoading();	
			 	if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);
					window.location.reload();	
				}
			},"json");
		  }
		});
		
		
	});
	
}


function openDialogEditLote(id){
	var inv= new Inventario('content_dialog');
	 	
	$('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/inv_lotes_edit",{"id":id},function(data){
		$('#inventario_page').hideLoading();	
	//	var dialog=createNewDialog("Editar lote de jardines",data);
		inv.doDialog("LotesEdit","inventario_page",data); 
		inv.addListener("onCloseWindow",function(){
				//alert('fsd');	
		})		
		$("#liberar_parcela").click(function(){
			inv.viewLiberarUbicacion(id);
		});
		
		 
		 $("#form_user_edit").validate({
				rules: {
					osarios: {
						required: true 
					},
					cavidades: {
						required: true 
					} 
				},
				messages : {
 
					osarios : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					cavidades : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					} 
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			 $('#inventario_page').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_inventario/inventario_list",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data);
				$('#inventario_page').hideLoading();	
			 	if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);
					window.location.reload();	
				}
			},"json");
		  }
		});
		
		
	});
	
}


function createDiv(){
	var rand="Dialog_"+Math.floor(Math.random() * (1000 - 1 + 1) + 1);
	$("#content_dialog").append("<div id=\""+rand+"\"></div>");
	return rand;
}
function createNewDialog(title,data){
	var rand=createDiv();
	$("#"+rand).attr("title",title);
	$("#"+rand).html(data);
	$("#"+rand).dialog({
		modal: true,
		width:500,
		close: function (ev, ui) {
			$(this).dialog("destroy");
			$(this).remove();

		}
	});	
	return rand;
}

var _reservas= new Reservas("content_dialog");

function _reservar(id){
 
	_reservas.add_jardin(id);
	/* inventario_page es el div donde se 
	mostrara el icono de loading al momento de reservar*/
	_reservas.show_dialog('inventario_page');
	
	
}
</script>
<style>
div.dataTables_filter{
	
	}
h2{
	color:#FFF;	
	margin-top:0px;
	margin-bottom:0px;
}
	
</style>
<div id="inventario_page" class="fsPage" style="width:98%">
<h2>Listado de Inventario</h2>
	<table border="0" class="display" id="role_list" style="font-size:13px;width:100%">
      <thead>
        <tr>
          <th width="100">Jardin</th>
          <th>Fase </th>
          <th>Modulo</th>
          <th>Parcela</th>
          <th>Estatus </th>
          <th>Contrato</th>
          <th>Cavidades</th>
          <th>Osarios</th>
          <th>No. Reserva</th>
          <th>Tipo <br />
          Reserva</th>
          <th>Inicia <br />
          Reserva</th>
          <th>Termina <br />
          Reserva</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
 
      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>