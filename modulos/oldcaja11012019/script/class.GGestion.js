/* GESTION */
var GGestion = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="GAbonoCapital";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(nit,contrato){
		var instance=this; 
		this.nit=nit;		
		instance.post("./?mod_caja/delegate&view_generar_gestion_pago",{ 
				'nit':nit ,
				'contrato':contrato
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}) 
			
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
			setTimeout(function(){ $("#monto_a_abonar").focus() },1000);
 
			var procesar_abono=false;
			var monto_abono=0;
			var plazo=0;
			var cambio_plazo="";
			
			$("#monto_a_abonar").change(function(){
				monto_abono=$(this).val();
			});		
			
			
			$("#procesar_saldos").click(function(){
				if (!procesar_abono){
				//	alert('No se puede procesar el abono sin antes completar todos los datos correctamente!');
				//	return false;
				}  
 				instance.post("./?mod_caja/delegate&DoCrearGestionGenerica",{
						"monto_abono":monto_abono,
						"gestion":$("#ID_GESTION").val(),
						"tipo_movimiento":$("#tipo_movimiento").val(),
						"no_documento":$("#no_documento").val(),  
						"motorizado_n":$("#motorizado_n").val(),  			
						"fecha_requerimiento_especial_xx":$("#fecha_requerimiento_especial_xx").val(),
						"monto_descuento":$("#monto_descuento").val(),
						'contrato':contrato,
						'id_nit':nit,
						"comentario":$("#cp_comentarios").val() 
					},function(data){
						alert(data.mensaje)	
						if (data.valid){ 
							$("#close_view").click(); 
						} 
						
					},"json");		
			});
						
		});			
	} 
	
});