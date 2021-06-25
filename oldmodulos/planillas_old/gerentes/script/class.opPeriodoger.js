var opPeriodo = new Class({
	
	dialog_container : null, 
	
	initialize : function(dialog_container, choice){
		this.main_class       = "opPeriodo";
		this.dialog_container = dialog_container;
		this.opc              = choice; 
	},	  
  
   
	/* Metodo que crea la ventana modal <Es llamada desde SelectTipomov.php> */
	doViewQuestion : function(){
		var instance    = this;
		var opc         = this.opc;
		
		
		/* <1. Url donde estan los elementos que quiero desplegar en la ventana modal> */
		/* <2. En caso yo quiera enviarle un parametro a esa pagina                    */
		/* <3. Es el retorno del contenido de la pagina que se envia en el URL         */
		/*instance.post("?mod_planillas/gerentes/view/pl_periodo_cierre&choice="+this.opc+"&view",null, */
		instance.post("./?mod_planillas/pl_centralfile_ger&choice="+this.opc+"&view",null,
		    function(data){ 
		       
			   /* dialog_container es el div que esta en selecttipomov.php */
			  var dialog = instance.createDialog(instance.dialog_container, "Seleccione Datos de Periodo", data, 430);
			
			/* Devuelve el Id de ese cuadro y se almacena para manejarlo en esa funcion */
			instance._dialog = dialog;
			
			/* ubicacion en la pantalla fisicamente */
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			/* como se crea la ventana se encuentran todos los elementos de la pagina */ 
			$("#procesar").click(function(){
				if ( $("#frm_datos_cierre").valid() ){
	                window.location.href="./?mod_planillas/gerentes/comision_gerentes&&anio=" + $("#anio").val()+"&periodo=" + $("#periodo").val() + 
				                        "&type="+$('input[name=type]:radio:checked').val()
										
				   /*$.post("./?mod_planillas/gerentes/comision_gerentes", $("#frm_datos_cierre").serializeArray(), function(data){*/
	               /*     alert(data.anio);*/
						  
				}
				 /* redirect */
				 /*window.location.href="./?mod_caja/delegate&operacion&buscador&setid=true&id=" + tipo_movimiento;*/
			});
			
			$("#cancelar").click(function(){
				instance.close(dialog);	
			});
			
			/* list view */
			$("#periodo").change(function(){
				if ($(this).val()!=""){
					mes = $(this).val();
					$("#procesar").attr("disabled",false);
				}else{
					$("#procesar").attr("disabled",true);
				}
			});
			 
			/* se Dispara otro metodo */ 		
			instance.validateForm();

		});	
	} ,

	validateForm : function(){
		$("#frm_datos_cierre").validate({
			/* Estas son las reglas que hay que respetar */
			rules: {
				"periodo": {
					required: true 
				},
				"type": {
					required: true 
				},
				"anio": {
					required: true 
				}  
			},
			messages : {
				"periodo": {
					required: "Este campo es obligatorio" 
				},
				"type": {
					required: "Este campo es obligatorio"  
				},
				"anio": {
					required: "Este campo es obligatorio"  
				} 		 		
				
			}
		
		});	
		/* si en caso no existe no puse nada en mensajes es default */
		$.validator.messages.required = "Campo obligatorio.";
	}
});