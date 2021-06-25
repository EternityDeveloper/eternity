 

function valida_cedula(ced) {
	var c = ced.replace(/-/g,'');
	var Cedula = c.substr(0, c.length - 1);
	var Verificador = c.substr(c.length - 1, 1);
	
	var suma = 0;

	if(ced.length < 11) { return false; }
	for (i=0;i < Cedula.length;i++) {
		mod = "";
		 if((i % 2) == 0){mod = 1} else {mod = 2}
		 res = Cedula.substr(i,1) * mod;
		 if (res > 9) {
			  res = res.toString();
			  uno = res.substr(0,1);
			  dos = res.substr(1,1);
			  res = eval(uno) + eval(dos);
		 }
		 suma += eval(res);
	}
	el_numero = (10 - (suma % 10)) % 10;
 	if ((el_numero == Verificador) && (Cedula.substr(0,3) != "000")) {
		return true;
	}else{
		return false;
	}
}

function effectShadow(name,from_id){
	for(i=0;i<2;i++) {
		$("#"+name).css("background-color","#FFFFCC").fadeTo('slow', 0.1,function(){
			$(this).css("background-color","#FFFFCC");
		}).fadeTo('slow', 1.0,function(){
			$(this).css("background-color","#fffff");
		});
	}
}

function getObject(name,number,form_id){
	i=0;
	object=null;
	//$('#form_contacto').find('#welcome')
	//if (form_id!=null) alert(form_id + " - " +number);
	$('#'+form_id).find('select[name="'+name+'"]').each(function(){			   
		if (i==number){
		//	alert(number);
			object=this;	
		}						 
		i++;
	});

	if (object==null){
		i=0;
		$('#'+form_id).find('input[name="'+name+'"]').each(function(){
			if (i==number){
				object=this;	
			}						 
			i++;
		});
	}
	
	if (object==null){
		i=0;
		$('#'+form_id).find('textarea[name="'+name+'"]').each(function(){
			if (i==number){
				object=this;	
			}						 
			i++;
		});
	}
	if (object==null){
		i=0;
		$('#'+form_id).find("#"+name).each(function(){
			if (i==number){
				object=this;	
			}						 
			i++;
		});
	}			
	return object;
}

function getInputObject(name,number){
	i=0;
	object=null;
	$('input[name="'+name+'"]').each(function(){
		if (i==number){
			object=this;	
		}						 
		i++;
	});
	
	if (object==null){
		i=0;
		$('textarea[name="'+name+'"]').each(function(){
			if (i==number){
				object=this;	
			}						 
			i++;
		});
	}
	return object;
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

function createTable(table_name,tableSettings){
	return $("#"+table_name).dataTable(tableSettings);	
}
function base64decode(base64) {
    return decodeURIComponent(escape(atob(base64)));
}
function pad(str, max) {
  return str.length < max ? pad("0" + str, max) : str;
}

function showAddress(id,id_remove,component){
	$('#'+component).find('#'+id_remove).hide();
	$('#'+component).find("#"+id).show("fast");
}