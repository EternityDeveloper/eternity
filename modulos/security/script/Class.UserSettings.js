/*Esta clase es para gestionar todas las opciones del usuario desde creaacion hasta edicion*/
var UserSettings = new Class({
	dialog_container : null, 
    userID : null,
	initialize : function(dialog_container){
		this.main_class="Prospectos";
		this.dialog_container=dialog_container;
	},
    setUserID : function(id){
        this.userID=id;
    },
    getUserID : function(){
        return  this.userID;
    },
    /*ID es el encrypt del usuario*/
    showEditWindow : function(id){
        var instance=this;
        this.setUserID(id);
        this.post("?mod_security/role_list",{"general_settings":1,"request":id},function(data){
			   var dialog=instance.createDialog(instance.dialog_container,"Datos Generales",data,500);
			  // instance._dialog=dialog;
			   var n = $('#'+dialog);
			   n.dialog('option', 'position', [(document.scrollLeft/550), 100]);  
			   $("#bt_change_password").click(function(){
					 instance.viewChangePassword();
			   });	  
			   $("#bt_codigo_figs").click(function(){
					 instance.viewChangeCodeFigs();
			   });	
			   $("#bt_general_cancel").click(function(){
					 instance.close(dialog);
			   });
               
               $("#Roles").change(function(){ 
                    $("#bt_role_save").show();
               });
               
               $("#bt_role_save").click(function(){
                    
                    instance.post("?mod_security/role_list",
                    {
                        "general_settings":1,
                        'update_role':1,
                        "request":instance.getUserID(),
                        'roles':$("#Roles").val() 
                    },
                    function(data){
                        alert(data.mensaje);
                        if (!data.error){
							instance.fire("user_change");
                            $("#bt_role_save").hide();
                        }

                    },"json");  
               });
			   
			   $("#tipo_usuario").change(function(){ 
                    $("#bt_tipo_user_save").show();
               });
			   $("#bt_tipo_user_save").click(function(){
                    
                    instance.post("?mod_security/role_list",
                    {
                        "general_settings":1,
                        'update_tipo_user':1,
                        "request":instance.getUserID(), 
						'idtipo_usuario':$("#tipo_usuario").val()
                    },
                    function(data){
                        alert(data.mensaje);
						
                        if (!data.error){
							instance.fire("user_change");
                            $("#bt_role_save").hide();
                        }

                    },"json");  
               });
			   
			   $("#g_estatus").change(function(){ 
                    $("#bt_save_disable").show();
               });
			   
			   $("#bt_save_disable").click(function(){  
                   instance.disable_user();
               });
						   
			   			   
			   
        });
        
    },
	disable_user : function(){
		var instance=this;
		instance.post("?mod_security/role_list",
		{
			"general_settings":1,
			'update_estatus':1,
			"request":instance.getUserID(),
			'estatus':$("#g_estatus").val() 
		},
		function(data){
			alert(data.mensaje);
			if (!data.error){
				instance.fire("user_change");
				$("#bt_role_save").hide();
			}
		
		},"json");  
	},
	viewChangePassword : function(){
        var instance=this;
		var view='<form id="password_change" name="password_change"><div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;"> <table width="100%" border="1"> <tr> <td height="35">Contraseña nueva:</td> <td><input name="password" type="password" id="password" autocomplete="off" /></td> </tr> <tr> <td height="35">Confirmación repita:</td> <td><input name="password_repeat" type="password" id="password_repeat" /></td> </tr> <tr> <td colspan="2" align="center"><div class="buttons" style="width:200px;"><br> <button type="button" class="positive" id="bt_save_pwd"> <img src="images/apply2.png" alt=""/> Guardar</button> <a href="#"  class="negative" id="bt_cancel_pwd"><img src="images/cross.png" alt=""/> Cancel</a> </div></td> </tr></table></div></form>';
        
        var dialog=this.createDialog(instance.dialog_container,"Modificar contraseña",view,500);
        var n = $('#'+dialog);
        n.dialog('option', 'position', [(document.scrollLeft/550), 150]);  
        $("#password").focus();
        $("#bt_save_pwd").click(function(){
            
            if ($("#password_change").valid()){
               instance.post("?mod_security/role_list",{"general_settings":1,
                                                        'change_password':1,
                                                        "request":instance.getUserID(),
                                                        'password':$("#password").val(),
                                                        'password_repeat':$("#password_repeat").val()
                                                       },
                    function(data){
                        alert(data.mensaje);
                        if (!data.error){
							instance.fire("user_change");
                            instance.close(dialog);
                        }

                },"json");  
            }
            
        });		 
        $("#bt_cancel_pwd").click(function(){
            instance.close(dialog);
        });
        
        this.validate_pwd();
        
	},
	viewChangeCodeFigs : function(){
        var instance=this;
		var view='<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;"> <table width="100%" border="1"> <tr> <td height="35">Codigo:</td> <td><input name="codigo_figs" type="text" id="codigo_figs" autocomplete="off" /></td> </tr> <tr> <td colspan="2" align="center"><div class="buttons" style="width:200px;"><br> <button type="button" class="positive" id="bt_save_bt"> <img src="images/apply2.png" alt=""/> Guardar</button> <a href="#"  class="negative" id="bt_cancel_bt"><img src="images/cross.png" alt=""/> Cancel</a> </div></td> </tr></table></div>';
        
        var dialog=this.createDialog(instance.dialog_container,"Modificar codigo sistema figs",view,500);
        var n = $('#'+dialog);
        n.dialog('option', 'position', [(document.scrollLeft/550), 150]);  
        $("#codigo_figs").focus();
		
		$('#codigo_figs').bind('keypress', function(e) {
			if(e.keyCode==13){
				$("#bt_save_bt").click();
			}
		});			
        $("#bt_save_bt").click(function(){
            
            if ($("#codigo_figs").val()!=""){
               instance.post("?mod_security/role_list",{"general_settings":1,
                                                        'change_code_figs':1,
                                                        "request":instance.getUserID(),
                                                        'codigo_figs':$("#codigo_figs").val() 
                                                       },
                    function(data){
                        alert(data.mensaje);
                        if (!data.error){
							instance.fire("user_change");
                            instance.close(dialog);
                        }else{
							$("#codigo_figs").val('');
						}

                },"json");  
            }else{
				alert('Error: debe de llenar el campo codigo!');	
			}
            
        });		 
        $("#bt_cancel_bt").click(function(){
            instance.close(dialog);
        });
        
        
        
	},
    validate_pwd: function(){ 
        $("#password_change").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 7 
                },
                password_repeat : {
                    required: true,
                    minlength: 7,
                    equalTo: "#password"
                }
            },
            messages : {
                password : {
                    required: "Este campo es obligatorio",
                    minlength: "Debes de digitar un minimo de 7 caracteres"	
                },
                password_repeat : {
                    required: "Este campo es obligatorio",
                    minlength: "Debes de digitar un minimo de 7 caracteres",
                    equalTo : "Por favor digite el mismo valor!"
                }

            }

        });    
    }
    
});