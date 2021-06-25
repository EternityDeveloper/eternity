/*
	Clase que maneja el comportamiento 
	del mapa
*/
var TMap = new Class({
	_map_manager: 	"",
	_id 		: 	null,
	_mapLayer	:	null,
	_mapstreet	:	null,
	_maphibrido	:	null,
	_dpolygon 	: null,
	_lgeocercas : null,
	_css_class  : 	"",
	_layer_list : [],
	_id_pool : 0, 
	_geom: null,
	_marker : null,
	initialize : function(map_container){
		this.main_class="TrackMap"; 
		this._id_pool=this.getRand();
		this._id="map_"+this._id_pool;
		this._layer_list[this._id_pool]=[];
		this._id=map_container;
		
	},
	putClass : function(_class){
		this._css_class=this._css_class+" "+ _class;
	},
	putLayer : function(layer){
		this._layer_list[this._id_pool].push(layer);
	},
	getLayers : function(){
		return this._layer_list[this._id_pool];
	}, 
	drawMapView : function(){
		var instance=this;
		this._mapLayer = new OpenLayers.Map({
					div:this._id,
					maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508.34)
				});
				
		this._mapLayer.addControl(new OpenLayers.Control.LayerSwitcher());
			
		this._mapstreet = new OpenLayers.Layer.Google(
			"Mapa Calles", 
			{numZoomLevels: 20}
		);
		this._maphibrido = new OpenLayers.Layer.Google(
			"Mapa Hibrido",
			{
				type: google.maps.MapTypeId.HYBRID, 
				numZoomLevels: 20,
				minResolution: "auto",
                minExtent: new OpenLayers.Bounds(-1, -1, 1, 1),
                maxResolution: "auto"
			}
		);
	

		this._mapLayer.addLayers([this._mapstreet, this._maphibrido ]);	 
 		var lonLat = new OpenLayers.LonLat(-69.9333333,18.4833333)
            .transform(
                new OpenLayers.Projection("EPSG:4326"), //transformando de WGS 1984
                this._mapLayer.getProjectionObject() 
            );		
		this._mapLayer.setCenter(lonLat, 12);	 
		
		/*Evento avisando que el mapa a cargado*/
		this.fire("onMapLoad",this);
		 
		return this._id; 	
	},
	drawPolygon : function(){
		var instance=this;
		/* creacion del layer de geocercas */
		this._lgeocercas = new OpenLayers.Layer.Vector("ZONA");
		this._mapLayer.addLayer(this._lgeocercas);
		
		var panel = new OpenLayers.Control.Panel({
			displayClass: "olControlEditingToolbar"
		});
		 
		this._dpolygon= new OpenLayers.Control.DrawFeature(this._lgeocercas,
							OpenLayers.Handler.Polygon,
							{displayClass: "olControlDrawFeaturePolygon", title: "Definir ZONA"}) 
		 
		panel.addControls([
			new OpenLayers.Control.Navigation({title: "Navegar"}),
			this._dpolygon
		]);
		
		this._dpolygon.events.register('activate',this, function(data){
			instance._lgeocercas.removeAllFeatures();
			instance.fire("onRemovePolygon");
		});

		this._dpolygon.events.register('featureadded',this, function(data)
		{
		   if(data && data.type && data.type == 'featureadded')
			{
				var feature = data.feature;
				geom = feature.clone().geometry.transform(new OpenLayers.Projection("EPSG:900913"),
													new OpenLayers.Projection('EPSG:4326')).toString(); 
													
				this._dpolygon.deactivate();
	 			instance._geom=geom;	
				 
				instance.drawPolygonInMap($.base64.encode(geom)); 	
				
				instance.fire("onDrawPolygon",$.base64.encode(geom));
				
				/*Verifico que no sea una edicion*/
			/*	if (!instance.getViewEditGeom()){
					instance.openWindowAddGeom();
				}else{ 
					
				}	*/			
				
			}
		
		});		
		
		this._mapLayer.addControl(panel);	
		
	},
	drawPolygonInMap : function(poligono){
		var instance=this; 
		
		instance._lgeocercas.removeAllFeatures();

		var in_options = {
				'internalProjection': new OpenLayers.Projection("EPSG:900913"),
				'externalProjection': new OpenLayers.Projection("EPSG:4326")
			}; 	
		var config = OpenLayers.Util.extend(
			{extractStyles: true}, in_options);
	 
		var wkt = new OpenLayers.Format.WKT(config)	
		var poligono=$.base64.decode(poligono); 
		
		if ($.trim(poligono)!=""){		
			var feature = wkt.read(poligono); 
			this._lgeocercas.addFeatures([feature]); 
			this._mapLayer.zoomToExtent(this._lgeocercas.getDataExtent());
		}	
	},
	
	drawPolygonToVector : function(poligono,vector,color){
		var instance=this; 
		
		vector.removeAllFeatures();

		var in_options = {
				'internalProjection': new OpenLayers.Projection("EPSG:900913"),
				'externalProjection': new OpenLayers.Projection("EPSG:4326")
			}; 	
		var config = OpenLayers.Util.extend(
			{extractStyles: true}, in_options);
	 
		var wkt = new OpenLayers.Format.WKT(config)	
		var poligono=$.base64.decode(poligono); 
	
		
		if ($.trim(poligono)!=""){		
			var feature = wkt.read(poligono); 
				
			vector.addFeatures([feature]); 
			this._mapLayer.zoomToExtent(vector.getDataExtent());
		}	
	}	
	
	
});