var MantenimientoCaja = new Class({
	dialog_container : null, 
	fields : {
		tipo_movimiento :1	
	}, 
	initialize : function(dialog_container){
		this.main_class="MantenimientoCaja";
		this.dialog_container=dialog_container; 
	},	  
	/*VER EL ESTADO DE CUENTA*/
	drawTable : function(tb){
		var instance=this;   
		createTable(tb,{
				"bFilter": true,
				"bSort": false,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_caja/delegate&caja&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "id_caja" },
						{ "mData": "descripcion" },
						{ "mData": "cajero" },
						{ "mData": "ip" },
						{ "mData": "monto_inicial" },
						{ "mData": "settings" }
					],
				  "oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					},
				  "fnDrawCallback": function( oSettings ) {
					$(".edit_mante_caja").click(function(){
						instance.doViewEditCaja($(this).attr("id"));
					});	 	
				  } 
				});	
				
		  $("#create_caja").click(function(){
			  instance.doCreateCaja();
		  });	
		  this.addListener("onCreateCaja",function(){
			  window.location.reload();
		  });
	},
	doViewEditCaja : function(id){
		var instance=this;
		instance.post("./?mod_caja/delegate&caja",{
				"view_edit_caja":'1',
				"id":id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar Caja",data,650);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_caja_add").click(function(){
				if ($("#frm_caja").valid()){ 
					instance.post("./?mod_caja/delegate&caja",$("#frm_caja").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.error){
							instance.fire("onCreateCaja");
							instance.close(dialog);
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
			
			$("#cajero").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#cajero").val( ui.item.nombre );
					$("#id_cajero").val( ui.item.value ); 
					return false;
				}
			});			
			instance.validateForm();

		});	
	},
	
	doCreateCaja : function(){
		var instance=this;
		instance.post("./?mod_caja/delegate&caja",{
				"view_add_caja":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Agregar Caja",data,550);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_caja_add").click(function(){
				if ($("#frm_caja").valid()){ 
					instance.post("./?mod_caja/delegate&caja", $("#frm_caja").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.error){
							instance.fire("onCreateCaja");
							instance.close(dialog);
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
			
			$("#cajero").combogrid({
				url: './?mod_caja/delegate&caja&getListUsuario=1', 
				colModel: [ 
						 {'columnName':'nombre','width':'60','label':'Nombre'}
						],
				select: function( event, ui ) {
					$("#cajero").val( ui.item.nombre );
					$("#id_cajero").val( ui.item.value ); 
					return false;
				}
			});			
			instance.validateForm();

		});	
	} ,
	validateForm : function(){
		$("#frm_caja").validate({
			rules: {
				"id_caja": {
					required: true 
				},
				"descripcion": {
					required: true 
				},
				"cajero": {
					required: true 
				},
				"ip_caja": {
					required: true 
				},
				"monto_inicial": {
					required: true 
				}
			},
			messages : {
				"id_caja": {
					required: "Este campo es obligatorio" 
				},
				"descripcion": {
					required: "Este campo es obligatorio"  
				},
				"cajero": {
					required: "Este campo es obligatorio"  
				},
				"ip_caja": {
					required: "Este campo es obligatorio"  
				},
				"monto_inicial": {
					required: "Este campo es obligatorio"  
				}		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});