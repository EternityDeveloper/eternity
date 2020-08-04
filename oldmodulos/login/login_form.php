<style>
 
 
#container_login{
	margin:0 auto;
	margin-top:10%;
	min-height:200px;
	width:300px;
	padding: 5px 5px 5px 5px;
/*	-moz-border-radius:2px; 
	border-radius:2px; 
	-webkit-border-radius:2px; */
	-moz-box-shadow: 0px 0px 0px #000;
    -webkit-box-shadow: 6px 3px 6px 1px #CCC;
     box-shadow:        0px 1px 8px 2px #CCC; 
	 
	background:#EBEBEB;
	 
}


</style>


<div id="container_login" >
<div class="login_icon"><img src="images/avatar_2x.png" width="96px" height="96px" />
</div>
  <div class="div_login" >
   <!-- <div>Acceso de usuario</div>-->
    <form id="form1" name="form1" method="post" action=""><input type="hidden" name="action" id="action" value="login" />
  <table width="270" border="0" align="center" >
    <tr>
      <td align="center"><input type="text" name="user" id="user" placeholder="Usuario" class="textfield"  autocomplete="off" />        <input type="password" name="pwd" id="pwd" placeholder="Contraseña" class="textfield"/>        </td>
    </tr>
    <tr>
      <td align="center" style="padding-bottom:15px;">
        <input type="submit" name="button" id="button" value="Entrar" class="greenButton greenButtonLogin"     />      </td>
    </tr>
    <?
	$error=$_REQUEST['error']; 
	 if ($error!=""){?>
    <tr>
      <td align="center" style="color:#F00;font-size:18px;">Usuario ó Contraseña Invalida</td>
      </tr>
     <? }?> 
  </table>
  </form>
</div>
</div>
