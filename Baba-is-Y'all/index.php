<?php
	session_start();

	//check first time visit for user
	$user_is_first_timer = !isset( $_COOKIE["FirstTimer"] );
	setcookie( "FirstTimer", 1, strtotime( '+1 year' ) );
	//setcookie("FirstTimer", "", time() - 3600);			//delete the cookie for testing purposes
	
	// login 
	$config = parse_ini_file('../config.ini'); 
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');

	$message="";
	if(count($_POST)>0) {

		//check length of inputs
		if(strlen($_POST['usernameLogin']) > 16){
			$message = "Username too long! Use max 16 characters";
		}
		if(strlen($_POST['passwordLogin']) > 50){
			$message = "Password too long! Use max 50 characters";
		}
		if((strlen($_POST['passwordLogin']) < 4) || (strlen($_POST['usernameLogin']) < 4)){
			$message = "Username/ Password too short! Use min 4 characters";
		}

		//make new user
		else if($_POST['submit'] == "REGISTER"){
			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM users WHERE USERNAME = ?");
			$stmt->bind_param("s", $_POST["usernameLogin"]);
			$stmt->execute();
			$result = $stmt->get_result();

			$count = mysqli_num_rows($result);

			if($count>0){
				$message = "Username taken! Please try a different one.";
			}else{
				$insert_query = $conn->prepare("INSERT into users (USER_ID, USERNAME, PASSWORD) values (null, ?, ?);");
				$insert_query->bind_param("ss", $_POST['usernameLogin'], $_POST["passwordLogin"]);

				//execute insertion of new user
				if(!$insert_query->execute()){
					echo $insert_query->error;
					die("PHP/MYSQL Error : " . $insert_query->error . " whoops");
				}else{
					$message = "Username registered!";
					$_SESSION['username'] = $_POST['usernameLogin'];
					header("Refresh: 1;url=map_home.php");
				}
				
			}
			$stmt->close();
		}

		//login to user account
		else if($_POST['submit'] == "LOGIN"){
			$stmt = $conn->prepare("SELECT * FROM users WHERE USERNAME = ? and password = ?");
			$stmt->bind_param("ss", $_POST["usernameLogin"], $_POST["passwordLogin"]);
			$stmt->execute();
			$result = $stmt->get_result();

			$count  = mysqli_num_rows($result);
			if($count==0) {
				$message = "Invalid Username or Password!";
			} else {
				$message = "Login successful!";
				$_SESSION['username'] = $_POST['usernameLogin'];
				header("Refresh: 1;url=map_home.php");
			}
			$stmt->close();
		}

		//logout current user
		else if($_POST['submit'] == 'Logout'){
			$_SESSION['username'] = "Baba";
			$message = "You have been logged out";
			//header("Refresh:0");
		}
		
		mysqli_close($conn);
		
	}



	$username = (isset($_SESSION['username']) ? $_SESSION['username'] : 'Baba');

	//reroute to main screen if already logged in
	if($username !== "Baba" && strpos($_SERVER['HTTP_REFERER'],"map_home.php") == false){
	   header("Location: map_home.php");
	   exit();
	}
	
 
	

?>
<script>
	var CURRENT_USER = '<?php echo $username;?>';
</script>



<html>
	<head>
		<title>Baba is Y'all - Home Page</title>
		<meta charset="utf-8">

		<!-- bootstrap stuff -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

		<link rel="stylesheet" href="layout_style.css">
		

		<!-- extra styling -->
		<style>
			html {
			  scroll-behavior: smooth;
			}
			canvas{
				width:100%;
				height:92%;
				background-color: #000;
			}
			
			body{
				background-color: #000;
			}
			body.light-mode{
				background-color:#fff;
			}
                                         
			div#banner { 
				position: absolute; 
				top: 0; 
				left: 0; 
				background-color: #FFFF66;
				text-align: center; 
				font-size: 1.5vw;
				width: 100%;
				margin: 0 auto; 
				padding: 7px; 
				border: 1px solid #000; 
			}

			#mainScreen{
				border: 32px solid transparent;
				border-image:url('demo_maps/blank_map.png') 79 repeat;
				min-height: 600px;
				height:100%;
			}

			/* login php */
			.message{
				color: #FF0000;
				font-weight: bold;
				text-align: center;
				width: 100%;
				font-size: 1.5vw;
				margin-top: 2%;
				margin-bottom: 2%;
			}
			.btnSubmit{
				width:85%;
				font-size:1vw;
				text-align: center;
			}


			@font-face {font-family: BabaFont; src: url('KeyLime.ttf');}
			/* buy the license https://shapedfonts.com/project/key-lime/ */

			/* text styled like the Baba is You title */
			.babaStyle{
				margin:auto;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				width:85%;
				background-color: #000;
				padding:1%;
				color:#DC386A;
				text-align: center;
				font-size:2.8vw;
				text-decoration-line: underline;
				text-decoration-style: dashed;
				text-decoration-color: #fff;
				font-family: 'BabaFont';
			}
			
		</style>
		<script>
			function gotoURL(url){location.href = url;}
		</script>
	</head>
	<body>
		<div id="banner">This game is best played in full screen mode!</div>
		
		<script>
			$(function(){
			setTimeout(function(){ showTarget(); }, 5000);
			});
			function showTarget(){
			$("#banner").show();
			}
		</script>

		<div id='mainScreen'>
			<div id='loginBox' class='container top-buffer1'>
				<!-- title -->
				<div class='row row-no-gutters'>
					<div class='col-xs-6 text-center col-xs-offset-3'>
						<a href='map_home.php'>
						<img src='baba_is_yall_title_wide_char.png' class='img-responsive' width="100%" style='margin:auto;'>
						</a>
					</div>
				</div>
				<!-- login -->
				
				<div class='row row-no-gutters top-buffer2'>
					<div class='col-xs-8 col-xs-offset-2' style='border:3px dashed #DC386A'>
						<!-- login / register form -->
						<div id='loginBox' class='fs1' style='text-align: center;margin-top: 2%'>
						<form name="frmUser" method="post" action="">
							<div id='noUser'>
								<div class="message"><?php if($message!="") { echo $message; } ?></div>
								<input type='text' name="usernameLogin" id='usernameLogin' style='width:60%;font-family: Arial;font-size:1.75vw;margin-top: 3%' placeholder="Username"><br>
								<input type='password' name='passwordLogin' id='passwordLogin' style='width:60%;font-family: Arial;font-size:1.75vw;margin-top: 2%' placeholder="Password"><br>
								<div class='row row-no-gutters top-buffer1'>
									<div class='col-xs-4 col-xs-offset-2'><input type="submit" name="submit" value="LOGIN" class="btnSubmit" style='width:60%;font-family: Arial;font-size:1.75vw;margin-bottom: 5%;background-color: #DC386A;'></div>
									<div class='col-xs-4'><input type="submit" name="submit" value="REGISTER" class="btnSubmit" style='width:60%;font-family: Arial;font-size:1.75vw;margin-bottom: 5%;background-color: #DC386A;'></div>
								</div>
							</div>
						</form>
						</div>
					</div>
				</div>

				<!-- other options -->
				<div class='row row-no-gutters top-buffer2'>
					<div class='col-xs-8 col-xs-offset-2'>
						<div class='row row-no-gutters'>
							<!--
							<div class='col-xs-4 text-center'>
								<button style='width:80%;font-family: Arial;font-size:1vw;'>New Account</button>
							</div>
							-->
							<div class='col-xs-4 text-center col-xs-offset-2'>
								<button style='width:85%' onclick='gotoURL("tutorial.php")'>Go to Tutorial</button>
							</div>
							<div class='col-xs-4 text-center'>
								<button style='width:85%' onclick='gotoURL("map_home.php")'>Go to Level Map</button>
							</div>
						</div>
						<div class='col-xs-4 text-center col-xs-offset-4'>
								<button style='width:80%; background-color:#DC386A; margin-top: 20px;' class="btn-toggle">Toggle Dark Mode</button>
								<script>
									const btn = document.querySelector('.btn-toggle');
									btn.addEventListener('click', function() {document.body.classList.toggle('light-mode');localStorage.darkmode = (document.body.classList.contains('light-mode') ? "no" : "yes")})
								</script>
						</div>
					</div>
				</div>

				<div class='row row-no-gutters top-buffer3'>
					<div class='col-xs-12'>
						&nbsp;
					</div>
				</div>

			<!--
			<canvas id='tutCanvas'>
				
			</canvas>
			-->
			<script>
				//get dark mode preferences
				if(localStorage.darkmode && localStorage.darkmode == "no"){
					document.body.classList.toggle('light-mode');
				}else{
					localStorage.darkmode = "yes";
				}
			</script>

		</div>
	</body>
</html>