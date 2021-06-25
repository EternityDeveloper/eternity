var Servicios = new Class({
	dialog_container : null,
	_contrato_id : null, 
	_tb_servicio : "servicios_list",
	_servicio : null,
	initialize : function(dialog_container){
		this.main_class="Servicios";
		this.dialog_container=dialog_container; 
	},
	chargeView : function(productos){
		var instance=this;
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		this.addListener("onLoadPlanView",function(data){
			$('#'+instance.dialog_container).hideLoading();	
			var dialog=instance.createDialog(instance.dialog_container,"Productos Funerarios",data,500);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 50]);
			 // 'product_list_not_show':product_list_not_show
			createTable(instance._tb_servicio,{
						"bSort": false,
						"bInfo": false,
						"bPaginate": true,
						"bLengthChange": false,
						"bFilter": true, 
						"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_servicios/servicios_list&dt_list=1&status=1",
							"sServerMethod": "GET",
							"aoColumns": [ 
									{ "mData": "serv_codigo" } ,
									{ "mData": "serv_descripcion" } ,
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
							"fnServerData": function ( sSource, aoData, fnCallback ) {
 								//aoData.push( { "name": "more_data", "value": "my_value" } );
								$.getJSON( sSource, aoData, function (json) { 
									fnCallback(json)
								  
									/*AL DAR CLICK EN EL TR*/
									var tr=$('td', $('.servicios_edit').parent().parent()).parent();
									tr.css( 'cursor', 'pointer' );
									tr.click(function(){
										var nTds=$(this).children();
									 
										var  servicio= { 
													"codigo":$(nTds[0]).text(),
													"descripcion":$(nTds[1]).text(),
													"servicio_id":$(nTds).find("a").attr("id"),
													"cantidad":1
												}
										 
										instance.close(dialog);
										instance._servicio=servicio;
										instance.fire("servicios_select",servicio); 	
									});
									/*AGREGANDO HIGTHLIGHT*/
									tr.hover(function(){ 
										$(this).addClass('hover_tr');  
									},function(){ 
										$(this).removeClass('hover_tr'); 
									});
									
								} );
							},
							"fnDrawCallback": function( oSettings ) {
								
								if (!instance._isCharge){
									 /*
									 $('<button id="client_add"  class="greenButton">Agregar</button>').appendTo('#listado_cliente div.dataTables_filter'); 	
									 $("#client_add").click(function(){ 
									 	instance.close(dialog);
										instance.doViewCreatePerson(); 	
										
									  });*/
									 
									
									instance._isCharge=true;
								}
							}
							
						});
			 
		});
		
		this.chargePlainView(productos);
	},
	
	chargePlainView : function(){
		var instance=this;	 
		$.post("./?mod_servicios/servicios_list",{
			"view_simple_servicio":'1'
		},function(data){  
			instance.fire("onLoadPlanView",data); 
		});
	}
	
});