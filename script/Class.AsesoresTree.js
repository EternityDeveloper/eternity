var AsesoresTree = new Class({
	rand:null,
	_personal_data:[],
	_tb_select:null,
	_cl_dialog : null,
	_tb_asesor : "tb_estructura_comercial",
	_list_type : {
		'filter_gerente_ventas':0,
		'filter_show_my_asesores':0	,
		'filter_show_all_asesores':0	
	} ,//maneja estatus para listar los asesores, gerentes etc
	initialize : function(dialog_container){
		this.main_class="AsesoresTree";
		this.dialog_container=dialog_container;
	},
	show_dialog : function(dialog_loading_name){ 
		//$('#'+dialog_loading_name).showLoading({'addClass': 'loading-indicator-bars'});	
		var rand=Math.floor(Math.random() * (1000 - 1 + 1) + 1);
		this.rand=rand;
		this._tb_select="tb_asesor_"+rand;
		var instance=this;
		instance.post("./?mod_inventario/reserva/reservar",{
						"listar_asesores":"1",
						"rand":rand,
						"list_type":JSON.stringify(instance._list_type) 
					},function(data){	   
 
			instance._cl_dialog=instance._dialog=instance.doDialog("modal_listado_asesor",instance.dialog_container,data);  
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}) ; 
			$(".list_ase_cx").click(function(){  
				var  asesor= { 
					"nombre":$.base64.decode($(this).attr("name")),
					"code":$(this).attr("id"),
					"id_nit":$(this).attr("id_nit"),
					"nombre_gerente":$.base64.decode($(this).attr("name_gerente"))
				}
				instance.CloseDialog(instance._cl_dialog);
				instance._personal_data=asesor;
				instance.fire("asesor_select",asesor);  	 
			});	 	
					
 			createTable(instance._tb_asesor,{
				"bSort": false,
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					}
				});
			

				
		});	
		
	},
	/*Crea el filtro para que el listado solo se muestren los gerentes de ventas*/
	filterByGerentesVentas : function(){
		this._list_type.filter_gerente_ventas=1;
	},
	/*Crea el filtro para que muestre solo los asesores de un gerente*/
	filterByMyAsesores : function(){
		this._list_type.filter_show_my_asesores=1;
	},
	/*Crea el filtro para que muestre todos los asesores*/
	filterByAsesores : function(){
		this._list_type.filter_show_all_asesores=1;
	},	
	createTable : function(){
		var instance=this;
		createTable(instance._tb_asesor,{
				"bSort": false,
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
					"bProcessing": true,
					"bServerSide": true,
					"bLengthChange": false,
					"sAjaxSource": "./?mod_inventario/reserva/reservar&listar_asesores=1&showlist=1&list_type="+JSON.stringify(instance._list_type),
					"sServerMethod": "GET",
					"aoColumns": [
							{ "mData": "id_comercial" },
							{ "mData": "primer_nombre" },
							{ "mData": "primer_apellido" },
							{ "mData": "tabla" },
							{ "mData": "option" } 
						],
				  "oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					},
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						//aoData.push( { "name": "more_data", "value": "my_value" } );
						$.getJSON( sSource, aoData, function (json) { 
							fnCallback(json)
							$('.rs_add_link').click(function(){
								var nTds = $('td', $(this).parent().parent()); 
								instance.post("./?mod_inventario/reserva/reservar&listar_asesores=1",
								{
									"getComercialParent":"1",
									"code":$(nTds[0]).text()
								},function(inf){ 
									instance.close(cl_dialog);
									var  asesor= { 
										"nombre":$(nTds[1]).text() + " "+$(nTds[2]).text(),
										"code":$(nTds[0]).text(),
										"idnit":$(this).attr("id"),
										"data":inf 
									}  
									//alert($(this).attr("id"));
									instance.close(instance._cl_dialog);
									instance._personal_data=asesor;
									instance.fire("asesor_select",asesor);  
								},"json");								

							});	
							
							/*AL DAR CLICK EN EL TR*/
							var tr=$('td', $('.rs_add_link').parent().parent()).parent();
							tr.css( 'cursor', 'pointer' );
							tr.click(function(){
								var nTds=$(this).children(); 
								//$('#'+instance._cl_dialog).showLoading({'addClass': 'loading-indicator-bars'});	 
								instance.post("./?mod_inventario/reserva/reservar&listar_asesores=1",
								{
									"getComercialParent":"1",
									"code":$(nTds[0]).text()
								},function(inf){
								//	$('#'+instance._cl_dialog).hideLoading();
									instance.close(instance._cl_dialog);
									var  asesor= { 
										"nombre":$(nTds[1]).text() + " "+$(nTds[2]).text(),
										"code":$(nTds[0]).text(),
										"idnit":$(nTds).find("a").attr("id"),
										"data":inf 
									}  
									instance._personal_data=asesor;
									instance.fire("asesor_select",asesor);  
								},"json");	
								
								
							});
							/*AGREGANDO HIGTHLIGHT*/
							tr.hover(function(){ 
								$(this).addClass('hover_tr');  
							},function(){ 
								$(this).removeClass('hover_tr'); 
							});
							
						} );
					}
					
				});
 	
	},
	createTree : function(){
		var instance=this;
		
		$("#tree_estruct").jstree({
				//"plugins" : ["themes","html_data","dnd","ui","hotkeys","search"],
				"plugins" : ["themes","html_data","dnd","ui","types","search"],
				"core" : { "initially_open" : [ "top_main","gerentes_divicion" ]},
				"types" : {
					"valid_children" : [ "default" ],
					"types" : {
						"root" : {
							"icon" : { 
								"image" : "./images/1379792004_building.png" 
							},
							"valid_children" : [ "default" ],
							"max_depth" : 2,
							"hover_node" : true,
							"select_node" : true
						},					
						"ceo" : {
							"icon" : { 
								"image" : "./images/1379792434_administrator.png" 
							},
							"valid_children" : [ "subgerente" ],
							"max_depth" : 2,
							"hover_node" : true,
							"select_node" : true
						},
						"seller" : {
							"icon" : { 
								"image" : "./images/1379792259_Businessman.png" 
							},
							"valid_children" : [ "default" ],
							"max_depth" : 2,
							"hover_node" : true,
							"select_node" : true
						},
						"subgerente" : {
							"icon" : { 
								"image" : "./images/1379791605_ceo.png" 
							},
							"valid_children" : [ "default" ],
							"max_depth" : 2,
							"hover_node" : true,
							"select_node" : true
						},
						"director" : {
							"icon" : { 
								"image" : "./images/1379794630_administrator.png" 
							},
							"valid_children" : [ "default" ],
							"max_depth" : 2,
							"hover_node" : true,
							"select_node" : true
						}
					}				
				}
			})
			.bind("loaded.jstree", function (event, data) {
				// you get two params - event & data - check the core docs for a detailed description
			}).bind("dblclick.jstree", function (event) {
				 var node = $(event.target).closest("li").attr('id');
				 if (node!="top_main"){ 
					// alert($(event.target).closest("li").children().closest("a").children().closest("span").text());
					var  asesor= { 
						"nombre":$(event.target).closest("li").children().closest("a").children().closest("span").text(),
						"code":$(event.target).closest("li").attr('ids'),
						"idnit":$(event.target).closest("li").attr('idnit')
					}  
					//alert($(event.target).closest("li").attr('idnit'))
					instance.close(instance._cl_dialog);
					instance._personal_data=asesor;
					instance.fire("asesor_select",asesor);
				 }
				
			}).bind("open_node.jstree", function (event, data) {
				 //selected(data);	
				//alert('xx');
			}).bind("select_node.jstree", function (event, data) {
				//selected(data);
				//alert('xx');
			});	
		
	 
		  $("#asesor_button").click(function () {
			   $("#tree_estruct").jstree("search", $("#asesor_search").val());
		   });
		   
		   $("#asesor_search").keypress(function (e) {
			   if (e.keyCode==13){
				$("#tree_estruct").jstree("search", $("#asesor_search").val());
			   }
		   });
		
	 
	},
	close : function(client_dialog){
		$("#"+client_dialog).dialog("destroy");
		$("#"+client_dialog).remove(); 
	},
	
	doViewCreateAsesor : function(data){

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
	}
	
});