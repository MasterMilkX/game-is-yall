<?php
	session_start();


	// login 
	$config = parse_ini_file('../config.ini'); 
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');

	function addUser($name, $pass, $email, $level){
		echo "<div class='row row-no-gutters border' style='border:white;font-size:1em;color:white'>";
		echo "<div class='col-xs-3 border' style='text-align:center;'>" . $name . "</div>";
		echo "<div class='col-xs-3 border' style='text-align:center'>" . $pass . "</div>";
		echo "<div class='col-xs-3 border' style='text-align:center'>" . $email . "</div>";
		echo "<div class='col-xs-3 border' style='text-align:center'>" . $level . "</div>";
		echo "</div>";
	}
	$valid = false;

	//show stats to milk
	if(strcmp($_SESSION['username'],"milk") == 0){
		// get all of the test users
		$accessData = "SELECT USERNAME, PASSWORD, EMAIL from users WHERE EMAIL != 'NULL'";
		$sql = $conn->query($accessData);
		if(!$sql)
			die("PHP/MYSQL Error : " . $conn->error);

		//save them to arrays
		$usernames = array();
		$passwords = array();
		$emails = array();
		while($row = $sql->fetch_assoc()){	
			$usernames[] = $row['USERNAME'];			
			$passwords[] = $row['PASSWORD'];	
			$emails[] = $row['EMAIL'];				
		}

		//get the levels they made if possible
		$userLevels = array();
		for($c=0;$c<count($usernames);$c++){
			$levelData = "SELECT LEVEL_ID from levels WHERE AUTHOR LIKE '%" . $usernames[$c] . "%'";
			//$levelData = 'SELECT LEVEL_ID from levels where AUTHOR like "%Baba%" or AUTHOR like "%PCG.js%";';
			$sql2 = $conn->query($levelData);

			$levels = array();
			while($row = $sql2->fetch_assoc()){	
				$levels[] = $row['LEVEL_ID'];
			}

			if(count($levels) > 0){
				$userLevels[] = join(", ", $levels);
			}else{
				$userLevels[] = "< empty >";
			}
			
		}


	}
	//everyone else
	else{
		echo "Nothing to see here. Go <a href='map_home.php'>home</a>";
	}


?>
<html>
	<head>
		<title>USER STUDY STATS</title>
		<meta charset="utf-8">

		<!-- bootstrap stuff -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

		<!-- canvas.js stuff -->
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


		<!-- main stylesheet -->
		<link rel="stylesheet" href="layout_style.css">

		<!-- extra styles -->
		<style>
			body{
				background-color: #150E16;
				color:white;
			}
			a{
				color:#f00;
			}

		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	</head>
	<body>
		<div class='container top-buffer2'>
			<div class='row row-no-gutters border' style="border:3px solid white;color:white;font-size:1.5em;text-align:center">
				<div class='col-xs-3'>USERNAME</div>
				<div class='col-xs-3'>PASSWORD</div>
				<div class='col-xs-3'>EMAIL</div>
				<div class='col-xs-3'>LEVELS</div>
			</div>
			<?php
				for($r=0;$r<count($usernames);$r++){
					addUser($usernames[$r],$passwords[$r],$emails[$r],$userLevels[$r]);
				}
			?>
		</div>
	</body>
</html>