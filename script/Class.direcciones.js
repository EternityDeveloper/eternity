var address={provincia:"",munic:"",ciudad:"",sector:"",number:"",component:""};
var addressName={provincia:"",munic:"",ciudad:"",sector:"",number:"",component:""};

var PersonAddress= Class({
	back_formName:null,
	formName: null,
	container : null,
	client_id : null,
	contact_id :0,
	initialize : function(formName,client_id,containerName){
		 this.formName=formName; 
		 this.back_formName=formName;
		 this.container=containerName;
		 this.client_id=client_id; 
	},
	setContactID : function(contact){
		this.contact_id=contact;
	},
	addNewAddress : function(dispached,itemFormSubmit){
		var rand=this.getRand();
		var obj={	  
					comp:this.formName,
					client_id:this.client_id,
					add_edit_buttom:1,
					'contact_id':this.contact_id,
					'dispached':dispached,
					'itemFormSubmit': itemFormSubmit,
					"createAddress":1,
					'rand':rand
				}
		var form_id=this.formName;
		var instance=this;
		$.post("./?mod_component/comp_direcciones",obj,function(data){
			
			$("#"+instance.container).before(data);
			//instance.addAdvanceSearch();
			instance.effectShadow("master_direction"); 

			
 			$('.direcciones_faster').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_component/request_direcciones&action=finder&number=0&component="+form_id,
				  	{"address":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			$(".direcciones_faster").on("change", 
				function(e) {  
					instance.selectedAdd(e.val);
					//preparado_por=e.val; 
			});
					
						
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+instance.formName).remove();
			});	
			
			$("#address_save").click(function(){
				if ($("#"+instance.formName).valid()){
					/*Evento Vista cargada correctamente!*/ 
					instance.fire("AddressSave",$("#"+instance.formName).serializeArray());	
					/*REMUEVO LA VISTA*/
					$("#"+instance.formName).remove();
				}
			});
			
		})	
	
	},
	selectedAdd : function(label){
		var instance=this;  
		  spName = label.split(" / ");
		  sp = label.split(" / "); 
		  address.provincia=sp[0];
		  address.munic=sp[1];
		  address.ciudad=sp[2];
		  address.sector=sp[3];
		  address.number=sp[4];
		  address.component=sp[5];
		  
							  
		  addressName.provincia=spName[0];
		  addressName.munic=spName[1];
		  addressName.ciudad=spName[2];
		  addressName.sector=spName[3];
		  
		   var proccess={
				  component:address.component,
				  loadlist: function(){
					  
						  var oEvent=function(){ return {
								id: null,
								comparer:null,
								number:null,
								component:null,
								selectItem: function(){
									var comp=this.comparer;
									var number=this.number;
									var component=this.component;
									//alert(comp);
									 /*
									$(getObject(this.id,this.number,this.component)).children().map(function() {
										alert($.trim(items))
										var items=$(this).text();
										if ($.trim(items)==comp){
											$(this).prop('selected', true);
											
											$(getObject("faster_search[]",number,component)).val('');
										}
									 });	*/
								}  
							  }
						  }
						 
						  $(getObject("provincia_id[]",address.number,address.component)).val(address.provincia);
						  
						  var municipio= new oEvent();
						  municipio.id="municipio_id[]";
						  municipio.comparer=addressName.munic;
						  municipio.number=address.number;
						  municipio.component=address.component;
					  
						  loadAddressComponent(address.provincia,'loadmunicipio',address.number,'municipio_charge',municipio);
						
						  var ciudad= new oEvent();
						  ciudad.id="cuidad_id[]";
						  ciudad.comparer=addressName.ciudad;			
						  ciudad.number=address.number;
						  ciudad.component=address.component;
						  loadAddressComponent(address.munic,'loadciudad',address.number,'ciudad_charge',ciudad);
						  
						  var sector= new oEvent(); 
						  sector.id="sector_id[]";
						  sector.comparer=addressName.sector;
						  sector.number=address.number;
						  sector.component=address.component;
						  loadAddressComponent(address.ciudad,'loadsector',address.number,'sector_charge',sector);

				  }
			  };

		  instance.showAddressView("faster_search_rm","address",proccess);		
	},
	
	addAdvanceSearch : function(){
		
		var value=$(getObject("faster_search[]",0,this.formName));
		var form_id=this.formName;
		var instance=this;
		
		$(value).autocomplete({
			  source: function(request,response) {
				$.ajax({
					  url: "./?mod_component/request_direcciones&action=finder&number="+0+"&address="+value.val()+"&component="+form_id,
					  dataType: "json",
					  data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						name_startsWith: request.term
					  },
					  success: function( data ) {
						response( $.map( data, function( item ) {
						  return {
							label: item.address,
							value: item.sector
						  }
						}));
					  }
					});  			  
			  },
			  minLength: 2,
			  select: function( event, ui ) {

				  
				  spName = ui.item.label.split(" / ");
				  sp = ui.item.value.split(" / "); 
				  address.provincia=sp[0];
				  address.munic=sp[1];
				  address.ciudad=sp[2];
				  address.sector=sp[3];
				  address.number=sp[4];
				  address.component=sp[5];
				  
									  
				  addressName.provincia=spName[0];
				  addressName.munic=spName[1];
				  addressName.ciudad=spName[2];
				  addressName.sector=spName[3];
				  
				   var proccess={
						  component:address.component,
						  loadlist: function(){
							  
								  var oEvent=function(){ return {
										id: null,
										comparer:null,
										number:null,
										component:null,
										selectItem: function(){
											var comp=this.comparer;
											var number=this.number;
											var component=this.component;
											$(getObject(this.id,this.number,this.component)).children().map(function() {
												var items=$(this).text();
												if ($.trim(items)==comp){
													$(this).prop('selected', true);  
												}
											 });	
										}  
									  }
								  }
 
								  $(getObject("provincia_id[]",address.number,address.component)).val(address.provincia);
								  
								  var municipio= new oEvent();
								  municipio.id="municipio_id[]";
								  municipio.comparer=addressName.munic;
								  municipio.number=address.number;
								  municipio.component=address.component;
							 	  
								  
								  loadAddressComponent(address.provincia,'loadmunicipio',address.number,'municipio_charge',municipio);
								
								  var ciudad= new oEvent();
								  ciudad.id="cuidad_id[]";
								  ciudad.comparer=addressName.ciudad;			
								  ciudad.number=address.number;
								  ciudad.component=address.component;
								  loadAddressComponent(address.munic,'loadciudad',address.number,'ciudad_charge',ciudad);
								  
								  var sector= new oEvent(); 
								  sector.id="sector_id[]";
								  sector.comparer=addressName.sector;
								  sector.number=address.number;
								  sector.component=address.component;
								  loadAddressComponent(address.ciudad,'loadsector',address.number,'sector_charge',sector);
	  
						  }
					  };

				  instance.showAddressView("faster_search_rm","address",proccess);
				  
			  },
			  open: function() {
				$(this).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			  },
			  close: function() {
				$(this).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			  }
		});
	},
	showAddress : function(itemHidden,itemShow){
		$(getObject(itemHidden,0,this.formName)).hide();
		$(getObject(itemShow,0,this.formName)).show("fast");
	},
	showAddressView : function(itemHidden,itemShow,proccess){
		$(getObject(itemHidden,0,this.formName)).hide();
		$(getObject(itemShow,0,this.formName)).show("fast",function(){
			if (proccess!=null){
				proccess.loadlist();
			}
		});
	},
	effectShadow: function(itemName){
		$(getObject(itemName,0,this.formName)).css("background-color","#FFFFCC").fadeTo('slow', 0.1,function(){
			$(this).css("background-color","#FFFFCC");
		}).fadeTo('slow', 1.0,function(){
			$(this).css("background-color","#fffff");
		});
 
	},
	viewAddress : function(divContainer,contact_id,index){
		var container=this.container;
		var instance = this;
		var laststatus=0;
		var name_from_address=this.formName+"_"+Math.floor(Math.random()*1000);
		this.formName=name_from_address;
		var rand=this.getRand();
		var obj={	
					'index' : index,
					comp:name_from_address,
					client_id:this.client_id,
					remove_advance_find:1,
					remove_estatus_bt:0,
					'contact_id' : contact_id,
					'rand': rand
				}
		var lastEstatusTipoDireccion=null;
		this.post("./?mod_component/comp_direcciones",obj,function(data){
 
			var dialog=instance.createDialog(divContainer,"Ver detalles Dirección",data,700);
			
			instance.addAdvanceSearch();
			instance.effectShadow("master_direction"); 			
			
			lastEstatusTipoDireccion=$(getObject("direccion_tipo[]",0,obj.comp)).val();
			 
			$("#cerrar_ventana2").click(function(){
			   $("#"+dialog).dialog("close");
			});
			
			$("#address_save_btn_"+rand).click(function(){
				if ($("#"+instance.formName).valid()){
					
					var address_type=lastEstatusTipoDireccion; //$(getObject("direccion_tipo[]",0,obj.comp)).val();
					var direccion_id=$(getObject("direccion_id",0,obj.comp)).val();;
					  
					var sndX={
							'estatus': $("#estatus_disable").val(),
							'adress_submit':'1',
							'id': obj.client_id,
							'tipo_direccion': address_type,
							'contact_id' : instance.contact_id,
							'direccion_id' : direccion_id
						};
					   
					instance.post("./?mod_client/client_edit",sndX,function(data){	
						if (data.typeError=="104"){
							/*Evento Vista cargada correctamente!*/ 
							instance.fire("AddressEditSave",$("#"+instance.formName).serializeArray());	
							$("#"+instance.formName).remove();
 							$("#"+dialog).dialog("close");
							$("#"+dialog).remove();
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
					},"json");	
					
								
				}
			});
			  
			$(getObject("estado[]",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 }); 
			 
			$(getObject("estado[]",0,obj.comp)).change(function(){
				  
				 if ($(getObject("estado[]",0,obj.comp)).val()!=0){
				 //	alert(lastEstatusTipoDireccion);
					 var c=confirm("Esta seguro que desea desabilitar esta direccion?");
					 
						if (c){
							
							var address_type=lastEstatusTipoDireccion;//$(getObject("direccion_tipo[]",0,obj.comp)).val();
							var direccion_id=$(getObject("direccion_id",0,obj.comp)).val();
							 
							var sndX={
									'estatus': $(this).val(),
									'adress_submit':'1',
									'id': obj.client_id,
									'tipo_direccion': address_type,
									'contact_id' : instance.contact_id,
									'direccion_id' : direccion_id
								};
							 
							 
							$.post("./?mod_client/client_edit",sndX,function(data){	
								if (data.typeError=="104"){
									instance.formName=instance.back_formName;
									instance.fire("AddressChangeEstatus");
									$("#"+dialog).dialog("close");
									$("#"+dialog).remove();
									alert(data.mensaje);
								}else{
									alert(data.mensaje + " error "+data.typeError);
								} 
							},"json");
						 
							 
						}else{
							$(this).val(laststatus);
						}
				  }else{
					 $(this).val(laststatus); 
				   }
				 
			 });		
			
		});		
		
	},
	loadAddress : function(){
		var container=this.container;
		var instance = this;
		
		$("#"+this.formName+"list").remove();
	
		var obj={	
					comp:this.formName+"list",
					client_id:this.client_id,
					remove_advance_find:1,
					remove_estatus_bt:0,
					'contact_id' : this.contact_id 
				}
		var laststatus=0;
		
		
		$.post("./?mod_component/comp_direcciones_list",obj,function(data){
			$("#"+container).after(data);
			
			
			$(getObject("role_list",0,obj.comp)).dataTable({
				"bFilter": false,
				"bLengthChange": false,
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
				
			/*CAPTURA EL LISTADO DE DIRECCIONES QUE SE LISTAN PARA SER EDITADOS*/
			$(".direccion_edit").click(function(){
				var inf={
					"index":$(this).attr("id"),
					"contact_id":$(this).attr("contact_id")
				}
				instance.fire("doEditAddress",inf);
			});

		});
	}
	
});