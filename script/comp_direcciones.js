// JavaScript Document
var address={provincia:"",munic:"",ciudad:"",sector:"",number:"",component:""};
var addressName={provincia:"",munic:"",ciudad:"",sector:"",number:"",component:""};

var adressEvent=function(){ return {
	id: null,
	comparer:null,
	number:null,
	component:null,
	selectItem: function(){
		var comp=this.comparer;
		$(getObject(this.id,this.number,this.component)).children().map(function() {
			var items=$(this).val();
			//alert(items);
			if ($.trim(items)==comp){
				$(this).prop('selected', true);
			}
		 });	
		 
		$(getObject(this.id,this.number,this.component)).prop('disabled', 'disabled');
	}  
  }
}


function showAddressView(id,id_remove,proccess){
	//alert(proccess.component)
	$('#'+proccess.component).find('#'+id_remove).hide();
	$('#'+proccess.component).find("#"+id).show("fast",function(){
		if (proccess!=null){
			proccess.loadlist();
		}
	});
}

function showAddress(id,id_remove,component){
	$('#'+component).find('#'+id_remove).hide();
	$('#'+component).find("#"+id).show("fast");
}


/* Carga los componentes de una determinada clasificacion
	por ejemplo quiero cargar los municipios que pertenecens a x provincias
*/
function loadAddressComponent(id_field,modulo,number,when_charge_id,eventObject){
	if (id_field!=-1){
		$.post("./?mod_component/request_direcciones",{number:number,action:modulo,id_field:id_field,component:eventObject.component},function(data){
			//alert(eventObject.component+" - " +when_charge_id )
			//alert($('#'+eventObject.component).find("#"+when_charge_id).html());
			$('#'+eventObject.component).find("#"+when_charge_id).html(data);
			if (eventObject!=null){
				eventObject.selectItem(); 
				setTimeout(function () { }, 1000);
				
			}
		})
	}
}

function loadAddressField(id_field,modulo,number,when_charge_id,component){
	if (id_field!=-1){
		$.post("./?mod_component/request_direcciones",{number:number,action:modulo,id_field:id_field,component:component},function(data){
			
			$('#'+component).find("#"+when_charge_id).html(data);
		})
	}
}

function createFasterSearch(number,form_id){
	var i=0;
 			//alert(number);
			var value=$(getObject("faster_search[]",number,form_id));
	 
			$(value).autocomplete({
				  source: function(request,response) {
					$.ajax({
						  url: "./?mod_component/request_direcciones&action=finder&number="+number+"&address="+value.val()+"&component="+form_id,
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
													
													$(getObject("faster_search[]",number,component)).val('');
												}
											 });	
										}  
									  }
								  }
								 
								  
								  //$("#provincia_id_"+address.number).val(address.provincia);
								
								  $(getObject("provincia_id[]",address.number,address.component)).val(address.provincia);
								 // alert(getObject("provincia_id[]",address.number) +" - "+ address.number);
								  
								  var municipio= new oEvent();
								  municipio.id="municipio_id[]";
								  municipio.comparer=addressName.munic;
								  municipio.number=address.number;
								  municipio.component=address.component;
								  
								//  alert(municipio.component);
								  loadAddressComponent(address.provincia,'loadmunicipio',address.number,'municipio_charge_'+address.number,municipio);
								
								  var ciudad= new oEvent();
								  ciudad.id="cuidad_id[]";
								  ciudad.comparer=addressName.ciudad;			
								  ciudad.number=address.number;
								  ciudad.component=address.component;
								  loadAddressComponent(address.munic,'loadciudad',address.number,'ciudad_charge_'+address.number,ciudad);
								  
								  var sector= new oEvent(); 
								  sector.id="sector_id[]";
								  sector.comparer=addressName.sector;
								  sector.number=address.number;
								  sector.component=address.component;
								  loadAddressComponent(address.ciudad,'loadsector',address.number,'sector_charge_'+address.number,sector);
	  
						  }
					  };
					  
					  showAddressView('address_'+address.number,'faster_search_rm_'+address.number,proccess);
					  
					  
					  
				  },
				  open: function() {
					$(this).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				  },
				  close: function() {
					$(this).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
				  }
			});
			
			$(getObject("faster_search[]",number,form_id)).focus();
			
 
}


