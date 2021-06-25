/*  */
var DistribuccionMapa = new Class({
	dialog_container : null,  
	_rand : null,
	_vector_circle : null,
	_vector_labels : null,
	_maps: null,
	initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
	},	   
	viewOnMap : function(id_mapa){
		var instance=this;
		var gerente="";
		var productos="";
		
		$('#gerentes').select2();
		$('#productos').select2();
		
		$("#gerentes").on("change", 
			function(e) { 
				gerente=e.val; 
		});		
		$("#productos").on("change", 
			function(e) { 
				productos=e.val; 
		});			
		$("#bt_distribuccion").click(function(){
			instance.filtrar_mapa({"optenerDistribucion":1,"gerente":gerente,"productos":productos});	
		});
		
		var map = new TMap(id_mapa);
		map.addListener("onMapLoad",function(maps){  
		
			instance._vector_circle = new OpenLayers.Layer.Vector("Overlay");															
			instance._vector_labels = new OpenLayers.Layer.Vector("Vector", 
			{
				styleMap: new OpenLayers.StyleMap(            
				{
					label : "${labelText}",                    
					fontColor: "blue",
					fontSize: "14px",
					fontFamily: "Courier New, monospace",
					fontWeight: "bold",
					labelAlign: "lc",
					labelXOffset: "14",
					labelYOffset: "0",
					labelOutlineColor: "white",
					labelOutlineWidth: 3
				})
			});
			 
			maps._mapLayer.addLayer(instance._vector_circle);  
			maps._mapLayer.addLayer(instance._vector_labels)
			
			instance._maps=maps; 
			instance.filtrar_mapa({"optenerDistribucion":1});
 

		});  				
		map.drawMapView(); 
 			
	},
	filtrar_mapa : function(info){
		var instance=this;
		this._vector_labels.removeFeatures( this._vector_labels.features, {silent: true} );
		this._vector_circle.removeFeatures( this._vector_circle.features, {silent: true} );
		
		
		instance.post("./?mod_cobros/delegate&zonas&distribuccion=1",info,
		function(data){   
			var feat_label = [];
			var feat_circle = [];				
			
			var ac=0;
			for(i=0;i<data.length;i++){
				if (data[i].LongLact==null ){
					ac=ac+ parseInt(data[i].TOTAL);
				}else{
					ac=ac+ parseInt(data[i].TOTAL);
				}
			}
			
			$("#cantidad_").html(ac);
			
			
			for(i=0;i<data.length;i++){
				if (data[i].LongLact==null ){
					//alert('fsd');
				}else{
					var sp= data[i].LongLact.split(",");
			//		console.log(data[i]);
					
					var lonLat = new OpenLayers.LonLat(sp[0],sp[1])
						.transform(
							new OpenLayers.Projection("EPSG:4326"), //transformando de WGS 1984
							instance._maps._mapLayer.getProjectionObject() 
						);						
					var tamano=10;	
					tamano=tamano*data[i].TOTAL;
					
					var point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
					var circle = OpenLayers.Geometry.Polygon.createRegularPolygon(
						point,
						tamano,
						30,
						0
					);
					var feature = new OpenLayers.Feature.Vector(circle);							
					feat_circle.push(feature);
					
					var pt = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
					feat_label.push(new OpenLayers.Feature.Vector(pt,{labelText:data[i].TOTAL}));
				//		
					
				}
			//	console.log(data[i]);
			}
			
			instance._vector_labels.addFeatures(feat_label);
			instance._vector_circle.addFeatures(feat_circle);	

		},"json");		
	}	
	 
});