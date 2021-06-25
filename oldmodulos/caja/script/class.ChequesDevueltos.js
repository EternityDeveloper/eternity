var ChequesDevueltos = new Class({
	dialog_container : null,
	config: null,
	enc_id_nit :0,
	_view : null,
	_token : null,
	initialize : function(dialog_container){
		this.main_class="CierreCaja";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
		var instance=this;
		this.addListener("item_added",function(){
			instance.post("./?mod_caja/delegate&cheque_devueltos_list&getListItemAdded",{},function(data){ 
				$("#items_cheque").html(data);
			}); 
			if (!confirm("Desea seguir agregando items al listado?")){
				instance.CloseDialog("modal_listado_cheque");
			}
		}); 
		
		/*Evento que espera cuando seleccionan un responsable*/
		this.addListener("onResponsableSelect",function(data){
			instance.doReponerView(data.id,data.id_nit,data.contrato);			
		});		
	},
	doReponer : function(id){  
		var instance=this;   

		this.doBuscarResponsable(id); 
	}, 
	/*Responsable por el pago de la devolucion del cheque devuelto*/
	doBuscarResponsable : function(id){ 
		var instance=this;    
		this.post("./?mod_caja/delegate&cheque_devueltos_list&doViewResponsable",
		{ "id":id },
		function(data){ 
			instance._dialog=instance.doDialog("modal_view_responsable",
											instance.dialog_container,data);
			$("._repsonsable_sel").click(function(){ 
				if (confirm("Desea emitir un documento para cobrar la Comision bancaria por cheque devuelto?")){  
					instance.post("./?mod_caja/delegate&doGererarReciboComisionChequeDevuelto",{ 
							'id':id,
							"id_nit":$(this).attr("id_nit"),
							"contrato":$(this).attr("contrato")
					},function(data){ 
						 
					});
				}  		
				instance.CloseDialog("modal_view_responsable");
				instance.fire("onResponsableSelect",{
													 id_nit  : $(this).attr("id_nit"),
													 contrato: $(this).attr("contrato"),
													 "id"	 : id
													}); 		
			});								
		});
	},
	
	doRegistrar : function(){  
		var instance=this;    
		var rand_id=instance.getRand();  
		this.post("./?mod_caja/delegate&view_registar_cheque",{},function(data){ 
			instance._dialog=instance.doDialog("modal_devolucion_cuota",
												instance.dialog_container,data);
			  
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			 
			$("#fecha_cheque").datepicker({
						changeMonth: true,
						changeYear: true,
						yearRange: '1900:2050',
						monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
						monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
						dateFormat: 'yy-mm-dd',  
						dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
						dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
						dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
							
					});
			 
 			 
			$("#_find_contrato").click(function(){
				$("#_find_contrato").prop("disabled",true);
				instance.buscar_cheque();
				instance.addListener("waitForLoad",function(){
					$("#_find_contrato").prop("disabled",false);
				}) 
			});
			
			$("#bt_chk_cancel").click(function(){
				instance.CloseDialog("modal_devolucion_cuota");
			});
			$("#bt_registro_cheque").click(function(){
				if ($("#banco_debito").val()==""){
					alert('Debe de ingresar el banco');
					return false;	
				} 
				var data={
					"token":instance._token,
					"comentario":$("#observacion").val(),
					"fecha_cheque":$("#fecha_cheque").val(),
					"banco_debito":$("#banco_debito").val(),
					"doRegistrarChequeDV":1
				}; 
 				instance.post("./?mod_caja/delegate",data,
					function(data){ 
						alert(data.mensaje);	
						if (!data.error){ 
							window.location.reload();
 						} 
					},"json");
			});
			  
		}); 
		
			
	},
	
	buscar_cheque: function(){
		var instance=this;    
		var rand_id=instance.getRand();  
		this.post("./?mod_caja/delegate&cheque_devueltos_list",{"view_listado_cheque":1},function(data){ 
			instance._dialog=instance.doDialog("modal_listado_cheque",
												instance.dialog_container,data);
			
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			/*Evento que avisa que cargo la otra ventana*/
			instance.fire("waitForLoad");
			createTable("tb_cheques_devueltos",{
							"bSort": false,
							"bInfo": false,
							"bPaginate": true,
							"bLengthChange": false,
							"bFilter": true, 
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_caja/delegate&cheque_devueltos_list&filter=1",
							"sServerMethod": "POST",
							"aoColumns": [
									{ "mData": "FECHA" },
									{ "mData": "contrato" },
									{ "mData": "doc_id" },
									{ "mData": "AUTORIZACION" },
									{ "mData": "_MONTO" },
									{ "mData": "banco" } ,
									{ "mData": "url" } 
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
								} );
							},
						  "fnDrawCallback": function( oSettings ) {  
								$("._do_agregar").click(function(){
 									var data={
										"item":$(this).attr("id"),
										"doPutOnView":1
									}; 
									var parent=$(this).parent();
									instance.post("./?mod_caja/delegate&cheque_devueltos_list=1",data,
										function(data){ 
											if (!data.error){
												$(parent).html('<img src="images/succesfull-icon.png"/>');
												//alert($(parent).html());
												instance.fire("item_added");
											}else{
												alert(data.mensaje);	
											}
										},"json");
								});
								 
							}
						});
			
		
		}); 	 
	 	
	},
	
	doReponerView : function(id,id_nit,contrato){ 
		var instance=this;   
		var rand_id=instance.getRand();  
 
		this.post("./?mod_caja/delegate&view_pago_cuota",
			{	
				"id_nit":id_nit,
				"contrato":contrato,
				"reserva":""
			},
			function(data){ 
			instance._dialog=instance.doDialog(
												"modal_pago_cuota",
												instance.dialog_container,
												data
												);
			
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			setTimeout(function(){ 
					$("#banco_credito_view").show();
			},1500);
			$("#title_template").html("Reposición de cheque");  
			
 			instance._forma_pago = new FormaPago(instance.dialog_container);	
			
 			instance._forma_pago.setContrato(contrato);
			instance._forma_pago.setNIT(id_nit);
			 
			$("#detalle_general").show();
			 
			instance.loadScript("modulos/caja/script/class.Facturar.js?nocache="+instance.getRand(),function(){
				var fact=new Facturar(instance.dialog_container);
				fact._contrato=contrato;
				fact._id_nit=id_nit;
				fact._reserva="";
				fact.setToken(rand_id);
				fact.doLoadRecibos({
					valor_a_pagar : function(monto){ 
					//	instance._forma_pago.setMontoMinimo(monto);	
						/*$("#total_a_pagar").html('<span class="badge alert-danger">'+
													instance.number_format(monto)+'</span>');*/	
					}	
				});
			},function(){
				alert('No se pudo cargar la libreria');	
			}); 
			  
			  
			 /*ASIGNO EL TOKEN PARA SINCRONIZAR TODOS LOS COMPONENTES*/		
			instance._forma_pago.setToken(rand_id);
			
			instance._forma_pago.addListener("savePago",function(info){ 
				instance._forma_pago._data.monto_minimo_a_pagar=1; 
			//	alert(info.monto_acumulado);
				//$("#f_pago_total_a_pagar").html(info.monto_acumulado)
				//instance.updateMessageError(info);  
			}); 
			instance._forma_pago.addListener("onFormaPagoLoad",function(){
				$("#fiscal_question").hide();
				$("#bottom_question_ncf").hide();
			});
			instance._forma_pago.addListener("renderDetalleFormaPago",function(info){  
				instance.fire("newFormaPago",info); 
			}); 
			instance._forma_pago.addListener("removePago",function(info){  
				instance.fire("newFormaPago",info); 
			}); 			
				 	
			
			/*EVENTO QUE SE DISPARA CUANDO REMUEVEN UN PAGO*/
			instance._forma_pago.addListener("removePago",function(info){ 
				instance._forma_pago._data.monto_minimo_a_pagar=1;  
				instance._forma_pago._data.monto_a_cobrar=info.detalle.monto_a_pagar; 
				
			});	   
			
			instance._forma_pago._data.total_reserva=0; 
		    instance._forma_pago._data.monto_minimo_a_pagar=1;
	 		
			instance._forma_pago.setFormaPagoContentView('forma_pago_view');
			instance._forma_pago.loadModule();			 
	 		
			//$("#fecha_atraso").hide();
			$("#fecha_requerimiento_especial_xx").datepicker({
						changeMonth: true,
						changeYear: true,
						yearRange: '1900:2050',
						monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
						monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
						dateFormat: 'yy-mm-dd',  
						dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
						dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
						dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
							
					});	
 
			$("#bt_caja_cancel").click(function(){
				instance.fire("closeDialog")
				instance.CloseDialog(instance._dialog);	 
			});   
			$("#bt_caja_process").click(function(){
				if ($("#banco_credito").val()==""){
					alert("Debe de seleccionar el banco!");
					return false;	
				}
				if ($("#caja_payment").valid()){ 
					var err=false; 
					data={
						"rand_id":rand_id,
						"fecha_reposicion":$("#fecha_requerimiento_especial_xx").val(),
						"reposicion_id":id,
						"observacion":$("#observacion").val(),
						"banco_credito":$("#banco_credito").val(),
						"id_nit":id_nit,
						"contrato":contrato,
						"doReponerChequeDV":true
					}; 
					instance.post("./?mod_caja/delegate",data,
						function(data){ 
							 alert(data.mensaje);
							 if (!data.error){	 
							 	 window.location.reload();
							 }		
						},"json");
				  	 
				}	
			});
			 
		}); 
	}
});