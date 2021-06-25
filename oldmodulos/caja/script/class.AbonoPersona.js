var AbonoPersona = new Class({
	dialog_container : null,
	_data :{
		caja_submit : 1, 
  		id_nit : null,
 		observacion :null,
		motorizado : null,
		tipo_documento :null,
		total_reserva:0
 	}, 
	_forma_pago : null,
	_view : null,
	fields : {
		tipo_movimiento :1	
	},
	lastsearch : null,
	initialize : function(dialog_container){
		this.main_class="AbonoPersona";
		this.dialog_container=dialog_container; 
	},	 
	searchPerson : function(){
		var instance=this; 
		
		$("#numero_documento").keypress(function(event){ 
			if ( event.keyCode == 13 ) { 
				 event.preventDefault();
				$("#_buscar").click();
			} 
		});
		
		$("#_buscar").click(function(){ 
			 if ($.trim($("#numero_documento").val())!=""){ 
				instance.search($("#numero_documento").val());
			 } 
		});

	},
	search : function(documento){
		if (documento!=""){
			var instance=this; 
			this.lastsearch=documento;
			$("#numero_documento").val(documento)
			instance.post("./?mod_caja/delegate&mov_inicial",{"search_peson":1,"field":documento},function(data){
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
				},function(){ 
					$(this).removeClass('hover_tr'); 
				}).css( 'cursor', 'pointer' ).click(function(){
					$('#detalle_search').html('');
					instance.viewDetailAccount($(this).attr("id")); 
				}); 
			});		
		
		}
	},
	/*VER EL ESTADO DE CUENTA*/
	viewDetailAccount : function(id,view){
		var instance=this; 
		this._data.id_nit=id; 
		this._view=view;
		this.post("./?mod_caja/delegate&operacion&mov_cliente",{"view_statment":1,"no":id},function(data){ 
			$("#"+view).html(data);
			 
			$("#bt_abono_reserva").click(function(){
				instance.viewPersonPayment(id);
			});
			  
			$("#back_r").click(function(){
				window.location.href="?mod_caja/delegate&mov_inicial&search="+instance.lastsearch;
			});		
		});
	},
	
	viewPersonPayment : function(id){
		var instance=this; 
		this._data.id_nit=id; 
		
		var documento=new TDocSerieRVenta(instance.dialog_container);
		documento.addListener("onChangeTDocumento",function(obj){
			$("#factura_view_").html('');
			if (obj.FISCAL=="S"){
				factura.loadView('factura_view_');
			}
		})
		var factura = new CFactura(instance.dialog_container,id);
		
		this.post("./?mod_caja/delegate&operacion",{"payment_client":1,"id_nit":id},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Transacción",data,730);
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 0]);
						
			instance.validateForm();
			var sp=data.split("-->");
			sp=sp[0].split("<!--"); 
			 
			instance._forma_pago = new FormaPago(instance.dialog_container);
			instance._forma_pago._data.total_reserva=0;//parseInt($.trim(sp[1]));
		    instance._forma_pago._data.monto_minimo_a_pagar=1;//(instance._forma_pago._data.total_reserva*2000);
			instance._forma_pago._data.monto_a_pagar=(instance._forma_pago._data.total_reserva*2000);
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
			 
				
			$("#factura_mov_label").click(function(){
				 var options = { percent: 0 };
				 $("#mov_caja_factura").toggle("blind", options, 500 );
			});
			 
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	 
			});  
			 
			$("#bt_caja_process").click(function(){
				if ($("#caja_payment").valid()){
					
					if (instance._forma_pago.total_abonado_pagos>0) {
					
					
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
									 instance.viewDetailAccount(id,instance._view);
								}
								
							 },"json");
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
				motorizado: {
					required: true,
					minlength:2
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
				motorizado	 : {
					required: "Este campo es obligatorio",
					minlength: "Debes de digitar un minimo de 2 caracteres"	
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
		
 
		
	},
		
});