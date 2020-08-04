var _Class_Item_loading=[];
var wsclientm;

wconnect();

function wconnect(){
	wsclientm=new WebSocket("ws://ws.memorial.com.do:3001");
	wsclientm.onopen = function () {
		
		//alert(getCook("token"));
		var login={method:"doLogin",token:getCook("token")}
		console.log("Connection opened "+ login.token)  
		if (login.token!=""){
			wsclientm.send(JSON.stringify(login))
		}
	}
	wsclientm.onclose = function () {
		console.log("Connection closed")
		setTimeout(function(){
		 if (wsclientm.readyState != 1) {
			wconnect();
		}		
		},20000);
	}
	wsclientm.onerror = function () {
		//console.error("Connection error")
	}
	wsclientm.onmessage = function (event) { 
		console.log(event.data)
	} 
}

function getCook(cookiename) 
{
// Get name followed by anything except a semicolon
	var cookiestring=RegExp(""+cookiename+"[^;]+").exec(document.cookie);
// Return everything after the equal sign
	return unescape(!!cookiestring ? cookiestring.toString().replace(/^[^=]+./,"") : "");
}
var Class = function(methods) {   
    var klass = function() {    
		var instance=this;
		this.events = {};
		this.main_class="main_class";
		this.fire = function(evt, args) {
            for (x in this.events[this.main_class+evt]){
               this.events[this.main_class+evt][x].call(this, args);
			}
        }
		this.send=function(obj){
			this.getWS().send(JSON.stringify(obj));	
		}
		this.getWS= function(){
			return wsclientm;	
		}
		this.addListener = function(evt, fn) {
			 
			if (!this.existEvent(evt,fn)){			
				if (this.events[this.main_class+evt] == null) {
					this.events[this.main_class+evt] = []
				}

				this.events[this.main_class+evt].push(fn); 
			}
           
        }
		
		this.number_format =function(value){
			return parseFloat(value,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
		}
		this.existEvent = function(evt,fn){
			var instance= this;
			var rt=false;
			$.each(this.events,function(i,val){  
				if ((i==(instance.main_class+evt)) && (instance.events[instance.main_class+evt]==fn)){
					rt=true;
				} 
			});
			return rt;
		}
		
		this.removeListener = function(evt) {
            if (this.events[this.main_class+evt] != null) {
               delete this.events[this.main_class+evt];
            }
            
        }
		this.getRand = function(){
			return Math.floor(Math.random() * (1000 - 1 + 1) + 1);	
		}
		this.close = function(dialog){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		}
		this.createDiv = function(contentDiv){
			var rand="Dialog_"+this.getRand();	
			$("#"+contentDiv).append("<div id=\""+rand+"\"></div>");
			return rand;
		}
		
		this.createDialog = function(contentDiv,title,data,width){
			var rand=this.createDiv(contentDiv);
			var width_=500;
			if (width>0){
				width_=width;
			}
			var instance=this;
			$("#"+rand).attr("title",title);
			$("#"+rand).html(data);
			$("#"+rand).dialog({
				modal: true,
				width:width_, 
				close: function (ev, ui) {
					$(this).dialog("destroy");
					$(this).remove();
					instance.fire("onCloseWindow",instance);
				}
			});	
			return rand;		
		}
		this.CloseDialog= function(dialogID){
			$("#"+dialogID).modal("hide");  
		}
		this.doDialog = function(divid,contentDiv,data){
			var rand=this.createDiv(contentDiv);
			$('#'+rand).html(data);
			var instance=this; 
 			$('#'+divid).on('hide.bs.modal', function (event) {
				$(this).remove();
				instance.fire("onCloseWindow",instance);
			});
  			$('#'+divid).modal(); 
			return divid;		
		}	
			
		this.Block=function(mensaje){
			$.blockUI({ message: "Por favor espere!"}); 	
		}
		this.unBlock=function(){ 
			$.unblockUI(); 	
		}
		this.post= function(url,obj,callback,type){
			//this.Block(); 
			var stype="text"
			if (type!=null){
				stype=type;	
			} 
			$.get(url,obj,function(data){
			//	 instance.unBlock();
				 callback(data);
			},stype);
			 
		}
		/*Formatea un numero a formato de monto */
		this.number_format= function(value){
			return parseFloat(value,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()	
		}

		this.loadScript = function(script_name,func,fail){ 
			var instance=this;
			if (!$.isArray(script_name)){
				if (_Class_Item_loading[script_name]!=1){
 					/*Si es diferente de 1 entonce carga el objeto*/
					$.getScript(script_name).done(function( script, textStatus ) {
						_Class_Item_loading[script_name]=1;
 						func();
					  }).fail(function( jqxhr, settings, exception ) {
 						fail();
					});
				}else{ 
					func(); 
				}
			}else{
				var length_array=script_name.length-1; 
				var i=0;
				var counter=0;
				var _fail=false;
				
				var lda={
					_loading : function(obj){
						if (_Class_Item_loading[script_name[i]]!=1){ 
							var script_n=script_name[i]; 
							/*Si es diferente de 1 entonce carga el objeto*/
							$.getScript(script_name[i]).done(function( script, textStatus ) {  
								_Class_Item_loading[script_n]=1;  
								if (counter==length_array){ 
 									func();
								}else{
									i++;
									obj._loading(obj);						
								}
								counter++;  
							  }).fail(function( jqxhr, settings, exception ) {
								if (!_fail){ 
 									_fail=true; 
									fail();
								} 
							});
						}else{ 
							if (length_array>i){
 								i++;
								obj._loading(obj);	
							}else{
  								func();
							}
						}
					}
					
				} 
				
				lda._loading(lda) 
			}
			 		
		}  		
		
        this.initialize.apply(this, arguments);          
    };  
    
    for (var property in methods) { 
       klass.prototype[property] = methods[property];
    }
          
    if (!klass.prototype.initialize) klass.prototype.initialize = function(){};      
    
    return klass;    
};