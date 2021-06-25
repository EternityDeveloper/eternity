/*
	Modulo de factura
*/
var Facturar = new Class({
	dialog_container : null,
	_token: null,
	_id_nit : null, 
	_no_reserva : null,
	initialize : function(dialog_container){
		this.main_class="Facturar";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
	},
	setToken : function(token){
		this._token=token;
	},
	getToken : function(){
		return this._token;
	},
	/*
		Este metodo es el principal 
		el cual se programa el comportamiento del a ventana emergente
	*/
	doView : function(id_nit,contrato,reserva,tipo_movimiento){ 
		var instance=this;  
		this._contrato=contrato; 
		var recibo_no=null;
		var asesor=null;
		var reporte_venta=null;
		var comp = new PagoComponent(this.dialog_container);
		comp.addListener("closeDialog",function(){
			instance.fire("onClose");
		})
		var accion={
			title	: "Cobrar",
			reserva	: reserva,
			contrato: contrato,
			"id_nit" 	: id_nit,
			enablesubmit : false,
			/*Al cargar la pagina*/
			onload: function(){
				var inst=this;  
				comp.doDisableSubmit();
				$("#detalle_general").show();
				instance._contrato=contrato;
				instance._reserva=reserva;	
				instance._id_nit=id_nit;					
 				instance.doLoadRecibos({
					valor_a_pagar : function(monto){
						comp._forma_pago.setMontoMinimo(monto);	
						$("#total_a_pagar").html('<span class="badge alert-danger">'+
													instance.number_format(monto)+'</span>');	
					}	
				}); 
				$("#fecha_atraso").show();
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
				
			//	$("#detalle_recibo").show(); 
					
			//	if (tipo_movimiento!=""){

					$("#detalle_recibo").show(); 
					
				/*	$("#recibo_venta").change(function(){
						 instance.post("./?mod_estructurac/list_view2",
						 		{"ListAsesores":'1',"sSearch":query.term},function(data){ 
							 
						  },"json"); 	
					});*/

					$('#txt_asesor').select2({
					  multiple: false,
					  minimumInputLength: 4,
					  query: function (query){ 
						  $.post("./?mod_estructurac/list_view2",{"ListAsesores":'1',"sSearch":query.term},function(data){ 
							 query.callback(data);
						   },"json");   
					  }
					});		 
					$("#txt_asesor").on("change", 
						function(e) { 
							asesor=e.val; 
					});  			
			//	}
				
				comp.addListener("newFormaPago",function(info){
					$("#total_a_pagar").html('<span class="badge alert-danger">'+
													instance.number_format(info.detalle.monto_a_pagar)+'</span>');	
					
					comp.doEnableSubmit();
					
					if (info.proceder){
						//comp.doEnableSubmit();	
					}else{
					//	comp.doDisableSubmit();
					} 
				});	
			},
			/*cuando el formulario esta lleno*/
			onSubmit : function(){ 
				data={};
				data.forma_pago_token=comp._forma_pago.getToken();
 				data.observacion=$("#observacion").val();  		
				data.dofacturar=1;	
 				data.isNCF=comp._forma_pago._containNCF; // SI DESEA COMPROBANTE FISCAL
				data.isCF=comp._forma_pago._containCF; // SI NECESITA COMPROBANTE FINAL
				data.rnc=comp._forma_pago._rnc_ced_factura;  
				data.fecha_requerimiento_especial_xx=$("#fecha_requerimiento_especial_xx").val()
				data.contrato=contrato; 
				data.reserva=reserva; 
				data.id_nit=id_nit; 
				data.tipo_movimiento=tipo_movimiento;   
				data.asesor=asesor;
				data.no_recibo_venta=$("#recibo_venta").val();
				data.reporte_venta=$("#reporte_venta").val();
				comp.doDisableSubmit(); 
				 
				instance.post("./?mod_caja/delegate",data,
					function(data){ 
						alert(data.mensaje); 
						comp.doEnableSubmit();
						if (!data.error){
							if (confirm("Desea imprimir el recibo?")){
								instance.doPrintRecibo(data.recibo);
								//window.open("./?mod_caja/delegate&recibo_factura&id="+data.recibo);
							}
							instance.fire("onClose");
							comp.Cerrar();	 
						//	window.location.reload();							
						}		
					},"json");
					
					
			} 	
		}   
		comp.doView(accion);  
	},
	doPrintRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&print_dialog",{id:id},function(data){ 
 			instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//window.location.reload();
			});
			
			$('#detalle_imprimir').load(function(){
				$('#detalle_imprimir')[0].contentWindow.print();
				setTimeout(function(){
					instance.CloseDialog("DetalleImprimir");
				},5000);
			});	
			
		});
	},
	doViewQuestionRemove : function(recibo){
		var instance=this; 
		instance.post("./?mod_caja/delegate&doViewQuestionRemove",{ 
			'recibo':recibo 
		},function(data){  
			 instance.doDialog("view_modal_recibo",instance.dialog_container,data); 
			 $("#doAnularRecb").click(function(){ 
				data={};  	
				data.doRecibosRemove=1;
				data.token=instance.getToken();
				data.id_recibo=recibo;	 
				data.descripcion=$("#remove_comentario_rb").text();	 
				instance.post("./?mod_caja/delegate",data,
					function(data){   
						alert(data.mensaje);
						if (!data.error){
							instance.doLoadRecibos({
								valor_a_pagar : function(monto){
									 
								}	
							}); 
							instance.CloseDialog("view_modal_recibo");	  
						}
					},
				"json");	 
			 });
			 
		})
	},	
	doLoadRecibos: function(obj){
		var instance=this;
		/*CARGO EL LISTADO DE SOLICITUDES PARA PODER REALIZAR EL ABONO A CAPITAL*/
		instance.post(
			"./?mod_caja/delegate",
			{
				view_listados_recibos:1,
				contrato : instance._contrato,
				reserva  : instance._reserva,
				id_nit   : instance._id_nit
			},
			function(data){  
				if (data.total_recibo>0){
					$("#detalle_general").html(data.html) 
					
					$(".listado_rc_").click(function(){ 
						data={};  
						if ($(this).prop("checked")){
							data.doRecibosAdd=1;	
						}else{
							data.doRecibosAdd=0;	
						} 
						data.token=instance.getToken();
						data.id_recibo=$(this).val();	 
						instance.post("./?mod_caja/delegate",data,
							function(data){   
								$("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
																instance.number_format(data.monto_a_pagar)+'</span>');
								instance.monto_a_pagar=data.monto_a_pagar;
								obj.valor_a_pagar(data.monto_a_pagar); 
				
							},"json");							
					}); 
					
					$(".recibo_remove").click(function(){ 
						instance.doViewQuestionRemove($(this).val()); 					
					});

					/*AGREGAR TODAS LA CUOTAS*/
					$(".select_all_cu").click(function(){ 
						if ($(this).prop("checked")){ 
							$(".listado_rc_").each(function(index, element) {  
								$(this).prop("disabled",false); 
								$(this).prop("checked", true); 
								data={};  
								if ($(this).prop("checked")){
									data.doRecibosAdd=1;	
								}else{
									data.doRecibosAdd=0;	
								} 
								data.token=instance.getToken();
								data.id_recibo=$(this).val();	 
								instance.post("./?mod_caja/delegate",data,
									function(data){   
										$("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
																		instance.number_format(data.monto_a_pagar)+'</span>');
										instance.monto_a_pagar=data.monto_a_pagar;
										obj.valor_a_pagar(data.monto_a_pagar); 
						
									},"json");	
							});	
						}else{ 
							$(".listado_rc_").each(function(index, element) {  
								$(this).prop("disabled",true); 
								$(this).prop("checked", false); 
								data={};  
								if ($(this).prop("checked")){
									data.doRecibosAdd=1;	
								}else{
									data.doRecibosAdd=0;	
								} 
								data.token=instance.getToken();
								data.id_recibo=$(this).val();	 
								instance.post("./?mod_caja/delegate",data,
									function(data){   
										$("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
																		instance.number_format(data.monto_a_pagar)+'</span>');
										instance.monto_a_pagar=data.monto_a_pagar;
										obj.valor_a_pagar(data.monto_a_pagar); 
						
									},"json");	
							});		 
						}
					}); 
					 
				}else{ 
					$("#detalle_general").hide();	
				}							
		},"json");			
	},
	doLoadAbonos : function(id_nit){
		var instance=this;  	
		this.post("./?mod_caja/delegate",{
									"view_person_abono_resumen":1,
									"id_nit":id_nit,
									"token":instance._token
								},function(data){ 
		 
			$("#detalle_general").show();
			$("#detalle_general").html(data); 
			
			var tr_abono=$(".abono_persona"); 
			tr_abono.css('cursor', 'pointer' );
			tr_abono.click(function(){
				var nTds=$(this).children();
				//$(nTds).find("input").prop("checked",true);  
				if ($(nTds).find("input").prop("checked")){
					$(nTds).find("input").prop("checked",false);   
					instance.doPutAbonoToInicial($(nTds).find("input").val(),"remove");
				}else{
					$(nTds).find("input").prop("checked",true); 
					instance.doPutAbonoToInicial($(nTds).find("input").val(),"add");	
				} 	
			}); 
			
			tr_abono.hover(function(){ 
				$(this).addClass('hover_tr');  
			},function(){ 
				$(this).removeClass('hover_tr'); 
			});
			 
		});		
	},
	/*Agrega un abono*/
	doPutAbonoToInicial : function(items,cmd){
		var instance=this; 
		instance.post("./?mod_caja/delegate&processSelectAbono",{ 
			'items':items,
			"cmd": cmd,
			"token":instance._token,
			'id_nit':this._id_nit
		},function(data){  
			 
		})
	},
	
	/*VISTA QUE MUESTRA EL LISTADO QUE HA ABONADO UN CLIENTE*/
	viewAbonoPerson : function(id,financiamiento){
		var instance=this; 
		this._id_nit=id; 	
		//alert(financiamiento.moneda);	
		this.post("./?mod_caja/delegate",
			{
				"view_person_abono":1,
				"id_nit":id,
				"token":instance._token,
				'moneda':financiamiento.moneda
		},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Iniciales",data,1000);
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 0]);
			
			var obj={
				monto:0	
			}
			
			$("#bt_abni_cerrar").click(function(){
				instance.close(dialog);	  
			});
			
			var tr_abono=$(".abono_persona"); 
		//	tr_abono.css('cursor', 'pointer' );
	/*		tr_abono.click(function(){
				var nTds=$(this).children();
				//$(nTds).find("input").prop("checked",true);  
				if ($(nTds).find("input").prop("checked")){
					$(nTds).find("input").prop("checked",false);   
					instance.doPutAbono($(nTds).find("input").val(),"remove",obj);
				}else{
					$(nTds).find("input").prop("checked",true); 
					instance.doPutAbono($(nTds).find("input").val(),"add",obj);	
				} 	
			}); 
			*/
			
			var agregar_pago=$(".agregar_pago_bt"); 
			agregar_pago.click(function(){
					
			});
			
			var tr_abono=$(".abnp_check"); 
			tr_abono.css('cursor', 'pointer' );
			tr_abono.click(function(){ ;
 				check=$(this);
				if (check.prop("checked")){   
					instance.doPutAbono(check.val(),"add",obj);
				}else{
 					instance.doPutAbono(check.val(),"remove",obj);	
				} 	
			}); 			
			tr_abono.hover(function(){ 
				$(this).addClass('hover_tr');  
			},function(){ 
				$(this).removeClass('hover_tr'); 
			});
			
			$(".bt_agrega_fp").click(function(){
				instance.doChangeTasa($(".tasa_cambio_css").attr("id"),
									  $(this).parent().find("input").val(),
									  id,
									  financiamiento,
									  dialog); 
			});
			 
						 
		});
	},
	/*Agrega un abono*/
	doPutAbono : function(items,cmd,obj){
		var instance=this; 
		instance.post("./?mod_caja/delegate&processPutAbono",{ 
			'items':items,
			"cmd": cmd,
			"token":instance._token,
			'id_nit':this._id_nit
		},function(data){  
			$("#abi_monto_total").html('<strong>'+instance.number_format(data.monto)+'</strong>');
			$("#abi_monto_rd").html('<strong>'+instance.number_format(data.monto_rd)+'</strong>'); 
			obj.monto=data.monto; 
			instance.fire("onSelectMontoCaja",data.html);
		},"json")
	},
	/*Agrega un abono*/
	doChangeTasa : function(items,tasa,obj,id,financiamiento,dialog){
		var instance=this; 
		instance.post("./?mod_caja/delegate&processChangeTasa",{ 
			'items':items, 
			"token":instance._token,
			'id_nit':this._id_nit,
			"item":items,
			'tasa':tasa
		},function(data){ 
			if (data.valid){ 
				$("#abi_monto_total").html('<strong>'+instance.number_format(data.monto)+'</strong>');
				$("#abi_monto_rd").html('<strong>'+instance.number_format(data.monto_rd)+'</strong>'); 
 				instance.fire("onSelectMontoCaja",data.html);
				//instance.close(dialog);	 
				//instance.viewAbonoPerson(id,financiamiento);
			}
		},"json")
	}	 	 
	
	
});