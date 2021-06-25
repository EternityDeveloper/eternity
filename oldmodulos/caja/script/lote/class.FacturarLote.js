/*
	Modulo de factura por lote
*/
var FacturarLote = new Class({
	dialog_container : null,
	_token: null,
	_monto_a_cobrar: 1,
	initialize : function(dialog_container){
		this.main_class="FacturarLote";
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
	doView : function(){ 
		var instance=this;  
		var documento=new TDocSerieRVenta(instance.dialog_container); 
		this.post("./?mod_caja/delegate&lote_delegate&view_main",{ },
				function(data){ 
			instance._dialog=instance.doDialog("modal_pago_cuota",
												instance.dialog_container,data);
			setTimeout(function(){												
				$("#no_codigo_barra").focus();
			},500);
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			}); 			
			$("#title_template").html("Facturar lote");
 			instance.validateForm();  
			var monto_a_cobrar=1;
			$("#no_codigo_barra").keypress(function(e){
				var code = e.keyCode || e.which;
				if(code == 13) { 
					instance.post("./?mod_caja/delegate&lote_delegate&doAddRecibo",{"id":$(this).val(),"action":"add"},function(data){ 
						instance.drawDetalleRecibo(data);
						$("#no_codigo_barra").val('');	
						$("#no_codigo_barra").focus();	
					},"json");	
				}	
			});	 
			
			$(".add_recibo_manual").click(function(){
				instance.doViewReciboManual();
			}); 
			 
			$("#bt_caja_cancel").click(function(){
				instance.fire("closeDialog")
				instance.CloseDialog(instance._dialog);	 
			});   
			 
			$("#bt_caja_process").click(function(){
				if ($("#caja_payment").valid()){ 
					var err=false; 
					if(!err){ 
					}
					  
						 
				}	
			});
			
			instance.doFormaPago();
			 
		});	
	},
	doFormaPago : function(){
		var instance=this;
		instance._forma_pago = new FormaPago(instance.dialog_container);	
	//	instance._forma_pago.setNIT(comportamiento_obj.id_nit);
		
		//instance._forma_pago.setContrato(comportamiento_obj.contrato);
		  
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
		
		instance._forma_pago._data.total_reserva=0; 
		instance._forma_pago._data.monto_minimo_a_pagar=1;
		
		instance._forma_pago.setFormaPagoContentView('forma_pago_view');
		instance._forma_pago.loadModule();			
	},
	doViewReciboManual : function(){
		var instance=this;
		this.post("./?mod_caja/delegate&lote_delegate&view_create_recibo_manual",{ },
				function(data){ 
			instance._dialog=instance.doDialog("ModaReciboManual",
												instance.dialog_container,data);
												
			var contrato='';
			$('#txt_contrato').select2({
			  multiple: false,
			  minimumInputLength: 5,
			  query: function (query){ 
				  instance.post("./?mod_contratos/listar",{"getSelectContrato":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});		 
			$("#txt_contrato").on("change", 
				function(e) { 
					contrato=e.val; 
				  instance.post("./?mod_caja/delegate&lote_delegate&info_detalle_contrato",
				  			{"id":contrato},function(data){ 
						if (data.valid){
					 		$("#oficial_n").val(data.oficial);
							$("#motorizado_n").val(data.motorizado);
							$("#tipo_moneda_rm").html(data.tipo_moneda);
							$("#compromiso_rm").html('<span class="day_restantes">'+data.compromiso+'<span>');
							$("#plazo_rm").html(data.plazo); 
							$("#monto_a_abonar").focus();
						}
				   },"json");   
					
			}); 			
												
		});
	},
	drawDetalleRecibo : function(data){
		var instance=this;
		if (data.valid){ 	
			$("#detalle_item").html(data.html);
			this._monto_a_cobrar=data.MONTO_TOTAL;
			$("#monto_total_f").html(instance.number_format(this._monto_a_cobrar));
			
			$(".recibo_remove").click(function(){ 
				instance.post("./?mod_caja/delegate&lote_delegate&doAddRecibo",
						{"id":$(this).val(),"action":"remove"},
				function(data){ 
					instance.drawDetalleRecibo(data);
					$("#no_codigo_barra").val('');	
					$("#no_codigo_barra").focus();	
				},"json");
			});				
		}else{
			$("#mensaje").html(data.mensaje);
			$("#mensaje").show() 
			$("#mensaje").fadeOut({duration:1000}); 
		}
	},
	validateForm: function(){
		var instance=this;
		 	 
		
	},	
	doPrintRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&print_dialog",{id:id},function(data){ 
 			instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				window.location.reload();
			});
			
			$('#detalle_imprimir').load(function(){
				$('#detalle_imprimir')[0].contentWindow.print();
				setTimeout(function(){
					instance.CloseDialog("DetalleImprimir");
				},500);
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