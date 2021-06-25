/*  COBRO*/
var GenerarMeta = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
	},	 
	doInit : function(){ 
		var instance=this; 
	 
		$("#list_zonas").dataTable({
			"bFilter": true,
			"bInfo": false,
			"bPaginate": true,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});	
		 
		$(".date_pick").datepicker({
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
	showZonaOnMap  : function(){
		var colors=[
			'#425fb2','#f1cc21','#05aa8b',
			'#edfdff','#adaff2','#50a40b',
			'#f1cc21','#f4c09b','#d6641f',
			'#f58807','#d48c52','#3e9165',
			'#05aa8b','#5f9133','#c6ffa1'
		];
		
		var map = new TMap('main_map_zona');
		map.addListener("onMapLoad",function(maps){ 
			var i=0;
			/*DIBUJO EL POLIGONO*/ 
			$(".list_zona_item").each(function(index, element) { 
				var v = new OpenLayers.Layer.Vector($(this).attr("item_name"));
		 
				var style_poly =
				   {
					   strokeColor: "#000000",
					   strokeOpacity: 1,
					   strokeWidth: 2,
					   fillColor: colors[i],
					   fillOpacity: 0.6
					};		
				v.style = style_poly;	
				i++;
				maps._mapLayer.addLayer(v);			
                maps.drawPolygonToVector($(this).attr("poligono"),v); 
            }); 
			//maps._mapLayer.updateSize();  
		}); 						
		map.drawMapView(); 
		
		return map._mapLayer; 		
	},
	
	getToken : function(){
		return this._rand;
	}, 
	doCreateZonaView : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&zonas",{
				"zona_add":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Crear ZONA",data,900);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			var motorizado=null;
			var oficial=null;
			/*UN ARRAY QUE MANEJA LOS RESPONSABLES DE UNA ACTIVIDAD*/
			var actividad_responsable=[];
			
			$("#act_add").click(function(){
			//	if ($("#frm_actividad_").valid()){ 
					var info=$("#frm_actividad_").serializeArray();
					info.push({name: "actividad_responsable", value: JSON.stringify(actividad_responsable)}); 
					
					instance.post("./?mod_cobros/delegate&processLaborCobro",info,function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
			//	}
			});
			
			var map = new TMap('map_zona');
			map.addListener("onMapLoad",function(maps){
				maps.drawPolygon();
			});
			map.addListener("onDrawPolygon",function(polyg){
			 	instance._polygon=polyg;
				
				instance.post("./?mod_cobros/delegate&zonas",{
						"calculateZonaCLICTT":'1',
						"polygon":instance._polygon 
				},function(data){ 
					  $("#z_clientes").html(data.clientes);
					  $("#z_contratos").html(data.contratos); 
				},"json");				
			});	
			map.addListener("onRemovePolygon",function(){
			 	instance._polygon=null;
			});						
			map.drawMapView();    
			
			/*CREAR ZONA*/
			$("#btz_create").click(function(){ 

				if ($('#txt_cod_zona').val()==null){
					alert('Debe de ingresar el codigo de la zona!');
					return ;	
				}
				if ($('#txt_zona').val()==null){
					alert('Debe de ingresar el nombre de la zona!');
					return ;	
				}
				if (instance._polygon==null){
					alert('Debe de seleccionar un area en el mapa!');
					return ;	
				}	
				if (motorizado==null){
					alert('Debe de seleccionar un motorizado!');
					return ;	
				}
				if (oficial==null){
					alert('Debe de seleccionar un Oficial!');
					return ;	
				}				
								
				instance.post("./?mod_cobros/delegate&zonas",{
						"addZona":'1',
						"polygon":instance._polygon,
						"motorizado":motorizado,
						"nombre_zona":$('#txt_zona').val(),
						'codigo_zona':$('#txt_cod_zona').val(),
						"oficial_nit":oficial
				},function(data){ 
					instance.close(dialog); 
					window.location.reload();
				},"json");
																 
			});	
 
 
 			$('#txt_oficial').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			
			$("#txt_oficial").on("change", 
				function(e) { 
					oficial=e.val; 
			});
			
			$('#txt_motorizado').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			
			$("#txt_motorizado").on("change", 
				function(e) { 
					motorizado=e.val; 
			});
			//$('#e21').select2('data', preload_data ) 			 
		});	
	} , 
	/*EDITAR ZONA*/
	doEditZonaView : function(zona){
		var instance=this;
		instance.post("./?mod_cobros/delegate&zonas",{
				"zona_edit":'1',
				"zona_id":zona
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Editar ZONA",data.html,900);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			var motorizado=data.motorizado;
			var oficial=data.oficial_nit; 
			instance._polygon=data.polygon;
			
			$("#z_clientes").html(data.total_clientes);
			$("#z_contratos").html(data.total_contratos); 
			
			/*UN ARRAY QUE MANEJA LOS RESPONSABLES DE UNA ACTIVIDAD*/
			var actividad_responsable=[];
			
			$("#act_add").click(function(){
			//	if ($("#frm_actividad_").valid()){ 
					var info=$("#frm_actividad_").serializeArray();
					info.push({name: "actividad_responsable", value: JSON.stringify(actividad_responsable)}); 
					
					instance.post("./?mod_cobros/delegate&processLaborCobro",info,function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
			//	}
			});
			
			var map = new TMap('map_zona');
			map.addListener("onMapLoad",function(maps){
				/*AGREGO EL LAYER POLIGONO*/
				maps.drawPolygon();
				/*DIBUJO EL POLIGONO*/ 
				maps.drawPolygonInMap(instance._polygon); 
			});
			map.addListener("onDrawPolygon",function(polyg){
			 	instance._polygon=polyg;
				
				instance.post("./?mod_cobros/delegate&zonas",{
						"calculateZonaCLICTT":'1',
						"polygon":instance._polygon 
				},function(data){ 
					  $("#z_clientes").html(data.clientes);
					  $("#z_contratos").html(data.contratos); 
				},"json");				
			});	
			map.addListener("onRemovePolygon",function(){
			 	instance._polygon=null;
			});						
			map.drawMapView();    
			
			/*CREAR ZONA*/
			$("#btz_create").click(function(){ 

				if ($('#txt_cod_zona').val()==null){
					alert('Debe de ingresar el codigo de la zona!');
					return ;	
				}
				if ($('#txt_zona').val()==null){
					alert('Debe de ingresar el nombre de la zona!');
					return ;	
				}
				if (instance._polygon==null){
					alert('Debe de seleccionar un area en el mapa!');
					return ;	
				}	
				if (oficial==null){
					alert('Debe de seleccionar un motorizado!');
					return ;	
				}
				if (motorizado==null){
					alert('Debe de seleccionar un motorizado!');
					return ;	
				}
								
				instance.post("./?mod_cobros/delegate&zonas",{
						"editZona":'1',
						"polygon":instance._polygon,
						"motorizado":motorizado,
						"nombre_zona":$('#txt_zona').val(),
						'codigo_zona':$('#txt_cod_zona').val(),
						'oficial_nit':oficial
				},function(data){ 
					instance.close(dialog); 
					window.location.reload();
				},"json");
																 
			});	
 			  
			$("#dt_motorizado").click(function(){
				$(this).hide();
				$('#item_motorizado').show();
				$('#txt_motorizado').focus();
			});
			 
			$('#txt_motorizado').select2({
				  multiple: false,
				  minimumInputLength: 4,
				  query: function (query){  
					  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
						 query.callback(data);
					   },"json");    
				  }
				});
			  
			$("#txt_motorizado").on("change", 
				function(e) { 
					motorizado=e.val; 
			});
			
			
			$("#dt_oficial").click(function(){
				$(this).hide();
				$('#item_oficial').show();
				$('#txt_oficial').focus();
			});
			 
			$('#txt_oficial').select2({
				  multiple: false,
				  minimumInputLength: 4,
				  query: function (query){  
					  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
						 query.callback(data);
					   },"json");    
				  }
				});
			  
			$("#txt_oficial").on("change", 
				function(e) { 
					oficial=e.val; 
			});			
			 			 
		},"json");	
	} ,
	
	/*ASGINAR CARTERA VIEW*/
	doAsignarView : function(request){
		var instance=this;		
		var req=$.base64.encode(request);
		instance.post("./?mod_cobros/delegate&metas&distribucion",{
				"doViewAsignarCartera":'1',
				"request":req
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Asignar cartera",data,500);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			$('#txt_oficial').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			
			var oficial=null;
			$("#txt_oficial").on("change", 
				function(e) { 
					oficial=e.val; 
			});	
			
			$("#bt_asignar_create").click(function(){ 
				if (oficial!=null){
					instance.post("./?mod_cobros/delegate&metas&distribucion",{
							"asignarCartera":'1',
							"request":req,
							"oficial":oficial
					},function(data){  
						$.growlUI('','Agregado a la cartera');
						instance.close(dialog); 	
						setTimeout(function(){
							window.location.reload();	
						},3000);
					});
				}
			});	
			
				
			 			 
		});	
	},
	/*Generar cartera*/
	doGenerarCartera : function(){
		var instance=this;		
		 
		instance.post("./?mod_cobros/delegate&metas&distribucion",{
				"doViewGenerarMeta":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Generar cartera",data,1000);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			$('#txt_oficial').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			
			var oficial=null;
			$("#txt_oficial").on("change", 
				function(e) { 
					oficial=e.val; 
			});	
			
			$("#bt_asignar_create").click(function(){ 
				 
				instance.post("./?mod_cobros/delegate&metas&distribucion",{
						"generarCartera":'1' 
				},function(data){  
					$.growlUI('','Agregado a la cartera');
					instance.close(dialog); 	
					setTimeout(function(){
						window.location.reload();	
					},3000);
				});
			 
			});	
			
				
			 			 
		});	
	} 	
	
	 
});