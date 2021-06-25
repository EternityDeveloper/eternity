// JavaScript Document
var Descuento = new Class({
	tb_item_list : null,
	initialize : function(dialog_container,table_name){
		this.main_class="Descuento";
		this.dialog_container=dialog_container;
		this.tb_item_list=table_name; 
	},
	
	createTable : function(){
		var instance=this;
		
		 createTable(this.tb_item_list,{
						"bFilter": true,
						"bSort": false,
						"bInfo": false,
						"bPaginate": true,
						"bLengthChange": false,
						"bProcessing": true,
						"bServerSide": true,
						"sAjaxSource": "./?mod_financiamiento/descuentos/listar&x_search=1",
						"sServerMethod": "POST",
						"aoColumns": [ 
								{ "mData": "codigo" },
								{ "mData": "descripcion" },
								{ "mData": "monto" },
								{ "mData": "porcentaje" },
								{ "mData": "ingresado" },
								{ "mData": "monto_ingresado" },
								{ "mData": "autorizacion" },
								{ "mData": "negocios" },
								{ "mData": "moneda" },
								{ "mData": "prioridad" },
								{ "mData": "bt_editar" }
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
								$(".bt_view_plan").click(function(){
								//	instance.tb_last_edit_id=$(this).attr("id");
								//	instance.viewPlanList($(this).attr("id"));
								});	
								
								$(".desc_edit_plan").click(function(){
									instance.doEditView($(this).attr("id"));
								});	
							}
						});		
	},
	
	putCreateButton : function(buttom){
		var instance=this;
		$("#"+buttom).click(function(){
			instance.doCreateView();
		});	
	},
	
	doEditView : function(id){

		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_financiamiento/descuentos/listar",{
				"edit_descuento":'1' ,
				"id":id
			},function(data){
				
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Editar Descuento",data,450);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 50]); 
 
 				$("#desc_bt_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				
				$("#desc_bt_save").click(function(){
					instance.doSaveForm(dialog,"edit");
				}); 
				instance.validateForm(); 
 
			});
 
	},
	doCreateView : function(){

		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_financiamiento/descuentos/listar",{
				"add_descuento":'1' 
			},function(data){
				
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Descuento",data,450);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 50]); 
 				
				$("#codigo").change(function(){
					var action={
						emptyCode : function(){
							$("#codigo").val('');
							$("#codigo").focus();
						}
					}
					
					instance.validateCode($(this).val(),action);	
				});
				
 				$("#desc_bt_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				
				$("#desc_bt_save").click(function(){
					instance.doSaveForm(dialog,"create");
				}); 
				instance.validateForm(); 
 
			});
 
	},
	validateForm : function(){
		$("#form_descuento").validate({
			rules: {
				"codigo": {
					required: true 
				},
				"descripcion" : {
					required: true 
				},
				"money" : {
					required: true ,
					number: true
				},
				"moneda": {
					required: true
				},
				"tipo_monto": {
					required: true 
				},
				"prioridad": {
					required: true 
				}  
				
			},
			messages : {
				"codigo": {
					required: 'Campo obligatorio' 
				},
				"descripcion" : {
					required: 'Campo obligatorio' 
				},
				"money" : {
					required: 'Campo obligatorio' ,
					number: 'El campo es numerico'
				},
				"moneda": {
					required: 'Campo obligatorio' 
				},
				"tipo_monto": {
					required:  'Campo obligatorio'  
				},
				"prioridad": {
					required: 'Campo obligatorio'   
				} 	
				
			}
		
		});		
		$.validator.messages.required = "Campo obligatorio.";
	},
	doSaveForm : function(div_loading,events){
		var instance=this;
		if ($("#form_descuento").valid()){
		 
			var data={
				"form_submit_desc":1,
				"form_data" : $("#form_descuento").serializeArray(),
				"events":events
			};
			$('#'+div_loading).showLoading({'addClass': 'loading-indicator-bars'});	
			
			$.get("./?mod_financiamiento/descuentos/listar",data,function(data){
				$('#'+div_loading).hideLoading();	
				if (!data.error){
					alert(data.mensaje);
					window.location.reload();
				}else{
					alert(data.mensaje);
				} 
	
			},"json"); 
			 
		}
		
	},
	validateCode : function(code,obj){
		var instance=this;
		var data={
			"validate_code":1,
			"codigo" :code 
		};  
		$.post("./?mod_financiamiento/descuentos/listar",data,function(data){
			if (data.exist){
				alert('Este codigo ya ha sido registrado en el sistema!');
				obj.emptyCode();
			} 
		},"json"); 
 
		
	}



});