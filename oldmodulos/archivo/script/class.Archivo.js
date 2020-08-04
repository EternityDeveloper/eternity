var ArchivoMovil = new Class({
	dialog_container : null,
	initialize : function(dialog_container){
		this.main_class="Contratos";
		this.dialog_container=dialog_container;  
	},  
	doViewList : function(table_name){
		var instance=this;
		this._table_view_name=table_name;
		createTable(this._table_view_name,{
				"bFilter": true,
				"bSort": false,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_archivo/delegate&listado&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "contrato_numero" },
						{ "mData": "nombre_cliente" },
						{ "mData": "fecha_ingreso" },
						{ "mData": "EM_NOMBRE" },
 						{ "mData": "estatus" },
						{ "mData": "ubicacion" }, 
						{ "mData": "bt_editar" },
						{ "mData": "bt_print" }
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
						//alert('fds');
						if (!instance._isCharge){
							$('<button id="pro_refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter'); 
						  
							instance._isCharge=true;	
						}						  
						$(".edit_archivo").click(function(){
							instance.doEdit($(this).attr("id"));	
						});	
							
						$(".view_archivo").click(function(){
							instance.doViewACEdit($(this).attr("id"));	
						});	
															

					} 
				});	
				
	},
	doEdit : function(id){
		var instance=this;
		var archivo="";
		this.post("./?mod_archivo/delegate&archivo",{"template":1},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Seleccionar ubicacion en el archimovil",data,1024);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$(".archimovil").click(function(){ 	 
				instance.archivoDetalle(id,$(this).attr("id"));
			});
			 
		});
 		
	}, 
	archivoDetalle : function(id,archivo){
		var instance=this;
		this.post("./?mod_archivo/delegate&archivo",{"template":2},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Detalle de la ubicacion",data,830);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			
			//alert(id + ' ' + archivo);
			$(".archiv").click(function(){
				//alert($(this).attr('id'));	
				instance.post("./?mod_archivo/delegate",
							{
								"archivo_id":archivo,
								"contrato":id,
								"ubicacion":$(this).attr('id'),
								"update_archivo":1
							},
				function(data){
					alert(data.mensaje);	
					if (!data.error){
						//instance.fire("onCreateData");
						instance.close(dialog);
					}
				},"json");				
			});
 			
			
		});
	},
	
	doViewACEdit : function(id){
		var instance=this; 
		instance.post("./?mod_archivo/delegate&ViewACEdit",{"id":id},function(data){ 
 			instance.doDialog("view_modal_edit_document",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
  
			$("#pap_print_doct").click(function(){  
				var tipo_documento=$("#tipo_documento").val();
				if (tipo_documento==''){
					alert('Debe de seleccionar el TIPO DE DOCUMENTO');
					return false;
				} 	 
				instance.post("./?mod_archivo/delegate&doPrintContrato",{
						"contrato":id,
						"tipo_documento":tipo_documento,
						"comentario":$("#comentario").val()
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload()
						} 						
					},"json");		
			}); 
			
						
		});			
	},
	doAsignarCorrelativoArchivo : function(id){
		var instance=this;
		var archivo="";
		this.post("./?mod_archivo/delegate&detalle_asignar_correlativo",{"id":id},function(data){ 
 			instance.doDialog("view_modal_asignar_cliente",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			});
			
			$(".print_label").click(function(){
				var id=$(this).attr("id");
				instance.post("./?mod_archivo/delegate&detall_imprimir_label_contrato",
				{},function(data){ 
					instance.doDialog("view_modal_label_contrato",instance.dialog_container,data); 
					instance.addListener("onCloseWindow",function(){ 
					});			
					 
					 
					 $("#doImprimir").click(function(){ 
						instance.post("./?mod_archivo/delegate&procesar_impresion_label_contrato",
						{"id":id,"printer":$("#printer_list option:selected").attr("id"),"formato":"CONTRATO"},function(data){ 
							if (!data.valid){ 
								var dprint={method:"doPrint","token":getCook("token"),"data":data.data} 
								instance.send(dprint);
							}else{
								alert(data.mensaje);
							} 
						},"json");
					 });
					
				});				
			});
			
			$("#imprimir_no_archvio").click(function(){
				var id=$(this).attr("item");
				instance.post("./?mod_archivo/delegate&detall_imprimir_label_contrato",
				{},function(data){ 
					instance.doDialog("view_modal_label_contrato",instance.dialog_container,data); 
					instance.addListener("onCloseWindow",function(){ 
					});
					setTimeout(function(){$("#doImprimir").focus()},300);
					 $("#doImprimir").click(function(){ 
						instance.post("./?mod_archivo/delegate&procesar_impresion_label_contrato",
						{"id":id,"printer":$("#printer_list option:selected").attr("id"),"formato":"FOLDER"},function(data){ 
							if (!data.valid){ 
								var dprint={method:"doPrint","token":getCook("token"),"data":data.data} 
								instance.send(dprint);
							}else{
								alert(data.mensaje);
							} 
						},"json");
					 });
					
				});				
			});		
			 	 
			$("#detalle_asignar_cliente").click(function(){ 	 
				if (confirm("Desea asignar el numero de correlativo?")){
					if (confirm("Usted esta seguro de asignar el numero de correlativo?")){
						instance.post("./?mod_archivo/delegate&procesar_asignar_correlativo",
						{"id":id},function(data){ 
							if (!data.valid){
								$(".detalle_contrato_asignacion").hide(); 
								$(".detalle_transapcion").show();
								$("#_numero_correlativo").html('<h1>'+data.numero_de_archivo+'</h1>')
								
								 
							}else{
								alert(data.mensaje);
							} 
						},"json");
					}
					
				}
			});
			 
		}); 
	}	
	
});