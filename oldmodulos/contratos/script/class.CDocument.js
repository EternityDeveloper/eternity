var CDocument = new Class({
	dialog_container : null,
	_id_nit : null,
	_rand : 0,
	_producto : null,
	_filtro : null,
	_address_obj : null,
	_direccion_id : null,
	initialize : function(dialog_container,financiamiento){
		this.main_class="CDireccion";
		this.dialog_container=dialog_container;  
	},  
	document_remove : function(){
		var instance=this;
		$(".document_id").click(function(){  
			var scan_id=$(this).attr("id")
		 
			instance.post("./?mod_contratos/listar",{
								"comentario_remove_doc":'1' 
							},function(data){ 		
				var dialog=instance.createDialog(instance.dialog_container,"Remover Documento",data,500);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 20]);   
				
				$("#bt_remove_doc").click(function(){
					instance.post("./?mod_contratos/listar",{
						"remove_document":'1',
						"doc_descripcion":$("#doc_descripcion_rmv").val(),
						"scan_id": scan_id
					},function(data){ 
						$("#listado_documentos").html(data); 
						instance.document_remove();
						instance.close(dialog);
					});	
				});
			});	

		});
	},	
	/*Carga la vista de detalle de agregar Direccion*/
	loadView: function(serie_contrato,no_contrato){ 
		var instance=this;
		instance.post("./?mod_contratos/listar",{
				"add_document":'1',
				"serie_contrato":serie_contrato,
				"no_contrato":no_contrato
		},function(data){
			   
			var dialog=instance.createDialog(instance.dialog_container,"Documentos",data,500);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]);  
			
			var info=$("#bt_add_direccion").attr("data");
 
			$("#add_close").click(function(){
				instance.close(dialog); 
			});
			
			$("#bt_cargar_documento").click(function(){ 
				instance.post("./?mod_contratos/listar",{
					"save_document":'1',
					"tipo_scan":$("#tipo_scan").val(),
					"descripcion":$("#doc_descripcion").val(),
					"empresa":$("#doc_empresa").val(),
					"serie_contrato":serie_contrato,
					"no_contrato":no_contrato
				},function(data){ 
					$("#listado_documentos").html(data);  
					instance.document_remove();
					instance.close(dialog);
				});	
	
			});
			
			 var ul = $('#upload ul');
			
				$('#drop a').click(function(){
					$(this).parent().find('input').click();
				});
			
				$('#upload').fileupload({
					dropZone: $('#drop'),

					add: function (e, data) {
			
						var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
							' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');
			
						tpl.find('p').text(data.files[0].name)
									 .append('<i>' + formatFileSize(data.files[0].size) + '</i>');
			
						data.context = tpl.appendTo(ul);
 		
						tpl.find('span').click(function(){
			
							if(tpl.hasClass('working')){
								jqXHR.abort();
							}
			
							tpl.fadeOut(function(){
								tpl.remove();
							});
			
						});
			
 						var jqXHR = data.submit();
					},
			
					progress: function(e, data){
						var progress = parseInt(data.loaded / data.total * 100, 10);
						data.context.find('input').val(progress).change();			
						if(progress == 100){
							data.context.removeClass('working');
						}
					},
			
					fail:function(e, data){
						data.context.addClass('error');
					}
			
				});
			
				$(document).on('drop dragover', function (e) {
					e.preventDefault();
				}); 
				function formatFileSize(bytes) {
					if (typeof bytes !== 'number') {
						return '';
					}
			
					if (bytes >= 1000000000) {
						return (bytes / 1000000000).toFixed(2) + ' GB';
					}
			
					if (bytes >= 1000000) {
						return (bytes / 1000000).toFixed(2) + ' MB';
					}
			
					return (bytes / 1000).toFixed(2) + ' KB';
				}
		});
	}
	
});