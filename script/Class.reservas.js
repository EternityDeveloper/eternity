// JavaScript Document
var Reservas = new Class({
	rand : '', //Almacena el random de los ids del formulario form_reserva_(rand)
	dialog_container : null,
	_cliente : null,
	_asesor : null,
	_form_pago : {
			'Abono':'1',
			'Horas':'2',
			'Gerencia':'3'
		},
	_info_submit : {
			'tipo_reserva':0,
			'reporte_venta':'',
			'personal_data':'',
			'asesor_data':''
	 	},
	_if_person_add:false,
	_if_asesor_add: false,
	_jardin : [], //Array donde se registran los ids de los jardines que seran reservados
	_follow_add: false,
	_tb_reserva:null,
	_actual_dialog: null,
	initialize : function(dialog_container){
		this.main_class="reserva";
		this.dialog_container=dialog_container;
		this._jardin =null;
		this._jardin=[];
		
		this._cliente = new ClienteReserva(this.dialog_container);
		this._asesor =  new AsesoresTree(this.dialog_container);
		this._asesor.filterByMyAsesores();
	},
	
	add_jardin : function(id){
		var exist=false;
		$.each(this._jardin,function(key,value){
			if (value==id){
				exist=true;
				alert('Este item ya se encuentra en la lista!');	
			}
		});
		
		if (!exist){
			this._jardin.push(id);
		}
		
		return !exist;	
	},
	
	remove_all_list : function(){
		this._jardin =null;
		this._jardin=[];
		this.clean_form_values();
		this._cliente.removeListener("client_select");
		this._cliente.removeListener("asesor_select");
	},
	
	show_dialog : function(dialog_loading_name){
	//	$('#'+dialog_loading_name).showLoading({'addClass': 'loading-indicator-bars'});	
		var rand=Math.floor(Math.random() * (1000 - 1 + 1) + 1);
		this.rand=rand;
		this._tb_reserva="tb_reserva_"+rand;
		
		var instance=this;
		this._cliente.addListener("client_select",function(person){
			$("#rs_cliente").html(person['nombre']+  " "+person['apellido']);
			instance._info_submit['personal_data']=person;
			instance._if_person_add=true; 
			
			/*SE CAMBIO POR QUE EN EL LISTADO DE CLIENTES ES DE PROSPECTOS*/
			instance.post("./?mod_inventario/reserva/reservar",
			{
				"getDataAsesorByCode":"1",
				"code":person['code_asesor']
			},function(inf){
		 
				var  asesor= { 
					"nombre":inf.nombre +" "+ inf.apellido,
					"code":inf.code,
					"idnit":inf.id_nit,
					"data":inf 
				}  
				$("#rs_asesor").html(asesor['nombre']);
				instance._info_submit['asesor_data']=asesor;
				instance._if_asesor_add=true;
			},"json");			
			
		});
		/*
		this._asesor.addListener("asesor_select",function(asesor){
			$("#rs_asesor").html(asesor['nombre']);
			instance._info_submit['asesor_data']=asesor;
			instance._if_asesor_add=true;
		}); */
		this.addListener("onCloseWindow",function(data){
			var div=this.createDiv(this.dialog_container);
			$("#"+div).html("<strong>Desea seguir agregando más productos a la reserva?</strong>");
			$("#"+div).dialog({
					  resizable: false,
					  height:140,
					  modal: true,
					  close: function (ev, ui) {
						$(this).dialog("destroy");
						$(this).remove();
						data.removeListener("onCloseWindow");
						data.remove_all_list();
					  },
					  buttons: {
						"Si": function() {
							$(this).dialog("destroy");
							$(this).remove();
							data.removeListener("onCloseWindow");
						},
						"No": function() {
							$(this).dialog("destroy");
							$(this).remove();
							data.removeListener("onCloseWindow");
							data.remove_all_list();
						}
					  }
				});
		});
		
		this.post("./?mod_inventario/reserva/reservar",{
						"rand":rand,
						"json":JSON.stringify(this._jardin),
						"forma_pago":JSON.stringify(this._info_submit)
					},function(data){
						
		//	$('#'+dialog_loading_name).hideLoading();	
			var dialog=instance.createDialog(instance.dialog_container,"Reserva",data,950);
			
			instance._actual_dialog=dialog;
			/*MODIFICANDO LA POSICION*/
			var n = $('#'+dialog);
			x=(document.scrollLeft/950);
    		y= 80;
			n.dialog('option', 'position', [x, y]); 
			/////////////////////////////			
	
			/*CREO LA TABLA DEL LISTADO DE ITEMS*/
			createTable(instance._tb_reserva,{
						"bFilter": false,
						"bInfo": false,
						"bPaginate": false,
						  "oLanguage": {
								"sLengthMenu": "Mostrar _MENU_ registros por pagina",
								"sZeroRecords": "No se ha encontrado - lo siento",
								"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
								"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
								"sInfoFiltered": "(filtrado de _MAX_ total registros)",
								"sSearch":"Buscar"
							}
						});
			
			$("#bt_seguir").click(function(){
				/*Almaceno los valores del formulario */
				instance.save_form_values();	
				
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
				instance.removeListener("onCloseWindow");
			});	
			
			$("#bt_rs_cancel").click(function(){
				/*Remuevo los datos*/
				instance.remove_all_list();	
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
				instance.removeListener("onCloseWindow");
			});	
			
			/*ELIMINIAR UNA DE LAS RESERVAS*/
			$(".res_links").click(function(){
				var elim = confirm('Esta seguro de eliminar este item?');
				if (elim){
					/* ELIMINO EL ITEM*/
					//instance._jardin[$(this).attr("rel")]=null;
					var data_inf=[];
					for(i=0;i<instance._jardin.length;i++){
						if (i!=$(this).attr("rel")){
							data_inf.push(instance._jardin[i]);
						}
					}
					instance._jardin=data_inf;
					
					$(this).parent().parent().remove();
					//$('tr', $(this).parent().parent()).remove();;
					/*REMUEVO LA VENTANA*/
				//	$("#"+dialog).dialog("destroy");
				//	$("#"+dialog).remove();
					/*REMUEVO EL EVENTO*/
				//	instance.removeListener("onCloseWindow");
					/*CARGO NUEVAMENTE LA VENTANA ACTUALIZADA*/
				//	instance.show_dialog(dialog_loading_name);
				}
				
			});
			
			$("#bt_rs_buscar").click(function(){ 
				instance._cliente.show_dialog(dialog_loading_name);
			});
			
			$("#bt_asesor_buscar").click(function(){
				instance._asesor.show_dialog(dialog_loading_name);
			});
			
			/*BOTON QUE RESERVA*/
			$("#bt_reserva_save").click(function(){
				/*Almaceno los valores del formulario */
				instance.save_form_values();	
				/*y envio el request*/
			    instance.sendRequest();
			});
			
			/*Valido la forma de pago*/
			instance.form_pago();
			
			$("#bt_seguir").tooltip();
			
		},"text");
	},
	
	/* Al momento de editar la reserva */
	doEditItemsFromList : function(dialog_loading_name){ 
		var instance=this;
		$(".editItems").click(function(){
			instance.editReserva($(this).attr("id"));		 
		}); 
	},
	editReserva : function(id_reserva){
		///var id_reserva=$(this).attr("id");
		//var click_event=this;
		var instance=this;
		instance.post("./?mod_inventario/reserva/reservar",{
			"view_reserva_edit":"1",
			"reserva_id": id_reserva
		},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Reserva",data,950);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/950), 20]); 
			  
			$("#bt_rs_cancel").click(function(){
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
			});
			  
			/*CREO LA TABLA DEL LISTADO DE ITEMS RESERVADOS */
			createTable("tb_items_reservados",{
					"bFilter": false,
					"bInfo": false,
					"bPaginate": false,
					  "oLanguage": {
							"sLengthMenu": "Mostrar _MENU_ registros por pagina",
							"sZeroRecords": "No se ha encontrado - lo siento",
							"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
							"sInfoFiltered": "(filtrado de _MAX_ total registros)",
							"sSearch":"Buscar"
						}
					});
			
			$(".remove_items_rev").click(function(){
				var id=$(this).attr("id");
				
				var data='<br><center><strong> <p>Esta seguro de eliminar este item de la reserva.</p></strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
						
					ndialog=instance.createDialog(dialog,"Eliminar",data,420);
	   
					$("#caputra_si").click(function(){  
						instance.post("./?mod_inventario/reserva/reservar",
							{remove_item:1,reserva:id_reserva,items:id},
						function(data){ 
							instance.close(dialog); 
							instance.close(ndialog); 
							alert(data.mensaje);
							if (data.error){
								instance.editReserva(id_reserva);			
							}	 		
						},"json");
					});
					$("#captura_no").click(function(){
						instance.close(ndialog); 
						 
					});	 
			});
			
			/*CREO LA TABLA DEL LISTADO DE ITEMS RESERVADOS */
			createTable("tb_abonos_reservas",{
					"bFilter": false,
					"bInfo": false,
					"bPaginate": false,
					  "oLanguage": {
							"sLengthMenu": "Mostrar _MENU_ registros por pagina",
							"sZeroRecords": "No se ha encontrado - lo siento",
							"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
							"sInfoFiltered": "(filtrado de _MAX_ total registros)",
							"sSearch":"Buscar"
						}
					});			

		},"text");		
	},
	form_pago : function(){
		var instance = this;
		/*EVENTO QUE CAPTURA EL TIPO DE RESERVA SELECCIONADO*/
		$("#tipo_reserva").change(function(){
			var data=$(this).val().split("_"); 
			/*SI ES DIFERENTE DESABILITO EL BOTON DE RESERVAR
			Y OCULTO EL TIPO DE RESERVA Y LOS PAGOS EN EFECTIVOS*/
			$("#bt_reserva_save").prop("disabled",true); 
 				
			/*SI ES ABONO */ 
			if (data[0]!="0"){
				$(".ft_reporte_venta").hide();
				$("#abono_reserva_monto").hide();
				/*ABONO ESTA MARCADO*/
				if (data[1]=="1"){ 
					instance._info_submit['tipo_reserva']=1;
					$(".fp_tipo_reserva").show();
				//	$("#abono_reserva_monto").show();
				//	$("#abono_reserva_monto").focus();
					//$(".ft_reporte_venta").show();
					instance.clean_form_field_values();
					$("#bt_reserva_save").prop("disabled",false); 	
				}else  if (data[2]=="1"){
					/*SI GERENCIA ESTA MARCADO*/ 
					instance._info_submit['tipo_reserva']=3;
					$("#bt_reserva_save").prop("disabled",false); 
				}else if (data[3]>0){ 
					/*HORAS*/
					//$(".ft_reporte_venta").show();
					instance._info_submit['tipo_reserva']=2;
					$("#bt_reserva_save").prop("disabled",false); 
				} 
			} 
		}); 
		 
		/*CUANDO CARGUE LA PANTALLA ENTONCES SI HA SELECCIONADO UN TIPO DE RESERVA ENTONCES 
		HABILITAR EL BOTON DE RESERVAR*/
		if ((this._info_submit['tipo_reserva']==this._form_pago['Abono']) ||(this._info_submit['tipo_reserva']==this._form_pago['Horas']) || (this._info_submit['tipo_reserva']==this._form_pago['Gerencia'])){
			$("#bt_reserva_save").prop("disabled",false); 
		}
		$("#no_recibo").change(function(){
			if ($.trim($(this).val())!=""){
				//$("#bt_reserva_save").prop("disabled",false); 	
			}
		});
		
	},
	
	/*VALIDACION PARA LA FORMA DE PAGO EN EFECTIVO*/
	forma_pago_form_efectivo_validate: function(){
		$("#form_reserva_"+this.rand).validate({
				rules: {
					forma_pago : {
						required: true
					},
					serie_recibo: {
						required: true
					},
					no_recibo: {
						required: true
					},
					monto: {
						required: true,
						number:true
					},
					tipo_cambio: {
						required: true,
						number:true 
					},
					no_documento: {
						required: true 
					} ,
					aprobacion: {
						required: true 
					},
					banco : {
						required: true
					},
					no_recibo : {
						required: true
					}
				},
				messages : {
					forma_pago : {
						required: "Este campo es obligatorio" 
					},
					serie_recibo : {
						required: "Este campo es obligatorio" 
					},
					no_recibo : {
						required: "Este campo es obligatorio" 
					},
					monto : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					tipo_cambio : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					no_documento : {
						required: "Este campo es obligatorio" 
					},
					aprobacion : {
						required: "Este campo es obligatorio" 
					},
					banco : {
						required: "Este campo es obligatorio" 
					},
					no_recibo : {
						required: "Este campo es obligatorio" 
					}
				}
			});
	},
	
	save_form_values : function(){
		this._info_submit['no_recibo']=$("#no_recibo").val();	
		this._info_submit['monto_abono']=$("#monto_abono").val();		
	},
	
	/*Elimino todas la variables utilizadas*/
	clean_form_values : function(){
		this._info_submit['serie_recibo']='';
		this._info_submit['no_recibo']='';
		this._info_submit['monto']='';
		this._info_submit['tipo_cambio']='';
		this._info_submit['no_documento']='';
		this._info_submit['aprobacion']='';	
		this._info_submit['tipo_reserva']=0;
		this._info_submit['forma_pago']=0;
		this._info_submit['reporte_venta']="";
		this._info_submit['personal_data']=[];
		this._info_submit['asesor_data']=[];
		
	},
	
	clean_form_field_values: function(){
		this._info_submit['serie_recibo']='';
		this._info_submit['no_recibo']='';
		this._info_submit['monto']='';
		this._info_submit['tipo_cambio']='';
		this._info_submit['no_documento']='';
		this._info_submit['aprobacion']='';	
		this._info_submit['reporte_venta']="";
		 	
		$("#serie_recibo").val('');
		$("#no_recibo").val('');
		$("#monto").val('');
		//$("#tipo_cambio").val('');
		$("#no_documento").val('');
		$("#aprobacion").val('');
		$("#banco").val('0');
 		$("#reporte_venta").val("");								
		
	},
	
	sendRequest : function(){
		/*SI NO HAY JARDINES SELECCIONADOS ENTONCES NO ENVIES EL FORMULARIO*/
		if (this._jardin.length>0){
			this.forma_pago_form_efectivo_validate();
			
			if (this._if_person_add){
				if (this._if_asesor_add){
					if ($("#form_reserva_"+this.rand).valid()){
				if (confirm("Esta seguro de realizar esta reserva?")){
					var instance=this;
 					instance.post("./?mod_inventario/reserva/reservar",{ 
											"process":"true",
											"json":JSON.stringify(this._jardin),
											"forma_pago":JSON.stringify(this._info_submit),
											"monto_abono":$("#monto_abono").val()
						},function(data){ 
						
							if (data.typeError=="100"){ 
								instance.close(instance._actual_dialog);
								
								var data='<div class="panel panel-success text-center">  <div class="panel-heading"><center><h3 class="panel-title"><strong><p class="text-center">DETALLE DE RESERVA</p></strong></h3>  </div> <div class="panel-body"><img src="images/information.png" width="64" height="64" /><br /> NUMERO DE RESERVA <br>(<strong>'+data.no_reserva+'</strong>)&nbsp;<br><br><br><button type="button" class="redButton" id="bt_info_close">Cerrar</button></center></div> </div> ';
								
								ndialog=instance.createDialog(instance.dialog_container,"",data,320); 
								$("#bt_info_close").click(function(){
									window.location.reload(); 
									 
								});	
							}else{
								alert(data.mensaje + " error "+data.typeError);
							}
							
							
						},"json");
				}
			}
				}else{
					alert('Debe seleccionar un asesor!');	
				}
			}else{
				alert('Debe seleccionar un cliente!');	
			}
		}else{
			alert('Debe de seleccionar un producto para completar el proceso!');	
		}
	}
 
});

var ClienteReserva = new Class({
	rand:null,
	_personal_data:[],
	_tb_clientes:null,
	_isCharge : false,
	_person_component : null,
	initialize : function(dialog_container){
		this.main_class="ClienteReserva";
		this.dialog_container=dialog_container;
	},
	
	show_dialog : function(dialog_loading_name){
		var instance=this;
		$('#'+dialog_loading_name).showLoading({'addClass': 'loading-indicator-bars'});	
		var rand=Math.floor(Math.random() * (1000 - 1 + 1) + 1);
		this.rand=rand;
		this._tb_clientes="tb_clientes_"+rand;
		var instance=this;
		$.post("./?mod_inventario/reserva/reservar",{
						"listar_clientes":"1",
						"rand":rand  },function(data){	
						
						
			$('#'+dialog_loading_name).hideLoading();	
			var cl_dialog=instance.createDialog(instance.dialog_container,"Seleccionar prospecto",data,950);
			var n = $('#'+cl_dialog);
			x=(document.scrollLeft/950);
    		y= 20;
			n.dialog('option', 'position', [x, y]); 
			/*CREO LA TABLA*/
			createTable(instance._tb_clientes,{
						"bSort": false,
						"bInfo": false,
						"bPaginate": true,
						"bLengthChange": false,
						"bFilter": true, 
						"bPaginate": true,
						"bProcessing": true,
						"bServerSide": true,
						"sAjaxSource": "./?mod_inventario/reserva/reservar&dt_list=1",
						"sServerMethod": "POST",
						
						"aoColumns": [
									{ "mData": "tipo_documento" },
									{ "mData": "id_nit" },
									{ "mData": "nombre" },
									{ "mData": "apellido" },
									{ "mData": "fecha_nacimiento" }, 
									{ "mData": "option2" }
								],
						"oLanguage": {
								"sLengthMenu": "Mostrar _MENU_ registros por pagina",
								"sZeroRecords": "No se ha encontrado - lo siento",
								"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
								"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
								"sInfoFiltered": "(filtrado de _MAX_ total registros)",
								"sSearch":"Buscar"
							},
						"fnServerData": function ( sSource, aoData, fnCallback ) {
 								$.getJSON( sSource, aoData, function (json) { 
									fnCallback(json)
									 /*
									$('.rs_add_link').click(function(){
										instance._isCharge=false;
										var nTds = $('td', $(this).parent().parent());
										var  person= { 
													"nombre":$(nTds[2]).text(),
													"apellido":$(nTds[3]).text(),
													"id_nit":$(this).attr("id"),
												}
								
										instance.close(cl_dialog);
										instance._personal_data=person;
										instance.fire("client_select",person);
									});	
									*/
									/*AL DAR CLICK EN EL TR*/
									var tr=$('td', $('.rs_add_link').parent().parent()).parent();
									tr.css( 'cursor', 'pointer' );
									tr.click(function(){
										var nTds=$(this).children();
									 	instance._isCharge=false;
										var  person= { 
													"nombre":$(nTds[2]).text(),
													"apellido":$(nTds[3]).text(),
													"id_nit":$(nTds).find("a").attr("id"),
													'code_asesor':$(nTds).find("a").attr("asesor")
												}
												 
										instance.close(cl_dialog);
										instance._personal_data=person;
										instance.fire("client_select",person);
										
										//alert($($(this).children().find("a")).attr("id"));	
									});
									/*AGREGANDO HIGTHLIGHT*/
									tr.hover(function(){ 
										$(this).addClass('hover_tr');  
									},function(){ 
										$(this).removeClass('hover_tr'); 
									});
									
								} );
							},
						"fnDrawCallback": function( oSettings ) { 
							if (!instance._isCharge){
								 
								 $('<button id="client_add"  class="greenButton">Agregar</button>').appendTo('#listado_cliente div.dataTables_filter'); 	
								 $("#client_add").click(function(){ 
									instance.close(cl_dialog);
									instance.doViewCreatePerson(); 	
									
								  });
								 
								
								instance._isCharge=true;
							}
						}
							
						});
 
		});	
		
	},
	
	doViewCreatePerson: function(data){

		var instance=this;
		this._person_component= new ModuloPersonas('Prospecto',this.dialog_container,this._dialog);
		this._person_component.loadMainView();
		
		var person= new Persona(this._person_component);
		
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("create");
		/*********************************************/
	 	
		person.addListener("cancel_creation",function(){
			instance._isCharge=false;
			instance._person_component.closeView();
		});
		
		/*Agrego el modulo al main content*/
		this._person_component.addModule(person);
 
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreatePerson",function(data){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			instance._person_component.closeView();
			/*********************************/
			
			/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
			person.setView("edit");
			/**********************************/
			/*Y vuelvo a cargar la vista*/
			instance._person_component.loadMainView();
			/**************************************/
			
			var direccion= new Direccion(instance._person_component);
			instance._person_component.addModule(direccion);
			
			direccion.addListener("doLoadViewComplete",function(obj){
				instance.insertIntoView(obj)
			});
			
			/**/
			var empresa= new personEmpresa(instance._person_component);
			instance._person_component.addModule(empresa);
			/***************************************************/
			 
			/**/
			var telefono= new Telefono(instance._person_component);
			instance._person_component.addModule(telefono);
			/***************************************************/
			 
			var email= new Email(instance._person_component);
			instance._person_component.addModule(email);	
			
			var reference= new Referencia(instance._person_component);
			instance._person_component.addModule(reference);
			
			var referidos = new Referidos(instance._person_component);
			instance._person_component.addModule(referidos);
			
			/*Le digo que cliente es el que sera editado*/
			instance._person_component.setPersonID(data.nit);
			/************************************************/
			/*SETTEO LA VISTA PARA QUE CARGE DE PRIMERO LA VISTA DE DIRECCION*/
			instance._person_component.selected(person.getTabID());
			 
		
		});
		
		/*SELECCIONO QUE MODULO SERA EL PRIMERO EN SER MOSTRADO*/
		this._person_component.selected(person.getTabID()); 

		/* CAPTURO EL EVENTO DE LA PERSONA SELECCIONADA */
		this.addListener("doIsPerson",function(person){
			
			//alert(person.primer_nombre + " "+person.primer_apellido+ " "+person.person_id);
			var  persons= { 
						"nombre":person.primer_nombre,
						"apellido":person.primer_apellido,
						"id_nit":person.person_id
					}
			  
			instance._personal_data=persons;
			instance.fire("client_select",persons);		 
		});
	},
	/*INSERTA LOS BOTONES DE CANCELAR Y SELECCIONAR EN UNA VISTA*/
	insertIntoView : function(obj){
		var instance=this;
		var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
		data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
		$("#main_module").append(data);
		
		$("#bt_pros_cancelar").click(function(){
			$("#"+instance._person_component._dialog).dialog("destroy");
			$("#"+instance._person_component._dialog).remove();		
		});
		
		$("#bt_pros_select").click(function(){
		//	$('#'+instance.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});
			var person=instance._person_component.getModule("Persona").getFormData();
			//alert(person.primer_nombre + " - "+person.primer_apellido);
			$("#"+instance._person_component._dialog).dialog("destroy");
			$("#"+instance._person_component._dialog).remove();	
			
			instance.fire("doIsPerson",person);	
		//	$('#'+instance.dialog_container).hideLoading();	
		});

	},
	
	close : function(client_dialog){
		$("#"+client_dialog).dialog("destroy");
		$("#"+client_dialog).remove(); 
	}
	
});

