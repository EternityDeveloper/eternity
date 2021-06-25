/* ABONO A CAPITAL*/
var CCancelacionTotal = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="GAbonoCapital";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(contrato){
		var instance=this; 
		this._contrato=contrato;
		
		instance.post("./?mod_cobros/delegate&view_cancelacion_total",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data.html); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}) 
			
			var capital_pendiente=data.monto_capital_pendiente;
			var por_descuento= data.por_descuento; 
			
			setTimeout(function(){ $("#monto_a_abonar").focus() },1000);
 
			var procesar_abono=true;
 
			$("#por_descuento").change(function(){
				var porciento=$(this).val();
				if (porciento>0){
					por_descuento=porciento; 
					$("#monto_descuento").html(instance.number_format((por_descuento*capital_pendiente)/100));
					procesar_abono=true;
				}else{
					$("#por_descuento").val(0);
					procesar_abono=false;
				}
			});		
			
			$("#procesar_cancelacion_total").click(function(){
				if (!procesar_abono){
					alert('Error en proceso!');
					return false;
				}  
				instance.post("./?mod_cobros/delegate&CGCanelacionTotal",{
						"por_descuento":por_descuento,
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
						
		},"json");			
	} 
	
});