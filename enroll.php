<?php include 'setup.php'?>
<?php include 'utils.php'?>

<html>
<head>
	<title>Enrollment status</title>
</head>
<body>
<?php include 'header.php'?>
<?php
	$failureMsgPreamble = '<div align="center" style="background-color: red;border: 1px solid;color: #FFFFFF;font-family: Trebuchet MS;font-size: 25px;margin: 50px;padding: 30px;">';
	$successMsgPreamble = '<div align="center" style="background-color: green;border: 1px solid;color: #FFF;font-family: Trebuchet MS;margin: 50px;padding: 30px;">';
	
	$isfnamevalid = false;
	$islnamevalid = false;
	$isaddressvalid = false;
	$isphnovalid = false;
	$isemailvalid = false;
	$areproofsuploaded = false;
	
	function isAValidFileType($fileName){
		$allowedExtensions = array('jpg', 'jpeg', 'pdf', 'tif', 'gif', 'png');
		return in_array(strtolower(end(explode(".", $fileName))), $allowedExtensions);
	}
	
	function getFormattedErrorMessage(){
		
		global $isfnamevalid;
		global $islnamevalid;
		global $isaddressvalid;
		global $isphnovalid;
		global $isemailvalid;
		global $areproofsuploaded;
		
		$msg = '<div style="background-color:#002664"><p>Issues found</p><ol>';
		if(!$isfnamevalid){
			$msg .= '<li> Firstname is not proper </li>';
		}
		if(!$islnamevalid){
			$msg .= '<li> Lastname is not proper </li>';
		}
		if(!$isaddressvalid){
			$msg .= '<li> Addess is not proper </li>';
		}
		if(!$isphnovalid){
			$msg .= '<li> Phone number is not valid </li>';
		}
		if(!$isemailvalid){
			$msg .= '<li> Email address is not proper </li>';
		}
		if(!$areproofsuploaded){
			$msg .= '<li>A valid address & graduation proof are required</li>';
		}
		$msg .= '</ol></div>';
		return $msg;
	}

	// Sanity check the form data
	function isFormDataLooksOK(){
		
		global $isfnamevalid;
		global $islnamevalid;
		global $isaddressvalid;
		global $isphnovalid;
		global $isemailvalid;
		global $areproofsuploaded;

		if(strlen(trim($_POST['firstname'])) != 0){
			$isfnamevalid = true;
		}
		if(strlen(trim($_POST['lastname'])) != 0){
			$islnamevalid = true;
		}
		if(strlen(trim($_POST['address'])) != 0){
			$isaddressvalid = true;
		}
		if(isAValidPhNo(trim($_POST['phonenumber']))){
			$isphnovalid = true;
		}
		
		if(strlen(trim($_POST['emailaddress'])) != 0 && isAValidEmail(trim($_POST['emailaddress']))){
			$isemailvalid = true;
		}
		
		if(isset($_FILES['addressproof']) && isset($_FILES['graduateproof'])) {
			// Make sure the file was sent without errors
			if($_FILES['addressproof']['error'] == 0 && isset($_FILES['graduateproof'])) {
				if(isAValidFileType($_FILES['addressproof']['name']) && isAValidFileType($_FILES['graduateproof']['name'])){
					$areproofsuploaded = true;
				}
			}
		}
		
		return $isfnamevalid && $islnamevalid && $isaddressvalid && $isphnovalid &&  $isemailvalid && $areproofsuploaded;
	}
	
	// Make sure that the user is not registering multiple times
	function isThisADuplicateEntry(){
		global $connection;
		
		$isADuplicate = true;
	
		$stmt = $connection->stmt_init();
		
		$trimmedphno = substr($_POST['phonenumber'], -10);
		$emailaddress = $_POST['emailaddress'];
		
		if($stmt->prepare('SELECT * FROM `voter` WHERE `phno` = ? or `email` = ?')) {
			$stmt->bind_param('ss', $trimmedphno, $emailaddress);
			$stmt->execute();
			$stmt->store_result();
			
			if($stmt->num_rows == 0){
				$isADuplicate = false;
			}
		}
		
		$stmt->close();
		return $isADuplicate;
	}
	
	function proceedWithEnrollment(){
		global $connection;
		
		$id = strtoupper(str_rand(20, 'alphanum'));
		$firstname = $_POST['firstname'];
		$middlename = $_POST['middlename'];
		$lastname = $_POST['lastname'];
		$address = $_POST['address'];
		$trimmedphno = substr($_POST['phonenumber'], -10);
		$emailaddress = $_POST['emailaddress'];
		$enrollmenttime = date( 'Y-m-d H:i:s', time());
		$token = strtoupper(str_rand(20, 'alphanum'));
		$addressproof_attachment_name = 'addressproof_' . $_FILES['addressproof']['name'];
		$graduateproof_attachment_name = 'graduationproof_' . $_FILES['graduateproof']['name'];
		
		$stmt = $connection->stmt_init();
		
		if($stmt->prepare('INSERT INTO `voter` (`id`, `fname`, `mname`, `lname`, `address`, `phno`, `email`, `enrollmenttime`, `token`, `addressproof_attachment_name`, `graduateproof_attachment_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
			$stmt->bind_param('sssssssssss', $id, $firstname, $middlename, $lastname, $address, $trimmedphno, $emailaddress, $enrollmenttime, $token, $addressproof_attachment_name, $graduateproof_attachment_name);
			$stmt->execute();
			$stmt->close();
		} else {
			die('Some unknown error occurred during registration');
		}
		saveFiles($id);
		sendMail($token, $emailaddress);
		return $token;
	}
	
	function saveFiles($id){
		move_uploaded_file($_FILES["addressproof"]["tmp_name"], "assets/" . $id . '_addressproof_' . $_FILES['addressproof']['name']);
		move_uploaded_file($_FILES["graduateproof"]["tmp_name"], "assets/" . $id . '_graduationproof_' . $_FILES['graduateproof']['name']);
	}
	
	function sendMail($token, $to){
		
		// subject
		$subject = 'Your registration details for upcoming Loksatta Karnataka MLC primary election';

		// message
		$body = '
		<p style="font-family: Trebuchet MS; font-size: 16px; font-weight: normal;">Your submission to register to vote in Lok Satta\'s primary election for Bangalore Graduate Constituency will be reviewed by the Election Committee soon. You will receive a notification once your identity is validated manually by the election comittee.</p>

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
	
	// Main action starts here
	
	if(!isFormDataLooksOK()) {
		echo $failureMsgPreamble . 'The data you entered is not proper, Please fill the form properly by going <a href="javascript: history.go(-1)">back</a>';
		echo getFormattedErrorMessage();
	} else if(isThisADuplicateEntry()) {
		echo $failureMsgPreamble . '<p>Looks like you are already registered. We do not allow multiple registrations from the same person.</p> <p>If you have not registered already, it could be an issue with the enrollment system,  Please contact Loksatta secretery at <br><b>secretary.bangalore@loksattakarnataka.org</b><br> for further assitance</p>';
	} else {
		// Everything seems to be OK for the time being
		$id = proceedWithEnrollment();
		echo $successMsgPreamble . '<p>Thank you for showing interest to participate in Loksatta Primaries. </p> <h1>We have sent an e-mail containing your registartion token which is very important for online voting</h1><p>Please note that Loksatta Karnataka primaries will be held on <b>18th December 2011.</b><p><br /><br />
		<p>We encourage you to spread the message of Loksatta MLC primaries by requesting your friends and family members to enroll.</p><p>Please visit Loksatta Karnataka where you can find more info on Loksatta and its ideology. The website also has various articles and opinions contributed by many active citizens like you. Pleae visit, <a href="http://www.loksattakarnataka.org">Loksatta karnataka website</a> for more info.</p>';
	}
	echo '</div>';
?>

</body>
</html>