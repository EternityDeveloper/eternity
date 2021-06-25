// JavaScript Document
var MantenimientoReserva = new Class({
	rand : '', //Almacena el random de los ids del formulario form_reserva_(rand)
	dialog_container : null,
	tb_item_list : null,
	page_loading : null,
	initialize : function(dialog_container,table_name,page_loading){
		this.main_class="MantenimientoReserva";
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
			var createW= new createReservaWindow(instance.dialog_container,instance.page_loading);
			createW.openCreateWindow();
		});
		
		$(".edit").click(function(){ 
			var createW= new createReservaWindow(instance.dialog_container,instance.page_loading);
			createW.openEditWindow($(this).attr("id"));
		});
	}
	
});


var createReservaWindow = new Class({
	dialog_container : null,
	_dialog : null,
	initialize : function(dialog_container,page_loading){
		this.main_class="MantenimientoReserva";
		this.dialog_container=dialog_container;
		this.page_loading=page_loading;
	},
	openCreateWindow : function(){
		var instance=this;
		
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_inventario/reserva/reservar",{
				"tipo_reserva_add":'1',
				"edit":'0' 
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Tipo de reserva",data,320);
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
				
				
			});
	},
	
	openEditWindow : function(value){
		var instance=this;
		
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_inventario/reserva/reservar",{
				"tipo_reserva_add":'1',
				"edit":'1',
				'id':value
			},function(data){
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Editar Tipo de reserva",data,320);
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
	
	save: function(){
		
		if ($("#form_interfase").valid()){
		 	$('#'+this._dialog).showLoading({'addClass': 'loading-indicator-bars'});	
			var instance=this;
			$.post("./?mod_inventario/reserva/reservar",$("#form_interfase").serializeArray(),function(data){
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
					id_reserva : {
						required: true,
						maxlength:5
					},
					reserva_descrip: {
						required: true
					},
					horas: {
						required: true,
						number:true
					}
				},
				messages : {
					id_reserva : {
						required: "Este campo es obligatorio",
						maxlength : "El maximo de digitos es 5"
					},
					reserva_descrip : {
						required: "Este campo es obligatorio" 
					},
					horas: {
						required: "Este campo es obligatorio" ,
						number:"Este campo es numerico"
					}
				}
			});
	}
	
});