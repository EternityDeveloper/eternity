/* PROCESAR FACTURA */
var ProcFactura = new Class({
	dialog_container : null,  
	_rand : null,
	_recibo : '',
	initialize : function(dialog_container){
		this.main_class="ProcFactura";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(nit){
		var instance=this; 
		this.nit=nit;		
		instance.post("./?mod_caja/delegate&view_process_recibo_vr",{ 
				'id_nit':nit 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			}) 			
			$(".sp_comentario").hide();
			instance.doLoadRecibos(nit);
			$("#bt_caja_process").prop("disabled",true);			
			
			$("#bt_caja_process").click(function(){
				if (instance._recibo!=''){
					data={
						"comentario":$("#cp_comentarios").val(),
						"recibo":instance._recibo,
						"facturar_recibo":1
					};  
					instance.post("./?mod_caja/delegate",
						data,
						function(data){   
								
					},"json");
				}
			});
		});			
	},	
	doLoadRecibos: function(id_nit){
		var instance=this;
		/*CARGO EL LISTADO DE SOLICITUDES PARA PODER REALIZAR EL ABONO A CAPITAL*/
		instance.post(
			"./?mod_caja/delegate",
			{
				view_listados_recibos:1,
				id_nit   : id_nit
			},
			function(data){  					
				if (data.total_recibo>0){
					$("#listado_recibo").html(data.html) 
					$(".no_recibo_fact").click(function(){
						$("#"+$(this).attr("ref")).toggle();	
					});
					
					$(".listado_rc_").click(function(){ 
 						if ($(this).prop("checked")){
							$("#bt_caja_process").prop("disabled",false);
							$(".sp_comentario").show();
							data.doRecibosAdd=1;	
							instance._recibo=$(this).val();
						}else{
							$("#bt_caja_process").prop("disabled",true);							
							data.doRecibosAdd=0;	
							$(".sp_comentario").hide();
							instance._recibo=$(this).val();							
						}
												
					}); 
					
					$(".recibo_remove").click(function(){ 
						instance.doViewQuestionRemove($(this).val()); 					
						
					});
					 
				}else{ 
					$("#detalle_general").hide();	
				}							
		},"json");			
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
				data.token=instance.getRand();
				data.id_recibo=recibo;	 
				data.descripcion=$("#remove_comentario_rb").text();	 
				instance.post("./?mod_caja/delegate",data,
					function(data){   
						alert(data.mensaje);
						if (!data.error){
							instance.doLoadRecibos(); 
							instance.CloseDialog("view_modal_recibo");	  
						}
					},
				"json");	 
			 });
			 
		})
	},
	setToken : function(token){
		this._token=token;
	},
	getToken : function(){
		return this._token;
	},
	
});