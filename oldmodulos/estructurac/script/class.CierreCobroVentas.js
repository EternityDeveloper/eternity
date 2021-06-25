/*CIERRES DE ANOS */
var CierreCobroVentas = new Class({
	dialog_container : null, 
	_id_gestion: null,
	initialize : function(dialog_container){
		this.main_class="CierreCobroVentas";
		this.dialog_container=dialog_container; 
	},	  
	 
	doInit : function(){ 
		var instance=this; 
		$(".fecha").datepicker({
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
		
		$("#bt_m_save").click(function(){ 
			instance.post("./?mod_estructurac/delegate&processCierres",$("#cierre_form").serializeArray(),function(data){
				alert(data.mensaje);	
				if (!data.valid){
				//s	window.location.reload();
				}
			},"json");
			 
		});	
		
		
	},      
	filtroAvanzado : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&labor_cobro",{
				"view_add_caja":'1',
				'contrato':''
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Labor de cobro",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#act_add").click(function(){
				if ($("#frm_actividad_").valid()){ 
					instance.post("./?mod_cobros/delegate&processLaborCobro",$("#frm_actividad_").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
						//s	window.location.reload();
						}
					},"json");
				}
			});
			
			$("#accion").change(function(){ 
				if ($("#accion option:selected").attr('gestion')=="S"){
					alert($("#accion option:selected").attr('gestion')); 
				}
			});

			
			$("#act_cancel").click(function(){
				instance.close(dialog);	
			}); 
 
			 
		});	
	}, 
	validateFormAccion : function(){
		$("#frm_cobro").validate({
			rules: {
				"idaccion": {
					required: true 
				},
				"accion": {
					required: true 
				} 
			},
			messages : {
				"idaccion": {
					required: "Este campo es obligatorio" 
				},
				"accion": {
					required: "Este campo es obligatorio"  
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});