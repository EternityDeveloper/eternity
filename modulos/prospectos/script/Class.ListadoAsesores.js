var ListadoAsesores = new Class({
	dialog_container : null,
	_dialog : null,
	_table_view_name:null,
	_component : null,
	_person_component: null,
	_asesor : null,
	_prospecto_data: {
		"form_complete":false,
		"data":null
	},
	_asesor_data: {
		"form_complete":false,
		"data":null
	},
	_tipo_prospecto_data : {
		"form_complete":false,
		"tipos_prospectos":null,
		"data":null
	 },
	_isCharge: false,
	_actividad : null,
	_list_reasign : [] ,//Listado de prospecto que se van a reasignar
	initialize : function(dialog_container,page_loading){
		this.main_class="Prospectos";
		this.dialog_container=dialog_container;
		this.page_loading=page_loading;
	},
	setAsesorData : function(nombre,code,idnit){
		var  asesor= { 
			"nombre":nombre,
			"code":code,
			"idnit":idnit
		}   
		this._asesor_data.data=asesor;
		this._asesor_data.form_complete=true;
	//	this._asesor.fire("asesor_select",asesor);  
	},
	listar_cartera_asesor : function(table_name){
		var instance=this;
		this._table_view_name=table_name;
		createTable(this._table_view_name,{
				"bFilter": true,
				"bSort": true,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_prospectos/listado_asesor&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "checklist" },
						{ "mData": "nombre_completo" }
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
						if (!instance._isCharge){
							$('<button id="pro_refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter');  

							$('<button id="remove_bt" class="greenButton removebt" style="display:none">Eliminar</button>').appendTo('div.dataTables_filter');  

							instance._isCharge=true;	
						}
							
						$("#remove_bt").click(function(){
							instance.doViewRemove($(".remove_asesor:checked").val());
						});	
						
						$(".remove_client_prosp").click(function(){
							instance.doRemoveView($(this).attr("id"));
						});	
						$(".remove_asesor").click(function(){
							$(".remove_asesor").prop("checked",false);
							$(this).prop("checked",true);
							$("#remove_bt").show();
							if (!$(this).prop('checked')){
								$("#remove_bt").hide();
							}
						});
						
					} 
				});	
	},	
	
	doViewRemove : function(id){
		var instance=this;
		instance.post("./?mod_prospectos/listado_asesor",{
				"view_asesor":'1',
				"id":id 
			},function(data){				
				var dialog=instance.createDialog(instance.dialog_container,"PERSONA A ELMINAR",data,500);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
				
				$("#bt_asesor_f_cancel").click(function(){
					instance.close(dialog);	
				});
				
				$("#bt_remove_asesor").click(function(){
					if (confirm("Esta seguro que quiere Eliminar este asesor?")){
						if (confirm("Realmente esta seguro que quiere Eliminar este asesor?")){ 
							instance.post("./?mod_prospectos/listado_asesor",{
									"remove_asesor":'1' ,
									'id':id,
									"comentario":$("#asesor_comentario").val()
							},function(data){ 
								alert(data.mensaje);	
								if (!data.error){
									window.location.reload();
								} 
							},"json");
						}
					}
				});
				
			});
		/*if (confirm("Esta seguro que quiere remover el prospecto?")){
			$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_prospectos/listar",{
					"remove_prospecto":'1' ,
					'id':id
			},function(data){ 
				$('#'+instance.dialog_container).hideLoading();	
				alert(data.mensaje);	
				if (!data.error){
					window.location.reload();
				} 
			},"json");
		}*/
	},
	viewSimpleList : function(){
			
		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		$.post("./?mod_prospectos/listar",{
				"simple_list":'1' 
			},function(data){				
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Listado de  prospecto",data,1000);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
				instance.createSimpleListProspecto('simple_prospect_list',dialog);
			});
	},

	doSelectTipoProspect : function(data){
		var instance=this;
		this._tipo_prospecto_data.form_complete=true;
		this._tipo_prospecto_data.data=data;
		this._tipo_prospecto_data.tipos_prospectos=$("#tipos_prospectos").val();
		 
	},
 	/*valida un documento de indentidad para determinar 
	  si ya esta creado en el sistema*/
 	validateDocument : function(id_document){
		var instance=this; 
		if ($("#indentification").val().length>=6){
			var valid=false;
			
			if (id_document.tipo.trim()!="CEDULA"){
				valid=true;
			}else{
				valid=valida_cedula($("#indentification").val());
			}
			
			if (valid){
				$(".finder").hide();
				$("#prosp_loading").show();
				   
				$('#'+instance.dialog_container).hideLoading();	
				$.post("./?mod_prospectos/listar",{validate_identifaction:"1","numero_documento":$("#indentification").val(),"tipo_documento":id_document.id},function(data){	
					$('#'+instance.dialog_container).hideLoading();			 	
					$(".finder").show();
					$("#prosp_loading").hide(); 

					/*SI HAY QUE CREARLO*/ 
					if (data.addnew){
						if (confirm("El prospecto no existe en nuestra base de datos desea proceder a registrar sus datos personales?")){
							var dat={
								'identificacion':$("#indentification").val(),
								'tipo_documento':id_document.id
							};
							
							instance.fire("doCreateProspecto",dat);
						}
					}else{  
						if ( (typeof data.persona.pros_estatus=="undefined") || 
												(data.persona.pros_estatus==null)){
							/*SI EL PROSPECTO NO ESTA ENTONCES */
							instance.doViewEditProspecto(data.persona.id_nit);
							
							/* CAPTURO EL EVENTO DEL PROSPECTO SELECCIONADO */
							instance.addListener("onSelectProspecto",function(person){
								$(".finder").hide(); 
								$("#pros_rs_cliente").html(person.primer_nombre + " "+person.primer_apellido);
								$("#pros_rs_cliente").show();
								instance._prospecto_data.data=person;
								instance._prospecto_data.form_complete=true;
								//alert(person.primer_nombre+ " "+person.primer_apellido);
							});
						 
						}else{
							alert('Este cliente esta siendo prospectado y no acepta duplicados!');	
						}					
						
					}
					 /*
					if (data.existe==0){
						if (confirm("El prospecto no existe en nuestra base de datos desea proceder a registrar sus datos personales?")){
							var dat={
								'identificacion':$("#indentification").val(),
								'tipo_documento':id_document.id
							};
							
							instance.fire("doCreateProspecto",dat);
						}
					}else{
						alert(data.mensaje);
					}*/ 
				},"json");	
			}else{
				$("#indentification").focus();
				alert('Numero de cedula invalida!');	
			}
		}else{
			$("#indentification").focus();
			alert('Debe de ingresar un número de identificación!');	
		}
	},
	
	/*VALIDA LOS CAMPOS DE TEXTOS Y LISTBOX*/
	validateFormPropectacion : function(){
		$("#from_propectacion").validate({
			rules: {
				"tipo_resp_det_prospec_select[]": {
					required: true 
				},
				"tipo_resp_det_prospec_input[]": {
					required: true 
				}
			},
			messages : {
				"tipo_resp_det_prospec_select[]" : {
					required: "Este campo es obligatorio"
				},
				"tipo_resp_det_prospec_input[]": {
					required: "Este campo es obligatorio"
				}	
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	},
	
	validateForm : function(){
		$("#form_prospecto").validate({
			rules: {
				"tipos_prospectos": {
					required: true 
				} 	
			},
			messages : {
				"tipos_prospectos" : {
					required: "Este campo es obligatorio"
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
	
});