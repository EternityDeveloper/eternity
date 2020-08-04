/* NOTAS DE CREDITOS Y DEBITO */
var NotasCD = new Class({
	dialog_container : null,  
	_rand : null,
	_monto: 0,
	_contrato : '',
	initialize : function(dialog_container){
		this.main_class="NotasCD";
		this.dialog_container=dialog_container; 
	}, 
	setContrato : function(contrato){
		this._contrato=contrato;
	},		
	doView: function(documento){
		var instance=this; 
		this.documento=documento;	
		this._rand=this.getRand(); 
		instance.post("./?mod_caja/delegate&view_notascd",{ 
				'question':1
		},function(data){   
			instance.doDialog("moda_ncd",instance.dialog_container,data.html); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}); 
		 
			$("#_do_capitalizar").click(function(){
			  instance.doViewNotaCreditoCapitaliza(documento);
			});	
			$("#_do_nota_comprobante").click(function(){
			  instance.doViewNotaCreditoComprobante(documento);
			});					 		
		},"json");		
			
	},
	
	doViewNotaCreditoComprobante : function(documento){ 
		var instance=this;
		instance.post("./?mod_caja/delegate&view_notascd",{ 
				'generar_comprobante':1,
				'documento':documento 
		},function(data){    
			instance.doDialog("moda_ncd",instance.dialog_container,data.html); 
 	   
			$("#aplicar_nota_credito").click(function(){ 
				instance.post("./?mod_caja/delegate&ProcesarNCcomprobante",{
						"rand":instance._rand, 
						'documento':documento,
						"comentario":$("#notas_cometario").val()
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload();
						} 
						
					},"json");		
			});
						
		},"json");		
	},
	
	doViewNotaCreditoCapitaliza : function(documento){
		var instance=this;
		instance.post("./?mod_caja/delegate&view_notascd",{ 
				'documento':documento 
		},function(data){   
			instance.doDialog("moda_ncd",instance.dialog_container,data.html); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}); 
			var monto=data.monto;
			instance._monto=monto;
			$("#moto_nota_cd").keypress(function(event) {
			  if ( event.which == 13 ) {
				  $("#agregar_nota_monto").click();
				 event.preventDefault(); 
			  } 
			});			
			
			setTimeout(function(){
				$("#moto_nota_cd").focus();	
			},1000);
			
			$("#agregar_nota_monto").click(function(){ 
				var monto_valor=$("#moto_nota_cd").val(); 
				if (monto_valor>instance._monto){
					alert('El monto no puede ser mayor que el monto del documento!');
					return false;	
				}
				
				instance.post("./?mod_caja/delegate&cCaddMonto",{
						"valor_monto":monto_valor,
						"rand":instance._rand,
						'documento':documento 
					},function(data){ 
						$("#detalle_trans_nc").html(data.html);
						
						$(".trans_remove_item").click(function(){
							alert($(this).attr("id"));	
						});
						
						$("#moto_nota_cd").val('');	
						if (!data.valid){
							alert(data.mensaje);	
						} 
					},"json");		
			});			
			
			$("#procesar_nota_credito").click(function(){ 
				instance.post("./?mod_caja/delegate&ProcesarNC",{
						"rand":instance._rand, 
						'documento':documento,
						"comentario":$("#notas_cometario").val()
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload();
						} 
						
					},"json");		
			});
						
		},"json");		
	},
	add_field: function(){
		var instance=this; 
	},
	/*Carga el modulo de notas de credito*/
	loadModule : function(){
		var instance=this;  
		this.setToken(this.getRand());
		instance.post("./?mod_caja/delegate&view_nota_credito_pago",{"payment_view":"payment_list",
														"token":this.token,
														'contrato':this._contrato},function(data){
																		
			$("#detalle_nota_credito").show();												
			$("#detalle_nota_credito").html(data);	
			/*EVENTOS DE NOTAS DE CREDITO*/
			instance.capturarNotasEvent();
					
										
		});
	},
	getToken : function(){
		return this.token;
	},
	setToken  : function(token){
		this.token=token;
	},	
	capturarNotasEvent: function(){
		var instance=this;
		$(".listado_nc").click(function(){ 
			data={};  
			if ($(this).prop("checked")){
				data.doNCRecibosAdd=1;	
			}else{
				data.doNCRecibosAdd=0;	
			} 
			data.token=instance.getToken();
			data.id_recibo=$(this).val();	 
			instance.putToServerItem(data);						
		});  

		/*AGREGAR TODAS LA CUOTAS*/
		$(".select_all_nc").click(function(){ 
			if ($(this).prop("checked")){ 
				$(".listado_nc").each(function(index, element) {  
					$(this).prop("disabled",false); 
					$(this).prop("checked", true); 
					data={};  
					if ($(this).prop("checked")){
						data.doNCRecibosAdd=1;	
					}else{
						data.doNCRecibosAdd=0;	
					} 
					data.token=instance.getToken();
					data.id_recibo=$(this).val();	 
					instance.putToServerItem(data);	
				});	
			}else{ 
				$(".listado_nc").each(function(index, element) {  
					$(this).prop("disabled",true); 
					$(this).prop("checked", false); 
					data={};  
					if ($(this).prop("checked")){
						data.doNCRecibosAdd=1;	
					}else{
						data.doNCRecibosAdd=0;	
					} 
					data.token=instance.getToken();
					data.id_recibo=$(this).val();	 
					instance.putToServerItem(data);
				});		 
			}
		}); 		
	},
	putToServerItem : function(data){
		var instance=this;
		instance.post("./?mod_caja/delegate",data,
			function(data){   
 				$("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
												instance.number_format(data.monto_a_pagar)+'</span>');
				instance.monto_a_pagar=data.monto_a_pagar;
				//obj.valor_a_pagar(data.monto_a_pagar); 
				instance.fire("_refresh");
			},"json");		 
	}
	
});