<?php include 'setup.php'?>
<html>
<head>
<title>Voting page</title>
<style>
form {
	font-family: Trebuchet MS;
	font-size: 15px;
}
</style>
</head>
<body>
<?php include 'header.php'?>
<?php
$failureMsgPreamble = '<div align="center" style="background-color: red;border: 1px solid;color: #FFFFFF;font-family: Trebuchet MS;font-size: 25px;margin: 50px;padding: 30px;">';
$successMsgPreamble = '<div align="center" style="background-color: green;border: 1px solid;color: #FFF;font-family: Trebuchet MS;margin: 50px;padding: 30px;">';
$inputformmarkup = '<div align="center"><form action="vote.php" method="post">';
$token="";
$submit = '<input type="submit" value="submit"></form>';
$istokenvalid = false;
$isvotervalid = false;
$alreadyoted = false;
$stmt = $connection->stmt_init();
	if(isset($_REQUEST["token"])){
		$token = $_REQUEST["token"];
		if(isAValidKey($token)){
			if(hasAlreadyVoted($token)){
				echo $failureMsgPreamble  .'Sorry, We don\'t allow people to vote more than once.</div>';
			} else {
			if(isset($_REQUEST["candidateid"])){
				$candidateid=$_REQUEST["candidateid"];
				vote($token,$candidateid);
				//echo isAValidVoter($token);
			} else {
				$inputformmarkup .=  '<p>Please select the candidate you wish to cast your vote for</p>' ;
				$inputformmarkup .= '
					<p>
						<table>
							<tr>
								<td>
									<input id="ashwin" type="radio" name="candidateid" value="ashwin" />
									<label for="ashwin">Ashwin Mahesh</label>
								</td>
							</tr>
							<tr>
								<td>
									<input id="meenashi" type="radio" name="candidateid" value="meenashi" />
									<label for="meenashi">Meenakshi Bharath</label>
								</td>
							</tr>
							<tr>
								<td>
									<input id="pradeep" type="radio" name="candidateid" value="pradeep" />
									<label for="pradeep">Pradeep Pydah</label>
								</td>
							</tr>
							<tr>
								<td>
									<input id="shekhar" type="radio" name="candidateid" value="shekhar" />
									<label for="shekhar">Shekhar</label>
								</td>
							</tr>
						</table>
					</p>
				';
				$inputformmarkup .= '<input type="hidden" name="token" value="'.$token.'">';
				$inputformmarkup .= $submit;
				$inputformmarkup .= '</div>';
				echo $inputformmarkup;
			}
		}
	} else {
		echo $failureMsgPreamble  .'Invalid Security token.Please check your token and try again .</div>';
		$inputformmarkup .= 'Please enter the token you have receieved over your mail : <input type="text" name="token" />';
		$inputformmarkup .= $submit;
		$inputformmarkup .= '</div>';
		echo $inputformmarkup;
	}
} else {
	$inputformmarkup .= 'Please enter the token you have receieved over your mail : <input type="text" name="token" />';
	$inputformmarkup .= $submit;
	$inputformmarkup .= '</div>';
	echo $inputformmarkup;
 }

function isAValidKey($token) {
	global $stmt,$failureMsgPreamble;
	$stmt->prepare('SELECT `id` FROM `voter` WHERE `token` = ?');
	$stmt->bind_param('s', $token);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows == 0) {
		return false;
	} else {
		return true;
	}
}

function isAValidVoter($token) {
	global $stmt;
	$stmt->prepare('SELECT  * FROM `voter` WHERE `token` = ? and `isAValidVoter` != 0');
	$stmt->bind_param('s', $token);
	$stmt->execute();
	$stmt->store_result();
	//check to see if this user is a valid voter
	if($stmt->num_rows == 0) {
		return false;
	} else {
		return true;
	}
}

function hasAlreadyVoted($token) {
	global $stmt;
	$stmt->prepare('SELECT  `isAValidVoter` FROM `voter` WHERE `token` = ? and   `alreadyVoted` != 0');
	$stmt->bind_param('s', $token);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($isvotervalid);
	// If he hasn't voted
	if($stmt->num_rows == 0) {
		return false;
	} else {
		return true;
	}
}

function eligibleForVote($token) {
	global $successMsgPreamble,$failureMsgPreamble;
	if(isAValidKey($token)) {
		if(isAValidVoter($token)) {
			if(!hasAlreadyVoted($token)) {
				return true;
			} else {
				echo $failureMsgPreamble  .'Sorry, We don\'t allow people to vote more than once.</div>';
			}
		} else {
			echo $failureMsgPreamble  .'You cannot vote due to one of the following reason(s)<br><br>Either<br><p>Your identity is yet to be manually validated by the election comission</p>or<p>Your identity could not be certified by our internal election comission as valid</p> Please contact Loksatta secretary at <b><a href="mailto:secretary.bangalore@loksattakarnataka.org" style="text-decoration:none;font: 25px Trebuchet MS;color: #444;">secretary.bangalore@loksattakarnataka.org</a></b> for further assitance</p></div>';
		}
	} else {
		echo $failureMsgPreamble  .'Invalid Security token.Please check your token .</div>';
	}
	return false;
}

function vote($token,$candidateid) {
	global $successMsgPreamble,$failureMsgPreamble,$stmt;
	if(eligibleForVote($token)) {
		$stmt->prepare('UPDATE `voter` SET `alreadyVoted` = 1,`candidateId` = ? WHERE `token` = ?');
		$stmt->bind_param('ss', $candidateid,$token);
		$stmt->execute();
		echo $successMsgPreamble  .'
		<h1>Your vote has been recorded.</h1>
		<p>We thank you for participating in Loksatta Primaries and by that you have taken the first step towards participating in democracy.</p>
		<p>Want to make more contribution to demoracy?</p>
		<p>Pariticipate in social transformation and politics. We as Loksatta have a pristine view about politics and ways to solve the problems Karnataka as a state and India as nation face. We are citizens like and are willing to make contribution to our country. We request you have a lokk at Loksatta ideologies and the credibility Loksatta and its leaders have at <a href="http://www.loksatta.org/cms">Loksatta</a></p>
		<p>Please visit Loksatta Karnataka where you can find more info on Loksatta and its ideology. The website also has various articles and opinions contributed by many active citizens like you. Pleae visit, <a href="http://www.loksattakarnataka.org">Loksatta karnataka website</a> for more info.</p>
		</div>';
	}
}
?>
</body>
</html>