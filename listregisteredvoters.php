<?php include 'setup.php'?>
<html>
<head>
	<title>List of registered voters</title>
</head>
<body>

<?php include 'header.php'?>

<?php
	echo '<div align="center"> <style>tr th { background-color: #002664; color: #FFFFFF; padding-left: 10px; } tr td {background-color: #FFFFFF; border-left: 1px solid; border-top: 1px solid; border-bottom: 2px solid; color: #002664; padding: 2px 10px; }</style>';
	
	$stmt = $connection->stmt_init();
	
	if($stmt->prepare('SELECT `id`, `fname`, `mname`, `lname`, `address`, `phno`, `email`, `enrollmenttime`, `addressproof_attachment_name`, `graduateproof_attachment_name` FROM `voter` ORDER BY `fname` ASC')) {

		$stmt->execute();
		$stmt->store_result();
		
		// Bind your result columns to variables
		$stmt->bind_result($id, $fname, $mname, $lname, $address, $phno, $email, $enrollmenttime, $addressproof_attachment_name, $graduateproof_attachment_name);
		
		if($stmt->num_rows == 0){
			echo '<p>No registrations found in the database</p>';
		} else {
			echo '<h1>List of registed voters</h1><table><tr><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Address</th><th>#Phone</th><th>@email</th><th>Address Proof</th><th>Degree certificate</th><th>Enrolled on</th></tr>';
			// Fetch the result of the query
			while($stmt->fetch()) {
				echo '<tr><td>' . $fname .'</td><td>' . $mname .'</td><td>' . $lname .'</td><td>' . $address .'</td><td>' . $phno .'</td><td><a href="mailto:' . $email . '">' . $email .'<td><a href="assets/' . $id . '_' . $addressproof_attachment_name  .'" target="_blank">Open</a></td><td><a href="assets/' . $id . '_' . $graduateproof_attachment_name . '" target="_blank">Open</a></td><td>' . $enrollmenttime . '</td></tr>';
			}
			echo '</table>';
		}
	}
	$stmt->close();
	
	echo '</div>';
?>
</body>
</html>