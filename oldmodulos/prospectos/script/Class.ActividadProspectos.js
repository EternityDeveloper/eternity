var ActividadesProspectos = new Class({
	dialog_container : null,
	_prospecto_id : null,
	_form_name : "frm_new_actividad",
	_actividad : {
		'id_actividad':null,
		'detalle' : null,
		'fecha_contacto':null,
		'fecha': null,
		'hora': null,
		'lugar':null,
		'pilar': null,
		'estatus' : null,
		'is_apoyo': false	
	},
	initialize : function(dialog_container,prospecto_id){
		this.main_class="ActividadesProspectos";
		this.dialog_container=dialog_container;
		this._prospecto_id=prospecto_id;
		this._actividad =null;
		this._actividad={
			'id_actividad':null,
			'detalle' : null,
			'fecha_contacto':null,
			'fecha': null,
			'hora': null,
			'lugar':null,
			'pilar': null,
			'estatus' : null,
			'is_apoyo': false	
		};
	},
	
	chargeView : function(){
		var instance=this;
	  	this.validarIfExistActividad();
	},
	
	createListViewActividades : function(){
		var instance=this; 
 		instance.post("./?mod_prospectos/listar",{
				"actividades_view":'1',
				'prospecto_id':this._prospecto_id
			},function(data){
			//	$('#'+instance.dialog_container).hideLoading();		
				$('#'+instance.dialog_container).html(data);
				  
				$("#bt_actividad_add").click(function(){
					 instance.registerActividadView(instance.dialog_container,true);
				}); 
				
				instance.createTableView('tb_listado_actividad');
				
				/*BOTON QUE SE UTILIZARA PARA CERRAR UNA ACTIVIDAD ANTES DE TIEMPO*/
	  			$("#bt_close_actividad").click(function(){ 
					instance.detail_activity_view($(this).val());
				});
 
			});	
	},
	
	createTableView : function(table_name){
		createTable(table_name,{
						'bSort':false,
						"bFilter": false,
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
	
	validarIfExistActividad : function(){
		var instance=this;
		//var rand= this.getRand();
	//	$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		instance.post("./?mod_prospectos/listar",{
				"validate_actividad":'1',
				'prospecto_id':this._prospecto_id
			},function(data){
				//$('#'+instance.dialog_container).hideLoading();
				/*DETERMINA CUAL VISTA CARGAR 
					SI ES LA VISTA DE REGISTRAR ACTIVIDAD
					O ES EL LISTADO DE ACTIVIDADES*/  
			//	instance.createListViewActividades();
			//	alert(data.hasActividad);
				if ((data.hasActividad==true) && (data.time_diff>=0)){
					instance.detail_activity_view(data.id_actividad);
				}else{
					instance.createListViewActividades();
					//instance.registerActividadView(instance.dialog_container,true);
				}
				
			},"json");
	},
	
	registerActividadView : function(container,createDiv,config){
		var instance=this; 	
		var cnf="";
		if (config!=null){
			cnf={show_submit_bt:config.show_submit_bt,show_form:config.show_form};
		} 
		instance.post("./?mod_prospectos/listar",{
				"actividad_add":'1',
				'prospecto_id':this._prospecto_id,
				'config':cnf
			},function(data){
				//$('#'+container).hideLoading();	
				if (createDiv){
					var dialog=instance.createDialog(container,"Registrar Actividad",data,400);
					instance._dialog=dialog;
					var n = $('#'+dialog);
					n.dialog('option', 'position', [(document.scrollLeft/950), 10]); 
				}else{
					$('#'+container).html(data);
				}
				 
				$("#bt_actividad_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				
				$("#bt_actividad_save").click(function(){
					instance.formSubmit({
						onSubmit : function(){
							$("#"+dialog).dialog("destroy");
							$("#"+dialog).remove();
						}	
					});
				}); 
				
				$("#actividad").change(function(){
					var value=$(this).val();
					$(".fields_hidden").hide();
					 
					if (value=="CIE"){
						$(".all_opction").hide();
						$(".fields_hidden").hide();
						$(".pilar_actividad").show();
					}
					if (value=="CITA"){
						$(".fields_hidden").show();
					 	 
					}
					
					instance._actividad.id_actividad=value;
					//console.log("actividad VALOR =>" +instance._actividad.id_actividad);
				});
				
				$("#fecha").change(function(){
					instance._actividad.fecha=$(this).val();
				});
				
				$("#fecha_contacto").change(function(){
					instance._actividad.fecha_contacto=$(this).val();
				});
				
				$("#descripcion").change(function(){
					instance._actividad.detalle=$(this).val();
				});
				
				$("#hora").change(function(){
					instance._actividad.hora=$(this).val();
				});
				$('#hora').timeEntry();
				
				$("#lugar").change(function(){
					instance._actividad.lugar=$(this).val();
				});
				$("#tipos_prospectos").change(function(){
					instance._actividad.pilar=$(this).val(); 
				}); 
				$("#is_apoyo").change(function(){
					instance._actividad.is_apoyo=$(this).prop('checked');
				}); 
				
				instance.addCalendar(); 
				instance.validateForm();
			
				if (config!=null){
					if(typeof config.onLoadView == 'function'){  
						config.onLoadView();
					}
				}
 
			});
	},
	
	detail_activity_view : function(id_actividad){
		var instance=this;
		//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		instance.post("./?mod_prospectos/listar",{
				"actividad_detail":'1',
				'prospecto_id':this._prospecto_id,
				'id_actividad': id_actividad 
			},function(data){
			//	$('#'+instance.dialog_container).hideLoading();	
				$('#'+instance.dialog_container).html(data);
				 
				$("#estatus").change(function(){
					var value=$(this).val();
					instance._actividad.estatus=value; 
					/*Si es diferente de RESERVA ENTONCES*/
					if (value!=0){
						$(".activity_question_bt_view").show();	
						var data; 
						/*SI ES IGUAL A LA CIERRE*/
						if (value==6){
							
							data= {
								'show_submit_bt':false,
								'show_form': false,
								'show_activity':false,
								onLoadView : function(){
									/*Cuando*/
									$("#bt_question_actividad_save").click(function(){
										instance.formSubmit();
									}); 
									$(".all_opction").hide();
									$(".fields_hidden").hide();
									$(".pilar_actividad").show();
									//alert('Cargando');
									/*bt_question_actividad_cancel*/
								}
							};
							
						} 
						
						/*SI ES IGUAL A LA RESERVA*/
						if (value==7){
							
							data= {
								'show_submit_bt':false,
								'show_form': false,
								'show_activity':false,
								onLoadView : function(){
									/*Cuando*/
									$("#bt_question_actividad_save").click(function(){
										instance.formSubmit();
									});  
									/*bt_question_actividad_cancel*/
								}
							};
							
						} 
						
						/*SI ES IGUAL A SEGUIMIENTO FUTURO*/
						if (value==8){ 
							data= {
								'show_submit_bt':false,
								'show_form': false,
								onLoadView : function(){
									//alert('fdsa');
									/*Cuando*/
									$("#bt_question_actividad_save").click(function(){
										instance.formSubmit();
									}); 
									
									/*bt_question_actividad_cancel*/
								}
							};
							
						}
						
						instance.registerActividadView("activity_question",false,data);
					 
					}else{
						$(".activity_question_bt_view").hide();	
					}

				});
				
			},"text");
	},
	addCalendar : function(){
		$("#fecha").datepicker({
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
		
		$("#fecha_contacto").datepicker({
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
		
	},
	
	formSubmit : function(config){
		var instance=this;

		if ($("#"+this._form_name).valid()){
			var info = {
				'submitActividad':1,
				'prospecto_id': this._prospecto_id,
				'data' : this._actividad
			}
			
			instance.post("./?mod_prospectos/listar",info,function(data){ 
				if (!data.error){
					instance.fire("onCreate",data);	
					instance.chargeView();
					if (config!=null){
						if(typeof config.onSubmit == 'function'){ 
							config.onSubmit();
						}
					}
				//	alert(data.mensaje);
				}else{
					alert(data.mensaje);
				} 
			},"json");
			
		}

	},
	
	validateForm : function(){

		$("#"+this._form_name).validate({
			rules: {
				actividad: {
					required: true 
				},
				descripcion: {
					required: true 
				},
				fecha: {
					required: true 
				},
				fecha_contacto: {
					required: true 
				}
			},
			messages : {
				actividad : {
					required: "Este campo es obligatorio" 	
				},
				descripcion : {
					required: "Este campo es obligatorio" 	
				},
				fecha : {
					required: "Este campo es obligatorio" 	
				},
				fecha_contacto : {
					required: "Este campo es obligatorio" 	
				}	
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";

	}
	
});