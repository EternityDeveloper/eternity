var PagoComponent = new Class({
	dialog_container : null,
	_token: null,
	_id_nit : null, 
	_contrato : null,
	_dialog : null,
	_notas_cd : null,
	initialize : function(dialog_container){
		this.main_class="PagoComponent";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
	},
	setToken : function(token){
		this._token=token;
	},
	getToken : function(){
		return this._token;
	},
	doEnableSubmit : function(){
		setTimeout(function(){ 
			$("#bt_caja_process").attr("disabled",false);
		},500);
	},
	doDisableSubmit : function(){ 
		setTimeout(function(){ 
			$("#bt_caja_process").attr("disabled", true);
		},500);
	},
	/*
		Este metodo es el principal 
		el cual se programa el comportamiento del a ventana emergente
	*/
	doView : function(comportamiento_obj){
		var instance=this;  
		var documento=new TDocSerieRVenta(instance.dialog_container);
  		
		this.post("./?mod_caja/delegate&view_pago_cuota",
				{
					"id_nit":comportamiento_obj._id_nit,
					"contrato":comportamiento_obj.contrato,
					"reserva":comportamiento_obj.reserva
				},
				function(data){ 
			instance._dialog=instance.doDialog("modal_pago_cuota",
												instance.dialog_container,data);
			
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			
			$("#title_template").html(comportamiento_obj.title);
 			instance.validateForm();  
			
			
 			instance._forma_pago = new FormaPago(instance.dialog_container);	
			instance._forma_pago.setNIT(comportamiento_obj.id_nit);
			
			instance._forma_pago.setContrato(comportamiento_obj.contrato);
			  
			 /*ASIGNO EL TOKEN PARA SINCRONIZAR TODOS LOS COMPONENTES*/		
			instance._forma_pago.setToken(instance.getToken());
			
			instance._forma_pago.addListener("savePago",function(info){ 
				instance._forma_pago._data.monto_minimo_a_pagar=1; 
			//	alert(info.monto_acumulado);
				//$("#f_pago_total_a_pagar").html(info.monto_acumulado)
				//instance.updateMessageError(info);  
			}); 
			instance._forma_pago.addListener("onFormaPagoLoad",function(){
				instance.fire("onFormaPagoLoad");	
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
				/* VALIDA SI LA FORMAS DE PAGO INTRODUCCIDO 
					HAN COMPLETADO EL MONTO TOTAL DE LA CUOTA */
				//instance.updateMessageError(info.detalle); 
				
			});	  
								
 			 /*ASIGNO EL TOKEN PARA SINCRONIZAR TODOS LOS COMPONENTES*/
			documento.setToken(instance.getToken());
			documento.addListener("AvisoCobroMontos",function(data){   
				instance._forma_pago._data.monto_a_cobrar=data.monto;
			}); 
			 
		//	documento.loadViewReciboCaja('doc_serie_view',id); 
			
			instance._forma_pago._data.total_reserva=0; 
		    instance._forma_pago._data.monto_minimo_a_pagar=1;
	 		
			instance._forma_pago.setFormaPagoContentView('forma_pago_view');
			instance._forma_pago.loadModule();			 
			
			/*Cargo el modulo de notas de credito*/
			instance.loadScript("./?mod_caja/delegate&includeScript&script_name=NotasCD",function(){ 
 				instance._notas_cd = new NotasCD(instance.dialog_container);	
				instance._notas_cd.setContrato(comportamiento_obj.contrato);				
				instance._notas_cd.loadModule();
				instance._notas_cd.addListener("_refresh",function(){
					instance.doEnableSubmit();	
				});	
			},function(){
				alert('Error al cargar el modulo');	
			});
		 
			
			  
			$("#bt_caja_cancel").click(function(){
				instance.fire("closeDialog")
				instance.CloseDialog(instance._dialog);	 
			});  
			

			comportamiento_obj.onload();
			 
			$("#bt_caja_process").click(function(){
				if ($("#caja_payment").valid()){ 
					var err=false; 
					if(!err){
						 comportamiento_obj.onSubmit();
					}
					  
						 
				}	
			});
			 
		});
	},
	Cerrar : function(){
		$("#bt_caja_cancel").click();
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
		
		/*
		$("#tipo_movimiento").change(function(){
			var mov=$("#tipo_movimiento :selected").attr('inf');
			$(".reserva_monto").hide();
			$(".reserva_abono").hide();
			if (mov==instance.fields.tipo_movimiento){
				instance.getMontos();
			}
 
		});*/
		
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