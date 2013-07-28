<?php include 'setup.php'?>

<html>
	<head>
		<title>Retrieve your registration info</title>
	</head>
	<body>
<?php include 'header.php'?>
<?php

$inputform = '<form action="forgotid.php" method="post" enctype="multipart/form-data">
			<h1 align="center" id="title">Please enter the following details so that we can send you the registartion id</h1>
			<table>
				<tr>
					<td><label for="mobileno">Mobile Number <span>*</span></label></td>
					<td><input name="mobileno" type="text" /></td>
				</tr>
				<tr>
					<td><label for="email">Email address <span>*</span></label></td>
					<td><input name="email" type="text" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>';
	
	function processDetails(){
		global $connection;
		
		$stmt = $connection->stmt_init();
		
		$trimmedphno = substr($_POST['mobileno'], -10);
		$emailaddress = $_POST['email'];
		
		if($stmt->prepare('SELECT `token` FROM `voter` where `phno` = ? and `email` = ?')) {
			$stmt->bind_param('ss', $trimmedphno, $emailaddress);
			$stmt->execute();
			$stmt->store_result();
			
			if($stmt->num_rows != 1){
				echo '<p>We are unable to find your data in our records. Please try with correct data, if it still does not work, <p>Please contact the Please contact Loksatta secretery at <br /><b>secretary.bangalore@loksattakarnataka.org</b><br /> for further assitance</p>';
			} else {
				// Bind your result columns to variables
				$stmt->bind_result($token);
				$stmt->fetch();
				
				sendMail($token, $emailaddress);
				echo '<p>We have sent you a mail containing your registation token. Thank you!!</p>';
			}
			
		}
	}
	
	function sendMail($token, $to){
		
		// subject
		$subject = 'Your registration details for upcoming Loksatta Karnataka MLC primary election';

		// message
		$body = '
		<p style="font-family: Trebuchet MS; font-size: 16px; font-weight: normal; background-color: #002477; color: white;">Please make a note of your regsitration token <b>' . $token . '</b> which very important for online voting.<br />You can goto the following URL to cast your vote on 18<sup>th</sup> December 2011.<br />The URL is <a href="http://www.mlcprimary.co.cc/vote.php?token=' . $token . '">http://www.mlcprimary.co.cc/vote.php?token=' . $token . '</a></p>

		<p style="font-family: Trebuchet MS; font-size: 16px; font-weight: normal;">We encourage you to spread the message of Loksatta MLC primary election by requesting your friends and family members to enroll.</p>

		<p style="font-family: Trebuchet MS; font-size: 16px; font-weight: normal;">Please visit Loksatta Karnataka website where you can find more info on Loksatta and its ideology. The website also has various articles and opinions contributed by many active citizens like you. Pleae visit, <a href="http://www.loksattakarnataka.org" style="text-decoration : none;">Loksatta karnataka website</a> for more info</p>
		';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= 'From: Admin Loksatta Karnataka <administrator@loksattakarnataka.org>' . "\r\n";
		$headers .= 'To: ' . $to . ' \r\n';
		
		// Mail it
		mail($to, $subject, $body, $headers);
	}	

	echo '<div align="center">';
	if(isset($_POST['mobileno']) && isset($_POST['email'])){
		processDetails();
	} else {
		echo $inputform;
	}
	echo '</div>';
?>
</body>
</html>