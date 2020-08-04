var PivotTable = new Class({
	dialog_container : null, 
	pivot_item : {
		conainer_top_item: "conainer_top_item",
		view_item: "view_item",	
		group_item : "group_item"
	},
	_data_pivot : [],
	_pivotField : null,
	initialize : function(container){
		this.main_class="PivotTable";
		this.dialog_container=container; 
	},
	loadJson : function(){
		var instance=this;
		this._data_pivot=[];
		$.post("./conf.json",null,function(data){ 
			for (obj in data){  
				instance._data_pivot.push(new PivotObject(data[obj]));
			} 
			instance.createPivotField();
		},"json"); 
	},
	create : function(){ 
		for (items in this.pivot_item) { 
			this.createDragAndDropItem(items);
		} 
		this.loadJson();
	},
	
	createDragAndDropItem : function(item_name){
		var instance=this;
		$("#"+item_name+"  > li" ).draggable({
		  appendTo: "body",
		  helper: "clone"
		});	
		var str="";
		for (items in this.pivot_item) {
			if (items!=item_name){
				str=str+"#"+items+" > li ,";
			}
		}
		str=str.substring(0,str.length-1);
  
		$("#"+item_name).droppable({ 
			  accept: str,
			  drop: function( event, ui ) {     
			  	//alert($(this).attr("id")) 
				var index=$(ui.draggable).attr("id").split("_")[1]; 
				ui.draggable.remove(); 
				instance._pivotField.draw($(this),index,instance._pivotField._group_fields[index]); 
			  }
		  });
		$("#"+item_name).sortable({
		   placeholder: "ui-state-highlight" 	   
		});	  
	},
	
	createPivotField : function(){
	//new PivotObject(obj,data[obj],instance.pivot_item.conainer_top_item)	
		this._pivotField= new PivotGroupField(this.pivot_item.conainer_top_item);
		for (index in this._data_pivot){    
			this._pivotField.addData(this._data_pivot[index].getData(),index);
		}
		this._pivotField.drawList();
	 
	}
	
});

var PivotObject = new Class({
	_id : 0,
	_data : null,
	_draw_in: null,
	initialize : function(data){
		this.main_class="PivotObject"; 
		this._data=data;  
	},
	getData : function(){
		return this._data;	
	} 
});

var PivotField= new Class({
	_id : 0,  
	_name_item : [],
	_group: null,
	initialize : function(groupField,name){
		this.main_class="PivotField"; 
		this._group=groupField;  
		this._id=this.getRand();
		this._name_item[this._id]=[];
		this.addItem(name);
	},
	getItems : function(){
		return this._name_item[this._id];
	},
	addItem : function(name){
		var idx=this.findItemExist(name);
		if (idx>=0){  
		 	 this._name_item[this._id][idx].counter++;
			// alert("Incrementando el counter =>"+this._name_item[this._id][idx].counter);
		}else{
			this._name_item[this._id].push({name:name,counter:1}); 
		}
	},
	findItemExist : function(name){
		var idx=-1; 
		for (index in this._name_item[this._id]){ 
			//alert(this._name_item[this._id][index].name+ " "+name);
			if (this._name_item[this._id][index].name==name){ 
				idx=index;
				break;
			}
		}  
		return idx;
	}
});

var PivotGroupField= new Class({
	_id : 0,
	_group_fields : [],
	_draw_in: null,
	initialize : function(draw_name){
		this.main_class="PivotGroupField";
		this._draw_in=draw_name; 
	},
	addData : function(data,index){
		var indx=-1;
		for(groupField in data){ 
			this.addPivotField(groupField,data[groupField]); 
		}	
	},
	addPivotField : function(groupField,name){ 
		var indx=-1;
		indx=this.findItemExist(groupField);
		if (indx>=0){ 
			this._group_fields[indx].addItem(name);
			return true;
		}else{ 
			var items= new PivotField(groupField,name);  
			this._group_fields.push(items);
			 
		}
		
		return false;
	},
	findItemExist : function(group){
		var indx=-1; 
		for (index in this._group_fields){ 
			if (this._group_fields[index]._group==group){ 
				indx=index; 
				break;
			}else{
				indx=-1;
			}
		}  
		return indx;
	},
	
	drawList : function(){ 
		for (index in this._group_fields){   
	/*		var dt='<li id="list_'+index+'" class="content_item "><span>'+ this._group_fields[index]._group + " ("+
			this._group_fields[index].getItems().length +')</span>'+'<span id="triangle_'+index+'" class="pvtTriangle" style="width:19px;height:19px;">&nbsp;</span></li>';
			$(dt).appendTo($("#"+this._draw_in));
		 	this.refreshEvent(index);*/
			this.draw($("#"+this._draw_in),index,this._group_fields[index]);
		}
	},
	draw : function(container,index,group_field){
		var dt='<li id="list_'+index+'" class="content_item "><span>'+ group_field._group + " ("+group_field.getItems().length +')</span>'+'<span id="triangle_'+index+'" class="pvtTriangle" style="width:19px;height:19px;">&nbsp;</span></li>';
		$(dt).appendTo(container);
		this.refreshEvent(index);		
	},
	refreshEvent : function(index){
		var instance=this;
		$("#triangle_"+index).click(function(){
			var index=$(this).attr("id").split("_")[1];
			alert(instance._group_fields[index].getItems().length);	
		});
	} 
});