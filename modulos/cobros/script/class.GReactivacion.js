/* ABONO A CAPITAL*/
var GReactivacion = new Class({
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
		
		instance.post("./?mod_cobros/delegate&view_reactivacion",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data.html); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}) 
			setTimeout(function(){ $("#porcient_penalidad").focus() },1000);
 			var capital_pagado=data.capital_pagado; 
			var valor_cuota=data.monto_cuota;
			var procesar_abono=false;
			var penalidad=data.penalidad;
			var plazo=data.mes_faltante; 
			var cambio_plazo=""
			var monto_reactivacion=0;
			procesar_abono=true;
			
			$("#porcient_penalidad").change(function(){
				if ($(this).val()>0){
					penalidad = (capital_pagado*$(this).val())/100; 
					$("#monto_reactivacion").val(instance.number_format(penalidad)); 
					$("#distribucion_m").html(instance.number_format(penalidad/plazo));   
					$("#monto_cuota_").html(instance.number_format(parseFloat(valor_cuota) + parseFloat(penalidad/plazo))); 
					procesar_abono=true;
				}else{
					procesar_abono=false;	
				}
								
			});	
			
			$("#monto_reactivacion").change(function(){
				if ($(this).val()>0){
					monto_reactivacion=$("#monto_reactivacion").val();
					//alert((monto_reactivacion/capital_pagado)*100)
					penalidad =monto_reactivacion; 
					//(capital_pagado*$(this).val())/100;
					$("#porcient_penalidad").val(instance.number_format((monto_reactivacion/capital_pagado)*100));  
					$("#distribucion_m").html(instance.number_format(penalidad/plazo));   
					$("#monto_cuota_").html(instance.number_format(parseFloat(valor_cuota) + parseFloat(penalidad/plazo))); 
					procesar_abono=true;
				}else{
					procesar_abono=false;	
				}
								
			});					
			
			$("#procesar_abono_saldo").click(function(){
				if (!procesar_abono){
					alert('No se puede procesar el abono sin antes completar todos los datos correctamente!');
					return false;
				}  
				instance.post("./?mod_cobros/delegate&CGReactivacion",{
						"penalidad":penalidad, 
						'contrato':instance._contrato,
						"comentario":$("#cp_comentarios").val()
					},function(data){
						alert(data.mensaje)	
						if (!data.error){
							window.location.reload();
						} 
						
					},"json");		
			});
						
		},"json");			
	} 
	
});