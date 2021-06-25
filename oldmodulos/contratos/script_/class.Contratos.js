var Contratos = new Class({
	dialog_container : null,
	_contrato_id : null,
	_is_inicial : false,
	_obj_monto_general : null,
	_form_name : "frm_new_actividad",
	_isCharge : false,
	_dt_prospecto : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_productos : {
		valid:false,
		idnit:0	,
		data:[]
	},
	_dt_personales : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_representante1 : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_representante1_roll_back : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_representante2 : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_representante2_roll_back : {
		valid:false,
		idnit:0	,
		data:null
	},	
	_dt_beneficiario1 : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_beneficiario1_roll_back : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_beneficiario2 : {
		valid:false,
		idnit:0	,
		data:null
	},
	_dt_beneficiario2_roll_back: {
		valid:false,
		idnit:0	,
		data:null
	},
	_totales : null,
	/*SE UTILIZA PARA FILTRAR LOS PLANES SELECCIONADOS QUE SEAN DE UN MISMO
	TIPO DE MONEDA PLAZO Y % DE ENGANCHE*/
	_plan_filtro : {
		moneda:null,
		plazo : null,
		enganche : null,
		productos : [],
		servicios : null,
		isfilter : false,
		situacion: ''  /*SI ES NECESIDAD O PRE-NECESIDAD*/
	},
	_isProspectacion : false,
	_captura_c_p: null,
	_asesor : {
		valid:false,
		idnit:0	,
		data:null		
	},
	_asesorOjb : null,
	_serie_contrato: null,
	_no_contrato : null,
	
	/*UPDATE CAMPOS NUEVOS*/
	_ct_financiamiento : null, 
	 
	initialize : function(dialog_container){
		this.main_class="Contratos";
		this.dialog_container=dialog_container; 
		this._totales= new PlanTotalDescuentos();
		
		var instance = this;
		
		this._asesorOjb =  new AsesoresTree(this.dialog_container);
		this._asesorOjb.addListener("asesor_select",function(asesor){
			instance._asesor.valid=true;
			instance._asesor.idnit=asesor.id_nit;
			instance._asesor.data=asesor;
			
			instance.post("./?mod_contratos/listar",
			{
				"contrato_data":"asesor",
				"idnit":asesor.id_nit
			},function(inf){    
			});		
			 
			//if (typeof asesor.data[0] !=="undefined"){
			$("#nombre_director").html(asesor.nombre);
			//}
			$("#nombre_gerente_g").html(asesor.nombre_gerente);			
			/*if (typeof asesor.data[1] !=="undefined"){
				
			}*/ 
			$("#nombre_asesor").html(asesor.nombre);
			$("#bt_find_asesor").html("Cambiar");
			$("#bt_add_propiedad").focus();
 
		}); 		
  
		this._captura_c_p= new Captura(this.dialog_container);
		
		this._captura_c_p.addListener("onEndCapture",function(obj){ 
			 
			if (obj._person.valid){ 	
				instance._dt_personales.valid=true;
				instance._dt_personales.idnit=obj._person.idnit;
				instance._dt_personales.data=obj._person.data; 		
				instance.draw_contratante_data(obj._person.data);  
			}else{
				instance._dt_personales.valid=false;
				instance._dt_personales.idnit="";
				instance._dt_personales.data=[]; 			
			} 
			
			$("#situacion").val('');
			$("#situacion").focus(); 
			if (obj._prospecto.valid){
				
				//pros_parentezco
				instance._isProspectacion=true;
				instance._dt_prospecto.valid=true;
				instance._dt_prospecto.data=obj._prospecto.data;
				instance._dt_prospecto.idnit=obj._prospecto.idnit;
		 
				/*CARGO LOS DATOS DEL ASESOR */
				instance._loadAsesor(obj._prospecto.idnit);
								 
				instance._asesor.valid=false;
				instance._asesor.data=[];
				instance._asesor.idnit=0;	
				
				$("#main_prospecto").show();
				instance.draw_prospecto_data(obj._prospecto.data);	
				
				if (obj._person.valid){
					if (instance._dt_prospecto.idnit==instance._dt_personales.idnit){
						//SELECCIONO PRENECESIDAD CUANDO ES PROSPECTADO UN CLIENTE
						$("#situacion").val('PRE');
						$("#situacion").change();
						$("#pros_parentezco").hide();
					}else{
						$('#prospect_parentesco').qtip({
								 show: 'focus',
								 hide: 'click',
								 style: { classes: 'qtip-green' }
							 });
						$('#prospect_parentesco').focus();	
					}
				}
			}else{
				instance._isProspectacion=false;
				instance._dt_prospecto.valid=false;
				instance._dt_prospecto.data=[];
				instance._dt_prospecto.idnit=''; 	
				$("#c_asesor").show();
				$("#main_prospecto").hide();
			}
			
		});
		
		this._captura_c_p.addListener("doNotCreatePerson",function(){
			window.location.href="./?mod_contratos/listar";		
		});
		
	},
	
	createTableViewOferta : function(table_name){
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
				"sAjaxSource": "./?mod_contratos/listar&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "contrato_numero" },
						{ "mData": "nombre_cliente" },
						{ "mData": "fecha_ingreso" },
						{ "mData": "fecha_venta" },
						{ "mData": "EM_NOMBRE" },
						{ "mData": "producto_total" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" }, 
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
						  
						//alert(oSettings.aoData[0].nTr);
					/*	for (i=0;i<oSettings.aoData.length;i++){
							 
							var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[4]).html().trim());
							 
							if (tiempo>=3 && tiempo<=5){
							//	alert($($(oSettings.aoData[i].nTr).children()[4]).html());
								oSettings.aoData[i].nTr.className="AlertColor5";
								//alert($(oSettings.aoData[i].nTr).children().length)
								//alert($($(oSettings.aoData[i].nTr).children()[5]).html());
							}
							if (tiempo>=0 && tiempo<=2){ 
								oSettings.aoData[i].nTr.className="AlertColorDanger"; 
							}
							if (tiempo<0){ 
								oSettings.aoData[i].nTr.className="AlertColorDanger"; 
								$($(oSettings.aoData[i].nTr).children()[8]).html('')
								$($(oSettings.aoData[i].nTr).children()[9]).html('')
							}
							$($(oSettings.aoData[i].nTr).children()[4]).html('<center>'+tiempo + " Dias </center>");
						}
 						*/
						if (!instance._isCharge){
							$('<button id="pro_refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter'); 
							$('<button id="contr_create" class="greenButton">Crear solicitud</button>').appendTo('div.dataTables_filter'); 
 			  
							$("#contr_create").click(function(){
								window.location.href="./?mod_contratos/listar&add_contrato=1";
							});	
							$(".edit_solicitud").click(function(){
								 
								window.location.href="./?mod_contratos/listar&edit_contrato=1&id="+$(this).attr('id');
							//	instance.doViewEditProspecto($(this).attr("id"));
							});	
							
							instance._isCharge=true;	
						}

					} 
				});	
	},
	
	createTableViewContrato : function(table_name){
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
				"sAjaxSource": "./?mod_contratos/list_contratos&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "contrato_numero" },
						{ "mData": "nombre_cliente" },
						{ "mData": "fecha_ingreso" },
						{ "mData": "fecha_venta" },												
						{ "mData": "EM_NOMBRE" },
						{ "mData": "producto_total" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" }, 
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
						//alert('fds');
						if (!instance._isCharge){
							$('<button id="pro_refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter'); 
						  
							instance._isCharge=true;	
						}						  
						$("#contr_create").click(function(){
							window.location.href="./?mod_contratos/listar&add_contrato=1";
						});	
						$(".edit_solicitud").click(function(){ 
							//window.location.href="./?mod_contratos/listar&edit_contrato=1&id="+$(this).attr('id');
							window.location.href="./?mod_contratos/list_contratos&view_contrato=1&id="+$(this).attr('id');
	
						});	
							
					

					} 
				});	
	},
	
	questionView : function(){
		var instance=this;
		this._captura_c_p.doquestionView(); 
	},
	editContratoView : function(id_nit_contratante,idnit_prospecto){
		var instance=this;	   
		var dialog=this.dialog_container;
		if (instance._captura_c_p==null){
			instance._captura_c_p= new Captura(instance.dialog_container);
		}  
		instance._captura_c_p.editContrat(dialog,id_nit_contratante,idnit_prospecto);
		
		$("#bt_find_person").click(function(){ 
			instance._captura_c_p.doquestionView(); 
		});
		 
		/*BUSQUEDA POR ASESORES*/
		$("#bt_find_asesor").click(function(){
			instance._asesorOjb.filterByAsesores();
			instance._asesorOjb.show_dialog(dialog); 
		});
  
		$("#bt_beneficiario1").click(function(){ 
		
			var obj = {
				"dialog":dialog,
				draw : function(person,data){
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					var last_beneficiario=instance._dt_beneficiario1.idnit; 
					//ALMACENO EL BENEFCIARIO ACTUAL ANTES DE ACTUALIZAR POR SI TENGO QUE 
					//REALIZAR UN ROLL BACK 
					$.extend(instance._dt_beneficiario1_roll_back,instance._dt_beneficiario1);
					
					instance._dt_beneficiario1.valid=true;
					instance._dt_beneficiario1.idnit=person.numero_documento;
					instance._dt_beneficiario1.data=data;  
					////////////////////////////////////////
					//$('#info_beneficiario1').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"beneficiario1",
						"data":instance._dt_beneficiario1.data
					},function(inf){
						//$('#info_beneficiario1').hideLoading();
						instance.draw_beneficiario("ben1","bt_beneficiario1",data); 
						$("#bt_beneficiario1").hide();
						$("#beneficiario1_save").attr("idnit",last_beneficiario);   
						$("#beneficiario1_save").show();   
						$("#beneficiario1_save").focus();
						$("#beneficiario1_cancel").show();
					});
					
				}
				
			} 
			instance.doViewBeneficiario(obj);			 
		});
		$("#beneficiario1_save").click(function(){ 
			instance.post("?mod_contratos/listar",{
				"contrato_data":"beneficiario1save",
				'last_beneficiario_nit':$("#beneficiario1_save").attr('idnit'),
				"serie_contrato":instance._serie_contrato,
				"no_contrato":instance._no_contrato
			},function(data){ 
				alert(data.mensaje); 
				if (!data.valid){
					$("#bt_beneficiario1").show();
					$("#beneficiario1_save").hide();  
					$("#beneficiario1_cancel").hide(); 
				}
			},"json")	
		});
		$("#beneficiario1_cancel").click(function(){  
			if (instance._dt_beneficiario1_roll_back.valid){
				$.extend(instance._dt_beneficiario1,instance._dt_beneficiario1_roll_back); 
				instance.draw_beneficiario("ben1","bt_beneficiario1",instance._dt_beneficiario1.data); 
				$("#bt_beneficiario1").show();
				$("#beneficiario1_save").hide();  
				$("#beneficiario1_cancel").hide(); 
			}else{
				var data={
					"id_documento":	"",
					"tipo_documento": "",
					"numero_documento":'',
					"primer_nombre": '',
					"segundo_nombre":'',
					"primer_apellido":'',
					"segundo_apellido":'',
					"fecha_nacimiento":'',
					"lugar_nacimiento":'',
					"parentesco" : '',
					"parentesco_id" :''
				};	
				instance.draw_beneficiario("ben1","bt_beneficiario1",data); 
				$("#bt_beneficiario1").show();
				$("#beneficiario1_save").hide();  
				$("#beneficiario1_cancel").hide(); 
			}
		});
		
		$("#bt_edit_beneficiario1").click(function(){
			if (instance._dt_beneficiario1.idnit!=0){
				//alert(instance._dt_beneficiario1.data.idnit);
				var obj={
					hideParentesco:false,
					draw : function(person,data){
						window.location.reload();
					}
				}
				instance.processEditBeneficiario(instance._dt_beneficiario1.data.idnit,obj); 
				
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});
		
		$("#bt_remove_beneficiario1").click(function(){   
			if (instance._dt_beneficiario1.data.id_beneficiario!=""){
				//alert(instance._dt_beneficiario1.data.idnit); 
				instance.removerBeneficiario(instance._dt_beneficiario1.data.id_beneficiario);
				
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});
		$("#bt_remove_beneficiario2").click(function(){   
			if (instance._dt_beneficiario2.data.id_beneficiario!=""){
				//alert(instance._dt_beneficiario1.data.idnit); 
				instance.removerBeneficiario(instance._dt_beneficiario2.data.id_beneficiario);
				
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});		
		
		$("#bt_beneficiario2").click(function(){
			var obj = {
				"dialog":dialog,
				draw : function(person,data){
					var last_beneficiario=instance._dt_beneficiario2.idnit; 
					//ALMACENO EL BENEFCIARIO ACTUAL ANTES DE ACTUALIZAR POR SI TENGO QUE 
					//REALIZAR UN ROLL BACK 
					$.extend(instance._dt_beneficiario2_roll_back,instance._dt_beneficiario2);
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					instance._dt_beneficiario2.valid=true;
					instance._dt_beneficiario2.idnit=person.numero_documento;
					instance._dt_beneficiario2.data=data; 
					////////////////////////////////////////
					//$('#info_beneficiario2').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"beneficiario2",
						"data":instance._dt_beneficiario2.data
					},function(inf){
						//$('#info_beneficiario2').hideLoading();
						instance.draw_beneficiario("ben2","bt_beneficiario2",data);   
						$("#bt_beneficiario2").hide();
						$("#beneficiario2_save").attr("idnit",last_beneficiario);   
						$("#beneficiario2_save").show();   
						$("#beneficiario2_save").focus();
						$("#beneficiario2_cancel").show();   
					});						
					
				}
				
			}
			instance.doViewBeneficiario(obj);
			 
		});
		$("#beneficiario2_save").click(function(){ 
			instance.post("?mod_contratos/listar",{
				"contrato_data":"beneficiario2save",
				'last_beneficiario_nit':$("#beneficiario2_save").attr('idnit'),
				"serie_contrato":instance._serie_contrato,
				"no_contrato":instance._no_contrato
			},function(data){ 
				alert(data.mensaje); 
				if (!data.valid){
					$("#bt_beneficiario2").show();
					$("#beneficiario2_save").hide();  
					$("#beneficiario2_cancel").hide(); 
				}
			},"json")	
		});
		$("#beneficiario2_cancel").click(function(){  
			if (instance._dt_beneficiario2_roll_back.valid){
				$.extend(instance._dt_beneficiario2,instance._dt_beneficiario2_roll_back); 
				instance.draw_beneficiario("ben2","bt_beneficiario2",instance._dt_beneficiario2.data); 
				$("#bt_beneficiario2").show();
				$("#beneficiario2_save").hide();  
				$("#beneficiario2_cancel").hide(); 
			}else{
				var data={
					"id_documento":	"",
					"tipo_documento": "",
					"numero_documento":'',
					"primer_nombre": '',
					"segundo_nombre":'',
					"primer_apellido":'',
					"segundo_apellido":'',
					"fecha_nacimiento":'',
					"lugar_nacimiento":'',
					"parentesco" : '',
					"parentesco_id" :''
				};	
				instance.draw_beneficiario("ben2","bt_beneficiario2",data); 
				$("#bt_beneficiario2").show();
				$("#beneficiario2_save").hide();  
				$("#beneficiario2_cancel").hide(); 
			}
		});
		
		$("#bt_edit_beneficiario2").click(function(){
				
			if ((instance._dt_beneficiario2.idnit!=0) || (instance._dt_beneficiario2.data.parentesco_id!="")){
			//	alert(instance._dt_beneficiario2.data.idnit);
				
				var obj={
					hideParentesco:true,
					draw : function(person,data){
					//	window.location.reload();
					}
				}				
				instance.processEditBeneficiarioMenor(instance._dt_beneficiario2.data,obj); 
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});
		
		instance._loadBeneficiarios();
		
		$("#bt_grl_representante").click(function(){ 
		 
			var obj = {
				"dialog":dialog,
				draw : function(person,data){
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					var last_selected=instance._dt_representante1.idnit; 
					
					//ALMACENO EL REPRESENTANTE ACTUAL ANTES DE ACTUALIZAR POR SI TENGO QUE 
					//REALIZAR UN ROLL BACK 
					$.extend(instance._dt_representante1_roll_back,instance._dt_representante1);
					  
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					instance._dt_representante1.valid=true;
					instance._dt_representante1.idnit=person.idnit;
					////////////////////////////////////////
					//$('#info_representante1').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"representante1",
						"id_nit":	instance._dt_representante1.idnit,
						"id_documento":	data.id_documento,
						"parentesco" :  data.parentesco_id,
					},function(inf){ 

						var person= new PersonalData("info_representante1","test",instance._dt_representante1.idnit);
						///CARGO LOS DATOS PERSONALES DEL CLIENTE
						person.getPersonData();
						person.addListener("personal_data_load",function(rs){
							 
							if (rs.valid){
								rs.personal.parentesco=data.parentesco; 
								instance._dt_representante1.data=rs;  
								instance.draw_representante_data("con1","bt_grl_representante",rs);  
								
								$("#bt_grl_representante").text("Cambiar");
								$("#bt_grl_representante").hide();
								$("#bt_grl_representante_save").attr("idnit",last_selected);   
								$("#bt_grl_representante_save").show();
								$("#bt_grl_representante_cancel").show();
							}else{
								alert('Error al tratar de seleccionar el cliente!');	
							}
						});
 
					});
					
				}
				
			}
			instance.drawDataForm(obj);
	 
		});		
		$("#edit_dt_representante1").click(function(){ 
			if (instance._dt_representante1.idnit!=0){
				var obj={
					hideParentesco:true,
					draw : function(person,data){
						window.location.reload();
					}
				}
				instance.processEditPersona(instance._dt_representante1.idnit,obj);
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});
		
		$("#bt_grl_representante_save").click(function(){
			instance.post("?mod_contratos/listar",{
				"contrato_data":"representante1save",
				'representante_nit':$("#bt_grl_representante_save").attr('idnit'),
				"serie_contrato":instance._serie_contrato,
				"no_contrato":instance._no_contrato
			},function(data){ 
				alert(data.mensaje); 
				if (!data.valid){
					$("#bt_grl_representante").show();
					$("#bt_grl_representante_save").hide();  
					$("#bt_grl_representante_cancel").hide(); 
				}
			},"json")	
		});
		$("#bt_grl_representante_cancel").click(function(){  
			if (instance._dt_representante1_roll_back.valid){
				$.extend(instance._dt_representante1,instance._dt_representante1_roll_back); 
				instance.draw_representante_data("con1","bt_grl_representante",instance._dt_representante1.data);
				$("#bt_grl_representante").show();
				$("#bt_grl_representante_save").hide();  
				$("#bt_grl_representante_cancel").hide(); 
			}else{ 	
				instance.clean_representante_data("con1");
				$("#bt_grl_representante").show();
				$("#bt_grl_representante_save").hide();  
				$("#bt_grl_representante_cancel").hide(); 
			}
		}); 
		
		
		$("#bt_grl_representante2").click(function(){
			
			var obj = {
				"dialog":dialog,
				draw : function(person,data){ 
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					var last_selected=instance._dt_representante2.idnit;  
					$.extend(instance._dt_representante2_roll_back,instance._dt_representante2);
					
					//AGREGO LOS DATOS DEL REPRESENTANTE
					instance._dt_representante2.valid=true;
					instance._dt_representante2.idnit=person.idnit;    
				 
					//$('#info_representante2').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"representante2",
						"id_nit":	instance._dt_representante2.idnit,
						"id_documento":	data.id_documento,
						"parentesco" :  data.parentesco_id
					},function(inf){ 
					//	instance.draw_representante_data("con2","bt_grl_representante2",data);     
						var person= new PersonalData("bt_grl_representante2","test",instance._dt_representante2.idnit);
						///CARGO LOS DATOS PERSONALES DEL CLIENTE
						person.getPersonData();
						person.addListener("personal_data_load",function(rs){ 
							$('#info_representante2').hideLoading(); 
							if (rs.valid){
								rs.personal.parentesco=data.parentesco; 
								instance._dt_representante2.data=rs;  
								instance.draw_representante_data("con2","bt_grl_representante2",rs);  
								$("#bt_grl_representante2").text("Cambiar"); 
								$("#bt_grl_representante2").hide();
								$("#bt_grl_representante_save2").attr("idnit",last_selected);   
								$("#bt_grl_representante_save2").show();
								$("#bt_grl_representante_cancel2").show();								
							}else{
								alert('Error al tratar de seleccionar el cliente!');	
							}
						});
						
					});
					
				}
				
			}
			instance.drawDataForm(obj);
			 
		});
		$("#bt_grl_representante_save2").click(function(){
			instance.post("?mod_contratos/listar",{
				"contrato_data":"representante2save",
				'representante_nit':$("#bt_grl_representante_save2").attr('idnit'),
				"serie_contrato":instance._serie_contrato,
				"no_contrato":instance._no_contrato
			},function(data){ 
				alert(data.mensaje); 
				if (!data.valid){
					$("#bt_grl_representante2").show();
					$("#bt_grl_representante_save2").hide();  
					$("#bt_grl_representante_cancel2").hide(); 
				}
			},"json")	
		});
		$("#bt_grl_representante_cancel2").click(function(){  
			if (instance._dt_representante2_roll_back.valid){
				$.extend(instance._dt_representante2,instance._dt_representante2_roll_back); 
				instance.draw_representante_data("con2","bt_grl_representante2",instance._dt_representante2.data);
				$("#bt_grl_representante2").show();
				$("#bt_grl_representante_save2").hide();  
				$("#bt_grl_representante_cancel2").hide(); 
			}else{ 	
				instance.clean_representante_data("con2");
				$("#bt_grl_representante2").show();
				$("#bt_grl_representante_save2").hide();  
				$("#bt_grl_representante_cancel2").hide(); 
			}
		});
		
		$("#edit_dt_representante2").click(function(){ 
			if (instance._dt_representante2.idnit!=0){
				var obj={
					hideParentesco:true,
					draw : function(person,data){
						window.location.reload();
					}
				}
				instance.processEditPersona(instance._dt_representante2.idnit,obj);
			}else{
				alert('No existe ningun representante asociado!');	
			}
		});		
		
		instance._loadRepresentates();		
				 
		$("#bt_add_propiedad").click(function(){ 
			instance.agregar_producto(dialog); 
		});
		instance._loadProductos();
		
		$("#bt_add_p_funerario").click(function(){
			instance.agregar_servicio(dialog);
		});		
		
		$("#empresa").change(function(){
			//$(".plan_jardin_memorial").hide();
			//$(".plan_capillas").hide(); 
			//$("#"+$(this).val()).show();
		});
			 
		$("#form_contrato").validate();
		$.validator.messages.required = "Campo obligatorio.";
		 
		/*GENERAR CONTRATO*/
		$("#bt_contrato_activar").click(function(){
			  
			if (!$("#form_contrato").valid()){
				return ;	
			}
			if ($("#forma_pago").val()==""){
				alert("Debe de seleccionar una forma de pago");
				$("#forma_pago").focus();
				return ;	
			}
 
			var valid=confirm("Esta seguro que desea activar este contrato?");
			 
			if (valid){
				var ds = { 
					"serie_contrato":instance._serie_contrato,
					"no_contrato":instance._no_contrato, 
					"forma_pago": $("#forma_pago").val(),
					"contrato_data":"activarContrato"
				};
 				instance.post("?mod_contratos/listar",ds,function(data){  
					alert(data.mensaje);
					if (data.error){ 
						window.location.href="./?mod_contratos/listar";
					} 
					
				},"json");
				
			}else{
				alert(mensaje);	
			}
			
		});

		var instance=this; 
		
		/*ANULAR SOLICITUD*/
		$("#bt_contrato_cancel").click(function(){
			instance.post("./?mod_contratos/listar&view_anular_solicitud",{ 
					"serie_contrato":instance._serie_contrato,
					"no_contrato":instance._no_contrato,
			},function(data){  
				instance.doDialog("myModal",instance.dialog_container,data);
				
				$("#doAnularSolicitud").click(function(){
					if ($("#anul_comentario").val()!=''){
						if ($("#anul_comentario").val().length>7){
							if (!confirm("Esta seguro de realizar esta operacion?")){
								return false;
							}
							instance.post("./?mod_contratos/listar&&anularSolicitud",{ 
									"serie_contrato":instance._serie_contrato,
									"no_contrato":instance._no_contrato,
									'token':instance._rand,
									'comentario':$("#anul_comentario").val(),
							},function(data){ 
								alert(data.mensaje);
								if (!data.valid){
									$("#myModal").modal('hide');
									window.location.reload();
								}
							},"json");
					
						}else{
							alert('Debe de ingresar un motivo mas claro de por que lo esta pasando a este estatus!');		
						}
					}else{
						alert('Debe de llenar el campo motivo');	
					}
				});
			});				  
 
		//	var valid=confirm("Esta seguro que desea Anular Esta solicitud?");
		//	if (valid){
			/*	var ds = { 
					"serie_contrato":instance._serie_contrato,
					"no_contrato":instance._no_contrato, 
					"contrato_data":"anularSolicitud"
				};
 				instance.post("?mod_contratos/listar",ds,function(data){  
					alert(data.mensaje);
					if (data.error){ 
						window.location.href="./?mod_contratos/listar";
					} 
					
				},"json");
				*/
			
		});
		
		
		
		$(".edit_product").click(function(){
			var product_id=$(this).attr("product_id");
			var rand = $(this).attr("id");  
			var product= new PlanProductos(dialog); 
			product.changeViewProduct(product_id,instance._serie_contrato,instance._no_contrato);
			product.addListener("onPlanProductSelect",function(data){
				
			});
			
			
		});
		
		$("#bt_imprimir").click(function(){ 
			window.open("./?mod_contratos/listar&print_oferta&id="+$(this).attr('contrato'),'mywindow','width=400,height=200,toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes')
			//window.location.href="./?mod_contratos/listar&print_oferta&id="+$(this).attr('contrato');
		});
		
		$("#bt_add_direccion").click(function(){
			var direccion= new CDireccion(instance.dialog_container); 
			direccion._direccion_id=$("#bt_add_direccion").attr("id_address");
			direccion.loadView(instance._serie_contrato,instance._no_contrato);
			
		});
		
		var doc= new CDocument(instance.dialog_container);  
		doc.document_remove();
		$("#bt_add_document").click(function(){ 
			doc.loadView(instance._serie_contrato,instance._no_contrato);
			
		});
		
		
 
	},	
	removerBeneficiario: function(beneficiario_id){
		var instance=this;
		instance.post("./?mod_contratos/listar&view_remove_window",{},function(data){ 
			instance.doDialog("doViewRemove",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				window.location.reload();
			});  
			$("#aplicar_cambio").click(function(){
				if ($("#comentario").val()!=''){
					if ($("#comentario").val().length>7){
						if (!confirm("Esta seguro de realizar esta operacion?")){
							return false;
						}
						instance.post("./?mod_contratos/listar&remover_beneficiario",{ 
								"serie_contrato":instance._serie_contrato,
								"no_contrato":instance._no_contrato,
								'beneficiario':beneficiario_id,
								'comentario':$("#comentario").val(),
						},function(data){ 
							alert(data.mensaje);
							if (!data.valid){
								$("#myModal").modal('hide');
								window.location.reload();
							}
						},"json");
				
					}else{
						alert('Debe de ingresar un motivo mas claro de por que lo esta pasando a este estatus!');		
					}
				}else{
					alert('Debe de llenar el campo motivo');	
				}
			});
		 
		});		
	},
	setContrato : function(serie_contrato,no_contrato){
		this._serie_contrato=serie_contrato;
		this._no_contrato=no_contrato;
	},
	
	_loadBeneficiarios : function(){
		var instance=this;
	 
		var beneficiario=[];
		beneficiario[0] = {
			"dialog":null,
			draw : function(person,data){
				/*AGREGO LOS DATOS DEL BENEFICIARIO*/
				instance._dt_beneficiario1.valid=true;
				instance._dt_beneficiario1.idnit=person.numero_documento;
				instance._dt_beneficiario1.data=data; 
				 
				////////////////////////////////////////
				//$('#info_beneficiario1').showLoading({'addClass': 'loading-indicator-bars'});	 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"beneficiario1",
					"data":instance._dt_beneficiario1.data
				},function(inf){
					//$('#info_beneficiario1').hideLoading();
					instance.draw_beneficiario("ben1","bt_beneficiario1",data);    
				});
				
			}
		}   
		beneficiario[1]= {
			"dialog":null,
			draw : function(person,data){
				/*AGREGO LOS DATOS DEL BENEFICIARIO*/
				instance._dt_beneficiario2.valid=true;
				instance._dt_beneficiario2.idnit=person.numero_documento;
				instance._dt_beneficiario2.data=data; 
				////////////////////////////////////////
				//$('#info_beneficiario2').showLoading({'addClass': 'loading-indicator-bars'});	 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"beneficiario2",
					"data":instance._dt_beneficiario2.data
				},function(inf){
					//$('#info_beneficiario2').hideLoading();
					instance.draw_beneficiario("ben2","bt_beneficiario2",data);      
				});						
				
			}
		}		
		
		//$('#beneficiarios_main').showLoading({'addClass': 'loading-indicator-bars'});	 
		instance.post("./?mod_contratos/listar",
		{
			"getBeneficiarios":"1",
			"serie_contrato":this._serie_contrato,
			"no_contrato":this._no_contrato
		},function(inf){
			//$('#beneficiarios_main').hideLoading();	
			for(i=0;i<inf.length;i++){		
				var data={
						"id_documento":	"",
						"tipo_documento": "",
						"idnit":inf[i].nit,
						"numero_documento":inf[i].id_nit,
						"primer_nombre": inf[i].nombre_1,
						"segundo_nombre":inf[i].nombre_2,
						"primer_apellido":inf[i].apellido_1,
						"segundo_apellido":inf[i].apelllido_2,
						"fecha_nacimiento":inf[i].fecha_nacimiento,
						"lugar_nacimiento":inf[i].lugar_nacimiento,
						"parentesco" : inf[i].parentesco,
						"parentesco_id" :inf[i].id_parentesco,
						'id_beneficiario' : inf[i].beneficiario
					}; 
				beneficiario[i].draw(data,data); 
			}
			    
		},"json");
			
	},
	
	_loadRepresentates : function(){
		var instance=this;
		var representante=[];
		representante[0] = {
			"dialog":"",
			draw : function(person,data){ 
				instance._dt_representante1.valid=true;
				instance._dt_representante1.idnit=person.idnit;
				//////////////////////////////////////// 	 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"representante1",
					"id_nit":	instance._dt_representante1.idnit,
					"id_documento":	data.id_documento,
					"parentesco" :  data.parentesco_id,
				},function(inf){ 
				
					var person= new PersonalData("info_representante1","test",instance._dt_representante1.idnit);
					///CARGO LOS DATOS PERSONALES DEL CLIENTE
					person.getPersonData();
					person.addListener("personal_data_load",function(rs){ 
						$('#info_representante1').hideLoading();
						
						if (rs.valid){
							rs.personal.parentesco=data.parentesco; 
							instance._dt_representante1.data=rs;  
							instance.draw_representante_data("con1","bt_grl_representante",rs);  
							$("#bt_grl_representante").text("Cambiar");
							$("#bt_grl_representante").show();
						}else{
							alert('Error al tratar de seleccionar el cliente!');	
						}
					});

				});
				
			}
			
		}	 
		representante[1]  = {
				"dialog":"",
				draw : function(person,data){ 
					//AGREGO LOS DATOS DEL REPRESENTANTE
					instance._dt_representante2.valid=true;
					instance._dt_representante2.idnit=person.idnit;   
					 
					//$('#info_representante2').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"representante2",
						"id_nit":	instance._dt_representante2.idnit,
						"id_documento":	data.id_documento,
						"parentesco" :  data.parentesco_id
					},function(inf){ 
					 
						var person= new PersonalData("bt_grl_representante2","test",instance._dt_representante2.idnit);
						///CARGO LOS DATOS PERSONALES DEL CLIENTE
						person.getPersonData();
						person.addListener("personal_data_load",function(rs){ 
						//	$('#info_representante2').hideLoading(); 
							if (rs.valid){
								rs.personal.parentesco=data.parentesco; 
								instance._dt_representante2.data=rs;  
								instance.draw_representante_data("con2","bt_grl_representante2",rs);  
								$("#bt_grl_representante2").text("Cambiar");
								$("#bt_grl_representante2").show();
							}else{
								alert('Error al tratar de seleccionar el cliente!');	
							}
						});
						
					});
					
				}
				
			}
		  
		instance.post("./?mod_contratos/listar",
		{
			"getRepresentantes":"1",
			"serie_contrato":this._serie_contrato,
			"no_contrato":this._no_contrato
		},function(inf){ 	 
			for(i=0;i<inf.length;i++){		
				var data={
						"idnit":inf[i].idnit,
						"id_documento":"",
						"parentesco_id" : inf[i].id_parentesco,
						"parentesco": inf[i].parentesco
					};
				if (representante[i]!=null){	
					representante[i].draw(data,data);
				}
				
			}
		},"json");
		
	},
	
	_loadProductos : function(){
		var instance=this;
		 /*
		$.post("./?mod_contratos/listar",
		{
			"getListProducts":"1",
			"serie_contrato":this._serie_contrato,
			"no_contrato":this._no_contrato
		},function(inf){ 	
 
			var product= new PlanProductos();
			product.setProductData(inf);
 
		},"json");*/
		
	},
	
	_loadAsesor : function(id_nit){
		var instance=this;   
		instance.post("./?mod_contratos/listar",
		{
			"getAsesor":"1",
			"id_nit":id_nit
		},function(asesor){   
			if (asesor[1]){
			//	$("#nombre_director").html(asesor[0].nombre +' '+asesor[0].apellido); 
				$("#nombre_gerente_g").html(asesor[1].nombre +' '+asesor[1].apellido);
				$("#nombre_asesor").html(asesor[0].nombre +' '+asesor[0].apellido);
			}
			//$("#bt_find_asesor").hide();
			$("#c_asesor").show();
		},"json");
		
	},
	
	/*VISTA QUE MANEJA LA CREACION DE SOLICITUD CONTRATOS*/	
	createView : function(prospectacion){
		var instance=this;	 
		
		if (prospectacion=="true"){
			this._isProspectacion=true;
		}

		var dialog=this.dialog_container;
 
		/*BUSQUEDA POR ASESORES*/
		$("#bt_find_asesor").click(function(){
			instance._asesorOjb.filterByAsesores();
			instance._asesorOjb.show_dialog(dialog); 
		});
  
		$("#bt_find_person").click(function(){
			if (instance._captura_c_p==null){
				instance._captura_c_p= new Captura(instance.dialog_container);
			}
			instance._captura_c_p.doquestionView();
			
		});
				
		$("#bt_beneficiario1").click(function(){ 
			var obj = {
				"dialog":dialog,
				draw : function(person,data){ 
					
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					instance._dt_beneficiario1.valid=true;
					instance._dt_beneficiario1.idnit=person.numero_documento;
					instance._dt_beneficiario1.data=data; 
					////////////////////////////////////////
					//$('#info_beneficiario1').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"beneficiario1",
						"data":instance._dt_beneficiario1.data
					},function(inf){
						//$('#info_beneficiario1').hideLoading();
						$("#bt_beneficiario2").focus();
						instance.draw_beneficiario("ben1","bt_beneficiario1",data);    
					});
					
				}
				
			} 
			instance.doViewBeneficiario(obj);			 
		});
		
		$("#bt_beneficiario2").click(function(){
			var obj = {
				"dialog":dialog,
				draw : function(person,data){
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					instance._dt_beneficiario2.valid=true;
					instance._dt_beneficiario2.idnit=person.numero_documento;
					instance._dt_beneficiario2.data=data; 
					////////////////////////////////////////
					//$('#info_beneficiario2').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"beneficiario2",
						"data":instance._dt_beneficiario2.data
					},function(inf){
						//$('#info_beneficiario2').hideLoading();
						$("#bt_grl_representante").focus();
						instance.draw_beneficiario("ben2","bt_beneficiario2",data);      
					});						
					
				}
				
			}
			instance.doViewBeneficiario(obj);
			 
		});
		 
		$("#bt_grl_representante").click(function(){
			var obj = {
				"dialog":dialog,
				draw : function(person,data){
					/*AGREGO LOS DATOS DEL REPRESENTANTE*/
					instance._dt_representante1.valid=true;
					instance._dt_representante1.idnit=person.idnit;
					////////////////////////////////////////
					//$('#info_representante1').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"representante1",
						"id_nit":	instance._dt_representante1.idnit,
						"id_documento":	data.id_documento,
						"parentesco" :  data.parentesco_id,
					},function(inf){ 
					
						var person= new PersonalData("info_representante1","test",instance._dt_representante1.idnit);
						///CARGO LOS DATOS PERSONALES DEL CLIENTE
						person.getPersonData();
						person.addListener("personal_data_load",function(rs){
							
							$('#info_representante1').hideLoading();
							if (rs.valid){
								rs.personal.parentesco=data.parentesco; 
								instance._dt_representante1.data=rs;  
								instance.draw_representante_data("con1","bt_grl_representante",rs);  
								$("#bt_grl_representante").text("Cambiar");
								$("#bt_grl_representante").show();
								$("#bt_grl_representante2").focus();
							}else{
								alert('Error al tratar de seleccionar el cliente!');	
							}
						});
 
					});
					
				}
				
			}
			instance.drawDataForm(obj);
	 
		});
		
		$("#bt_grl_representante2").click(function(){
			
			var obj = {
				"dialog":dialog,
				draw : function(person,data){ 
					//AGREGO LOS DATOS DEL REPRESENTANTE
					instance._dt_representante2.valid=true;
					instance._dt_representante2.idnit=person.idnit;   
					
				 
					//$('#info_representante2').showLoading({'addClass': 'loading-indicator-bars'});	 
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"representante2",
						"id_nit":	instance._dt_representante2.idnit,
						"id_documento":	data.id_documento,
						"parentesco" :  data.parentesco_id
					},function(inf){ 
					//	instance.draw_representante_data("con2","bt_grl_representante2",data);     
						var person= new PersonalData("bt_grl_representante2","test",instance._dt_representante2.idnit);
						///CARGO LOS DATOS PERSONALES DEL CLIENTE
						person.getPersonData();
						person.addListener("personal_data_load",function(rs){ 
							$('#info_representante2').hideLoading(); 
							if (rs.valid){
								rs.personal.parentesco=data.parentesco; 
								instance._dt_representante2.data=rs;  
								instance.draw_representante_data("con2","bt_grl_representante2",rs);  
								$("#bt_grl_representante2").text("Cambiar");
								$("#bt_grl_representante2").show();
							}else{
								alert('Error al tratar de seleccionar el cliente!');	
							}
						});
						
						$("#bt_find_asesor").focus();
						
					});
					
				}
				
			}
			instance.drawDataForm(obj);
			 
		});
				 
		$("#bt_add_propiedad").click(function(){
			if (instance._ct_financiamiento!=null){
				instance.agregar_producto2(dialog); 
			}else{
				alert('Debe de seleccionar el financiamiento!');	
				$("#bt_ch_financiamiento").focus();
			}
		});
		
		$("#bt_add_p_funerario").click(function(){
			if (instance._ct_financiamiento!=null){
				instance.agregar_servicio2(dialog);
			}else{
				alert('Debe de seleccionar el financiamiento!');	
				$("#bt_ch_financiamiento").focus();
			}
		});		
		
		$("#empresa").change(function(){
		//	$(".plan_jardin_memorial").hide();
		//	$(".plan_capillas").hide(); 
		//	$("#"+$(this).val()).show();
			//console.log($(this).val());
		});
		
		$("#situacion").change(function(){ 
			if ($(this).val()!=""){
				instance.post("?mod_contratos/listar",{
					request:1,
					control:"listEmpresa",
					value:$(this).val()
				},function(data){ 
					$("#crtl_empresa").html(data);
					
				},"text");
			}
		});
		
		$("#bt_ch_financiamiento").click(function(){ 
			var finan= new PlanesFinanciamiento(instance.dialog_container);
			finan.addListener("onSelectPlanGrup",function(obj){
				instance._ct_financiamiento=obj;
				instance._plan_filtro=obj;
				
				instance.post("./?mod_contratos/listar",{
						"process_add_finaciamiento":'1', 
						"financiamiento": $.base64.encode(JSON.stringify(obj)), 
					},function(data){   
						if (data.valid){ 
							$("#pln_moneda").html(obj.moneda);
							$("#pln_plazo").html(obj.plazo);
							$("#pln_enganche").html(obj.enganche);
						}else{
							$("#pln_moneda").html('');
							$("#pln_plazo").html('');
							$("#pln_enganche").html('');							
							alert(data.mensaje);
						}
					
				 },"json");	 
				
				setTimeout(function(){ window.location="#ch_financiamiento"  },50);
				
			});
			finan.viewGroupPlanList();
		});
		
		$("#bt_contrato_generar").hide();	

		$("#fecha_primer_pago").datepicker({
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
		$("#fecha_venta").datepicker({
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
		var fecha_primer_pago="";
		var fecha_venta="";
		$("#fecha_venta").change(function(){
			fecha_venta=$(this).val();	
		});
				
		$("#fecha_primer_pago").change(function(){
			fecha_primer_pago=$(this).val();	
		});		
		/*GENERAR CONTRATO*/
		$("#bt_contrato_generar").click(function(){
			var valid=true;
			var mensaje="";
 			  
			if (instance._obj_monto_general.monto_enganche>instance._obj_monto_general.monto_pago_caja){
				$("#alert_monto").show();
				valid=false;
				mensaje="El inicial es menor";				
			} 

			if (!instance._dt_representante1.valid){
				valid=false;
				mensaje="Debe de seleccionar el Representante #1";
			}	
			 		
			/*							 
			if (fecha_primer_pago==""){
				valid=false;
				mensaje="Debe de seleccinar la fecha de primer pago!";
			}*/
			/*
			fecha_venta=$("#fecha_venta").val();
			if (fecha_venta==""){
				valid=false;
				mensaje="Debe de seleccionar la fecha de venta!";
			}	*/			

			if (!instance._dt_personales.valid){
				valid=false;
				mensaje="Debe de seleccionar un contratante";
			}

			if (instance._isProspectacion){ 
				if (!instance._dt_prospecto.valid){
					valid=false;
					mensaje="Debe de seleccionar un prospecto";
				}
			}
			if (instance._isProspectacion){ 
				/*
				if ($.trim($("#parentesco").val())==""){
					valid=false;
					mensaje="Debe de seleccionar un parentesco";	
					$("#parentesco").focus();
				}*/
			} 
			
			if (!instance._isProspectacion){ 
				if (!instance._asesor.valid){
					valid=false;
					mensaje="Debe de seleccionar un asesor";
				}
			}
			
			if ($.trim($("#no_contrato").val())==""){
				valid=false;
				mensaje="Debe de ingesar un Numero de contrato";	
				$("#no_contrato").focus();
			}			
			if ($.trim($("#serie_contrato").val())==""){
				valid=false;
				mensaje="Debe de ingesar la serie del contrato";	
				$("#serie_contrato").focus();
			}				
			if ($.trim($("#empresa").val())==""){
				valid=false;
				mensaje="Debe de seleccionar una Empresa";	
				$("#empresa").focus();
			}
			if ($.trim($("#situacion").val())==""){
				valid=false;
				mensaje="Debe de seleccionar la situacion de la solicitud!";	
				$("#situacion").focus();
			}			
 
			 
			if (valid){
				var ds = {
					"situacion":$("#situacion").val(),
					"empresa":$("#empresa").val(),
					"observaciones":$("#observaciones").val(),
					"serie_contrato":$("#serie_contrato").val(),
					"no_contrato":$("#no_contrato").val(),
					"prosp_parentesco":$("#prospect_parentesco").val(),
					"contrato_data":"1",
					"fecha_primer_pago":fecha_primer_pago,
					"fecha_venta":fecha_venta,
					"submit":true
				}; 	
				instance.post("?mod_contratos/listar",ds,function(data){  
					alert(data.mensaje);
					if (!data.error){ 
						window.location.href="./?mod_contratos/listar";		
					} 
					
				},"json");
				
			}else{
				alert(mensaje);	
			}
			
		});
		
		$("#bt_contrato_cancel").click(function(){
			window.location.href="./?mod_contratos/listar";		
		});
		
		var view_abono= new Facturar(this.dialog_container); 
		view_abono.setToken(this.getRand());
		/*MCuando seleccione un monto de los abonados refresca la vista*/
		view_abono.addListener("onSelectMontoCaja",function(data){
			$("#detalle_caja").html(data);
			instance.fire("ReloadMontoData");
		});
		/*LISTADO DE ABONO X CAJA*/
		$("#bt_caja_add_pagos").click(function(){
			/*VISTA QUE MUESTRA LO QUE UN CLIENTE TIENE ABONADO EN CAJA*/
			if (instance._dt_personales.valid){
				if (instance._ct_financiamiento!=null){
					view_abono.viewAbonoPerson(instance._dt_personales.idnit,instance._ct_financiamiento);
				}else{
					alert('Debe de seleccionar el financiamiento!');	
					$("#bt_ch_financiamiento").focus();
				}				
			}else{
				alert('Debe de seleccionar un contratante!');	
			}
		});
		
		
		var dfin= new PreFacturar(this.dialog_container); 
		dfin.setToken(this.getRand());
		/*MCuando seleccione un monto de los abonados refresca la vista*/
		dfin.addListener("onSelectMontoCaja",function(data){
			$("#detalle_caja").html(data);
			instance.fire("ReloadMontoData");
		});			
		/*LISTADO DE ABONO X CAJA*/
		$("#bt_datos_financieros").click(function(){
			/*VISTA QUE MUESTRA LO QUE UN CLIENTE TIENE ABONADO EN CAJA*/
			if (instance._dt_personales.valid){
				var contrato=$("#serie_contrato").val()+" "+$("#no_contrato").val();
				dfin.doView(instance._dt_personales.idnit,contrato);
			}else{
				alert('Debe de seleccionar un contratante!');	
			}
		});			
		
		
		/*AGREGAR DESCUENTO X MONTO*/
		$("#bt_c_descuento_x_monto").click(function(){
			var descuentos= new ContratoDescuento(instance.dialog_container);
			descuentos.createView(instance._plan_filtro.moneda,instance._plan_filtro.situacion,'MONTO');
			descuentos.addListener("onSelectDiscount",function(desc){
				instance.post("./?mod_contratos/listar",{
						"view_carrito_main":'1',
						"process_descuento_add":'1',
						"descuento": $.base64.encode(JSON.stringify(desc)),
						"token": instance._rand ,
						'type':'MONTO'
					},function(data){ 
						$("#descuento_x_monto").html(data);
						instance.doRemoveMonto('MONTO','descuento_x_monto'); 
						
				 });	   
			});	
		});
		/*AGREGAR DESCUENTO X PORCIENTO*/
		$("#bt_c_descuento_x_porciento").click(function(){ 
			var descuentos= new ContratoDescuento(instance.dialog_container);
			descuentos.createView(instance._plan_filtro.moneda,instance._plan_filtro.situacion,'PORCIENTO');
			descuentos.addListener("onSelectDiscount",function(desc){
				instance.post("./?mod_contratos/listar",{
						"view_carrito_main":'1',
						"process_descuento_add":'1',
						"descuento": $.base64.encode(JSON.stringify(desc)),
						"token": instance._rand,
						'type':'PORCIENTO'
					},function(data){ 
						$("#descuento_x_prociento").html(data);
						instance.doRemoveMonto('PORCIENTO','descuento_x_prociento'); 
				 });	   
			});	
		});	
 
	}, 
	/*METODO QUE ELIMINA LOS DESCUENTO DEL CONTRATO*/
	doRemoveMonto : function(type,tb_name){
		var instance=this;
	//	var carro = new Carrito(null);
	//	carro.calcularMontoGeneral();		
		instance.fire("ReloadMontoData");	
		$(".bt_remove_monto").click(function(){ 
			instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"process_descuento_remove":'1',
				"index": $(this).attr("id"), 
				'type':type 
			},function(data){ 
				$("#"+tb_name).html(data);
				instance.doRemoveMonto(type,tb_name); 
			});	
									
		});
	}, 	
	validar_plan_if_select : function(){
		if (this._plan_filtro!=null){
			if ((this._plan_filtro.moneda!="") && (this._plan_filtro.plazo!="") && (this._plan_filtro.enganche!="")){
				return true;
			}
		}
		alert('Debe de seleccionar un plan');
		setTimeout(function(){ window.location="#ch_financiamiento"  },50);
		return false;
	},
	
	agregar_producto2 : function(dialog){
		var instance = this;
		
		if ($("#situacion").val()==""){
			alert('Debe de agregar una situacion a la oferta');
			$("#situacion").focus();
			return ;	
		}
		if (!this.validar_plan_if_select()){
			return ;
		}
		
		var _token=this.getRand();
		
		var carro = new Carrito(dialog);	
		var producto = new CProducto(dialog);
	 
		var financiamiento= new CFinanciamiento(dialog,producto);
		var detalle= new CDetalle(dialog,financiamiento); 
		
		carro.addListener("doRenderProduct",function(html){
			$("#listado_productos").append(html);  
			carro.doCarEditView(carro._rand,logic,"Editar producto");
		});
		
		instance.addListener("ReloadMontoData",function(){
			carro.calcularMontoGeneral();
		});
		
		/*VERIFICA SI EL MONTO DE CUOTAS ABONADAS SEA MAYOR IGUAL AL 10% DEL MONTO DE ENGANCHE*/
		carro.addListener("MontoGeneral",function(data){
			instance._obj_monto_general=data; 
 			if (!instance._obj_monto_general.doProcesarSolicitud){
				$("#alert_monto_mensaje").html(instance._obj_monto_general.doMensaje);
				$("#alert_monto").show(); 
				$("#bt_contrato_generar").hide();	 
			}else{ 
				$("#bt_contrato_generar").show();	
				$("#alert_monto").hide(); 		
			}
		});
		
		var logic={
			doLogic : function(carrito_obj){
				producto.doChargeModulo("template_producto",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				/*CUANDO CAMBIE O AGREGE UN PRODUCTO QUE ACTUALIZE TODAS LAS VISTAS*/
				producto.addListener("OnProductoSelect",function(){
					carrito_obj.calcularMontoGeneral();
					detalle.doReloadData();
					if (carrito_obj._type=="edit"){
						//financiamiento.cleanView();  
					}
				});
				financiamiento.doChargeModulo("template_plan",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				/*Refresca la vista principal cuando realizan algun cambio!*/
				financiamiento.addListener("OnPlanChange",function(){
					carrito_obj.calcularMontoGeneral();
				}); 
				
				detalle.doChargeModulo("template_detalle",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				detalle.addListener("OnRenderDetalle",function(){
					if (carrito_obj._type!="edit"){
						carrito_obj.enableButtom();
					}
				});
				/*Refresca la vista principal cuando realizan algun cambio!*/
				detalle.addListener("doDetailChange",function(){
					carrito_obj.calcularMontoGeneral();
				});
				 
			}
		}	
		 
		if (instance._dt_prospecto.valid){  
			producto.setIDNit(instance._dt_prospecto.idnit);  
		}else{ 
			producto.setIDNit(instance._dt_personales.idnit); 
		}		
		this._plan_filtro.situacion=$("#situacion").val(); 
		
		carro.doCarView(_token,logic,"Agregar producto"); 
	},
	
	agregar_servicio2 : function(dialog){
		var instance = this;
		if ($("#situacion").val()==""){
			alert('Debe de agregar una situacion a la oferta');
			$("#situacion").focus();
			return ;	
		}	
		if (!this.validar_plan_if_select()){
			return ;
		}	
		var carro = new Carrito(dialog);	
		var producto = new CServicio(dialog);
		var financiamiento= new CFinanciamiento(dialog,producto);
		var detalle= new CDetalle(dialog,financiamiento); 

		/*VERIFICA SI EL MONTO DE CUOTAS ABONADAS SEA MAYOR IGUAL AL 10% DEL MONTO DE ENGANCHE*/
		carro.addListener("MontoGeneral",function(data){
			instance._obj_monto_general=data;
			if (!instance._obj_monto_general.doProcesarSolicitud){
				$("#alert_monto_mensaje").html(instance._obj_monto_general.doMensaje);
				$("#alert_monto").show(); 
				$("#bt_contrato_generar").hide();	
			}else{ 
				$("#bt_contrato_generar").show();
				$("#alert_monto").hide(); 		
			}
		});

		instance.addListener("ReloadMontoData",function(){
			carro.calcularMontoGeneral();
		});				 
		var logic={
			doLogic : function(carrito_obj){
				producto.doChargeModulo("template_producto",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				/*CUANDO CAMBIE O AGREGE UN PRODUCTO QUE ACTUALIZE TODAS LAS VISTAS*/
				producto.addListener("OnProductoSelect",function(){
					carrito_obj.calcularMontoGeneral();
					detalle.doReloadData();
					if (carrito_obj._type=="edit"){
					//	financiamiento.cleanView(); 						
						//carro.calcularMontoGeneral();
					}
				});
				financiamiento.doChargeModulo("template_plan",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				/*Refresca la vista principal cuando realizan algun cambio!*/
				financiamiento.addListener("OnPlanChange",function(){
					carrito_obj.calcularMontoGeneral();
				});
								
				detalle.doChargeModulo("template_detalle",instance._plan_filtro,carrito_obj._rand,carrito_obj._type);
				detalle.addListener("OnRenderDetalle",function(){
					if (carrito_obj._type!="edit"){
						carrito_obj.enableButtom();
					}
				});
				/*Refresca la vista principal cuando realizan algun cambio!*/
				detalle.addListener("doDetailChange",function(){
					carrito_obj.calcularMontoGeneral();
				});
								
			}
		}	
		carro.addListener("doRenderProduct",function(html){
			$("#listado_servicios").append(html);  
			carro.doCarEditView(carro._rand,logic,"Editar servicio");
		}); 
				  
		if (instance._dt_prospecto.valid){  
			producto.setIDNit(instance._dt_prospecto.idnit);  
		}else{ 
			producto.setIDNit(instance._dt_personales.idnit); 
		}		
		this._plan_filtro.situacion=$("#situacion").val(); 
		carro.doCarView(this.getRand(),logic,"Agregar servicio"); 
	},
		
	/*Funcion que agrega un producto a una orden*/
	agregar_producto : function(dialog){
		var instance=this;
		var product= new PlanProductos(dialog);
	//	product._sl_financiamiento=this._ct_financiamiento; 
		/*SELECCIONO SI ES NECESIDAD O PRE-NECESIDAD*/
		this._plan_filtro.situacion=$("#situacion").val();
		/*Valido si ha elegido un financiamiento*/
		var valid=true;
		if (instance._ct_financiamiento==null){
			valid=false;
			alert('Debe de elegir el Financiamiento!');	
		}
		if (this._plan_filtro.situacion==""){
			valid=false;
			$("#situacion").focus();			
			alert('Debe de elegir una situacion!');	
		}
		
		if (valid){
			if (instance._dt_prospecto.valid){  
				product.setIDNit(instance._dt_prospecto.idnit);  
			}
			//alert(instance._plan_filtro.moneda)
			product.loadView(this._plan_filtro);
			product.addListener("onPlanProductSelect",function(data){
				instance._dt_productos.valid=true;
				instance._dt_productos.data.push(product);
				 
				var inf = {
					"producto":data.data._producto[product._randViewID].data,
					"financiamiento":data.data._financiamiento[product._randViewID].data, 
					"descuento":product._descuento[product._randViewID],
					"iniciales":data.data.info_inicial
				}
				
				instance.calcular_detalle(data);
				
				if (!instance._plan_filtro.isfilter){
					instance._plan_filtro.plazo=product._totales_plan.plazo;
					instance._plan_filtro.moneda=product._totales_plan.moneda;
					instance._plan_filtro.enganche=product._totales_plan.enganche;				
					instance._plan_filtro.isfilter=true;
				} 
				
			//	instance._plan_filtro.productos.push(product._producto[product._randViewID].data.product_id);
			 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"producto",
					"data":inf
				},function(inf){
					//$('#listado_productos').hideLoading();
					$("#listado_productos").append(data.html);   
					 product.captureEdit(instance._plan_filtro);
				});	
				$("#observaciones").focus();
				instance._totales.fire("onChangeData");
			});
			
			product.addListener("onPlanProductChange",function(data){
				
				var plan_list;
				
				for(i=0;i<instance._dt_productos.data.length;i++){	
					plan_list=totales=instance._dt_productos.data[i];
					if (plan_list._randViewID==data.data._randViewID){
						instance._dt_productos.data[i]=data.data;		
					}	
				}
			
				//$('#listado_productos').showLoading({'addClass': 'loading-indicator-bars'});	
				
				var inf = {
					"producto":product._producto[product._randViewID].data,
					"financiamiento":product._financiamiento[product._randViewID].data, 
					"descuento":product._descuento[product._randViewID]
				}
				
				instance.calcular_detalle(data);
				
				if (!instance._plan_filtro.isfilter){
					instance._plan_filtro.plazo=product._totales_plan.plazo;
					instance._plan_filtro.moneda=product._totales_plan.moneda;
					instance._plan_filtro.enganche=product._totales_plan.enganche;				
					instance._plan_filtro.isfilter=true;
				} 
				/*REMUEVO EL PRODUCTO PARA CAMBIAR LO POR EL NUEVO*/
				var product_id;
				for(i=0;i<instance._plan_filtro.productos.length;i++){	
					if (instance._plan_filtro.productos[i]==product._producto[product._randViewID].data.product_id){
						delete instance._plan_filtro.productos[i];
						instance._plan_filtro.productos.filter(function(a){return typeof a !== 'undefined';});
					}
	
				}
				
				//instance._plan_filtro.productos.push(product._producto[product._randViewID].data.product_id);
			 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"producto_edit",
					"data":inf
				},function(inf){
					//$('#listado_productos').hideLoading();
					$("#listado_productos").append(data.html);  
					 product.captureEdit(instance._plan_filtro);
				});	
				
				instance._totales.fire("onChangeData");
			});
			
			product.addListener("onRefreshView",function(data){
				instance.calcular_detalle(data); 
				$("#listado_productos").append(data.html);  
				product.captureEdit(instance._plan_filtro);
			});
		}
	},
	/*Funcion que agrega un servicio a una orden*/
	agregar_servicio: function(dialog){
		var instance=this;
		var product= new PlanServicios(dialog);
		/*SELECCIONO SI ES NECESIDAD O PRE-NECESIDAD*/
		this._plan_filtro.situacion=$("#situacion").val(); 	
		var valid=true;
		if (instance._ct_financiamiento==null){
			valid=false;
			alert('Debe de elegir el Financiamiento!');	
		}
		if (this._plan_filtro.situacion==""){
			valid=false;
			$("#situacion").focus();			
			alert('Debe de elegir una situacion!');	
		}
		
		if (valid){
			product.loadView(this._plan_filtro);
			product.addListener("onPlanServicioSelect",function(data){
				instance._dt_productos.valid=true;
				instance._dt_productos.data.push(data.data);
				//$('#listado_servicios').showLoading({'addClass': 'loading-indicator-bars'});
				//alert(instance._descuento[instance._randViewID].monto.length);	 
				var inf = {
					"servicio":product._producto[product._randViewID].data,
					"financiamiento":product._financiamiento[product._randViewID].data, 
					"descuento":product._descuento[product._randViewID]
				}
				instance.calcular_detalle(data);
				
				if (!instance._plan_filtro.isfilter){
					instance._plan_filtro.plazo=product._totales_plan.plazo;
					instance._plan_filtro.moneda=product._totales_plan.moneda;
					instance._plan_filtro.enganche=product._totales_plan.enganche;				
					instance._plan_filtro.isfilter=true;
				} 
				//instance._plan_filtro.productos.push(product._producto[product._randViewID].data.servicio_id);
				 
				instance.post("./?mod_contratos/listar",
				{
					"contrato_data":"servicio",
					"data":inf
				},function(inf){
					//$('#listado_servicios').hideLoading();
					$("#listado_servicios").append(data.html); 
					product.captureEdit(instance._plan_filtro);
				});	
				
				instance._totales.fire("onChangeData");
			});
		} 
	},
	
	calcular_detalle : function(data){
		var instance=this;
		 
		this._totales.TotalDescuentoMonto=0;
		this._totales.capital_total_pagar=0;
		this._totales.monto_enganche=0;
		this._totales.total_capitar_financiar=0;
		this._totales.capital_cuota=0;
		this._totales.precio_lista_menos_TotalDescuentoMonto=0;
		this._totales.total_interes_monto_anual=0;
		this._totales.total_interes_a_pagar=0;
		this._totales.mensualidades=0;
		this._totales.sub_total_a_pagar=0;
		this._totales.total_a_pagar=0; 
		
		var totales;
		for(i=0;i<instance._dt_productos.data.length;i++){
			 
			totales=instance._dt_productos.data[i]._totales_plan;
			
			this._totales.TotalDescuentoMonto=this._totales.TotalDescuentoMonto+ totales.TotalDescuentoMonto;
														
			this._totales.capital_total_pagar=this._totales.capital_total_pagar+ totales.capital_total_pagar;
	
			this._totales.monto_enganche=this._totales.monto_enganche+ totales.monto_enganche;													
											
			this._totales.total_capitar_financiar=this._totales.total_capitar_financiar+ totales.total_capitar_financiar;	
													
			this._totales.capital_cuota=this._totales.capital_cuota+ totales.capital_cuota;	
													
			this._totales.precio_lista_menos_TotalDescuentoMonto=this._totales.precio_lista_menos_TotalDescuentoMonto+
																totales.precio_lista_menos_TotalDescuentoMonto;
																
																	
			this._totales.total_interes_monto_anual=this._totales.total_interes_monto_anual+
																totales.total_interes_monto_anual;
																
			this._totales.total_interes_a_pagar=this._totales.total_interes_a_pagar+
																totales.total_interes_a_pagar;
	
			this._totales.mensualidades=this._totales.mensualidades+
																totales.mensualidades;
	 
			this._totales.sub_total_a_pagar=this._totales.sub_total_a_pagar+
																totales.sub_total_a_pagar;
																
			this._totales.precio_lista=this._totales.precio_lista+
																totales.precio_lista;
			
			this._totales.total_a_pagar=this._totales.total_a_pagar+
													totales.total_a_pagar;		
													
														
																
			//alert(instance._dt_productos.data[i])
		}
																										
	},
	
	/*CARGA EL MENU DE LISTAR PERSONA*/
	drawDataForm : function(obj){
		var instance=this;
		var action={
			//Quiero agregarlo!
			doAdd : function(data){ 
				instance.processCreatePerson(data,obj);
			},
			//No deseo agregar el cliente
			doNotAdd : function(data){
				
			},
			//Si el cliente existe
			onClientExist : function(data){ 
				if (!(data.personal.id_nit==instance._dt_personales.idnit)){
					instance.processEditPersona(data.personal.id_nit,obj);
				}else{
					alert('El contratante no puede ser Representante!');	
				}			
			}
		};
		
		this.finderView('Buscar',action);
  
	},
	
	processCreatePerson : function(data,obj){
		var instance=this;
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container); 
		var person= new Persona(person_component);
		//PONGO EL MODULO DE PERSONA EN MODO EDITAR
		person.setView("create"); 
		//********************************************
		person.addListener("cancel_creation",function(){
			person_component.closeView();
		}); 
		//Capturar cuando la vista ha sido creada
		person.addListener("onViewCreate",function(){
			$("#numero_documento").val(data.numero_documento);			  
			$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
			person.showParentesco();
		});
		
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreatePerson",function(data){ 
			data.parentesco_id=$("#parentesco option:selected").val();
			person_component.closeView();
			obj.draw(data,data); 
		});
		person_component.addModule(person); 
	 	person_component.loadMainView();
	},
	
	finderView : function(title,oAction){
		var instance=this;
 		instance.post("./?mod_contratos/listar",
		{
			"view_search":"1" 
		},function(data){
			//$('#'+instance.dialog_container).hideLoading();
			var dialog=instance.createDialog(instance.dialog_container,title,data,420);
   
			$("#_cancel").click(function(){ 
				instance.close(dialog);
			});
			 
			$("#id_documento").change(function(){
				var type_document=$('#id_documento option:selected').text();
				if ($("#id_documento").val().trim()!=""){
					$("#numero_documento").prop('disabled', false);
				}else{
					$("#numero_documento").prop('disabled', true);
				}
			});
			 
			$("#_buscar").click(function(){ 
				var valid_field=true;
				var type_document=$('#id_documento option:selected').text();
				if (type_document.trim()=="CEDULA"){					
					valid_field=valida_cedula($("#numero_documento").val());
				}
				if (type_document==""){valid_field=false;}
				
				if (($("#numero_documento").val().length>=7) && (valid_field)){
					//$('#'+dialog).showLoading({'addClass': 'loading-indicator-bars'});
					
					instance.post("./?mod_contratos/listar",{validarPersona:"1","numero_documento":$("#numero_documento").val(),"tipo_documento":$("#id_documento").val()},function(data){	
						//$('#'+dialog).hideLoading();
		
						if (data.addnew){
							var info={"numero_documento":$("#numero_documento").val(),"tipo_documento":$("#id_documento").val()};
							instance.close(dialog); 
							/*SI EL # DE IDENTIFICACION NO EXISTE*/
							var ifdata='<br><center><strong><p>Este numero de identificacion no existe en nuestra base de datos.</p> <p> Desea Agregarlo?</p> </strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
							
							dialog=instance.createDialog(instance.dialog_container,title,ifdata,420);
						  
							$("#caputra_si").click(function(){ 
								instance.close(dialog);
								if (oAction!=null){
									if (typeof oAction.doAdd=="function"){
										oAction.doAdd(info);
									}
								} 
							});
							$("#captura_no").click(function(){
								instance.close(dialog);
								//EVENTO SE DISPARA CUANDO SELECCIONAN QUE NO EN LA RESPUESTA
								if (oAction!=null){
									if (typeof oAction.doNotAdd=="function"){
										oAction.doNotAdd(data);
									}
								}
							});	 
								
						}else{  
							instance.close(dialog);
							if (oAction!=null){
								if (typeof oAction.onClientExist=="function"){
									oAction.onClientExist(data);
								}
							} 
						} 
						
					},"json");	
				}else{
					$("#numero_documento").val('');
					$("#numero_documento").focus();
					alert('Digite un numero de identificacion valido!');	
				}
				
			});   
			
		},"text");		
		 	 
	},	
	
	/*CREA LA VISTA PARA AGREGAR UN BENEFICIARIO*/
	doViewBeneficiario: function(obj_b){
		var instance=this; 
		var data='<br><br><center><button type="button" id="mayor_edad" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">MAYOR DE EDAD</span></button>&nbsp;&nbsp;<button id="menor_edad" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">MENOR DE EDAD</span></button></center>';
		
		var dialog=this.createDialog(this.dialog_container,"Beneficiario",data,400);
		
		$("#menor_edad").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();

			var person_component= new ModuloPersonas('Beneficiario',instance.dialog_container,instance._dialog);
			person_component.loadMainView();
			
			var person= new Beneficiario(person_component);
			//SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA
			person.setView("create","MENOR");
			//********************************************//
			person.addListener("onViewCancel",function(){
				person_component.closeView();
			});
			//Agrego el modulo al main content/
			person_component.addModule(person);
	 
			///Evento que captura cuando un cliente ha sido creado //
			person.addListener("onCreateBeneficiario",function(data){
				//Cierro la vista para refrescarla con los datos del cliente agregado //
				person_component.closeView();
				//********************************/
				obj_b.draw(data,data); 
			});

			
		}); 
				
		$("#mayor_edad").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
			
			var action={
				//Quiero agregarlo!
				doAdd : function(data){   
					instance.processCreateBeneficiario(data,obj_b);
				},
				//No deseo agregar el cliente
				doNotAdd : function(data){
					
				},
				//Si el cliente existe
				onClientExist : function(data){ 
					if (!(data.personal.id_nit==instance._dt_personales.idnit)){ 
						instance.processEditBeneficiario(data.personal.id_nit,obj_b);
					}else{
						alert('El contratante no puede ser Beneficiario!');	
					}
				}
			};
			
			instance.finderView('Buscar',action);

		}); 				
 	 
	},

	processEditPersona: function(idnit,obj){
		var instance=this;  
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);	
		var person= new Persona(person_component);
		//PONGO EL MODULO DE PERSONA EN MODO EDITAR
		person.setView("edit"); 
		person.addListener("cancel_creation",function(){
			person_component.closeView();
		}); 		
		person_component.addModule(person);

		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		}); 
		person.addListener("onViewCreate",function(){
			$(".dt_parentesco").show();
			if (obj!=null){ 
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}
		});	
		

 		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
	 
		direccion.addListener("doLoadViewComplete",function(rsobj){
			$(".dt_parentesco").show();
			if (obj!=null){
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}			
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
			});
			
			$("#bt_pros_select").click(function(){
				 
				var data={
							"id_documento":	$("#id_documento option:selected").val(),
							"tipo_documento": $("#id_documento option:selected").text(),
					 		"numero_documento":$("#numero_documento_text").val(),
							"primer_nombre": $("#primer_nombre").val(),
							"segundo_nombre":$("#segundo_nombre").val(),
							"primer_apellido":$("#primer_apellido").val(),
							"segundo_apellido":$("#segundo_apellido").val(),
							"fecha_nacimiento":$("#fecha_nacimiento").val(),
							"lugar_nacimiento":$("#lugar_nacimiento").val(), 
							"parentesco" : $("#parentesco option:selected").text(),
							"parentesco_id" :$("#parentesco option:selected").val(),
							'idnit':idnit
							
						};	
				
				var hide=false;
				if (obj!=null){
					if (obj.hideParentesco!=null){
						if (obj.hideParentesco){
							person_component.closeView();
							obj.draw(data,data); 
							hide=true;
						}
					}
				}
				
				if (($("#parentesco option:selected").val()!="") && (hide==false)){		
					person_component.closeView();	
					 
					obj.draw(data,data); 
				}else{
					alert('Debe de seleccionar el parentesco!');	
				} 
				
			});
			
		});
 		 
		var empresa= new personEmpresa(person_component);
		person_component.addModule(empresa);
  
		var telefono= new Telefono(person_component);
		person_component.addModule(telefono);
 		 
		var email= new Email(person_component);
		person_component.addModule(email);	
		
		var reference= new Referencia(person_component);
		person_component.addModule(reference);
		
		var referidos = new Referidos(person_component);
		person_component.addModule(referidos);
		///Le digo que cliente es el que sera editado/
		person_component.setPersonID(idnit);
	 	person_component.loadMainView();	
	},

	processEditBeneficiario: function(idnit,obj){
		var instance=this;  
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);	
		var person= new Persona(person_component);
		//PONGO EL MODULO DE PERSONA EN MODO EDITAR
		person.setView("edit"); 
		person_component.addModule(person);
		person.addListener("cancel_creation",function(){
			person_component.closeView();
		}); 
		
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		person.addListener("onViewCreate",function(){
			$(".dt_parentesco").show();
			if (obj!=null){
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
			});
			
			$("#bt_pros_select").click(function(){
				 
				var data={
							"id_nit":idnit,
							"id_documento":	$("#id_documento option:selected").val(),
							"tipo_documento": $("#id_documento option:selected").text(),
					 		"numero_documento":$("#numero_documento_text").val(),
							"primer_nombre": $("#primer_nombre").val(),
							"segundo_nombre":$("#segundo_nombre").val(),
							"primer_apellido":$("#primer_apellido").val(),
							"segundo_apellido":$("#segundo_apellido").val(),
							"fecha_nacimiento":$("#fecha_nacimiento").val(),
							"lugar_nacimiento":$("#lugar_nacimiento").val(), 
							"parentesco" : $("#parentesco option:selected").text(),
							"parentesco_id" :$("#parentesco option:selected").val()
						};	
						
				var hide=false;
				if (obj!=null){
					if (obj.hideParentesco!=null){
					//	alert(obj.hideParentesco);
						if (obj.hideParentesco){
							person_component.closeView();
							obj.draw(data,data); 
							hide=true;
						}
					}
				}
				
				if (($("#parentesco option:selected").val()!="" && hide==false)){		
					person_component.closeView();	
					obj.draw(data,data); 
				}else{
					alert('Debe de seleccionar el parentesco!');	
				} 
			});
			

			/*VERIFICO SI EL PROSPECTO ES EL MISMO BENEFICIARIO Y ACTUALIZO EL PARENTESCO*/
			if (instance._dt_prospecto.valid){
				//alert(instance._dt_prospecto.idnit+ " "+ idnit)
				if (instance._dt_prospecto.idnit==idnit){
					$("#parentesco").val($("#prospect_parentesco option:selected").val());
				}
			}
			
			
		});		
		 
 		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
 		 
		var empresa= new personEmpresa(person_component);
		person_component.addModule(empresa);
  
		var telefono= new Telefono(person_component);
		person_component.addModule(telefono);
 		 
		var email= new Email(person_component);
		person_component.addModule(email);	
		
		var reference= new Referencia(person_component);
		person_component.addModule(reference);
		
		var referidos = new Referidos(person_component);
		person_component.addModule(referidos);
		///Le digo que cliente es el que sera editado/
		person_component.setPersonID(idnit);
	 	person_component.loadMainView();	
	},
	
	processEditBeneficiarioMenor : function(data,obj){
		var instance=this;
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		
		var person= new Beneficiario(person_component);
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("edit","MENOR");
		person.setData(data); 
		person.setContrato(instance._serie_contrato,instance._no_contrato);
		person.setBeneficiario(data.id_beneficiario);
		/*********************************************/

		person.addListener("onViewCancel",function(){
			person_component.closeView();
		});		
		
		
		/*Capturar cuando la vista ha sido creada*/
		person.addListener("onViewCreate",function(){
		//	$("#numero_documento").val(data.numero_documento);			  
		//	$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
		});
		person.addListener("onViewCancel",function(){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			person_component.closeView();
			/*********************************/
		});		
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onEditBeneficiario",function(data){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			person_component.closeView();
			/*********************************/
			obj.draw(data,data); 
			window.location.reload();
		});
		
		person_component.addModule(person);
	  
	 	person_component.loadMainView();
	},	
	
	processCreateBeneficiario : function(data,obj){
		var instance=this;
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		
		var person= new Beneficiario(person_component);
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("create","MAYOR");
		/*********************************************/
		
		person.addListener("onViewCancel",function(){
			person_component.closeView();
		});
		
		/*Capturar cuando la vista ha sido creada*/
		person.addListener("onViewCreate",function(){
			$("#numero_documento").val(data.numero_documento);			  
			$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
		});
		
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreateBeneficiario",function(data){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			person_component.closeView();
			/*********************************/
			obj.draw(data,data); 
		});
		
		person_component.addModule(person);
	  
	 	person_component.loadMainView();
	},
	
	draw_beneficiario : function(patern,bt_name,data){
		///alert(data.personal.primer_nombre)
		$("#"+patern+"_primer_nombre").html(data.primer_nombre);
		$("#"+patern+"_segundo_nombre").html(data.segundo_nombre);
		//$("#"+patern+"_tercer_nombre").html(data.tercer_nombre);
		$("#"+patern+"_primer_apellido").html(data.primer_apellido);
		$("#"+patern+"_segundo_apellido").html(data.segundo_apellido); 
		//$("#"+patern+"_apellido_casado").html(data.apellido_conyuge); 
		
		$("#"+patern+"_cedula").html(data.tipo_documento+" ("+data.numero_documento+")");	

		$("#"+patern+"_fecha_nacimiento").html(data.fecha_nacimiento);
		$("#"+patern+"_nacionalidad").html(data.lugar_nacimiento);
		$("#"+patern+"_parentesco").html(data.parentesco); 
  		$("#"+bt_name).html("Cambiar");
		//$("#"+bt_name).hide();
	},
	
	/*Funcion que actualiza los labels de los datos personales del prospecto */
	draw_prospecto_data : function(data){
		///alert(data.personal.primer_nombre)
		$("#pros_nombre_completo").html(	
					data.personal.primer_nombre+" "
					+data.personal.segundo_nombre+" "
					+data.personal.primer_apellido+" "
					+data.personal.segundo_apellido+" " );
		
		$("#pros_documento").html(data.personal.descripcion+" ("+data.personal.numero_documento+")");	

		$("#pros_celular").html("");
		$("#pros_telefono").html("");
		$("#pros_oficina").html("");
			
		if (data.phone.length>0){
			for(var i=0;i<data.phone.length;i++){
				if (data.phone[i].tipo=="Celular"){
					$("#pros_celular").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				if (data.phone[i].tipo=="Residencia"){
					$("#pros_telefono").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				if (data.phone[i].tipo=="Laboral"){
					$("#pros_oficina").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				 
			} 
		}
		if (data.phone.address>0){
			//$("#pros_telefono").html();
		}
		
		$(".prospect_person_data").show();
		$("#bt_grl_propecto").hide();
	},
	
	/*Funcion que actualiza los labels de los datos personales del contratante */
	draw_contratante_data : function(data){
		///alert(data.personal.primer_nombre)
		$("#contratante_nombre_completo").html(	
					data.personal.primer_nombre+" "
					+data.personal.segundo_nombre+" "
					+data.personal.primer_apellido+" "
					+data.personal.segundo_apellido+" " );
		
		$("#contrato_titular").html(data.personal.descripcion+" ("+data.personal.numero_documento+")");	

		$("#contrato_telefono").html("");
		$("#contract_oficina").html("");
			
		if (data.phone.length>0){
			for(var i=0;i<data.phone.length;i++){
				if (data.phone[i].tipo=="Celular"){
					$("#contract_celular").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				if (data.phone[i].tipo=="Residencia"){
					$("#contrato_telefono").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				if (data.phone[i].tipo=="Laboral"){
					$("#contract_oficina").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}
				 
			} 
		}
		if (data.address.length>0){
			for(var i=0;i<data.address.length;i++){
				if (data.address[i].tipo=="Cobro"){
					$("#contrato_direccion").html(data.address[i].provincia+","+
											data.address[i].ciudad+","+
											data.address[i].municipio+","+
											data.address[i].sector+","
											+data.address[i].avenida+","
											+data.address[i].calle+","
											+data.address[i].zona+","
											+data.address[i].departamento 
										);	
				}
			}
		}
		
		$(".contrato_contratante").show();
	}, 
	
	/*Funcion que actualiza los labels de los datos personales del contratante */
	draw_representante_data : function(patern,bt_name,data){
 
		$("#"+patern+"_primer_nombre").html(data.personal.primer_nombre);
		$("#"+patern+"_segundo_nombre").html(data.personal.segundo_nombre);
		$("#"+patern+"_tercer_nombre").html(data.personal.tercer_nombre);
		$("#"+patern+"_primer_apellido").html(data.personal.primer_apellido);
		$("#"+patern+"_segundo_apellido").html(data.personal.segundo_apellido); 
		$("#"+patern+"_apellido_casado").html(data.personal.apellido_conyuge); 
		
		$("#"+patern+"_cedula").html(data.personal.descripcion+" ("+data.personal.numero_documento+")");	

		$("#"+patern+"_fecha_nacimiento").html(data.personal.fecha_nacimiento);
		$("#"+patern+"_nacionalidad").html(data.personal.lugar_nacimiento);
		$("#"+patern+"_parentesco").html(data.personal.parentesco);
		//$("#contract_oficina").html("");
			
		if (data.phone.length>0){
			for(var i=0;i<data.phone.length;i++){ 
				if (data.phone[i].tipo=="Residencia"){
					$("#"+patern+"_numero_telefono").html("("+data.phone[i].area+") "+data.phone[i].numero);	
				}   
			} 
		}
 
		$("#"+bt_name).hide();
	},
	clean_representante_data : function(patern){
 
		$("#"+patern+"_primer_nombre").html('');
		$("#"+patern+"_segundo_nombre").html('');
		$("#"+patern+"_tercer_nombre").html('');
		$("#"+patern+"_primer_apellido").html('');
		$("#"+patern+"_segundo_apellido").html(''); 
		$("#"+patern+"_apellido_casado").html(''); 
		
		$("#"+patern+"_cedula").html('');	

		$("#"+patern+"_fecha_nacimiento").html('');
		$("#"+patern+"_nacionalidad").html('');
		$("#"+patern+"_parentesco").html(''); 
			
		$("#"+patern+"_numero_telefono").html('');	 
	}	
	
	
});