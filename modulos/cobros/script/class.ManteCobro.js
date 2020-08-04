/*MANTENIMIENTO COBRO*/
var ManteCobro = new Class({
	dialog_container : null, 
	_id_gestion: null,
	initialize : function(dialog_container){
		this.main_class="ManteCobro";
		this.dialog_container=dialog_container; 
	},	  
	
	/*AQUI EMPIEZA LA GESTION*/ 
	tableViewGestion : function(tb,button,class_items){ 
		var instance=this;
		$("#"+tb).dataTable({
			"bFilter": true,
			"bInfo": false,
			"bPaginate": true,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});
			
		$("#"+button).click(function(){
			instance.doCreateGestion();
		});	
		
		$("."+class_items).click(function(){
			instance.doViewEditGestion($(this).attr("id"));	
		});
		
	}, 
	  
	doViewEditGestion : function(id){

		var instance=this;
		this._id_gestion=id;
		instance.post("./?mod_cobros/delegate&gestion_edit",{ 
				'id':id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar Gestion",data,1000);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_cobro_add").click(function(){
				if ($("#frm_cobro").valid()){ 
					instance.post("./?mod_cobros/delegate&processEditGestion", $("#frm_cobro").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
				}
			});
			
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	
			});
			
			/*Valido que el id de la caja no este asignado*/
			$("#id_caja").change(function(){
				if ($("#id_caja").val()!=""){ 
					instance.post("./?mod_caja/delegate&caja",{validateCajaExist:1,id_caja:$("#id_caja").val()},function(data){
						if (data.error){ 
							$("#id_caja").val('');
							alert(data.mensaje)	
						}

					},"json");
				}else{
					$("#id_caja").val('');	
				}
			});
			
			$("#escalamiento1").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#escalamiento1").val( ui.item.nombre );
					$("#escalamiento1_code").val( ui.item.value ); 
					return false;
				}
			});	
					
			$("#escalamiento2").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) { 
					$("#escalamiento2").val(ui.item.nombre);			
					$("#escalamiento2_code").val( ui.item.value ); 
					return false;
				}
			});	
			instance.validateFormGestion(); 
			
			instance.addListener("onCrateActivity",function(){
				instance.close(dialog);	
				instance.removeListener("onCrateActivity");
				instance.doViewEditGestion(id);	 
				
			})
			
			instance.tableViewActividad("list_actividad","abutton","edit_list_actividad");
			 
		});	
	}, 
	
	doCreateGestion : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&gestion_add",{
				"view_add_caja":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Agregar Gestion",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_cobro_add").click(function(){
				if ($("#frm_cobro").valid()){ 
					instance.post("./?mod_cobros/delegate&processGestion", $("#frm_cobro").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
				}
			});
			
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	
			});
			
			/*Valido que el id de la caja no este asignado*/
			$("#id_caja").change(function(){
				if ($("#id_caja").val()!=""){ 
					instance.post("./?mod_caja/delegate&caja",{validateCajaExist:1,id_caja:$("#id_caja").val()},function(data){
						if (data.error){ 
							$("#id_caja").val('');
							alert(data.mensaje)	
						}

					},"json");
				}else{
					$("#id_caja").val('');	
				}
			});
			
			$("#escalamiento1").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#escalamiento1").val( ui.item.nombre );
					$("#escalamiento1_code").val( ui.item.value ); 
					return false;
				}
			});	
					
			$("#escalamiento2").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) { 
					$("#escalamiento2").val(ui.item.nombre);			
					$("#escalamiento2_code").val( ui.item.value ); 
					return false;
				}
			});	
			instance.validateFormGestion(); 
		});	
	} ,
	
	tableViewActividad : function(tb,button,class_items){ 
		var instance=this;
		$("#"+tb).dataTable({
			"bFilter": true,
			"bInfo": false,
			"bLengthChange": false,
			"bPaginate": true,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});
			
			
		$("#"+button).click(function(){ 
			instance.doCreateActividad(instance._id_gestion);
		});	
		
		$("."+class_items).click(function(){
			instance.doEditActividad($(this).attr("id"));	
		});	
		
		 
				
	},		
	/*VIEW PARA CREAR ACTIVIDAD*/
	doCreateActividad : function(id){
		var instance=this;
 
		instance.post("./?mod_cobros/delegate&actividad_add",{
				"id_gestion":id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Agregar actividad",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#act_add").click(function(){
				if ($("#frm_actividad_").valid()){ 
					instance.post("./?mod_cobros/delegate&processActividadAdd",$("#frm_actividad_").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							instance.close(dialog);		
							instance.fire("onCrateActivity"); 
						}
					},"json");
				}
			});
			
			$("#act_cancel").click(function(){
				instance.close(dialog);	
			});
			
			/*Valido que el id de la caja no este asignado*/
			$("#id_caja").change(function(){
				if ($("#id_caja").val()!=""){ 
					instance.post("./?mod_caja/delegate&caja",{validateCajaExist:1,id_caja:$("#id_caja").val()},function(data){
						if (data.error){ 
							$("#id_caja").val('');
							alert(data.mensaje)	
						}

					},"json");
				}else{
					$("#id_caja").val('');	
				}
			});
			
			$("#act_escalamiento1").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#act_escalamiento1").val( ui.item.nombre );
					$("#act_escalamiento1_code").val( ui.item.value ); 
					return false;
				}
			});	
					
			$("#act_escalamiento2").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) { 
					$("#act_escalamiento2").val(ui.item.nombre);			
					$("#act_escalamiento2_code").val( ui.item.value ); 
					return false;
				}
			});	
			instance.validateFormActividad(); 
		});	
	},	
	/*VIEW PARA EDITAR ACTIVIDAD*/
	doEditActividad : function(id){
		var instance=this;
 
		instance.post("./?mod_cobros/delegate&actividad_edit",{
				"id_actividad":id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar actividad",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#act_add").click(function(){
				if ($("#frm_actividad_").valid()){ 
					instance.post("./?mod_cobros/delegate&processActividadEdit",$("#frm_actividad_").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							instance.close(dialog);		
							instance.fire("onCrateActivity"); 
						}
					},"json");
				}
			});
			
			$("#act_cancel").click(function(){
				instance.close(dialog);	
			});
			
			/*Valido que el id de la caja no este asignado*/
			$("#id_caja").change(function(){
				if ($("#id_caja").val()!=""){ 
					instance.post("./?mod_caja/delegate&caja",{validateCajaExist:1,id_caja:$("#id_caja").val()},function(data){
						if (data.error){ 
							$("#id_caja").val('');
							alert(data.mensaje)	
						}

					},"json");
				}else{
					$("#id_caja").val('');	
				}
			});
			
			$("#act_escalamiento1").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#act_escalamiento1").val( ui.item.nombre );
					$("#act_escalamiento1_code").val( ui.item.value ); 
					return false;
				}
			});	
					
			$("#act_escalamiento2").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) { 
					$("#act_escalamiento2").val(ui.item.nombre);			
					$("#act_escalamiento2_code").val( ui.item.value ); 
					return false;
				}
			});	
			instance.validateFormActividad(); 
		});	
	},	
	validateFormGestion : function(){
		$("#frm_cobro").validate({
			rules: {
				"idtipogestion": {
					required: true 
				},
				"gestion": {
					required: true 
				},
				"Tiempo_max": {
					required: true 
				} 
			},
			messages : {
				"idtipogestion": {
					required: "Este campo es obligatorio" 
				},
				"gestion": {
					required: "Este campo es obligatorio"  
				},
				"Tiempo_max": {
					required: "Este campo es obligatorio"  
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	},
	
	validateFormActividad : function(){
		$("#frm_actividad_").validate({
			rules: {
				"idtipoact": {
					required: true 
				},
				"actividad": {
					required: true 
				},
				"act_int_ext": {
					required: true 
				} ,
				"orden": {
					required: true 
				}  ,
				"act_tiempo_max": {
					required: true 
				},
				"asignar_actividad_a": {
					required: true 
				}
			},
			messages : {
				"idtipoact": {
					required: "Este campo es obligatorio" 
				},
				"actividad": {
					required: "Este campo es obligatorio"  
				},
				"act_int_ext": {
					required: "Este campo es obligatorio"  
				},
				"orden": {
					required: "Este campo es obligatorio"  
				} ,
				"act_tiempo_max": {
					required: "Este campo es obligatorio"  
				}  ,
				"asignar_actividad_a": {
					required: "Este campo es obligatorio"  
				} 			
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	},
	
	/*AQUI EMPIEZAN LAS GESTIONES DE COBRO*/ 
	tableViewAccion : function(tb,button,class_items){ 
		var instance=this;
		$("#"+tb).dataTable({
			"bFilter": true,
			"bInfo": false,
			"bPaginate": true,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});
			
		$("#"+button).click(function(){
			instance.doCreateAccion();
		});	
		
		$("."+class_items).click(function(){
			instance.doEditAccion($(this).attr("id"));	
		});
		
	},
	doCreateAccion : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&accion_add",{
				"view_add_caja":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Agregar accion",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_cobro_add").click(function(){
				if ($("#frm_cobro").valid()){ 
					instance.post("./?mod_cobros/delegate&processAccion", $("#frm_cobro").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
				}
			});
			
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	
			});
			 
			instance.validateFormAccion(); 
		});	
	},
	
	doEditAccion : function(id){
		var instance=this;
		instance.post("./?mod_cobros/delegate&accion_edit",{
				"id":id 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar acción",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_cobro_add").click(function(){
				if ($("#frm_cobro").valid()){ 
					instance.post("./?mod_cobros/delegate&processAccionEdit", $("#frm_cobro").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
				}
			});
			
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	
			});
			 
			instance.validateFormAccion(); 
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