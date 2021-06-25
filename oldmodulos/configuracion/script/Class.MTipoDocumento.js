var MTipoDocumento = new Class({
	dialog_container : null, 
	fields : {
		tipo_movimiento :1	
	}, 
	initialize : function(dialog_container){
		this.main_class="MTipoDocumento";
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
				"sAjaxSource": "./?mod_configuracion/delegate&tipo_documento&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "TIPO_DOC" },
						{ "mData": "DOCUMENTO" },
						{ "mData": "FISCAL" },
						{ "mData": "ANULA_MOVI" },
						{ "mData": "REP_VENTA" },
						{ "mData": "IMPRESION" },
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
						instance.doViewEdit($(this).attr("id"));
					});	 	
				  } 
				});	
				
		  $("#create_caja").click(function(){
			  instance.doViewCreate();
		  });	
		  this.addListener("onCreate",function(){
			  window.location.reload();  
		  });
	},
	
	doViewEdit : function(id){
		var instance=this;
		instance.post("./?mod_configuracion/delegate&tipo_documento",{
				"view_edit_documento":'1',
				"id":id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar Tipo de Documento",data,550);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_g_add").click(function(){
				if ($("#frm_general").valid()){ 
					instance.post("./?mod_configuracion/delegate&tipo_documento",$("#frm_general").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.error){
							instance.fire("onCreate");
							instance.close(dialog);
						}
					},"json");
				}
			});
			
			$("#bt_g_cancel").click(function(){
				instance.close(dialog);	
			}); 
			
				
			instance.validateForm();

		});	
	},
	
	doViewCreate : function(){
		var instance=this;
		instance.post("./?mod_configuracion/delegate&tipo_documento",{
				"view_add_documento":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Agregar Tipo Documento",data,550);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_g_add").click(function(){
				if ($("#frm_general").valid()){ 
					instance.post("./?mod_configuracion/delegate&tipo_documento", $("#frm_general").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.error){
							instance.fire("onCreate");
							instance.close(dialog);
						}
					},"json");
				}
			});
			
			$("#bt_g_cancel").click(function(){
				instance.close(dialog);	
			});
			
			/*Valido que el id de la caja no este asignado*/
			$("#TIPO_DOC").change(function(){
				if ($("#TIPO_DOC").val()!=""){ 
					instance.post("./?mod_configuracion/delegate&tipo_documento",{validateDocExist:1,TIPO_DOC:$("#TIPO_DOC").val()},function(data){
						if (data.error){ 
							$("#TIPO_DOC").val('');
							alert(data.mensaje)	
						}

					},"json");
				}else{
					$("#TIPO_DOC").val('');	
				}
			});
			 		
			instance.validateForm();

		});	
	} ,
	
	validateForm : function(){
		$("#frm_general").validate({
			rules: {
				"TIPO_DOC": {
					required: true 
				},
				"descripcion": {
					required: true 
				} 
			},
			messages : {
				"TIPO_DOC": {
					required: "Este campo es obligatorio" 
				},
				"descripcion": {
					required: "Este campo es obligatorio"  
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});