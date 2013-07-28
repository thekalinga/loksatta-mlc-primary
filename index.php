<html>
	<head>
	<style>
		#menu {
			margin-top: 100px;
		}
		
		#menu li{
			background-color: #002664;
			border: 2px solid #FFF;
			color: #FFFFFF;
			float: left;
			font: bold 20px Georgia,serif;
			list-style: none outside none;
			padding: 60px;
			vertical-align: middle;
			width: 200px;
			cursor: pointer;
		}
		
		#menu li:hover{
			color: #002664;
			border-color: #002664;
			background-color: #FFFFFF;
		}
		
		#menu li a{
			display: block;
			text-decoration: none;
			color: Silver;
		}
		
	</style>
	</head>
	<body>
		<?php include 'header.php'?>
		<div align="center">
			<ul id="menu">
				<li><a href="register.php">New Registration</a></li>
				<li><a href="forgotid.php">Forgot Id?</a></li>
				<li><a href="faq.php">F.A.Q</a></li>
			</ul>
		</div>
	</body>
	<script>
		function process(event){
			location.href=event.target.children[0].href;
		}
		document.getElementById('menu').addEventListener('click', process, false);
	</script>
</html>