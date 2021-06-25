 var CPagoMantenimiento = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="CPagoMantenimiento";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(contrato){
		var instance=this; 
		this._contrato=contrato;
		
		instance.post("./?mod_cobros/delegate&view_nota_credito",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})  
			
			$("#myModalLabel").html("Pago de mantenimiento");
			setTimeout(function(){ $("#monto_a_abonar").focus() },1000);
 
			var procesar_abono=false;
			var monto_abono=0;
			var plazo=0;
			var cambio_plazo=""
			$("#monto_a_abonar").change(function(){
				monto_abono=$(this).val(); 
				procesar_abono=true;
			});		
			
			$("#procesar_abono_saldo").click(function(){
				if (!procesar_abono){
					alert('No se puede procesar el requerimiento sin antes completar los datos!');
					$("#monto_a_abonar").focus()
					return false;
				}  
				instance.post("./?mod_cobros/delegate&CReqPagoMantenimiento",{
						"monto_abono":monto_abono,
						'contrato':instance._contrato,
						"comentario":$("#cp_comentarios").val()
					},function(data){
						alert(data.mensaje)	
						if (!data.error){
							/*if (confirm('Desea imprimir la solicitud')){
								$("#close_view").click();
								window.open("./?mod_cobros/delegate&solicitud_gestion_abono&id="+data.id_solicitud); 
							}else{*/
								window.location.reload();
						//	}
						} 
						
					},"json");		
			});
						
		});			
	} 
	
});