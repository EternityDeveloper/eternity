// JavaScript Document
var MantenimientoProspectos = new Class({
	rand : '', //Almacena el random de los ids del formulario form_reserva_(rand)
	dialog_container : null,
	tb_item_list : null,
	page_loading : null,
	initialize : function(dialog_container,table_name,page_loading){
		this.main_class="MantenimientoProspectos";
		this.dialog_container=dialog_container;
		this.tb_item_list=table_name;
		this.page_loading=page_loading;
	},
	createTable : function(){
		 createTable(this.tb_item_list,{
						"bFilter": true,
						"bInfo": false,
						"bPaginate": false,
						  "oLanguage": {
								"sLengthMenu": "Mostrar _MENU_ registros por pagina",
								"sZeroRecords": "No se ha encontrado - lo siento",
								"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
								"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
								"sInfoFiltered": "(filtrado de _MAX_ total registros)",
								"sSearch":"Buscar"
							}
						});		
	},
	activateButtom : function(button){
		var instance= this;
		$("#"+button).click(function(){
			var createW= new createProspectoWindow(instance.dialog_container,instance.page_loading);
			createW.openCreateWindow();
		});
		
		$(".edit").click(function(){ 
			var createW= new createProspectoWindow(instance.dialog_container,instance.page_loading);
			createW.openEditWindow($(this).attr("id"));
		});
	}
	
});


var createProspectoWindow = new Class({
	dialog_container : null,
	_dialog : null,
	_tb_listado_pregunta: "tb_listado_pregunta",
	initialize : function(dialog_container,page_loading){
		this.main_class="MantenimientoReserva";
		this.dialog_container=dialog_container;
		this.page_loading=page_loading;
	},
	openCreateWindow : function(){
		var instance=this;
		
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_prospectos/listar",{
				"add_tipo_pilar":'1',
				"edit":'0' 
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Tipo de Pilar",data,420);
				instance._dialog=dialog;
				
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/500), 100]); 
				
				$("#bt_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove(); 
				});
				
				$("#bt_save").click(function(){
					instance.save();
				});
				instance.validateForm();
				
			});
	},
	
	openEditWindow : function(value){
		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_prospectos/listar",{
				"edit_tipo_pilar":'1',
				'id':value
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Editar Tipo de Pilar",data,950);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/900), 100]); 
				
				$("#bt_prospecto_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove(); 
				});
				
				$("#bt_prospecto_save").click(function(){
					instance.save();
				});
				
				instance.validateForm();
				
				instance.optionListadoPregunta(value);
	
				
			});
	},
	
	optionListadoPregunta : function(value){
		var instance=this;	
		/*CAPTURO EL CUADRO DE PREGUNTAS*/
		var question= instance.viewProspectosQuestion(value);	
		
		createTable(this._tb_listado_pregunta,{
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_prospectos/listar&edit_tipo_pilar=1&x_search=1&b_tipo_pilar="+value,
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "id_pregunta" },
						{ "mData": "pregunta" },
						{ "mData": "tipo_respuesta" },
						{ "mData": "estatus" },
						{ "mData": "option1" }
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
						$(".prosp_add_link").click(function(){
							question.openEditWindow($(this).attr("id"));		
						});	
					}
				});	
 
	},	
	
	viewProspectosQuestion : function(value){
		var instance=this;
		var question= new createProspectoQuestion(this.dialog_container,this.page_loading);
		question.addListener("onCreateSuccessful",function(object){
			var oTable=$("#"+instance._tb_listado_pregunta).dataTable();
			oTable.fnClearTable(0);
			oTable.fnDraw(); 
		});
		
		$("#bt_add_seguir").click(function(){
			question.openAddWindow(value);	
		});
		$("#bt_v_add_seguir").click(function(){
			question.openAddWindow(value);		
		});	

		return 	question;
	},
	save: function(){
		
		if ($("#form_interfase").valid()){
		 	$('#'+this._dialog).showLoading({'addClass': 'loading-indicator-bars'});	
			var instance=this;
			$.post("./?mod_prospectos/listar",$("#form_interfase").serializeArray(),function(data){
				$('#'+instance._dialog).hideLoading();	
			 
				if (!data.error){
					alert(data.mensaje);
					window.location.reload();
				}else{
					alert(data.mensaje);
				}
			},"json");
		}
		
	},
	
	validateForm: function(){
		$("#form_interfase").validate({
				rules: {
					idtipo_pilar : {
						required: true,
						maxlength:5
					},
					dscrip_tipopilar: {
						required: true
					},
					dias_proteccion: {
						required: true,
						number:true
					},
					estatus: {
						required: true 
					}
				},
				messages : {
					idtipo_pilar : {
						required: "Este campo es obligatorio",
						maxlength : "El maximo de digitos es 5"
					},
					dscrip_tipopilar : {
						required: "Este campo es obligatorio" 
					},
					dias_proteccion: {
						required: "Este campo es obligatorio" ,
						number:"Este campo es numerico"
					},
					estatus: {
						required: "Este campo es obligatorio"  
					}
				}
			});
	}
	
});

var createProspectoQuestion = new Class({
	dialog_container : null,
	_dialog : null,
	initialize : function(dialog_container,page_loading){
		this.main_class="createProspectoQuestion";
		this.dialog_container=dialog_container;
		this.page_loading=page_loading;
	},
	openAddWindow : function(value){
		var instance=this;
		
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_prospectos/listar",{
				"add_pregunta":'1',
				"id":value 
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar pregunta",data,420);
				instance._dialog=dialog;
				
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/500), 80]); 
				
				$("#bt_question_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove(); 
				});
				
				$("#bt_question_save").click(function(){
					instance.save();
				});
				
				$("#tipo_resp_det_prospec").change(function(){
					//alert($(this).val());	
					switch($(this).val()){
						case "":
							$("#tipo_respuesta_dt").hide();
						break;
						case "boolean":	
							$("#tipo_respuesta_dt").hide();
						break;
						case "abierta":	
							$("#tipo_respuesta_dt").hide();
						break;
						case "valores":	
							$("#tipo_respuesta_dt").show();
						break;
					}
				});
				instance.validateForm();
			 
				
			});
	},
	openEditWindow : function(value){
		var instance=this;
		
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_prospectos/listar",{
				"edit_pregunta":'1',
				"question":value,
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Editar pregunta",data,420);
				instance._dialog=dialog;
				
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/500), 80]); 
				
				$("#bt_question_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove(); 
				});
				
				$("#bt_question_save").click(function(){
					instance.save();
				});
				
				$("#tipo_resp_det_prospec").change(function(){
					//alert($(this).val());	
					switch($(this).val()){
						case "":
							$("#tipo_respuesta_dt").hide();
						break;
						case "boolean":	
							$("#tipo_respuesta_dt").hide();
						break;
						case "abierta":	
							$("#tipo_respuesta_dt").hide();
						break;
						case "valores":	
							$("#tipo_respuesta_dt").show();
						break;
					}
				});
				instance.validateForm();
				
				//edit_pregunta
				//
				
			});
	},
	validateForm: function(){
		$("#form_interfase_question").validate({
				rules: { 
					pregunta_det_prospec: {
						required: true
					},
					tipo_resp_det_prospec: {
						required: true
					},
					valor1: {
						required: true
					} 
				},
				messages : { 
					pregunta_det_prospec : {
						required: "Este campo es obligatorio" 
					},
					tipo_resp_det_prospec: {
						required: "Este campo es obligatorio"
					},
					valor1: {
						required: "Este campo es obligatorio"
					} 
				}
			});
	},
	
	save: function(){
		var instance=this;
		if ($("#form_interfase_question").valid()){
		 	$('#'+this._dialog).showLoading({'addClass': 'loading-indicator-bars'});	
			var instance=this;
			$.post("./?mod_prospectos/listar",$("#form_interfase_question").serializeArray(),function(data){
				$('#'+instance._dialog).hideLoading();	
				if (!data.error){
					$("#"+instance._dialog).dialog("destroy");
					$("#"+instance._dialog).remove(); 
				  
					alert(data.mensaje);
					instance.fire("onCreateSuccessful",instance);
				}else{
					alert(data.mensaje);
				}
			},"json");
		}
		
	}
	
	
});