var geocoder;
var map;

var CIdentifciado = new Class({
	dialog_container : null, 
	_id : null,
	_address :null,
	initialize : function(dialog_container){
		this.main_class="CIdentifciado";
		this.dialog_container=dialog_container; 
	},	  
	 
	doInit : function(){ 
		var instance=this;
		$("#role_list").dataTable({
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
 
		
		$("#_verificar").click(function(){
			 
			if ( $("#contrato").val()!=""){
				instance.post("./?mod_cobros/delegate&processIdentificado",{
					id:instance._id,
					direccion:instance._address,
					contrato: $("#contrato").val()
				},function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
				},"json");
			}else{
				alert('Favor ingresar un numero de contrato!');	
			}
		});	
		
		$("#_cancelar").click(function(){ 
			if ( $("#descripcion").val()!=""){
				instance.post("./?mod_cobros/delegate&processNoIdentificado",{
					id:instance._id ,
					descripcion:$("#descripcion").val()
				},function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
				},"json"); 
			}else{
				alert('Favor ingresar una descripcion!');	
			}
		});				
	}, 
	doCreatePoint : function(obj){
		var instance=this;
		instance._id=$(obj).attr("id");
		var data=JSON.parse($.base64.decode($(obj).attr("data")));	
		//$("#_id_field").html(data.id);
		instance._traceOnMap(data);
		$("#panel").show();
	},
	_traceOnMap :function(obj){
		var instance= this;
 		var lat = parseFloat(obj.latitud);
		var lng = parseFloat(obj.longitud);
		var latlng = new google.maps.LatLng(lat, lng);
		if (marker!=null){
			marker.setMap(null);	
		}
		geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
		  if (results[1]) {
			map.setZoom(17);
			marker = new google.maps.Marker({
				position: latlng,
				map: map
			});
			instance._address=results[0].formatted_address;
			$("#direccion").val(instance._address);
			infowindow.setContent(results[0].formatted_address+" <br><strong>Hora:</strong>"+obj.hora_parada);
			infowindow.open(map, marker);
		  } else {
			alert('No results found');
		  }
		} else {
		  alert('Geocoder failed : ' + status);
		}
		});

	}      
	 
});