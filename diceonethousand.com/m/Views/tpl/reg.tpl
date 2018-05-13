<div id="divReg">	
	<table id="tabReg" align="center">
		<tr class="lineTabReg">
			<td align="right">**LOGIN** :</td>
			<td align="left"><input class="inp reg" type="text" id="iLogin" size="12" maxlength="32"/></td>			
		</tr>
		<tr>
			<td><div class="mesReg errLogin" id="eRLogin">**LOGINOCCUP**</div></td>
			<td><div class="mesReg errLogin" id="eRLoginShort">**LOGINSHORT**</div></td>  			
		</tr>
		<tr class="lineTabReg">
			<td align="right">**EMAIL** :</td>
			<td align="left"><input class="inp reg" type="text" id="iEmail" size="12" maxlength="32" autofocus="true"/></td>
		</tr>
		<tr>
			<td><div class="mesReg errLogin" id="eEmail">**NOTEMAIL**</div></td>                          
		</tr>		
		<tr class="lineTabReg">
			<td align="right">**PASSWORD** :</td>
			<td align="left"><input class="inp reg" type="password" id="iRegPass" size="12" maxlength="16"/></td> 
		</tr>
		<tr>
			<td><div class="mesReg errLogin" id="eRegPass">**PASSSHORT**</div></td>           
		</tr>
		<tr class="lineTabReg">
			<td align="right">**CONFPASSWORD** :</td>
			<td align="left"><input class="inp reg" type="password" id="iConfirm" size="12" maxlength="16"/></td> 
		</tr>
		<tr>
			<td><div class="mesReg errLogin" id="eConfPass">**NOTCONFIRMPASS**</div></td>           
		</tr> 		
	</table>	
	<div id="divImgCaptcha" ><img id="imgCaptcha"/></div>
	<div class="mesReg errLogin" id="eCaptcha">**ERRCAPNCHA**</div>	
	<div id="divCaptcha">		
		<span>**ENTERCAPTCHA** :</span>
		<input class="inp reg" type="text" id="iCaptcha" size="6" maxlength="6"/>
	</div>
	<div class="but" type="button" id="bReg">**REGISTRATION**</div>
</div>