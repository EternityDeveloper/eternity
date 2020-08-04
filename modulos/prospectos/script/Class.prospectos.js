var Prospectos = new Class({
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
		
		this._asesor=new AsesoresTree(this.dialog_container);  
		
		this.addListener("doCreateProspecto",this.doViewCreateProspecto);
		this.addListener("doSelectTipoProspect",this.doSelectTipoProspect);
		
		var instance=this;
		/* Evento asesor */
		this._asesor.addListener("asesor_select",function(asesor){
			$("#codigo_asesor").show();
			$("#codigo_asesor").html(asesor['nombre']);
			$("#bt_find_asesor").html("Cambiar");
			instance._asesor_data.data=asesor;
			instance._asesor_data.form_complete=true;
		}); 
		
		/*LE RESETEO LOS DATOS A LAS VARIABLES*/
		this._asesor_data.form_complete=false;
		this._asesor_data.data=null;
	},
	setAsesorData : function(nombre,code,idnit){
		var  asesor = { 
			"nombre":nombre,
			"code":code,
			"idnit":idnit
		}   
		this._asesor_data.data=asesor;
		this._asesor_data.form_complete=true;
	//	this._asesor.fire("asesor_select",asesor);  
	},
	createListTable : function(table_name){
		var instance=this;
		this._table_view_name=table_name;
		
		$("#pro_create").click(function(){
			instance.chargeView();
		});	
		createTable(this._table_view_name,{
				"bFilter": true,
				"bSort": true,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_prospectos/listar&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" },
						{ "mData": "TIME_TO_END" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" },
						{ "mData": "fecha_ultima_actividad" },
						{ "mData": "descrip_actividad" },
						{ "mData": "bt_editar_user" },
						{ "mData": "bt_editar" },
						{ "mData": "bt_reasing" },
						{ "mData": "bt_editar_user_remove" }
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
						   
						for (i=0;i<oSettings.aoData.length;i++){ 
							var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[4]).html().trim());
							/*SI EL TIEMPO RESTANTE ESTA ENTRE 0 Y 5 ENTONCES 
							CAMBIAR EL COLOR*/
							if ($($(oSettings.aoData[i].nTr).children()[6]).html().trim()!="Cierre"){
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
							}
							$($(oSettings.aoData[i].nTr).children()[4]).html('<center>'+tiempo + " Dias </center>");
						}
				  	
						$(".edit_client_prosp").click(function(){
							instance.doViewEditProspecto($(this).attr("id"));
						});	
						
						$(".remove_client_prosp").click(function(){
							instance.doRemoveView($(this).attr("id"));
						});	
						$(".reasign_prosp").click(function(){
							instance.changeOwnerByProspect($(this).attr("id"));
						});	
						
					} 
				});	
	},
	
	view_table_gestion : function(){
		var instance=this;
		$(".total_cuenta").click(function(){
			instance.view_detalle_gestion($(this).attr("id"));
		});
		$(".total_cuenta_detalle").click(function(){
			window.location.href="?mod_prospectos/listar&report_gerente_ase&p_fecha_desde="+$("#p_fecha_desde").val()+"&p_fecha_hasta="+$("#p_fecha_hasta").val()+"&id="+$(this).attr("id");
		});	
		
		$(".asesor_detalle").click(function(){
			window.location.href="?mod_prospectos/listar&report_ase_detalle&p_fecha_desde="+$("#p_fecha_desde").val()+"&p_fecha_hasta="+$("#p_fecha_hasta").val()+"&id="+$(this).attr("id");
		});				

				
				
	},
	
	view_detalle_gestion : function(type){
		var instance=this;
		this.post("./?mod_prospectos/listar&reporte_asesor",{
				"view_detalle_asesor":'1' 
			},function(data){ 
				$('#'+instance.dialog_container).hideLoading();	

				var dialog=instance.createDialog(instance.dialog_container,
												"Detalle de gestion",data,$(document).width()-100);
									
 				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/950), 20]); 	 
				
				$("#bt_repor_cerrar").click(function(){
					instance.close(dialog);	
				});
				
				createTable('prospecto_list_gestion',{
					"bFilter": false,
					"bSort": true,
					"bInfo": false,
					"bPaginate": true,
					"bLengthChange": false,
					"bProcessing": true,
					"bServerSide": true,
					"sAjaxSource": "./?mod_prospectos/listar&filter_gestion=1&type="+type,
					"sServerMethod": "POST",
					"aoColumns": [ 
							{ "mData": "nombre_completo" },
							{ "mData": "pilar_inicial" },
							{ "mData": "fecha_inicio" },
							{ "mData": "fecha_fin" },
							{ "mData": "TIME_TO_END" },
							{ "mData": "nombre_asesor" },
							{ "mData": "estatus" },
							{ "mData": "observaciones" },
							{ "mData": "fecha_ultima_actividad" },
							{ "mData": "descrip_actividad" },
							{ "mData": "bt_editar_user" } 
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
							for (i=0;i<oSettings.aoData.length;i++){ 
								var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[4]).html().trim());
								/*SI EL TIEMPO RESTANTE ESTA ENTRE 0 Y 5 ENTONCES 
								CAMBIAR EL COLOR*/
								if ($($(oSettings.aoData[i].nTr).children()[6]).html().trim()!="Cierre"){
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
								}
								$($(oSettings.aoData[i].nTr).children()[4]).html('<center>'+tiempo + " Dias </center>");
							}
							  
							$(".edit_client_prosp").click(function(){
								instance.doViewEditProspecto($(this).attr("id"));
							});	 
						} 
					});					
		});

	},
	
	createAllListTable : function(table_name){
		var instance=this;
		this._table_view_name=table_name;
		$("#pro_create").click(function(){
			instance.viewProspectoDirect();
		});		
		createTable(this._table_view_name,{
				"bFilter": true,
				"bSort": false,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "./?mod_prospectos/listado_all_prospecto&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" },
						{ "mData": "TIME_TO_END" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" },
						{ "mData": "fecha_ultima_actividad" },
						{ "mData": "descrip_actividad" },						
						{ "mData": "bt_editar_user" },
						{ "mData": "bt_editar" },
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
						for (i=0;i<oSettings.aoData.length;i++){ 
							var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[4]).html().trim());
							/*SI EL TIEMPO RESTANTE ESTA ENTRE 0 Y 5 ENTONCES 
							CAMBIAR EL COLOR*/
							if ($($(oSettings.aoData[i].nTr).children()[6]).html().trim()!="Cierre"){
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
							}
							$($(oSettings.aoData[i].nTr).children()[4]).html('<center>'+tiempo + " Dias </center>");
						}
						 
						if (!instance._isCharge){
							$('<button id="pro_refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter');  
							instance._isCharge=true;	
						}
							
						$(".edit_client_prosp").click(function(){
							instance.doViewEditProspecto($(this).attr("id"));
						});	
						
						$(".remove_client_prosp").click(function(){
							instance.doRemoveView($(this).attr("id"));
						});	
					} 
				});	
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
				"sAjaxSource": "./?mod_prospectos/cartera_asesor&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" },
						{ "mData": "nombre_asesor" }, 
						{ "mData": "observaciones" },
						{ "mData": "bt_editar_user" } 
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
							instance._isCharge=true;	
						}
							
						$(".edit_client_prosp").click(function(){
							instance.doViewEditProspecto($(this).attr("id"));
						});	
						
						$(".remove_client_prosp").click(function(){
							instance.doRemoveView($(this).attr("id"));
						});	
					} 
				});	
	},	
	
	doRemoveView : function(id){
		var instance=this;
		
		if (confirm("Esta seguro que quiere remover el prospecto?")){
			//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
			instance.post("./?mod_prospectos/listar",{
					"remove_prospecto":'1' ,
					'id':id
			},function(data){ 
				//$('#'+instance.dialog_container).hideLoading();	
				alert(data.mensaje);	
				if (!data.error){
					window.location.reload();
				} 
			},"json");
		}
	},
	
	createSimpleListProspecto : function(table_name,dialog){
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
				"sAjaxSource": "./?mod_prospectos/listar&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" },
						{ "mData": "TIME_TO_END" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" },
						{ "mData": "bt_editar_user" },
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
						for (i=0;i<oSettings.aoData.length;i++){
							var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[4]).html().trim());
							/*SI EL TIEMPO RESTANTE ESTA ENTRE 0 Y 5 ENTONCES 
							CAMBIAR EL COLOR*/
							if (tiempo>=3 && tiempo<=5){ 
								oSettings.aoData[i].nTr.className="AlertColor5"; 
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
						  
						$(".edit_client_prosp").click(function(){
							instance.doViewEditProspecto($(this).attr("id"));
						});	
						 
					},
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						$.getJSON( sSource, aoData, function (json) { 
							fnCallback(json)
							/*AL DAR CLICK EN EL TR*/
							var tr=$('td', $('.edit_client_prosp').parent().parent()).parent();
							tr.css( 'cursor', 'pointer' );
							tr.click(function(){
								var nTds=$(this).children();
								//alert($(nTds).find("a").attr("id"));
								 
								var  prospecto= {  
									"idnit":$(nTds).find("a").attr("id")
								}   
								if (dialog!=null){ 
									$("#"+dialog).dialog("destroy");
									$("#"+dialog).remove();
								}
								instance.fire("prospecto_selected",prospecto); 
							});
							/*AGREGANDO HIGTHLIGHT*/
							tr.hover(function(){ 
								$(this).addClass('hover_tr');  
							},function(){ 
								$(this).removeClass('hover_tr'); 
							});
						});
					}
				});	
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
	
	/*CARGA LOS PROSPECTOS QUE HAN SIDO FRACASADO*/
	createListTableFracaso : function(table_name){
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
				"sAjaxSource": "./?mod_prospectos/listar_fracasos&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "bt_editar" },
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" },
						{ "mData": "TIME_TO_END" },
						{ "mData": "nombre_asesor" },
						{ "mData": "estatus" },
						{ "mData": "observaciones" }
						
						  
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
						   
						for (i=0;i<oSettings.aoData.length;i++){
							var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[5]).html().trim());
							$($(oSettings.aoData[i].nTr).children()[5]).html('<center>'+tiempo + " Dias </center>");
						} 
						
						if (!instance._isCharge){
							$('<button id="reasingar" class="greenButton">Asignar a Gerente</button>').appendTo('div.dataTables_filter'); 
							$("#reasingar").hide();
							
							$("#reasingar").click(function(){
							 	instance.reasignProspectoViewGerente();
							});
							
							$(".edit_client_prosp").click(function(){
								instance.doViewEditProspecto($(this).attr("id"));
							});
							instance._isCharge=true;		
						}
							
							
						$(".reasign").click(function(){
								if ($(this).is(':checked')){  
									var strut={"id":$(this).attr("id"),"val":$(this).val()};
									instance._list_reasign.push(strut); 
								}else{
									for(var i=0;i<instance._list_reasign.length;i++){
										var x=instance._list_reasign[i];
										if (x.id==$(this).attr("id")){
											instance._list_reasign.splice(i,1); 
										}
									} 
								}
								if (instance._list_reasign.length>0){
									$("#reasingar").show();
								}else{
									$("#reasingar").hide();
								} 
							});  	
						
					} 
					 
				});	
	},
	
	/*CARGA LOS PROSPECTOS QUE HAN SIDO REASIGNADOS*/
	createListTableReasignado : function(table_name){
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
				"sAjaxSource": "./?mod_prospectos/listar_reasignados&x_search=1",
				"sServerMethod": "POST",
				"aoColumns": [ 
						{ "mData": "bt_editar" },
						{ "mData": "estatus" },
						{ "mData": "nombre_completo" },
						{ "mData": "pilar_inicial" },
						{ "mData": "fecha_inicio" },
						{ "mData": "fecha_fin" }
						
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
							$('<button id="bt_asignar_p" class="greenButton">Asignar a un asesor</button>').appendTo('div.dataTables_filter');  
							$("#bt_asignar_p").hide();
						 	$(".reasign").click(function(){
								if ($(this).is(':checked')){ 
									var strut={"id":$(this).attr("id"),"val":$(this).val()};
									instance._list_reasign.push(strut); 
								}else{
									for(var i=0;i<instance._list_reasign.length;i++){
										var x=instance._list_reasign[i];
										if (x.id==$(this).attr("id")){
											instance._list_reasign.splice(i,1); 
										}
									}
								}
								if (instance._list_reasign.length>0){
									$("#bt_asignar_p").show();
								}else{
									$("#bt_asignar_p").hide();
								} 
							});
							
							$("#bt_asignar_p").click(function(){
								instance.reasignProspectoViewAsesor();
							});
							
							$(".edit_client_prosp").click(function(){
								instance.doViewEditProspecto($(this).attr("id"));
							});	
							
							instance._isCharge=true;	
						}

					} 
				});	
	},
	
	/*carga la vista de reasignacion de prospectos a un Asesor*/
	reasignProspectoViewAsesor : function(){
		var instance=this; 
	//	$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	 
		this.post("./?mod_prospectos/listar_reasignados",{
				"asig_view":'1',
				'list_reasing':this._list_reasign
			},function(data){ 
			/*
				var dialog=instance.createDialog(
									instance.dialog_container,"Reasignacion de prospecto",
									data,$(document).width()-700);
									
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/950), 20]);*/ 
				var dialog=instance._dialog=instance.doDialog("modal_asignar_asesor",instance.dialog_container,data);  
				instance.addListener("onCloseWindow",function(){
					//alert('fsd');	
				}) ; 	
				 
				$("#bt_pro_f_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				 
				createTable("prospecto_list_fracasado",{
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
						
				var asesor=new AsesoresTree(instance.dialog_container);
				
				var data_asesor=null;
				  
				/* Evento asesor */
				asesor.addListener("asesor_select",function(asesor){
					$("#pros_new_rs_asesor").show();
					$("#pros_new_rs_asesor").html(asesor['nombre']);
					instance._asesor_data.data=asesor;
					instance._asesor_data.form_complete=true;
				}); 
				
				$("#bt_new_find_asesor").click(function(){ 
					asesor.filterByMyAsesores();
					asesor.show_dialog(dialog); 
				}); 
				$("#bt_new_asignar").click(function(){ 
					instance.doReasignAsesorSaveForm();
				}); 	
					 
				 
			});
 
	},	
	/* Funcion que guarda la reasignacion del prospecto a un asesor */
	doReasignAsesorSaveForm : function(){
		var instance=this;
		if (this._list_reasign.length>0){
			if (this._asesor_data.form_complete){
				var data={
					"submit_reasignacion_asesor":1, 
					"list_reasign":this._list_reasign,
					"asesor_data":this._asesor_data 
				};
				$.post("./?mod_prospectos/listar_reasignados",data,function(data){
					 
					if (!data.error){
						alert(data.mensaje);
						window.location.reload();
					}else{
						alert(data.mensaje);
					} 
		
				},"json");
			}else{
				alert('Error debe de seleccionar un Gerente!');	
			}
		}else{
			alert('Error debe de seleccionar un Prospecto!');	
		}
		
	},
	
	/*carga la vista de reasignacion de prospectos a un Gerente*/
	reasignProspectoViewGerente : function(){
		var instance=this; 
		this.post("./?mod_prospectos/listar_fracasos",{
				"reasig_view":'1',
				'list_reasing':this._list_reasign
			},function(data){
				/* 
				var dialog=instance.createDialog(
									instance.dialog_container,"Reasignacion de prospecto",
									data,$(document).width()-700);
									
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/950), 20]);*/ 
				var dialog=instance._dialog=instance.doDialog("modal_reasig",instance.dialog_container,data);  
				instance.addListener("onCloseWindow",function(){
					//alert('fsd');	
				}) ; 				
				 
				$("#bt_pro_f_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				 
				createTable("prospecto_list_fracasado",{
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
						
				var asesor=new AsesoresTree(instance.dialog_container);
				
				var data_asesor=null;
				  
				/* Evento asesor */
				asesor.addListener("asesor_select",function(asesor){
					$("#pros_new_rs_asesor").show();
					$("#pros_new_rs_asesor").html(asesor['nombre']);
					instance._asesor_data.data=asesor;
					instance._asesor_data.form_complete=true;
				}); 
				
				$("#bt_new_find_asesor").click(function(){ 
					asesor.filterByGerentesVentas();
					asesor.show_dialog(dialog); 
				}); 
				$("#bt_new_asignar").click(function(){ 
					instance.doReasignSaveForm();
				}); 	
					 
				 
			});
 
	},	
	/* Funcion que guarda el usuario reasignado */
	doReasignSaveForm : function(){
		var instance=this;
		if (this._list_reasign.length>0){
			if (this._asesor_data.form_complete){
				var data={
					"submit_reasignacion":1, 
					"list_reasign":this._list_reasign,
					"asesor_data":this._asesor_data 
				};
				instance.post("./?mod_prospectos/listar_fracasos",data,function(data){
					 
					if (!data.error){
						alert(data.mensaje);
						window.location.reload();
					}else{
						alert(data.mensaje);
					} 
		
				},"json");
			}else{
				alert('Error debe de seleccionar un Gerente!');	
			}
		}else{
			alert('Error debe de seleccionar un Prospecto!');	
		}
		
	},
	doSavePDirect : function(){
		var instance=this;
		if ($("#form_prospecto").valid()){  
			if (this._prospecto_data.form_complete){
				if (this._asesor_data.form_complete){
					var data={
						"prospectos_direct_submit":1, 
						"person_data":this._prospecto_data,
						"asesor_data":this._asesor_data, 
						"observacion": $("#observacion").val(),
						"pilar_origen": $("#pilar_origen").val(),
						"pilar_final": $("#pilar_final").val(),
					};
					instance.post("./?mod_prospectos/listar",data,function(data){
						 
						if (!data.error){
							alert(data.mensaje);
							window.location.reload();
						}else{
							alert(data.mensaje);
						} 
			
					},"json");
				}else{
					alert('Error debe de seleccionar un Asesor!');	
				}
			}else{
				$("#pros_rs_cliente").html();
				$("#pros_rs_cliente").hide();
				$(".finder").show();
				alert('Error debe de seleccionar un prospecto!');	
			}
		}
		
	},	
	viewProspectoDirect : function(){

		var instance=this; 
		this.post("./?mod_prospectos/listar",{
				"view_prospecto_direct":'1' 
			},function(data){ 
				instance._dialog=instance.doDialog("modal_add_prospecto",instance.dialog_container,data);  
				instance.addListener("onCloseWindow",function(){
 				}) ;				
				
				/*ESTO ES PARA EL CASO DE QUE SEA UN ASESOR Y NO UN GERENTE
				EL CUAL ENTRE EN ESTA ZONA */
				if (instance._asesor_data.form_complete){ 
					instance._asesor.fire("asesor_select",instance._asesor_data.data); 
				}

				$("#id_documento").change(function(){ 
					if ($("option:selected",this).text()!="Seleccione"){
 					 	$("#client_prospecto").show();
					}else{
						$("#client_prospecto").hide();	
					}
				});
				
				$("#bt_find_person").click(function(){
					var tipo={
						"tipo":$("#id_documento option:selected").text(),
						"id" : $("#id_documento option:selected").val()	
					}
					instance.validateDocumentDirect(tipo);
				}); 
				
				$("#bt_pro_f_save").click(function(){
					instance.doSavePDirect();
				}); 
				
				$("#bt_pro_f_cancel").click(function(){
					instance.close(dialog);
				}); 
				
				$("#bt_find_asesor").click(function(){
 					instance._asesor.filterByMyAsesores();
					instance._asesor.show_dialog(instance.dialog_container); 
				}); 
				
				instance.validateForm();
 
			});
 
	},	
	
 	/*valida un documento de indentidad para determinar 
	  si ya esta creado en el sistema*/
 	validateDocumentDirect : function(id_document){
		var instance=this; 
		if ($("#indentification").val().length>=6){
			var valid=false;
			
			if (id_document.tipo.trim()!="CEDULA"){
				valid=true;
			}else{
				valid=valida_cedula($("#indentification").val());
			}
			
			if (valid){
			//	$(".finder").hide();
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
						  
					}
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
	chargeView : function(){

		var instance=this; 
		this.post("./?mod_prospectos/listar",{
				"add_prospecto":'1' 
			},function(data){
				/* 	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar prospecto",data,550);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 100]); */
				
				instance._dialog=instance.doDialog("modal_add_prospecto",instance.dialog_container,data);  
				instance.addListener("onCloseWindow",function(){
					//alert('fsd');	
				}) ;				
				
				/*ESTO ES PARA EL CASO DE QUE SEA UN ASESOR Y NO UN GERENTE
				EL CUAL ENTRE EN ESTA ZONA */
				if (instance._asesor_data.form_complete){ 
					$("#prosp_find_asesor").hide();//oculto el boton de seleccionar!
					instance._asesor.fire("asesor_select",instance._asesor_data.data); 
				}

				$("#id_documento").change(function(){ 
					if ($("option:selected",this).text()!="Seleccione"){
					 //alert($("option:selected",this).text())
					 	$("#client_prospecto").show();
					}else{
						$("#client_prospecto").hide();	
					}
				});
				
				$("#bt_find_person").click(function(){
					var tipo={
						"tipo":$("#id_documento option:selected").text(),
						"id" : $("#id_documento option:selected").val()	
					}
					instance.validateDocument(tipo);
				}); 	
				$("#tipos_prospectos").change(function(){
					if ($(this).val()!=""){
						instance.chargeTypeProspectacion($(this).val());
					} 
				});
				
				$("#bt_pro_f_save").click(function(){
					instance.doSaveForm();
				}); 
				
				$("#bt_pro_f_cancel").click(function(){
					instance.close(dialog);
				}); 
				
				$("#bt_find_asesor").click(function(){
					//alert('fdad');
					instance._asesor.filterByMyAsesores();
					instance._asesor.show_dialog(instance.dialog_container); 
				}); 
				
				instance.validateForm();
 
			});
 
	},
	/*carga la vista de reasignacion de un asesor a otro de un mismo gerente*/
	changeOwnerByProspect : function(id){
		var instance=this; 
 		this.post("./?mod_prospectos/listar",{
				"asig_view":'1',
				'prospect':id
			},function(data){ 
				/*var dialog=instance.createDialog(
									instance.dialog_container,"Reasignacion de prospecto",
									data,$(document).width()-700);*/
				dialog=instance._dialog=instance.doDialog("modal_reasignacion",instance.dialog_container,data);  									
				instance._dialog=dialog; 
				
				$("#bt_pro_f_cancel").click(function(){
					instance.CloseDialog("modal_reasignacion");
				}); 
				 
				createTable("prospecto_list_fracasado",{
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
						
				var asesor=new AsesoresTree(instance.dialog_container);
				
				var data_asesor=null;
				  
				/* Evento asesor */
				asesor.addListener("asesor_select",function(asesor){
					$("#pros_new_rs_asesor").show();
					$("#pros_new_rs_asesor").html(asesor['nombre']);
					data_asesor=asesor;
				}); 
				
				$("#bt_new_find_asesor").click(function(){ 
					asesor.filterByMyAsesores();
					asesor.show_dialog(dialog); 
				}); 
				$("#bt_new_asignar").click(function(){ 
					var data={
						"submit_change_prospecto_to_asesor":1, 
						"prospect":id,
						"asesor_data":data_asesor
					};
					instance.post("./?mod_prospectos/listar",data,function(data){ 
						if (!data.error){
							alert(data.mensaje);
							window.location.reload();
						}else{
							alert(data.mensaje);
						} 
						
					},"json");
				}); 	
					 
				 
			});
 
	},
	chargeEditView : function(id){
		var instance=this; 
		instance.post("./?mod_prospectos/listar",{
				"prospecto_view":'1',
				'id':id
			},function(data){ 
				var dialog=instance.createDialog(instance.dialog_container,"Vista prospecto",data,$(document).width()-200);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/950), 10]); 
				
				 
				$("#bt_pro_f_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				
				/*  parametros (nombre donde sera insertada el objecto, ID=> es el codigo del prospecto id_nit) */
				var actividad = new ActividadesProspectos("actividades",id);
				actividad.chargeView();
				
 
			});
 
	},
	
	chargeTypeProspectacion : function(val){
		var instance=this;
		this._tipo_prospecto_data.form_complete=false;
		//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		this.post("./?mod_prospectos/listar",{
				"tipo_pilar_question":'1',
				"id": val
			},function(data){
/*				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Tipos de prospectacion",data,550);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 100]); */
				instance._dialog=instance.doDialog("modal_pilar_question",instance.dialog_container,data);  
				instance.addListener("onCloseWindow",function(){
					//alert('fsd');	
				}) ;
				
			//	instance.validateFormPropectacion();
				$("#bt_prospect_save").click(function(){
					if ($("#from_propectacion").valid()){
						var prospecto = $("#from_propectacion").serializeArray();
					//	$("#"+dialog).dialog("destroy");
					//	$("#"+dialog).remove();
						instance.fire("doSelectTipoProspect",prospecto);
						instance.CloseDialog(instance._dialog)
						
					}
				});
				
				$("#bt_prospect_cancel").click(function(){
					//$("#"+dialog).dialog("destroy");
					//$("#"+dialog).remove();
					instance.CloseDialog(instance._dialog);
					$("#tipos_prospectos").val("");
				}); 
				instance.addListener("onCloseWindow",function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
					$("#tipos_prospectos").val("");
				});
 
			});		
	},
	
	doSaveForm : function(){
		var instance=this;
		if ($("#form_prospecto").valid()){
			
			if (this._tipo_prospecto_data.form_complete){
				if (this._prospecto_data.form_complete){
					if (this._asesor_data.form_complete){
						var data={
							"prospectos_submit":1,
							"tipo_prospecto":this._tipo_prospecto_data,
							"person_data":this._prospecto_data,
							"asesor_data":this._asesor_data, 
							"observacion": $("#observacion").val()
						};
						instance.post("./?mod_prospectos/listar",data,function(data){
							 
							if (!data.error){
								alert(data.mensaje);
								window.location.reload();
							}else{
								alert(data.mensaje);
							} 
				
						},"json");
					}else{
						alert('Error debe de seleccionar un Asesor!');	
					}
				}else{
					$("#pros_rs_cliente").html();
					$("#pros_rs_cliente").hide();
					$(".finder").show();
					alert('Error debe de seleccionar un prospecto!');	
				}
			}else{
				$("#tipos_prospectos").val("");
				alert('Error debe de volver a llenar el formulario "Tipo prospecto"');	
			}
		}
		
	},
	
	doSelectTipoProspect : function(data){
		var instance=this;
		this._tipo_prospecto_data.form_complete=true;
		this._tipo_prospecto_data.data=data;
		this._tipo_prospecto_data.tipos_prospectos=$("#tipos_prospectos").val();
		 
	},
	
	doViewCreateProspecto : function(data){

		var instance=this;
		this._person_component= new ModuloPersonas('Prospecto',this.dialog_container,this._dialog);
		this._person_component.loadMainView();
		
		var person= new Persona(this._person_component);
		
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("create");
		/*********************************************/
		/*Capturar cuando la vista ha sido creada*/
		person.addListener("onViewCreate",function(){
			$("#numero_documento").val(data.identificacion);			  
			$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
			
			$("#tipo_clte").hide();			  
			$("#sys_clasificacion_persona").hide();
		});

		/*Agrego el modulo al main content*/
		this._person_component.addModule(person);
 
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreatePerson",function(data){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			instance._person_component.closeView();
			/*********************************/
			
			/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
			person.setView("edit");
			/**********************************/
			person.addListener("onViewCreate",function(){ 
				$("#tipo_clte").hide();			  
				$("#sys_clasificacion_persona").hide();
			});			
			/*Y vuelvo a cargar la vista*/
			instance._person_component.loadMainView();
			/**************************************/
			
			var direccion= new Direccion(instance._person_component);
			instance._person_component.addModule(direccion);
			
			direccion.addListener("doLoadViewComplete",function(obj){
				instance.insertIntoView(obj)
			});
			
			/**/
			var empresa= new personEmpresa(instance._person_component);
			instance._person_component.addModule(empresa);
			/***************************************************/
			 
			/**/
			var telefono= new Telefono(instance._person_component);
			instance._person_component.addModule(telefono);
			/***************************************************/
			 
			var email= new Email(instance._person_component);
			instance._person_component.addModule(email);	
			
			var reference= new Referencia(instance._person_component);
			instance._person_component.addModule(reference);
			
			var referidos = new Referidos(instance._person_component);
			instance._person_component.addModule(referidos);
			
			/*Le digo que cliente es el que sera editado*/
			instance._person_component.setPersonID(data.nit);
			/************************************************/
			/*SETTEO LA VISTA PARA QUE CARGE DE PRIMERO LA VISTA DE DIRECCION*/
			instance._person_component.selected(direccion.getTabID());
			 
		
		});
		
		/*SELECCIONO QUE MODULO SERA EL PRIMERO EN SER MOSTRADO*/
		this._person_component.selected(person.getTabID()); 

		/* CAPTURO EL EVENTO DEL PROSPECTO SELECCIONADO */
		this.addListener("onSelectProspecto",function(person){
			$(".finder").hide(); 
			$("#pros_rs_cliente").html(person.primer_nombre + " "+person.primer_apellido);
			$("#pros_rs_cliente").show();
			instance._prospecto_data.data=person;
			instance._prospecto_data.form_complete=true;
			//alert(person.primer_nombre+ " "+person.primer_apellido);
		});
	},
	
	doViewEditProspecto : function(id_nit){ 
		var instance=this;
		this._person_component= new ModuloPersonas('Prospecto',this.dialog_container,this._dialog);
  
		var person= new Persona(this._person_component);
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit");
		/**********************************/
		person.addListener("onViewCreate",function(){
			$("#tipo_clte").hide();			  
			$("#sys_clasificacion_persona").hide();
		});
				
		this._person_component.addModule(person);
		
		var direccion= new Direccion(this._person_component);
		this._person_component.addModule(direccion);
		
		direccion.addListener("doLoadViewComplete",function(obj){
			instance.insertIntoView(obj)
		});
		
		/**/
		var empresa= new personEmpresa(this._person_component);
		this._person_component.addModule(empresa);
		/***************************************************/
		 
		/**/
		var telefono= new Telefono(this._person_component);
		this._person_component.addModule(telefono);
		/***************************************************/
		 
		var email= new Email(this._person_component);
		this._person_component.addModule(email);	
		
		var reference= new Referencia(this._person_component);
		this._person_component.addModule(reference);
		
		var referidos = new Referidos(instance._person_component);
		instance._person_component.addModule(referidos);
		
		/*Y vuelvo a cargar la vista*/
		this._person_component.loadMainView();
		/**************************************/
  
		/*Le digo que cliente es el que sera editado*/
		this._person_component.setPersonID(id_nit);
		/************************************************/
		/*SETTEO LA VISTA PARA QUE CARGE DE PRIMERO LA VISTA DE DIRECCION*/
		//person_component.selected(person.getTabID());

		/* CAPTURO EL EVENTO DEL PROSPECTO SELECCIONADO */
	/*	this.addListener("onSelectProspecto",function(person){ 
			alert(person.primer_nombre+ " "+person.primer_apellido);
		});*/
	},
	
	/*INSERTA LOS BOTONES DE CANCELAR Y SELECCIONAR EN UNA VISTA*/
	insertIntoView : function(obj){
		var instance=this;
		var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
		data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
		$("#main_module").append(data);
		
		$("#bt_pros_cancelar").click(function(){
			$("#"+instance._person_component._dialog).dialog("destroy");
			$("#"+instance._person_component._dialog).remove();		
		});
		
		$("#bt_pros_select").click(function(){
		//	$('#'+instance.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});
			var person=instance._person_component.getModule("Persona").getFormData();
	//		alert(person.person_id + " - "+person.primer_apellido);
	//	        alert(person.person_id);
			instance.post("./?mod_prospectos/listar",{
						"validate_phone":"1",
						"id_nit":person.person_id
					},function(data){
						
				if (data.total_phone>0){
					instance.CloseDialog(instance._person_component._dialog);
				//	$("#"+instance._person_component._dialog).dialog("destroy");
				//	$("#"+instance._person_component._dialog).remove();	
					
					instance.fire("onSelectProspecto",person);	
				}else{
					
					instance._person_component.selected(instance._person_component.getModule("Telefono").getTabID());
					instance._person_component.__selectViewTab();
					alert('Error debe de registrar por lo menos un numero de telefono valido!');	
				}
			},"json");
		//	$('#'+instance.dialog_container).hideLoading();	
				
		});

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
