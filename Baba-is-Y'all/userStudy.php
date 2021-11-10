<?php
	session_start();
	//setcookie("FirstTimer", "", time() - 3600);			//delete the cookie for testing purposes
	
	// login 
	$config = parse_ini_file('../config.ini'); 
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');

	$message="";
	$fakeUsername="";
	$fakePassword="";

	//username reset if BABA
	if($_SESSION['username'] == "Baba" || (empty($_SESSION['username']) && !empty($_SESSION['password']))){
		//echo "resetting username";
		$_SESSION['username'] = "";
		$_SESSION['password'] = "";
	}

	if(count($_POST)>0) {

		if(empty($_SESSION['username']) && empty($_SESSION['password'])){
			
			//make new user
			if(empty($_POST['emailInput'])){
				$message = "Enter a valid email to register!";
			}else if(!strpos($_POST['emailInput'], "@")){
				$message = "Not a valid email!";
			}else if($_POST['submit'] == "Register"){
				$message = "OK!";

				//make a new user with the email
			
				// prepare and bind
				$stmt = $conn->prepare("SELECT * FROM users WHERE EMAIL = ?");
				$stmt->bind_param("s", $_POST["emailInput"]);
				$stmt->execute();
				$result = $stmt->get_result();

				$count = mysqli_num_rows($result);

				if($count>0){
					$message = "Email already registered! Please contact mlc761@nyu.edu for help.";
				}else{
					//generate fake credentials
					$fakeUsername = "Keke" . random_int(100,999);

					$c2 = 0;
					do{
						$fakeUsername = "Keke" . random_int(100,999);

						$cu = $conn->prepare("SELECT * FROM users WHERE USERNAME = ?");
						$cu->bind_param("s", $fakeUsername);
						$cu->execute();
						$res = $cu->get_result();

						 $c2 = mysqli_num_rows($res);
					}while($c2>0);


					$fakePassword = substr(str_shuffle(MD5(microtime())), 0, 10);

					$_SESSION['username'] = $fakeUsername;
					$_SESSION['password'] = $fakePassword;
						
					$insert_query = $conn->prepare("INSERT into users (USER_ID, USERNAME, PASSWORD, EMAIL) values (null, ?, ?, ?);");
					$insert_query->bind_param("sss", $fakeUsername, $fakePassword, $_POST["emailInput"]);

					//execute insertion of new user
					if(!$insert_query->execute()){
						echo $insert_query->error;
						die("PHP/MYSQL Error : " . $insert_query->error . " whoops");
					}else{
						$message = "Email registered! Generating username and password...";

					}
					
				}
				$stmt->close();
			}


			mysqli_close($conn);
			
			
		}

	}
	
 
?>



<html>
	<head>
		<title>Baba is Y'all - User Study</title>
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
			
			body{
				background-color: #000;
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
			a{
				color:#DC386A;
			}

			
		</style>
		<script>
			function gotoURL(url){location.href = url;}

			let TESTER_USERNAME = '<?php echo $_SESSION['username'];?>';
			let TESTER_PASSWD = '<?php echo $_SESSION['password'];?>';

			//show certain screens based on whether values are set
			function init(){
				
				if(TESTER_USERNAME != "" && TESTER_PASSWD != ""){
					document.getElementById("registration").style.display = "none";
					document.getElementById("description").style.display = "none";
					document.getElementById("usernameTxt").innerHTML = TESTER_USERNAME;
					document.getElementById("passwordTxt").innerHTML = TESTER_PASSWD;
				}else{
					document.getElementById("credentials").style.display = "none";
					document.getElementById("instructions").style.display = "none";
				}
				
			}
		</script>
	</head>
	<body onload='init()'>
		

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

				<div class='row row-no-gutters'><div class='col-xs-12' style='text-align: center;color:white'><h2>User Study</h2></div></div>
				<div class='row row-no-gutters top-buffer2'>
					<div class='col-xs-8 col-xs-offset-2' style='border:3px dashed #DC386A'>
						<div id='registration'>
							<!-- login / register form -->
							<div id='loginBox' class='fs1' style='text-align: center;margin-top: 2%'>
							<form name="frmUser" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
								<div id='noUser'>
									<div class="message"><?php if($message!="") { echo $message; } ?></div>
									<div class='row row-no-gutters top-buffer1'>

										<input type='text' name="emailInput" id='emailInput' style='width:60%;font-family: Arial;font-size:1.75vw;margin-top: 3%' placeholder="Email (preferrably NYU)"><br>
										<div class='col-xs-4 col-xs-offset-4 top-buffer2' ><input type="submit" name="submit" value="Register" class="btnSubmit" style='width:60%;font-family: Arial;font-size:1.75vw;margin-bottom: 5%;background-color: #DC386A;'></div>
									</div>
								</div>
							</form>
							</div>
						</div>
						<div id='credentials' style='text-align: center;font-size:2em;color:white'>
							Username: <span style='color:#f00' id='usernameTxt'></span><br>
							Password: <span style='color:#f00' id='passwordTxt'></span><br>
						</div>
					</div>
				</div>

				<div class='row row-no-gutters top-buffer3'>
					<div class='col-xs-12' style='color:white;font-size:1.25em;'>
						<div id='description' style='text-align:center'>
							<p>We need participants to create levels for the puzzle game 'Baba is You' using an AI assisted tool. We are testing the efficacy of mixed-initiative collaborative systems on game design.</p>
							<p>The experiment will take approximately 30 minutes of time. During this time, subjects must play a submitted level, create a new level, and complete a survey (excluding any additional time you spend to get familiar with the game 'Baba is You' if you've never played it before). ​Those who complete the study and complete the tasks given will be compensated with a $10 Amazon gift card. </p>
							<p>This experiment and all of the resources needed will be done online. The site is publicly available online, however we will provide you with an anonymous user ID and login information to use on the site to maintain confidentiality of the levels you create. </p>
							<p>If you would like to participate in the experiment, please register your email above (this will be the same email we will send the Amazon gift card to) and you will be provided with a generated username and password and further instructions</p>
							<p>Feel free to also ask any questions you have about the experiment.​ You can reach me, M Charity, at mlc761@nyu.edu.</p>
							<p style='color:yellow;font-size:1.5em;'>THANK YOU!</p>
						</div>
						<div id='instructions'>
							<!-- <span style='color:red;font-size:1.5em;text-align:center'>DO NOT RERFESH OR CLOSE THIS PAGE!</span> -->
							<br><br><u>INSTRUCTIONS</u>
							<ol>
								<li>(Optional) Watch the Baba is Y’all <a href='tutorial.php' target='_blank'>tutorials</a></li>
								<li><a href='map_home.php' target='_blank'>Play</a> at least one submitted level on the <a href='map_home.php#new_screen' target='_blank'>New</a> or <a href='map_home.php#top_screen' target='_blank'>Top</a> page (save the ID number)</li>
								<li><a href='level_editor.php' target='_blank'>Make</a> at least one level and submit it (save the ID number)</li>
								<li>Complete the <a href='https://forms.gle/BzaLkDAopcAqqYHR7' target='_blank'>survey</a> (please answer honestly!)</li>
							</ol>
							<p>Feel free to also ask any questions you have about the experiment.​ You can reach me, M Charity, at mlc761@nyu.edu.</p>
							<br><p style='color:yellow;font-size:1.25em;text-align:center'>THANK YOU FOR YOUR PARTICIPATION!</p>
						</div>
					</div>
				</div>


				<div class='row row-no-gutters top-buffer3'>
					<div class='col-xs-12'>
						&nbsp;
					</div>
				</div>

		</div>
	</body>
</html>