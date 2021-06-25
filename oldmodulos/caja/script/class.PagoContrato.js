var PagoContrato = new Class({
	dialog_container : null,
	_data :{
		caja_submit : 1, 
		monto_a_pagar :0, 
		forma_pago : 0,
		contrato : null,
		serie_factura : null,
		no_factura : null,
		fecha_factura : null, 
		observacion :null,
		motorizado : null,
		tipo_documento :null,
		tipo_movimiento :null
	}, 
	fields : {
		tipo_movimiento :1	
	},
	_forma_pago : null,
	_montos_contrato: null,
	_token : null,
	_id_nit: null,
	initialize : function(dialog_container){
		this.main_class="PagoContrato";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
	},
	setToken : function(token){
		this._token=token;
	},
	getToken : function(){
		return this._token;
	},
	enableSearch : function(){
		var instance=this;
		$("#numero_documento").keypress(function(event){ 
			if ( event.keyCode == 13 ) { 
				 event.preventDefault();
				 instance.searchContract();
			} 
		});
		$("#_buscar").click(function(){
			instance.searchContract();
		});
	},	
	searchContract : function(){
		var instance=this; 
		if ($.trim($("#numero_documento").val())!=""){
			this.post("./?mod_caja/delegate&mov_contrato",{"search_contrato":1,"field":$("#numero_documento").val()},function(data){
			 
				$("#detalle_search").show();
				$('#detalle_search').html('');
				$('#detalle_search').html(data);
				createTable("caja_table_list",{
						"bSort": false,
						"bInfo": false, 
						"bLengthChange": false,
						"bFilter": false, 
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
			
				$(".item_select").hover(function(){  
					//$(this).addClass('hover_tr');  
				},function(){ 
					$(this).removeClass('hover_tr'); 
				}).css( 'cursor', 'pointer' ).click(function(){
					$('#detalle_search').html('');
					window.location.href="./?mod_caja/delegate&mov_contrato&view_statment=1&no_contrato="+$(this).attr("id");
					//instance.viewDetailContract($(this).attr("id")); 
				}); 
			});
		}
	},
	viewDetailContract : function(id,id_nit,view){
		var instance=this; 
		window.location.href="./?mod_caja/delegate&mov_contrato&view_statment=1&no_contrato="+id+"&id_nit="+id_nit
		/*;
		this.post("./?mod_caja/delegate&mov_contrato",{"view_statment":1,"no_contrato":id,"id_nit":id_nit},
		  function(data){ 
			$("#"+view).html(data);
			$("#c_bt_transpacion").click(function(){
				instance.doPay(id,id_nit);
			});
		});*/
		
		/*CARGO LOS MONTOS A APAGAR*/
		/*
		this.post("./?mod_caja/delegate&operacion",{"getMontoPagarContrato":1,"id_nit":id_nit,"no_contrato":id},function(data){
			instance._montos_contrato=data;  
		},"json");*/
				
	},
	registerMovimiento : function(id,id_nit){
		var instance=this;
		this.post("./?mod_caja/delegate&operacion",{"getMontoPagarContrato":1,"id_nit":id_nit,"no_contrato":id},function(data){
			instance._montos_contrato=data;  
		},"json");
		$("#c_bt_transpacion").click(function(){
			instance.doPay(id,id_nit);
		});	
	},
	updateMessageError: function(data){
		/*VALIDA SI LA FORMAS DE PAGO INTRODUCCIDO HAN COMPLETADO EL MONTO TOTAL DE LA CUOTA*/ 
		if (data.monto_completo==1){
			$("#p_err_message").hide();
		}else{
			 $("#p_err_message").show();
			 $("#p_err_message").html('El monto introducido esta incompleto');
		}		
	},
	/*
		Este metodo es el principal 
		el cual se programa el comportamiento del a ventana emergente
	*/
	doView : function(doctype,id){
		var instance=this; 
		this._data.contrato=id;

		var documento=new TDocSerieRVenta(instance.dialog_container);
		documento.addListener("onChangeTDocumento",function(obj){
			$("#factura_view_").html('');
			if (obj.FISCAL=="S"){
				factura.loadView('factura_view_');
			}
		}); 
		var factura = new CFactura(instance.dialog_container,instance._id_nit);
	  
		this.post("./?mod_caja/delegate&operacion",{"payment_client":1,"id_nit":instance._id_nit,"no_reserva":id},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Transacción",data,730);
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 0]);
						
			instance.validateForm();  
 			instance._forma_pago = new FormaPago(instance.dialog_container);	
			 /*ASIGNO EL TOKEN PARA SINCRONIZAR TODOS LOS COMPONENTES*/		
			instance._forma_pago.setToken(instance.getToken());
			
			instance._forma_pago.addListener("savePago",function(info){ 
				instance._forma_pago._data.monto_minimo_a_pagar=1;   ;
				instance.updateMessageError(info);  
			});
			
			/*EVENTO QUE SE DISPARA CUANDO REMUEVEN UN PAGO*/
			instance._forma_pago.addListener("removePago",function(info){ 
				instance._forma_pago._data.monto_minimo_a_pagar=1;  
				instance._forma_pago._data.monto_a_cobrar=info.detalle.monto_a_pagar;
				/* VALIDA SI LA FORMAS DE PAGO INTRODUCCIDO 
					HAN COMPLETADO EL MONTO TOTAL DE LA CUOTA */
				instance.updateMessageError(info.detalle); 
				
			});	 
			
			instance._forma_pago.addListener("renderDetalleFormaPago",function(info){   
				instance._forma_pago._data.monto_a_cobrar=info.detalle.monto_a_pagar;
				instance.updateMessageError(info.detalle); 	
			});						
 			 /*ASIGNO EL TOKEN PARA SINCRONIZAR TODOS LOS COMPONENTES*/
			documento.setToken(instance.getToken());
			documento.addListener("AvisoCobroMontos",function(data){   
				instance._forma_pago._data.monto_a_cobrar=data.monto;
			}); 
			 
			documento.loadViewCustom('doc_serie_view',id); 
			
			instance._forma_pago._data.total_reserva=0; 
		    instance._forma_pago._data.monto_minimo_a_pagar=1;
	 		
			instance._forma_pago.setFormaPagoContentView('forma_pago_view');
			instance._forma_pago.loadModule();			 
			  
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	 
			});  
			 
			$("#bt_caja_process").click(function(){
				if ($("#caja_payment").valid()){
					var err=false; 
					if(!err){
						instance._data.forma_pago_token=instance._forma_pago.getToken();
						instance._data.tpdocumento_obj=documento.getData();
						instance._data.observacion=$("#observacion").val(); 
						instance._data.tipo_documento=$("#tipo_documento").val(); 
						instance._data.no_documento=$("#no_documento").val();
						instance._data.serie_documento=$("#serie_documento").val();
						instance._data.reporte_venta=$("#reporte_venta").val();
						instance._data.serie_factura=$("#serie_factura").val();
						instance._data.no_factura=$("#no_factura").val();
						instance._data.fecha_factura=$("#fecha_factura").val();
						instance._data.fact_empresa=$("#fact_empresa").val(); 
						instance._data.doctype=doctype;			
						instance._data.pago_contrato=1;			 
					
						instance.post("./?mod_caja/delegate",instance._data,
							function(data){ 
								alert(data.mensaje);
								if (!data.error){
									 instance.close(dialog);	 
									 window.location.reload();
								}
								
							},"json");
					}
					  
						 
				}	
			});
			 
		});
	},
	doPay : function(id,id_nit){
		var instance=this; 
		this._data.contrato=id;

		var documento=new TDocSerieRVenta(instance.dialog_container);
		documento.addListener("onChangeTDocumento",function(obj){
			$("#factura_view_").html('');
			if (obj.FISCAL=="S"){
				factura.loadView('factura_view_');
			}
		})
		var factura = new CFactura(instance.dialog_container,id_nit);
	 
		
		this.post("./?mod_caja/delegate&operacion",{"payment_client":1,"id_nit":id_nit,"no_reserva":id},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Transacción",data,730);
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 0]);
						
			instance.validateForm();
			var sp=data.split("-->");
			sp=sp[0].split("<!--"); 
			 
			instance._forma_pago = new FormaPago(instance.dialog_container);
			instance._forma_pago._data.total_reserva=0;//parseInt($.trim(sp[1]));
		    instance._forma_pago._data.monto_minimo_a_pagar=1;//(instance._forma_pago._data.total_reserva*2000);
			instance._forma_pago._data.monto_a_pagar=0;
			instance._forma_pago.setFormaPagoContentView('forma_pago_view');
			instance._forma_pago.loadModule();
			
 
			instance._forma_pago.addListener("savePago",function(data){
				instance._forma_pago._data.total_reserva=parseInt($.trim(sp[1]));
				instance._forma_pago._data.monto_minimo_a_pagar=1; 
				instance._forma_pago._data.monto_a_pagar=(instance._forma_pago._data.total_reserva*2000);
				
			});
			instance._forma_pago.addListener("removeMount",function(data){
				//alert(this.total_abonado_pagos);
			}); 
			 
			documento.loadView('doc_serie_view');
			 
			/*VERIFICO SI ES UN TIPO DE MOVIMIENTO CUOTA*/ 
			if (instance._montos_contrato.tipo_movimiento=="CUOTA"){ 
			/*	$("#payment_mensaje_td").html('<div class="alert alert-info" style="width:300px;margin: 0 auto;">La cuota a pagar es de <strong id="tt_pago_cuota">0</strong></div>');
				$("#tt_pago_cuota").html(parseFloat((instance._montos_contrato.valor_cuota),10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
				$("#payment_mensaje_td").show();*/
				instance._forma_pago._data.monto_a_pagar=instance._montos_contrato.valor_cuota;
				
				instance.post("./?mod_caja/delegate&operacion",{"payment_detalle_contrato":1,"id_nit":id_nit,"contrato":id},function(data){ 		
					
					$("#payment_mensaje_td").html(data);
					$("#payment_mensaje_td").show();
							
				});
				
			}
				
			$("#factura_mov_label").click(function(){
				 var options = { percent: 0 };
				 $("#mov_caja_factura").toggle("blind", options, 500 );
			});
			 
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	 
			});  
			 
			$("#bt_caja_process").click(function(){
				if ($("#caja_payment").valid()){
					var err=false;
					
					if (instance._forma_pago.total_abonado_pagos>0) {
						
						if(!err){
							instance._data.list_forma_pago=instance._forma_pago.getFormaPago();
							instance._data.tpdocumento_obj=documento.getData();
							instance._data.observacion=$("#observacion").val(); 
							instance._data.tipo_documento=$("#tipo_documento").val(); 
							instance._data.no_documento=$("#no_documento").val();
							instance._data.serie_documento=$("#serie_documento").val();
							instance._data.reporte_venta=$("#reporte_venta").val();
							instance._data.serie_factura=$("#serie_factura").val();
							instance._data.no_factura=$("#no_factura").val();
							instance._data.fecha_factura=$("#fecha_factura").val();
							instance._data.fact_empresa=$("#fact_empresa").val(); 
							
							
							instance._data.form_submit_abono_persona=1;
							 
						
							instance.post("./?mod_caja/delegate&operacion",instance._data,
								function(data){ 
									alert(data.mensaje);
									if (!data.error){
										 instance.close(dialog);	 
										 instance.viewDetailAccount(id);
									}
									
								 },"json");
						}
					}else{ 
					
						$("#vtotal_pago").html(parseFloat((instance._forma_pago._data.total_reserva*2000),10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
 						$("#alert_payment").show();
						$("#close_alert").click(function(){
							$("#alert_payment").hide();
						});
					}
						 
				}	
			});
			 
		});
		
	},
	
	validateForm: function(){
		var instance=this;
		$("#caja_payment").validate({
			rules: {
				tipo_movimiento: {
					required: true
				},
				tipo_documento: {
					required: true
				}, 
				tipo_documento: {
					required: true
				},
				tipo_documento: {
					required: true
				},
				tipo_documento: {
					required: true
				}
			},
			messages : {
				tipo_movimiento : {
					required: "Este campo es obligatorio"
				},
				tipo_documento	 : {
					required: "Este campo es obligatorio"
				}, 
				tipo_documento	 : {
					required: "Este campo es obligatorio"
				},
				tipo_documento	 : {
					required: "Este campo es obligatorio"
				},
				tipo_documento	 : {
					required: "Este campo es obligatorio"
				}
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
		
		$("#fecha_factura").datepicker({
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
		
		$("#tipo_movimiento").change(function(){
			var mov=$("#tipo_movimiento :selected").attr('inf');
			$(".reserva_monto").hide();
			$(".reserva_abono").hide();
			if (mov==instance.fields.tipo_movimiento){
				instance.getMontos();
			}
 
		});
		
		/*VIEW QUE MUESTRA LOS MONTOS ABONADOS A LA RESERVA*/
		$("#abono_reserva_view").click(function(){
			instance.post("./?mod_caja/ingreso",{"viewPagoReserva":1,"contrato":instance._data.contrato},function(data){ 
				var dialog=instance.createDialog(instance.dialog_container,"Abonos Reserva",data,800);
				
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
						
				$("#bt_abono_cancel").click(function(){
					instance.close(dialog);	
				});
			});
		});		
		
		$("#forma_pago").change(function(){
			var forma_id=$(this).val();
			$(".tipo_trans_tarjeta").hide();
			instance._data.forma_pago=forma_id;
			switch(forma_id){ 	
				case "2": //FORMA DE PAGO Tarjeta de credito
					$(".tipo_trans_tarjeta").show();
				break;	
				case "3": //FORMA DE PAGO Transferencia Bancaria
					$(".tipo_trans_tarjeta").show();
				break; 					
			}  
	 	 
		});	 
		
	},
	
	getMontos : function(){
		var instance=this;
		this.post("./?mod_caja/ingreso",{"getTotalesMonto":1,"contrato":this._data.contrato},function(data){ 
			
			if (data.valid){
				
			 
				$("#abono_reserva").val(data.data.reserva);
				$('#abono_reserva').formatCurrency();

				instance._data.reserva_monto= parseFloat(data.data.reserva);
				instance._data.enganche= parseFloat(data.data.enganche);
				instance._data.monto_a_pagar=parseFloat(data.data.enganche)-parseFloat(data.data.reserva);
				
				$("#monto_incial").val(instance._data.enganche);
				$('#monto_incial').formatCurrency();
				$("#monto").val(instance._data.monto_a_pagar);
				$('#monto').formatCurrency();
				
				$(".reserva_monto").show();
				if (instance._data.reserva_monto>0){
					$(".reserva_abono").show();
				}

			}
		},"json");
	},
	
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
					reporte_venta : {
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
					reporte_venta : {
						required: "Este campo es obligatorio" 
					}
				}
			});
	}
	
	
});