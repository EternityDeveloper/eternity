var Capillas = new Class({
	dialog_container : null,
	config: null,
	enc_id_nit :0,
	_view : null,
	_token : null,
	initialize : function(dialog_container){
		this.main_class="Capillas";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
		var instance=this; 
	}, 
	doRegistrar : function(){  
		var instance=this;    
		var rand_id=instance.getRand();  
		
	
		
		this.post("./?mod_capillas/delegate&agregar_obituario",{},function(data){ 
			instance._dialog=instance.doDialog("modal_agregar_obituario",
												instance.dialog_container,data);
			  
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			 
			$("#fecha_velacion_obi").datepicker({
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
			$("#fecha_salida_obi").datepicker({
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
			  
			var hora_exposicion_obi=""; 
			$('#hora_exposicion_obi').timepicker({'timeFormat':'H:i:s'});
			$('#hora_exposicion_obi').on('changeTime', function() {
				hora_exposicion_obi=$(this).val(); 
			});	

			var hora_salida_obi=""; 
			$('#hora_salida_obi').timepicker({'timeFormat':'H:i:s'});
			$('#hora_salida_obi').on('changeTime', function() {
				hora_salida_obi=$(this).val(); 
			});	
			
			var capillas_obi="";
			$("#capillas_obi").change(function(){
				capillas_obi=$(this).val();
			});
					 
			$("#bt_chk_cancel").click(function(){
				instance.CloseDialog("modal_devolucion_cuota");
			});
			
			$("#bt_registro_cheque").click(function(){
				if (hora_exposicion_obi==""){
					alert('Debe de ingresar una hora para la exposicion');
					return false;	
				} 
				if (hora_salida_obi==""){
					alert('Debe de ingresar una hora de salida');
					return false;	
				} 	
				if ($("#fecha_velacion_obi").val()==""){
					alert('Debe de ingresar una fecha velacion');
					return false;		
				}	
				if ($("#fecha_salida_obi").val()==""){
					alert('Debe de ingresar una fecha valida de salida');
					return false;		
				}	
										
				var data={
					"token"					:instance._token,
					"comentario"			:$("#observacion").val(),
					"fecha_velacion_obi"	:$("#fecha_velacion_obi").val(),
					"fecha_salida_obi"		:$("#fecha_salida_obi").val(),
					"hora_exposicion_obi"	:hora_exposicion_obi,
					"hora_salida_obi" 		:hora_salida_obi,
					"nombre_completo_obi"	:$("#nombre_completo_obi").val(),
					"capillas_obi"			:capillas_obi,
					"nombre_completo_obi"	:$("#nombre_completo_obi").val(),
					"detalle_inhumacion"	:$("#detalle_inhumacion").val(),
					"cementerio_obi"		:$("#cementerio_obi").val(),
					"visita_a_la_residencia_obi"	:$("#visita_a_la_residencia_obi").val(),
					"lectura_palabra_obi"	:$("#lectura_palabra_obi").val(),
					"misa_en_capillas"		:$("#misa_en_capillas").val(),
					"doRegistrarObituario"	:1
				}; 
 				instance.post("./?mod_capillas/delegate",data,
					function(data){ 
						alert(data.mensaje);	
						if (!data.error){ 
							window.location.reload();
 						} 
					},"json");
			});
			  
		});  	
	},  
	doEditar : function(id){  
		var instance=this;    
		var rand_id=instance.getRand();     
		this.post("./?mod_capillas/delegate&editar_obituario",{id:id},function(data){ 
			instance._dialog=instance.doDialog("modal_editar_obituario",
												instance.dialog_container,data);
			  
			instance.addListener("onCloseWindow",function(){
				instance.fire("closeDialog");
			});
			 
			$("#fecha_velacion_obi").datepicker({
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
			$("#fecha_salida_obi").datepicker({
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
			
			var hora_exposicion_obi=$("#hora_exposicion_obi").val(); 
			$('#hora_exposicion_obi').timepicker({'timeFormat':'H:i:s'});
			$('#hora_exposicion_obi').on('changeTime', function() {
				hora_exposicion_obi=$(this).val(); 
			});	

			var hora_salida_obi=$("#hora_salida_obi").val(); 
			$('#hora_salida_obi').timepicker({'timeFormat':'H:i:s'});
			$('#hora_salida_obi').on('changeTime', function() {
				hora_salida_obi=$(this).val(); 
			});	
			
			var capillas_obi=$("#capillas_obi").val(); 
			$("#capillas_obi").change(function(){
				capillas_obi=$(this).val();
			});
					 
			$("#bt_chk_cancel").click(function(){
				instance.CloseDialog("modal_devolucion_cuota");
			});
			
			$("#bt_registro_cheque").click(function(){
				if (hora_exposicion_obi==""){
					alert('Debe de ingresar una hora para la exposicion');
					return false;	
				} 
				if (hora_salida_obi==""){
					alert('Debe de ingresar una hora de salida');
					return false;	
				} 	
				if ($("#fecha_velacion_obi").val()==""){
					alert('Debe de ingresar una fecha velacion');
					return false;		
				}	
				if ($("#fecha_salida_obi").val()==""){
					alert('Debe de ingresar una fecha valida de salida');
					return false;		
				}	
										
				var data={
					"token"					:instance._token,
					"id"					:id,
					"estatus"				:$("#estatus").val(),
 					"fecha_velacion_obi"	:$("#fecha_velacion_obi").val(),
					"fecha_salida_obi"		:$("#fecha_salida_obi").val(),
					"hora_exposicion_obi"	:hora_exposicion_obi,
					"hora_salida_obi" 		:hora_salida_obi,
					"nombre_completo_obi"	:$("#nombre_completo_obi").val(),
					"capillas_obi"			:capillas_obi,
					"nombre_completo_obi"	:$("#nombre_completo_obi").val(),
					"detalle_inhumacion"	:$("#detalle_inhumacion").val(),
					"cementerio_obi"		:$("#cementerio_obi").val(),
					"visita_a_la_residencia_obi"	:$("#visita_a_la_residencia_obi").val(),
					"lectura_palabra_obi"	:$("#lectura_palabra_obi").val(),
					"misa_en_capillas"		:$("#misa_en_capillas").val(),
					"doActualizarObituario"	:1
				}; 
 				instance.post("./?mod_capillas/delegate",data,
					function(data){ 
						alert(data.mensaje);	
						if (!data.error){ 
							window.location.reload();
 						} 
					},"json");
			});
			  
		}); 
		
			
	}	
});