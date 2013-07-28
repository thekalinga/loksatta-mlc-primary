<html>
	<head>
		<title>Enrollment form for upcoming Loksatta MLC primary elections in Bangalore</title>
		<style>
		
			#title{
				background-color: #002664;
				border-top: 3px solid #002664;
				color: #FFFFFF;
				font-family: Trebuchet MS;
				font-size: 25px;
			}
			
			#application label{
				font-family: Georgia;
				font-size: 17px;
			}
			
			#application span{
				color: blue;
			}
			
			#application td{
				width: 300px;
			}
			
			#application input{
				width: 200px;
			}
			
			#application textarea{
				width: 200px;
			}
			
			#footer li{
				list-style:none;
				margin-left:1px;
				float:left;
			}
		</style>
	</head>
	<body>
		<?php include 'header.php'?>
		<div align="center">
			<form id="application" action="enroll.php" method="post" enctype="multipart/form-data" onsubmit="javascript:return validateForm();">
				<h1 align="center" id="title">Petition for online voting in the upcoming Loksatta MLC Primary</h1>
				<table style="width: 700px;">
					<tr>
						<td><label for="firstname">First name <span>*</span></label></td>
						<td><input name="firstname" type="text" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="middlename">Middle name</label></td>
						<td><input name="middlename" type="text" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="lastname">Last name <span>*</span></label></td>
						<td><input name="lastname" type="text" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="phonenumber">Phone No. <span>*</span></label></td>
						<td><input name="phonenumber" type="text" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="emailaddress">Email <span>*</span></label></td>
						<td><input name="emailaddress" type="text" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="address">Address <span>*</span></label></td>
						<td><textarea name="address" rows="6" onchange="resetBackGround(this)"></textarea></td>
					</tr>
					<tr>
						<td><label for="addressproof">Scanned copy of your local address proof<br />[Bangalore City or Bangalore Rural or Ramanagar districts] <span>*</span></label></td>
						<td><input name="addressproof" type="file" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr>
						<td><label for="graduateproof">Scanned copy of your graduation <br />[From anywhere in India] <span>*</span></label></td>
						<td><input name="graduateproof" type="file" onchange="resetBackGround(this)"/></td>
					</tr>
					<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
					<tr>
						<td></td>
						<td><input type="submit" value="Submit" /></td>
					</tr>
				</table>
				<span>NOTE: Only JPEG/JPG, PDF, TIF, GIF and PNG formats are supported</span> <br/>
				<span>*</span> = mandatory details
			</form>
		</div>
		<div id="footer">
			<ul align="center">
				<li><a href="/forgotid.php">Forgot id</a></li>
				<li style="float:right;">By Loksatta Karnataka</li>
			</ul>
		</div>
		<script>
			function resetBackGround(target){
				target.style.backgroundColor = 'white';
			}
			
			function validateForm(){
				var firstname = document.getElementsByName('firstname')[0];
				firstname.value = firstname.value.trim();
				var lastname = document.getElementsByName('lastname')[0];
				lastname.value = lastname.value.trim();
				var phonenumber = document.getElementsByName('phonenumber')[0];
				phonenumber.value = phonenumber.value.trim();
				var emailaddress = document.getElementsByName('emailaddress')[0];
				emailaddress.value = emailaddress.value.trim();
				var address = document.getElementsByName('address')[0];
				address.value = address.value.trim();
				var addressproof = document.getElementsByName('addressproof')[0];
				var graduateproof = document.getElementsByName('graduateproof')[0];
				
				var isValid = true;
				
				if(firstname.value.length == 0){
					firstname.style.backgroundColor = '#FF7C97';
					isValid = false;
				}
				if(lastname.value.length == 0){
					lastname.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				if(!isPhoneNumberValid(phonenumber)){
					phonenumber.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				if(!isEmailAddressValid(emailaddress.value)){
					emailaddress.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				if(address.value.length == 0){
					address.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				if(addressproof.value.length == 0){
					addressproof.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				if(graduateproof.value.length == 0){
					graduateproof.style.backgroundColor = '#FF7C97';
					isValid = isValid && false;
				}
				return isValid;
			}
			
			function isPhoneNumberValid(phno){
				if(phno.value.match(/^[+]?\d{10,12}$/)){
					phno.value = phno.value.match(/^.*(\d{10})$/)[1];
					return true;
				}
				return false;
			}
			
			function isEmailAddressValid(email){
				if(email.match(/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/) != null){
					return true;
				}
				return false;
			}
			
		</script>
	</body>
</html>