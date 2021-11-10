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
		}else if(strlen($_POST['passwordLogin']) > 50){
			$message = "Password too long! Use max 50 characters";
		}

		//make new user
		else if($_POST['submit'] == "Register"){
			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM users WHERE USERNAME = ?");
			$stmt->bind_param("s", $_POST["usernameLogin"]);
			$stmt->execute();
			$result = $stmt->get_result();

			$count = mysqli_num_rows($result);

			if($count>0){
				$message = "Username taken! Use a different one.";
			}
			else if(strlen($_POST["usernameLogin"]) < 4){
				$message = "Username too short! Use at least 4 characters!";
			}
			else{
				$insert_query = $conn->prepare("INSERT into users (USER_ID, USERNAME, PASSWORD) values (null, ?, ?);");
				$insert_query->bind_param("ss", $_POST['usernameLogin'], $_POST["passwordLogin"]);

				//execute insertion of new user
				if(!$insert_query->execute()){
					echo $insert_query->error;
					die("PHP/MYSQL Error : " . $insert_query->error . " whoops");
				}else{
					$message = "Username registered!";
					$_SESSION['username'] = $_POST['usernameLogin'];
					//header("Refresh:0");
				}
				
			}
			$stmt->close();
		}

		//login to user account
		else if($_POST['submit'] == "Login"){
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
				//header("Refresh:0");
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
	

?>
<script>
	var CURRENT_USER = '<?php echo $username;?>';
</script>


<!-- CAROUSEL VERSION OF MAP_HOME.PHP --> 
<html>
	<head>
		<title>Level Map - Baba is Y'all V2</title>
		<meta charset="utf-8">

		<!-- bootstrap stuff -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

		<link rel="stylesheet" href="layout_style.css">
		<link rel="stylesheet" href="slider_style.css">
		<!-- extra styles -->
		<style>

			html {
			  scroll-behavior: smooth;
			}
			/*
			div{
				border: 1px solid black;
			}
			*/

			body{
				background-color: #000;
			}
			body.light-mode {
				background-color: #fff;
			}

			/* title */

			#babaImg:hover + #babaBubble{
				cursor: pointer;
				visibility: visible;
			}


			#kekeImg:hover + #kekeBubble{
				cursor: pointer;
				visibility: visible;
			}


			#babaBubble{
				width:60%;
				float:left;
				margin-left:20%;
				color: #fff;
				font-size:1.25vw;
				text-align: center;
				justify-content: center;
				margin-top:1.25vw;
				visibility: hidden;
				border:3px solid #fff;
				border-radius: 25px;
			}
			body.light-mode #babaBubble{
				width:60%;
				float:left;
				margin-left:20%;
				color: #000;
				font-size:1.25vw;
				text-align: center;
				justify-content: center;
				margin-top:1.25vw;
				visibility: hidden;
				border:3px solid #000;
				border-radius: 25px;
			}

			#kekeBubble{
				width:60%;
				float:right;
				margin-right:20%;
				color: #fff;
				font-size:1.25vw;
				text-align: center;
				margin-top:1.25vw;
				visibility: hidden;
				border:3px solid #fff;
				border-radius: 25px;
			}
			body.light-mode #kekeBubble{
				width:60%;
				float:right;
				margin-right:20%;
				color: #000;
				font-size:1.25vw;
				text-align: center;
				margin-top:1.25vw;
				visibility: hidden;
				border:3px solid #000;
				border-radius: 25px;
			}

			body .carousel-control .glyphicon-chevron-right{
				color:#fff;
			}
			body .carousel-control .glyphicon-chevron-left{
				color:#fff;
			}
			body.light-mode .carousel-control .glyphicon-chevron-right{
				color:#000;
			}
			body.light-mode .carousel-control .glyphicon-chevron-left{
				color:#000;
			}

			body.light-mode #obj_table > div{
				border:2px solid black;
			}


			#darkModeBtn{
				position: absolute;
				z-index: 1;
				width:2.5vw;
				height:2.5vw;
				right:5vw;
				top:3vw;
			}



			/* navigation */

			.nav-pills > li.active > a, 
			.nav-pills > li.active > a:hover, 
			.nav-pills > li > a:hover, 
			.nav-pills > li.active > a:focus{
				background-color: #DC386A;
				color:#ffffff;
			}
			.nav-pills > li > a{
				color: #DC386A;
				font-size: 1.5vw;
			}


			.lpill{
				margin-right: 2vw;
			}
			.mpill{
				margin-left: 2vw;
				margin-right: 2vw;
			}
			.rpill{
				margin-left: 2vw;
			}

			#search_nav > li.active > a, 
			#search_nav > li.active > a:hover, 
			#search_nav > li > a:hover, 
			#search_nav > li.active > a:focus{
				background-color: #787878;
				color:#fff;
				width:15vw;
			}
			#search_nav > li > a{
				color: #fff;
				font-size: 1vw;

				width:15vw;
			}
			body.light-mode #search_nav > li > a{
				color:#000;
			}


			/* level selection cells */

			.levelNo{
				font-size:1.7vw;
				color:white;
				margin-top: 8%;
			    white-space: nowrap;
			    overflow: hidden;
			    text-overflow: ellipsis;
			    margin-left:0.75vw;
			    margin-right:0.75vw;
			}
			.levelNoMINI{
				font-size:1.75vw;
				color:white;
				margin-top: 7%;
			    white-space: nowrap;
			    overflow: hidden;
			    text-overflow: ellipsis;
			    margin-left:0.75vw;
			    margin-right:0.75vw;
			}
			.levelLabel, .levelRules{
				font-size:1.1vw;
				color:white;
				margin-bottom: -2%;
			}

			.levelNo2{
				font-size:3.5vw;
				color:white;
				margin-top: 15%;
			}
			.levelLabel2{
				font-size:2vw;
				color:white;
				margin-top:2%;
				margin-bottom: 0%;
			}

			.levelNameDiv{
				font-size:1.4vw;
				color:#DC386A;
				margin-top: -3%;
			    white-space: nowrap;
			    overflow: hidden;
			    text-overflow: ellipsis;
			    margin-left:0.75vw;
			    margin-right:0.75vw;
			}
			.levelNameDiv2{
				font-size:1vw;
				color:#f00;
				margin-top: -6%;
				margin-bottom: -1%;
			    white-space: nowrap;
			    overflow: hidden;
			    text-overflow: ellipsis;
			    margin-left:0.75vw;
			    margin-right:0.75vw;
			}

			.levelBox{
				position: relative;
				border: 0.3vw solid transparent
			}
			.levelBox .levelSelect{
				width:100%;
				height:100%;
				position:absolute;
			    width:100%;
			    top:0;
			    background-color:rgba(0, 0, 0, 0.65);
			    visibility: hidden;
			    margin: auto;
			    text-align: center;
			}
			.levelBox:hover .levelSelect{
				visibility: visible;
			}
			.levelBox .levelSelect button{
				width:50%;
				font-size: 1vw;
				margin-top: 7%;
				align-items: center;
    			justify-content: center;
			}

			/* carousel */

			.vert-align {
			    display: flex;
			    align-items: center;
			}

			.glyphicon-chevron-left{
				color:black;
			}
			.glyphicon-chevron-right{
				color:black;
			}

			.carousel-indicators {
			    bottom:-3vw;
			}
			.carousel-indicators li{
				border: 1px solid #DC386A;

				width:20px;
				height:20px;
			} 
			.carousel-indicators .active{
				background-color:#DC386A;

				width:20px;
				height:20px;
			}


			.aligned-row {
			  display: flex;
			  flex-flow: row wrap;

			  &::before {
			    display: block;
			  }
			}

			/* fonts */

			.fs1{
				font-size:1vw;
			}
			.fs125{
				font-size:1.25vw;
			}
			.fs25{
				font-size:2.5vw;
			}

			/* login php */

			.message{
				color: #FF0000;
				font-weight: bold;
				text-align: center;
				width: 100%;
				font-size: 1vw;
				margin-top: 5%;
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
				background-color: #222323;
				padding:1%;
				color:#DC386A;
				text-align: center;
				font-size:2.8vw;
				text-decoration-line: underline;
				text-decoration-style: dashed;
				text-decoration-color: #fff;
				font-family: 'BabaFont';
			}


			.newMarquee{
				background-color: #000;
			    white-space: nowrap;
				overflow: hidden;
				width:100%;
			}
			
			.redMarquee{
				color:#DC386A;
				display: inline-block;
				font-family: 'BabaFont';
				animation: marquee 25s linear infinite;
				font-size:2vw;
			}
			.whiteMarquee{
				color:#fff;
				display: inline-block;
				font-family: 'BabaFont';
				animation: marquee 25s linear infinite;
				font-size:2vw;
			}
			@keyframes marquee {
			    from {
			        transform: translateX(1200px);
			    }
			    to {
			        transform: translateX(-2000px);
			    }
			}

			/* overrides */
			.obj_lmcell{
				border-color: #ffffff;
			}
			.obj_rcell{
				border-color: #ffffff;
			}
			.obj_text{
				color:#ffffff;
			}
			body.light-mode .obj_lmcell{
				border-color: #000;
			}
			body.light-mode .obj_rcell{
				border-color: #000;
			}
			.thick-border-bottom{
				border-color: #ffffff;
			}
			body.light-mode .thick-border-bottom{
				border-color: #000;
			}
			body.light-mode .obj_text{
				color:#000;
			}
			:root{
				--text:#fff;
			}
			body.light-mode :root{
				--text:#000;
			}

			.txt-color{
				color:#ffffff;
			}
			body.light-mode .txt-color{
				color:#000;
			}

		</style>
		<style>
			:root{
				--text:#000;
			}
		</style>
		<script src='js/baba.js'></script>
	</head>

	<!-- PHP IMPORTS --->
			<?php

			//setup database connection
			$config = parse_ini_file('../config.ini'); 
			$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
			if(!$conn){die('BAD CONNECTION');}


			// get all of the levels
			$accessData = "SELECT LEVEL_ID, CHROMOSOME_REP, ASCII_MAP, AUTHOR, SOLUTION, RATING, TOTAL_RATINGS, DATE_CREATED, LEVEL_NAME, VIEWS from levels";
			$sql = $conn->query($accessData);
			if(!$sql)
				die("PHP/MYSQL Error : " . $conn->error);

			//save them to arrays to map to dictionaries later
			$level_ID = array();
			$chromo_rep = array();
			$ascii_map = array();
			$map_width = array();
			$map_height = array();
			$author = array();
			$solution = array();
			$rating = array();
			$totRating = array();
			$createDate = array();
			$levelNames = array();
			$views = array();
			while($row = $sql->fetch_assoc()){	
				$level_ID[] = $row['LEVEL_ID'];			// key 

				$chromo_rep[] = $row['CHROMOSOME_REP'];	// values 
				$ascii_map[] = $row['ASCII_MAP'];		//  |
				$map_width[] = $row['MAP_WIDTH'];		//  |
				$map_height[] = $row['MAP_HEIGHT'];		//  |
				$author[] = $row['AUTHOR'];				//  |
				$solution[] = $row['SOLUTION'];			//  |
				$rating[] = $row['RATING'];				//  |
				$totRating[] = $row['TOTAL_RATINGS'];	//  |
				$createDate[] = $row['DATE_CREATED'];	//  |
				$levelNames[] = $row['LEVEL_NAME'];	    //  |
				$views[] = $row['VIEWS'];	            //  |
			}

			?>


			<!-- PHP FUNCTIONS -->
			<?php

			//sorts from newest to oldest
			function dateSort($a,$b){
				if (strtotime($a) < strtotime($b)) 
			        return 1; 
			    else if (strtotime($a) > strtotime($b))  
			        return -1; 
			    else
			        return 0; 
			}

			//converts number to binary (chromosome) form
			function num2Bin($c){
				return strrev(str_pad(strval(decbin($c)), 18, "0", STR_PAD_LEFT));
			}

			//converts chromosome binary string to [A-I][12] format
			function chromo2Name($c){
				$alpha = array("A", "B", "C", "D", "E", "F", "G", "H", "I");

				$outstr = "";
				for($i=0;$i<9;$i++){
					//split into substring to get the pair of 01s
					$s = substr($c,$i*2,2);

					if($s == "01"){
						$outstr .= ($alpha[$i] . "2_");
					}else if($s == "10"){
						$outstr .= ($alpha[$i] . "1_");
					}else if($s == "11"){
						$outstr .= ($alpha[$i] . "12_");
					}
				}

				if($outstr == ""){			//unlikely to ever happen
					return "NO-RULES";
				}else{
					return substr($outstr,0,-1);		//chop off last _
				}
			}

			//turns rule representation to rulelist
			function rep2rules($rep, $maxRules=3){
				$alpha = array("A", "B", "C", "D", "E", "F", "G", "H", "I");
				$rules = array("X-IS-X", "X-IS-Y", "X-IS-PUSH", "X-IS-MOVE", "X-IS-STOP", "X-IS-KILL", "X-IS-SINK", "X-IS-[PAIR]", "[X,Y]-IS-YOU");

				$radd = 0;
				$extr = 0;
				$ruleOut = "";
				for($i=0;$i<9;$i++){

					if(strpos($rep, $alpha[$i]) !== false){		//rule in representation
						if($radd >= $maxRules){	//hit the limit already
							$extr+=1;
							continue;
						}
						else{					//hasn't hit the limit yet
							$ruleOut .= ($rules[$i] . "<br>");
							$radd += 1;
						}
						
					}
				}
				$ruleOut .= ($extr > 0 ? ("(+" . $extr . " rules)") : "");
				return $ruleOut;
			}
			
			//make a level box object based on level id
			function makeDatabaseLevelBox($levID, $pos){
				global $level_ID, $chromo_rep, $author, $levelNames;

				$lname = $levelNames[array_search($levID, $level_ID)];

				//placement in grid
				echo "<div class='col-xs-2 " . (strcmp($pos, 'left') == 0 ? "col-xs-offset-3 " : "") . "levelBox'>";

				//thumbnail image 
				if(strcmp($pos, 'left') == 0){
					echo "<img class='img-responsive mapPrev' name='" . $levID . "' style='float: right;' onError='this.src=\"demo_maps/load_error.png\"'>";
				}else if(strcmp($pos, 'mid') == 0){
					echo "<img class='img-responsive mapPrev' name='" . $levID . "' style='margin: auto;' onError='this.src=\"demo_maps/load_error.png\"'>";
				}else{
					echo "<img class='img-responsive mapPrev' name='" . $levID . "' style='float: left;' onError='this.src=\"demo_maps/load_error.png\"'>";
				}
				
				//content
				echo "<div class='levelSelect'>";
				echo "<h4 class='levelNo' >#" . $levID . "</h4>";
				//echo "<p class='levelLabel'>" . chromo2Name($chromo_rep[array_search($levID, $level_ID)]) . "</p>";
				$a = $author[array_search($levID, $level_ID)];
				//$a = str_replace("PCG.js", "Keke", $a)			#replace "PCG.js" with "Keke"

				//echo "<div class='levelNameDiv'>" . substr($lname,0,20) . (strlen($lname) > 20 ? "..." : "") . "</div>";
				if(!empty($lname) and strcmp($lname,'NULL') !== 0){
					echo "<div class='levelNameDiv'>" . substr($lname,0,20) . (strlen($lname) > 20 ? "..." : "") . "</div>";
				}else{
					echo "<div class='levelNameDiv'>-</div>";
				}
				
				echo "<p class='levelLabel'>" . substr($a,0,18) . (strlen($a) > 18 ? "..." : "") . "</p>";
				echo "<button onclick='gotoPlay(" . $levID . ",true)'>PLAY</button><br>";
				echo "<button onclick='gotoEdit(" . $levID . ")'>EDIT</button>";


				//closers
				echo "</div>";
				echo "</div>";

			}

			//make a level box based on chromosome representation
			function makeUnmadeLevelBox($chromo, $pos){
				//placement in grid
				echo "<div class='col-xs-2 " . (strcmp($pos, 'left') == 0 ? "col-xs-offset-3 " : "") . "levelBox'>";

				//no level image
				if(strcmp($pos, 'left') == 0){
					echo "<img src='demo_maps/no_level.png' class='img-responsive' style='float: right;' width='200px' height='200px'>";
				}else if(strcmp($pos, 'mid') == 0){
					echo "<img src='demo_maps/no_level.png' class='img-responsive' style='margin: auto;' width='200px' height='200px'>";
				}else{
					echo "<img src='demo_maps/no_level.png' class='img-responsive' style='float: left;' width='200px' height='200px'>";
				}

				//content
				echo "<div class='levelSelect'>";
				$r = chromo2Name($chromo);
				echo "<h4 class='levelNo'>" . $r . "</h4>";
				echo "<p class='levelRules'>". rep2rules($r) ."</p>";
				echo "<button onclick='gotoEdit(\"". $chromo ."\",true)'>EDIT</button>";

				//closers
				echo "</div>";
				echo "</div>";
				
			}

			//keeps the format for the matrix carousel scrolling
			function makeFillerBox($pos){
				//placement in grid
				echo "<div class='col-xs-2 " . (strcmp($pos, 'left') == 0 ? "col-xs-offset-3 " : "") . "levelBox'>";

				//no level image
				if(strcmp($pos, 'left') == 0){
					echo "<img src='demo_maps/transparent.png' class='img-responsive' style='float: right;'>";
				}else if(strcmp($pos, 'mid') == 0){
					echo "<img src='demo_maps/transparent.png' class='img-responsive' style='margin: auto;'>";
				}else{
					echo "<img src='demo_maps/transparent.png' class='img-responsive' style='float: left;'>";
				}

				echo "</div>";
				
			}

			//makes carousel of boxes
			function makeBoxSet($levelType,$maxScreen=6){
				$levelIDOrd = array();

				global $level_ID, $createDate, $totRating, $rating, $chromo_rep, $levelNames;

				$minLev = $levelType == "unmade" ? $maxScreen*9 : min(array($maxScreen*9,count($level_ID)));

				//sort levels by newest
				if (strcmp($levelType, "new") == 0){
					$dateSet = array();

					//populate set for sorting, sort, then make into new array set
					for($i=0;$i<$minLev;$i++){
						$dataSet[$level_ID[count($level_ID)-$i]] = $createDate[count($level_ID)-$i];
					}
					uasort($dataSet,"dateSort");  //sort
					$levelIDOrd = array_keys($dataSet);
				}

				//sort levels by highest rated (num rating * score)
				else if ($levelType == 'top'){
					$dateSet = array();

					//populate set for sorting, sort, then make into new array set
					for($i=0;$i<$minLev;$i++){
						$dataSet[$level_ID[$i]] = (int)$totRating[$i]*(float)$rating[$i];
					}
					arsort($dataSet);  //sort
					$levelIDOrd = array_keys($dataSet);
				}
				//add unmade chromosome level list
				else if ($levelType == "unmade"){
					$dataSet = array();

					//add the easiest unmade levels first
					$c = 0;
					while(count($dataSet) < ($minLev) && $c < 262144){
						$b = num2Bin($c);
						if(!in_array($b, $chromo_rep)){
							$dataSet[] = $b;
						}
						$c++;
					}

					//add most complex unmade levels second
					$c = 262143;
					while(count($dataSet) < ($minLev*2) && $c > 0){
						$b = num2Bin($c);
						if(!in_array($b, $chromo_rep)){
							$dataSet[] = $b;
						}
						$c--;
					}

					$levelIDOrd = $dataSet;
				}
				//empty carousel (for search screen)
				else if($levelType == 'empty'){
					$levelIDOrd = array();
					$maxScreen = 1;
				}

				//fewer unmade levels than max screens (NICE!)
				if (($maxScreen) > ceil($minLev/9)){
					$maxScreen = ceil(count($dataSet)/9);
				}

				$carInd = "levelCarousel_" . $levelType;

				//add indicators
				
				echo '<ol class="carousel-indicators">';
    			echo '<li data-target="#' . $carInd .'" data-slide-to="0" class="active"></li>';
    			for($i=1;$i<$maxScreen;$i++){
    				echo '<li data-target="#' . $carInd .'" data-slide-to="' . $i . '"></li>';
    			}
  				echo '</ol>';
  				

				echo '<div class="carousel-inner">';

				
				
  				

				//add the levels screen by screen to the carousel
				$li = 0;
				$sides = array('left', 'mid', 'right');
				for($s=0;$s<$maxScreen;$s++){
					//header
					echo "<div class='item" . ($s == 0 ? " active " : "") . "'>";
					echo '<div class="d-block w-100" src="..." >';

						//top row
						echo "<div class='row row-no-gutters'>";
						for($i=0;$i<3;$i++){
							if($li >= count($levelIDOrd)){
								makeFillerBox($sides[$i]);
							}else if ($levelType != "unmade"){
								makeDatabaseLevelBox($levelIDOrd[$li],$sides[$i]);
							}else{
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
							}
							$li++;
						}
						echo "</div>";

						//middle row
						echo "<div class='row row-no-gutters vert-align'>";
						for($i=0;$i<3;$i++){
							if($li >= count($levelIDOrd)){
								makeFillerBox($sides[$i]);
							}else if ($levelType != "unmade"){
								makeDatabaseLevelBox($levelIDOrd[$li],$sides[$i]);
							}else{
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
							}
							$li++;
						}
						echo "</div>";

						//bottom row
						echo "<div class='row row-no-gutters'>";
						for($i=0;$i<3;$i++){
							if($li >= count($levelIDOrd)){
								makeFillerBox($sides[$i]);
							}else if ($levelType != "unmade"){
								makeDatabaseLevelBox($levelIDOrd[$li],$sides[$i]);
							}else{
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
							}
							$li++;
						}
						echo "</div>";


					echo "</div>";
					echo "</div>";

				}

				//add complex levels to the end of the unmade levels screens (half easiest - vs. half hardest)
				if($levelType == "unmade"){

					$li = count($levelIDOrd)-1;
					for($s=0;$s<$maxScreen;$s++){
						//header
						echo "<div class='item'>";
						echo '<div class="d-block w-100" src="..." >';

							//top row
							echo "<div class='row row-no-gutters'>";
							for($i=0;$i<3;$i++){
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
								$li--;
							}
							echo "</div>";

							//middle row
							echo "<div class='row row-no-gutters vert-align'>";
							for($i=0;$i<3;$i++){
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
								$li--;
							}
							echo "</div>";

							//bottom row
							echo "<div class='row row-no-gutters'>";
							for($i=0;$i<3;$i++){
								makeUnmadeLevelBox($levelIDOrd[$li],$sides[$i]);
								$li--;
							}
							echo "</div>";


						echo "</div>";
						echo "</div>";

					}
				}

				echo "</div>";

			}

			//gets 2 pairs of levels for rating
			function makeRatePair(){
				global $author, $level_ID, $username, $totRating;

				echo "<div class='row row-no-gutters'>";

				//only get the indices where the author is not the same
				$validInd = array();
				for($i=0;$i<count($author);$i++){
					if($author[$i] == "Baba" || $author[$i] != $username){
						$validInd[$i] = $totRating[$i];
					}
				}

				// PICK LESS RATINGS
				//sort by the fewest total ratings
				asort($validInd);
				
				//use first 20 smallest
				$minRate = min(20, count($validInd));
				array_slice($validInd, 0, $minRate);

				//use only the ids (totals aren't needed anymore)
				$validInd = array_keys($validInd);

				//make probability ranking
				$probs = array();
				$s = 0;
				for($e=0;$e<$minRate;$e++){
					$s += $e;
				}
				for($e=$minRate;$e>0;$e--){
					$probs[] = ($e/$s);
				}

				//select levels probabilistically based on the ranking
				
				$id1 = -1;
				$id2 = -1;

				while($id1 == -1 || $id2 == -1){
					$c = 0;
					$r = mt_rand() / mt_getrandmax();

					for($p=0;$p<$minRate;$p++){
						$c += $probs[$p];
						if($r < $c){
							if($id1 == -1){
								$id1 = $level_ID[$validInd[$p]];
							}else if($level_ID[$validInd[$p]] != $id1){
								$id2 = $level_ID[$validInd[$p]];
							}
							break;
						}
					}	
				}
				

				// PICK ANY LEVEL

				/*
				//pick 2 random indices to use
				$pick2 = array_rand(array_keys($validInd),2);
				$id1 = $level_ID[$pick2[0]];
				$id2 = $level_ID[$pick2[1]];
				*/
				

				//level A
				echo "<div class='col-xs-3 col-xs-offset-2 levelBox'>";
					echo "<img class='img-responsive mapPrev' name='" . $id1 . "' width='250px' height='250px' style='margin:auto;' id='levelAImg'>";
					echo "<div class='levelSelect' id='levelAdiv'>";
					echo "<h4 class='levelNo2'>#" . $id1 . "</h4>";
					echo "<button id='levelAbtn' onclick='gotoPlay(" . $id1 . ",true)' style='font-size:1.8vw;'>PLAY</button>";
					echo "</div>";
				echo "</div>";
				
				//refresh button
				echo "<div class='col-xs-2'><br><br>";
				echo "<img src='flaticon/redo2.png' class='img-responsive' style='margin:auto;cursor: pointer;' onclick='resetRateLevels()'>";		//UPDATE ME 	
				echo "</div>";
										
				//level B
				echo "<div class='col-xs-3 levelBox'>";
					echo "<img class='img-responsive mapPrev' name='" . $id2 . "' width='250px' height='250px' style='margin:auto;' id='levelBImg'>";
					echo "<div class='levelSelect' id='levelBdiv'>";
					echo "<h4 class='levelNo2'>#" . $id2 . "</h4>";
					echo "<button id='levelBbtn' onclick='gotoPlay(" . $id2 . ",true)' style='font-size:1.8vw;'>PLAY</button>";
					echo "</div>";
				echo "</div>";


				echo "</div>";
			}



			//makes level sets with user levels
			function getUserLevels(){
				global $author, $level_ID, $username, $chromo_rep, $levelNames;
				$userLevels = array();

				//only get the indices where the author is the same or has +P CG.js
				$validInd = array();
				for($i=0;$i<count($author);$i++){
					//if(true){
					if(($author[$i] == $username) || ($author[$i] == ($username . " + PCG.js"))){
						$validInd[] = $i;
					}
				}

				//get ids for indices
				for($i=0;$i<count($validInd);$i++){
					$userLevels[] = $level_ID[$validInd[$i]];
				}

				//make the sets (4 per row)
				$totRows = ceil(count($userLevels)/4);
				$i = 0;
				for($r=0;$r<$totRows;$r++){
					if($r == 0 || $r == ($totRows-1)){
						echo "<div class='row row-no-gutters'>";
					}else{
						echo "<div class='row row-no-gutters vert-align'>";
					}
					
					//make level boxes
					for($j=0;$j<4;$j++){
						$levID = $userLevels[$i];
						$lname = $levelNames[array_search($levID, $level_ID)];
						echo "<div class='col-xs-3 levelBox'>";

						//map preview
						echo "<img class='img-responsive mapPrev' name='" . $levID . "' style='margin: auto;'>";

						//content
						echo "<div class='levelSelect'>";
						echo "<h4 class='levelNo' >#" . $levID . "</h4>";
						if(!empty($lname) and strcmp($lname,'NULL') !== 0){
							echo "<div class='levelNameDiv'>" . substr($lname,0,20) . (strlen($lname) > 20 ? "..." : "") . "</div>";
						}else{
							echo "<div class='levelNameDiv'>-</div>";
						}
						echo "<p class='levelLabel'>" . chromo2Name($chromo_rep[$validInd[$i]]) . "</p>";
						echo "<button onclick='gotoPlay(" . $levID . ",true)'>PLAY</button><br>";
						echo "<button onclick='gotoEdit(" . $levID . ")'>EDIT</button>";
						echo "</div>";

						echo "</div>";

						$i++;
					}
					echo "</div>";
				}
			}

			//removes the PCG.js from author name
			function removePCG($a){
				return str_replace(' + PCG.js', "", $a);
			}
			//determine if author is original
			function origAuthors($a){
				return !($a == "Baba" || $a == "PCG.js");
			}

			//returns stats of users in a marquee
			// MOST LEVELS, HIGHEST RATED, MOST RULES, LEVELS MADE TOTAL
			function topUserMarquee(){
				global $author, $chromo_rep, $rating;

				$fix_authors = array_map("removePCG",$author);
				$fix_authors = array_filter($fix_authors,"origAuthors");

				//get most occuring author
				$top_authors = array_count_values($fix_authors);
				arsort($top_authors);
				$ml_user = array_keys($top_authors)[0];


				//get user with most different rules
				$user_chroms = array();
				for($i=0;$i<count($chromo_rep);$i++){
					//remove BABA and PCG levels
					$aut = removePCG($author[$i]);
					if(in_array($author[$i], array("Baba", "PCG.js")) || $aut == ""){
						continue;
					}

					//make new entry if it doesn't exist
					if(!array_key_exists($aut, $user_chroms)){
						$user_chroms[$aut] = array();
						array_push($user_chroms[$aut], $chromo_rep[$i]);	//add chromosome representation 
					}else if(!in_array($chromo_rep[$i], $user_chroms[$aut])){
						array_push($user_chroms[$aut], $chromo_rep[$i]);
					}
				}
				$diff_authors = array_keys($user_chroms);
				$len_rules = array();
				for($i=0;$i<count($diff_authors);$i++){
					$aut = $diff_authors[$i];
					$len_rules[$aut] = count($user_chroms[$aut]);
				}
				arsort($len_rules);
				$mr_user = array_keys($len_rules)[0];

				//get user with highest rated levels
				$user_rates = array();
				for($i=0;$i<count($rating);$i++){
					//remove BABA and PCG levels
					$aut = removePCG($author[$i]);
					if(in_array($author[$i], array("Baba", "PCG.js")) || $aut == ""){
						continue;
					}

					//make new entry if it doesn't exist
					if(!array_key_exists($aut, $user_chroms)){
						$user_rates[$aut] = 0;
					}
					$user_rates[$aut] += $rating[$i];
				}
				arsort($user_rates);
				$hr_user = array_keys($user_rates)[0];

				//add levels made total
				$rm = count(array_count_values($chromo_rep));
				$prm = number_format($rm / 262144.0, 2);


				echo "<span class='whiteMarquee' style='margin-right:30%'>Most Rules: " . $mr_user . "</span>";
				echo "<span class='redMarquee' style='margin-right:30%'>Most Levels: " . $ml_user . "</span>";
				echo "<span class='whiteMarquee' style='margin-right:30%'>Highest Rated: " . $hr_user . "</span>";
				echo "<span class='redMarquee' style='margin-right:30%'>Levels Made: " . count($chromo_rep) . "</span>";
				echo "<span class='whiteMarquee' style='margin-right:30%'>Rules Made: " . $rm . " / 262144 (" . $prm . "%)</span>";
			}


			?>


			<!-- JAVASCRIPT FUNCTIONS -->

			<script>
				//Save database information to arrays and access with level ID no index when going to PLAY or EDIT page
				var levelIDArr = <?php echo json_encode($level_ID); ?>;
				var chromoRepArr = <?php echo json_encode($chromo_rep); ?>;
				var asciiMapArr = <?php echo json_encode($ascii_map); ?>;
				var authorArr = <?php echo json_encode($author); ?>;
				var solutionArr = <?php echo json_encode($solution); ?>;
				var ratingArr = <?php echo json_encode($rating); ?>;
				var levelNameArr = <?php echo json_encode($levelNames); ?>;
				var firstTime = "<?php echo $user_is_first_timer; ?>";

				function init(){
					updateMapIMG();

					//update user-based properties
					document.getElementById("userTab").innerHTML = (CURRENT_USER == "Baba" ? "Baba's Levels" : "My Levels");
					document.getElementById("userTitle").innerHTML = CURRENT_USER.toUpperCase();

					//get user level stats
					let allLevInd = [];
					for(let i=0;i<levelIDArr.length;i++){
						if((authorArr[i] == CURRENT_USER) || (authorArr[i] == (CURRENT_USER+" + PCG.js")))
							allLevInd.push(i);
					}	
					//distint representations and ratings
					let myrep = [];
					for(let i=0;i<allLevInd.length;i++){myrep.push(chromoRepArr[allLevInd[i]]);}

					let myrates = [];
					for(let i=0;i<allLevInd.length;i++){myrates.push(ratingArr[allLevInd[i]]);}

					document.getElementById("numLevelStat").innerHTML = allLevInd.length;
					document.getElementById("repLevelStat").innerHTML = myrep.filter((item, i, ar) => ar.indexOf(item) === i).length;
					document.getElementById("topLevelStat").innerHTML = topLevel(myrates,allLevInd);
				
					//show tutorial if new user
					if(firstTime){
						document.getElementById("babaBubble").style.visibility = "visible";
						document.getElementById("babaBubble").innerHTML = "First time here? <br> Click me to check out <br> the tutorial!";
					}
					else{
						document.getElementById("babaBubble").style.visibility = "visible";
						document.getElementById("babaBubble").innerHTML = "Welcome back, " + CURRENT_USER + "! <br> Click me to check out <br> the tutorial again!";
					}

				}

				//show speech bubbles for keke and baba on hover in the title
				function showChatBubble(entity){
					let bubble = document.getElementById(entity+"Bubble");
					bubble.style.visibility = "visible";
					document.body.style.cursor = "pointer";
				}
				function hideChatBubble(entity){
					let bubble = document.getElementById(entity+"Bubble");
					document.body.style.cursor = "default";
					
					if(firstTime != "1" || entity == "keke")
						bubble.style.visibility = "hidden";
				}


				//get index of top rated level
				function topLevel(rates,ind){
					let mr = -100;
					let mi = "?";
					for(let i=0;i<rates.length;i++){
						if(rates[i] > mr){
							mr = rates[i];
							mi = ind[i];
						}
					}
					return (mi == "?" ? "N/A": levelIDArr[mi]);
				}


				// START THE PLAY SCREEN
				function gotoPlay(levelID,newWin=false){
					//get data for level
					let index = levelIDArr.indexOf(levelID.toString());
					localStorage.testMap = asciiMapArr[index];
					localStorage.control = 'human';
					localStorage.author = authorArr[index];
					localStorage.bestSolution = (solutionArr[index]).toLowerCase().split("");

					if(!newWin)
						window.location.href = "game.php?level=" + levelID;
					else
						window.open("game.php?level=" + levelID);
				}

				// START THE EDIT SCREEN
				function gotoEdit(levelID, chromosome=false){
					//check if level id or chromosome
					if(chromosome){
						localStorage.editMap = [];
						localStorage.chromo = levelID;
					}else if(levelID != ""){
						//get data for level
						let index = levelIDArr.indexOf(levelID.toString());
						localStorage.editMap = asciiMapArr[index];
						localStorage.chromo = chromoRepArr[index];
					}else{
						localStorage.editMap = [];
						localStorage.chromo = "";
					}

					window.location.href = "level_editor.php";
					
				}

				// DRAW MAPs //

				//canvas for the map thumbnails
				var canvas = document.createElement("canvas");
				canvas.id = "game";
				var ctx = canvas.getContext("2d");
				canvas.width = 200;
				canvas.height = 200;

				var mapAllReady = false;
				var allMapsUpdated = false;


				//changes all of the image sources to preview maps
				function makePreviewMap(ascii_map){
					let miniMap = parseMap(ascii_map);
					let mapWidth = miniMap[0].length;
					let mapHeight = miniMap.length;

					let sf = 0;
					let offX = 0;
					let offY = 0;
					if((mapWidth/canvas.width) > (mapHeight/canvas.height)){
						sf = canvas.width / mapWidth;
						offX = 0;
						offY = (canvas.height - (sf*mapHeight)) / 2;
					}else{
						sf = canvas.height / mapHeight;
						offY = 0;
						offX = (canvas.width - (sf*mapWidth)) / 2;
					}
				

					//draw the map first
					ctx.save();
					//ctx.translate(-camera.x, -camera.y);		//camera
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					ctx.fillStyle = "#232323";
					ctx.fillRect(0,0,canvas.width,canvas.height);
					
					//make the map ready
					if(!mapAllReady){
						mapAllReady = mapReady();
						return "";
					}

					for(var r=0;r<miniMap.length;r++){
						for(var c=0;c<miniMap[0].length;c++){
							var img = imgHash[miniMap[r][c]][0]
							ctx.drawImage(img, 0, 0, img.width, img.height,
								c*sf+offX, r*sf+offY, sf, sf);
						}
					}

					ctx.restore();

					return canvas.toDataURL("map.png");
				}


				// MAKES ALL OF THE MAP THUMBNAILS FOR EACH LEVEL
				function updateMapIMG(){
					let allImgs = document.getElementsByClassName('mapPrev');
					for(let i=0;i<allImgs.length;i++){
						if(allImgs[i].name == null || allImgs[i].name == "")
							continue;
						let asciiMap = asciiMapArr[levelIDArr.indexOf(allImgs[i].name)]
						if(allImgs[i].src == null || allImgs[i].src == "" || allImgs[i].src == "demo_maps/blank_map.png"){
							allImgs[i].src = makePreviewMap(asciiMap);
						}
					}


					allMapsUpdated = true;
					//for(let i=0;i<allImgs.length;i++){if(allImgs[i].name == "46"){console.log(allImgs[i].src)}}

					//check that all imgs have a src
					for(let i=0;i<allImgs.length;i++){
						if((allImgs[i].src == "" || allImgs[i].src == "demo_maps/blank_map.png") && allImgs[i].name != ""){
							console.log("not ready: " + i + " - " + allImgs[i].name)
							allMapsUpdated = false;
						}
					}
				}

				// PUT IT ALL TOGETHER
				function setupPage(){
					if(!mapAllReady){mapAllReady = mapReady();}
					if(mapAllReady && !allMapsUpdated){updateMapIMG();}
					requestAnimationFrame(setupPage);
				}
				setupPage();

				// RESET THE LEVEL SET FOR THE RATED LEVELS (JS VERSION OF makeRatePair() in PHP)
				function resetRateLevels(){
					let dai = document.getElementById("levelAImg");
					let dbi = document.getElementById("levelBImg");

					//pick 2 new random levels
					let old2 = [dai.name, dbi.name];
					let new2 = [];
					for(let a=0;a<1000;a++){
						let i = Math.floor(Math.random()*levelIDArr.length);
						if((authorArr[i] == 'Baba' || authorArr[i] != CURRENT_USER) && (new2.length == 0 || (new2[0] != i)))
							new2.push(i);

						if(new2.length == 2)
							break;
					}

					if(new2.length != 2)
						return;

					//replace the data of the html objects
					let da = document.getElementById("levelAdiv");
					let db = document.getElementById("levelBdiv");
					let id1 = levelIDArr[new2[0]];
					let id2 = levelIDArr[new2[1]];

					dai.name = id1;
					dbi.name = id2;

					da.getElementsByTagName("H4")[0].innerHTML = ("#" + id1);
					db.getElementsByTagName("H4")[0].innerHTML = ("#" + id2);

					document.getElementById("levelAbtn").onclick = function(){gotoPlay(id1,true)};
					document.getElementById("levelBbtn").onclick = function(){gotoPlay(id2,true)};

					dai.src = makePreviewMap(asciiMapArr[new2[0]]);
					dbi.src = makePreviewMap(asciiMapArr[new2[1]]);

					//reset sliders
					document.getElementById("challengeSlide").value = 3;
					document.getElementById("designSlide").value = 3;

				}

				// CALCULATES THE RATINGS FOR THE 2 LEVELS BASED ON SLIDER POSITION
				function calcRating(){
					let rateA = 3;
					let rateB = 3;

					let ds = document.getElementById("designSlide").value;
					let cs = document.getElementById("challengeSlide").value;

					/*
					//left most
					rateA += (ds < 3 ? 3-ds : 0);
					rateA += (cs < 3 ? 3-cs : 0);

					//right most
					rateB += (ds > 3 ? ds-3 : 0);
					rateB += (cs > 3 ? cs-3 : 0);
					*/

					rateA += 3-ds;
					rateA += 3-cs;
					rateB += ds-3;
					rateB += cs-3;

					return [rateA, rateB];
				}

				// SUBMITS THE RATING FOR THE TWO LEVELS TO THE DATABASE
				function submitRating(){
					let aID = document.getElementById("levelAImg").name;
					let bID = document.getElementById("levelBImg").name;

					let two_rate = calcRating();
					let aRate = two_rate[0];
					let bRate = two_rate[1];

					$.ajax({
						type : "POST",
						url : "submitRating.php",
						data : { 
							levelA : aID,
							levelB : bID,
							rateA : aRate,
							rateB : bRate
						},	
						success: function(res){alert(res);resetRateLevels();},
						error: function(res){alert(res);}
					});
				}


				/// SEARCH PAGE FUNCTION ///

				
				var sortRules = {};
				var allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
				var useExactRule = false;

				// INITIALIZE SORT RULES
				function resetSortRules(){
					for(let r=0;r<allRules.length;r++){
						sortRules[allRules[r]] = false;
					}
					clearRuleTable();
				}

				// CLEAR THE RULE TABLE
				function clearRuleTable(){					
					for(let r=0;r<allRules.length;r++){
						let curRule = allRules[r];
						document.getElementById("obj_"+curRule).innerHTML = "&nbsp;";

					}
				}

				// TOGGLES RULES TO SEARCH FOR WHEN SORTING LEVELS
				function toggleSortRule(col){
					let rule = col.id.split("_")[1]   							//parse the rule down to letter and number combination (i.e. A2, E1)
					let active = (col.innerHTML == "X" ? true : false);			//check if already active

					//toggle active state of objective
					col.innerHTML = (active ? "&nbsp;" : "X");
					active = !active;	
					sortRules[rule] = active;
				}


				// MAKES A LEVEL BOX FOR THE CAROUSEL
				function makeLevelBox(levID, pos){
					let index = levelIDArr.indexOf(levID);

					//make wrapper div
					let d = document.createElement("div");
					d.classList.add("col-xs-2");
					if(pos == 'left')
						d.classList.add("col-xs-offset-3");
					d.classList.add("levelBox");


					//make image
					let i = document.createElement("img");
					i.classList.add("img-responsive");
					i.name = levID.toString();
					i.src = makePreviewMap(asciiMapArr[index]);
					if(pos == "left")
						i.style.float = "right";
					else if(pos == "mid")
						i.style.margin = 'auto';
					else if(pos == 'right')
						i.style.float = 'left';
					d.appendChild(i);


					//add content
					let d2 = document.createElement("div");
					d2.classList.add("levelSelect");

					let h = document.createElement("h4");
					h.classList.add("levelNoMINI");
					h.innerHTML = "#"+levID;

					let n = document.createElement("h4");
					n.classList.add("levelNameDiv2");
					n.innerHTML = (levelNameArr[index] ? levelNameArr[index].substring(0,18) + (levelNameArr[index].length > 18 ? "..." : "") : "-");

					let p = document.createElement("p");
					p.classList.add("levelLabel");
					p.innerHTML = authorArr[index].substring(0,18) + (authorArr[index].length > 18 ? "..." : "");

					//add buttons
					let b1 = document.createElement("button");
					b1.onclick = function(){gotoPlay(levID,true);}
					b1.innerHTML = "PLAY";

					let b2 = document.createElement("button");
					b2.onclick = function(){gotoEdit(levID);}
					b2.innerHTML = "EDIT";

					//put it all together
					d2.appendChild(h);
					d2.appendChild(n);
					d2.appendChild(p);
					d2.appendChild(b1);
					d2.appendChild(document.createElement("br"));
					d2.appendChild(b2);

					d.appendChild(d2);

					return d;

				}

				// MAKE EMPTY LEVEL BOX FOR THE CAROUSEL
				function makeEmptyBox(pos){
					//make wrapper div
					let d = document.createElement("div");
					d.classList.add("col-xs-2");
					if(pos == 'left')
						d.classList.add("col-xs-offset-3");
					d.classList.add("levelBox");


					//make image
					let i = document.createElement("img");
					i.classList.add("img-responsive");
					i.src = "demo_maps/transparent.png";
					if(pos == "left")
						i.style.float = "right";
					else if(pos == "mid")
						i.style.margin = 'auto';
					else if(pos == 'right')
						i.style.float = 'left';
					d.appendChild(i);

					return d;
				}

				//checks if a chromosome contains certain ruleset
				function hasRules(chromo, rl){
					let t = 0;
					for(let i=0;i<chromo.length;i++){
						if(rl[i] == '1' && chromo[i] == rl[i])
							t++;
					}
					return t;
				}

				// MAKES A CAROUSEL RETURNING RESULTS FROM A SEARCH
				function searchLevels(){
					//remove all child elements from the carousel to replace with new ones
					let resCar = document.getElementById("resultsCarousel");
					while (resCar.firstChild) {
				        resCar.removeChild(resCar.firstChild);
				    }

					//get search values (if available)
					let userSearch = document.getElementById("usernameSearch").value;
					let levIDSearch = document.getElementById("levelNoSearch").value;
					let levelNameSearch = document.getElementById("levelnameSearch").value;
					console.log(levelNameArr);
					let ruleSearch = "";
					for(let s=0;s<allRules.length;s++){
						ruleSearch += (sortRules[allRules[s]] ? "1" : "0");
					}

					//check level id first (most specific)
					if(levIDSearch != ""){
						let i = levelIDArr.indexOf(levIDSearch);

						//level id not found
						if(i == -1){
							resCar.innerHTML = "Level ID '" + levIDSearch + "' not found! Try again."; 
							document.getElementById("levelNoSearch").value = "";
						}else{
							//if found - should only be a single level 
							makeSearchCarousel([levIDSearch]);
						}

					}
					//then search by username
					else if(userSearch != ""){
						//get all direct matches first then inclusives
						let authList = [];
						for(let a=0;a<authorArr.length;a++){
							if(authorArr[a] == userSearch)		//add direct matches to front of list
								authList.unshift(a);
							else if(authorArr[a] == (userSearch + " + PCG.js"))	//add approximate matches to the back of the list
								authList.push(a);
						}

						//author not found
						if(authList.length == 0){
							resCar.innerHTML = "Author '" + userSearch + "' not found! Try again."; 
							document.getElementById("usernameSearch").value = "";
						}
						//show set
						else{
							//console.log(authList)
							makeSearchCarousel(authList.map(x => levelIDArr[x]));
						}
					}else if(levelNameSearch != ""){
						let nameList = [];
						for(let n=0;n<levelNameArr.length;n++){
							if(levelNameArr[n] == null)			//skip no-names
								continue;

							let mname = levelNameArr[n].toLowerCase();
							if(mname == levelNameSearch.toLowerCase())	//direct match to front of list
								nameList.unshift(n);
							else if(mname.includes(levelNameSearch.toLowerCase())) //partial match
								nameList.push(n);
						}

						//author not found
						if(nameList.length == 0){
							resCar.innerHTML = "Level name '" + levelNameSearch + "' not found! Try again."; 
							document.getElementById("levelnameSearch").value = "";
						}
						//show set
						else{
							//console.log(authList)
							makeSearchCarousel(nameList.map(x => levelIDArr[x]));
						}
					}
					//lastly search by rules
					else{
						let chromo = "";
						for(let s=0;s<allRules.length;s++){
							chromo += (sortRules[allRules[s]] ? "1" : "0");
						}

						let chromoList = [];
						for(let c=0;c<chromoRepArr.length;c++){
							if((useExactRule || (chromo.indexOf("1") == -1)) && chromoRepArr[c] == chromo)		//has exact match or has no rules
								chromoList.push([1,c]);
							else if(!useExactRule){							//may contain 1+ rules
								let r = hasRules(chromoRepArr[c], chromo);
								if(r > 0)
									chromoList.push([r,c]);		//add value then index to sort later
							}
						}

						//sort if not using exact rule (sort by most matching rules)
						if(chromoList.length > 0 && (!useExactRule || (chromo.indexOf("1") > -1))){
							chromoList.sort(function(a,b) {
							    return b[0]-a[0]
							});

							chromoList = chromoList.map(x => x[1]);
						}

						//no level found but give option to make it
						if(chromoList.length == 0){
							resetSortRules();

							resCar.appendChild(document.createElement("br"));
							resCar.appendChild(document.createElement("br"));
							resCar.innerHTML = "No level with those rules found. But you can create it!"; 
							resCar.appendChild(document.createElement("br"));
							resCar.appendChild(document.createElement("br"));

							//make a new level button to make the level
							let b = document.createElement("button");
							b.innerHTML = "MAKE THE LEVEL!";
							b.style.width = "15vw";
							b.style.height = '15vw';
							b.onclick = function(){gotoEdit(chromo,true);}
							resCar.appendChild(b);
						}
						//show set
						else{
							makeSearchCarousel(chromoList.map(x => levelIDArr[x]));
						}
					}

					$('.nav-pills a[href="#result_levels"]').trigger('click');
				}

				// MAKES A LEVEL CAROUSEL FROM THE IDS GIVEN
				function makeSearchCarousel(ids){
					let totScreens = Math.ceil(ids.length / 9);

					let c = document.createElement("div");
					c.classList.add("carousel-inner");

					//make screens
					let l = 0;
					let pos = ['left', 'mid', 'right'];
					for(let s=0;s<totScreens;s++){
						//item div
						let i = document.createElement("div");
						i.classList.add("item");
						if(s == 0)
							i.classList.add("active");

						//d block div
						let d = document.createElement("div");
						d.classList.add("d-block");
						d.classList.add("w-100");
						d.src = "...";

						//top row
						let r1 = document.createElement("div");
						r1.classList.add("row");
						r1.classList.add("row-no-gutters");
						for(let a=0;a<3;a++){
							if(l < ids.length)
								r1.appendChild(makeLevelBox(ids[l],pos[a]));
							else
								r1.appendChild(makeEmptyBox(pos[a]));
							l++;
						}

						//middle row
						let r2 = document.createElement("div");
						r2.classList.add("row");
						r2.classList.add("row-no-gutters");
						r2.classList.add("vert-align");
						for(let a=0;a<3;a++){
							if(l < ids.length)
								r2.appendChild(makeLevelBox(ids[l],pos[a]));
							else
								r2.appendChild(makeEmptyBox(pos[a]));
							l++;
						}

						//bottom row
						let r3 = document.createElement("div");
						r3.classList.add("row");
						r3.classList.add("row-no-gutters");
						for(let a=0;a<3;a++){
							if(l < ids.length)
								r3.appendChild(makeLevelBox(ids[l],pos[a]));
							else
								r3.appendChild(makeEmptyBox(pos[a]));
							l++;
						}

						//put it all together
						d.appendChild(r1);
						d.appendChild(r2);
						d.appendChild(r3);

						i.appendChild(d);

						c.appendChild(i);

					}

					document.getElementById("resultsCarousel").appendChild(c);
				}

				// Clears the rule table
				function clearTable(){
					resetSortRules();
				}

				// TOGGLE TO USE ANY RULE OR EXACT RULE
				function ruleTypeToggle(btn){
					useExactRule = !useExactRule;
					btn.innerHTML = "Levels with " + (useExactRule ? "EXACT " : "ANY ") + "rule";
				}

				function testme(e){
					alert("clicked!");
				}

			</script>


<!----------------------------------------        HTML FRONT-END LAYOUT CODE             ------------------------------------------->


	<body onload='init()'>
		<div class='container top-buffer1'>
			<!-- title -->
			<div class='row row-no-gutters'>
				<div class='col-xs-4'>
					<div id='babaBubble'>
						New here? <br> Click me to check out <br> the tutorial!
					</div>
					<a href='tutorial.php'>
					<img src='img/baba_tut_screen.png' class='img-responsive' width="20%" style='float:right;'  onmouseover="showChatBubble('baba')" onmouseleave="hideChatBubble('baba')">
					</a>
				</div>
				<div class='col-xs-4 text-center'>
					<a href='index.php'>
					<img src='baba_is_yall_title_wide.png' class='img-responsive' width="100%" style='margin:auto'>
					</a>
				</div>
				<div class='col-xs-4'>
					<div id='kekeBubble'>
						Wanna learn more? <br> Click me for some cool <br> stats and info!
					</div>
					<a href='stats_about.php'>
					<img src='img/keke_stat_screen.png' class='img-responsive' width="20%" style='float:left;' onmouseover="showChatBubble('keke')" onmouseleave="hideChatBubble('keke')">
					</a>
				</div>
				
			</div>

			<!-- dark mode toggle -->
			<img src='flaticon/sun.png' id='darkModeBtn' style='cursor:pointer;'>
			<script>
				const btn = document.getElementById('darkModeBtn');
				btn.src = ((!localStorage.darkmode || localStorage.darkmode == "no") ? "flaticon/moon.png" : "flaticon/sun.png");
				btn.addEventListener('click', function() {document.body.classList.toggle('light-mode'); localStorage.darkmode = (document.body.classList.contains("light-mode") ? "no" : "yes");btn.src = (localStorage.darkmode == "yes" ? "flaticon/sun.png" : "flaticon/moon.png");})
			</script>

			<!-- <div class='row row-no-gutters text-center top-buffer05'>
				<div class='col-xs-3 col-xs-offset-2'>
					<button style='background-color:#DC386A; width:70%;height:3vw;font-size:1.5vw;' class="btn-toggle">Toggle Dark Mode</button>
					<script>
						const btn = document.querySelector('.btn-toggle');
						btn.addEventListener('click', function() {document.body.classList.toggle('light-mode'); localStorage.darkmode = (document.body.classList.contains("light-mode") ? "no" : "yes");})
					</script>
				</div>
				<div class='col-xs-3 col-xs-offset-2'>
					<button id='feedback' style='background-color:#f00;width:70%;height:3vw;font-size:1.5vw;' onclick="window.open('https://forms.gle/zkAXTbkaGa1UDAhy8','_blank')">Beta Feedback Form</button>
					<script>
						//toggle feedback color
						let feedbackColor = "#ff0";
						setInterval(function(){feedbackColor = (feedbackColor == "#ff0" ? "#fff" : "#ff0");document.getElementById("feedback").style.backgroundColor = feedbackColor},500)
					</script>
				</div>
			</div> -->

			<!-- navigation bar -->
			<div class='row top-buffer05'>
				<div class='col-xs-12 text-center'>
					<ul class='nav nav-pills nav-justified' id='pages'>
						<li class='active lpill'><a data-toggle='pill' href='#new_screen'>New</a></li>
						<li class='mpill'><a data-toggle='pill' href='#top_screen'>Top</a></li>
						<li class='mpill'><a data-toggle='pill' href='#unmade_screen'>Unmade</a></li>
						<li class='mpill'><a data-toggle='pill' href='#rate_screen'>Rate</a></li>
						<li class='mpill'><a data-toggle='pill' href='#search_screen'>Search</a></li>
						<li class='rpill'><a data-toggle='pill' href='#user_screen' id='userTab'>Baba's Levels</a></li>
					</ul>
				</div>
			</div>

			<!-- save tabs -->
			<script>
				$('#pages a').click(function(e) {
				  e.preventDefault();
				  $(this).tab('show');
				  $(window).scrollTop(0); 
				});

				// store the currently selected tab in the hash value
				$("ul.nav-pills > li > a").on("shown.bs.tab", function(e) {
				  var id = $(e.target).attr("href").substr(1);
				  window.location.hash = id;
				});

				// on load of the page: switch to the currently selected tab
				var hash = window.location.hash;
				$('#pages a[href="' + hash + '"]').tab('show');
			</script>
			<script>
				setTimeout(function(){document.getElementById("babaBubble").style.visibility = "hidden";},3500);
			</script>


			<!-- map list -->
			<div class='row top-buffer05'>
				<div class='col'>

					<div class='tab-content'>

						<!-- new screen -->
						<div id='new_screen' class='tab-pane active'>
							<div id='levelCarousel_new' class="carousel slide" data-interval="false">
									
									<?php

										makeBoxSet('new');

									?>


								<a class="left carousel-control" href="#levelCarousel_new"  role="button" data-slide="prev" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</a>
								<a class="right carousel-control" href="#levelCarousel_new" role="button" data-slide="next" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</a>


								  <!-- arrows 
								  <div class='col-xs-2'>
										<img src='flaticon/left.png' class='img-responsive' width='100' height='100' style="margin: auto">
								   </div>
								  <div class='col-xs-2'>
										<img src='flaticon/right.png' class='img-responsive' width='100' height='100' style="margin: auto">
								   </div>
									-->
							</div>
							<div class='row row-no-gutters top-buffer3'>
								<div class='col-xs-2 col-xs-offset-5' style='text-align: center'>
									<button onclick="gotoEdit('')">Make a New Level</button>
								</div>
							</div>
						</div>

						<!-- top screen -->
						<div id='top_screen' class='tab-pane'>
							<div id='levelCarousel_top' class="carousel slide" data-interval="false">

								<?php

									makeBoxSet('top');

								?>


								<a class="left carousel-control" href="#levelCarousel_top"  role="button" data-slide="prev" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</a>
								<a class="right carousel-control" href="#levelCarousel_top" role="button" data-slide="next" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</a>

							</div>

							<div class='row row-no-gutters top-buffer3'>
								<div class='col-xs-2 col-xs-offset-5' style='text-align: center'>
									<button onclick="gotoEdit('')">Make a New Level</button>
								</div>
							</div>


						</div>

						<!-- unmade screen -->
						<div id='unmade_screen' class='tab-pane'>
							
							<div id='levelCarousel_unmade' class="carousel slide" data-interval="false">
								
								<?php

									makeBoxSet('unmade');

								?>

								<a class="left carousel-control" href="#levelCarousel_unmade"  role="button" data-slide="prev" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</a>
								<a class="right carousel-control" href="#levelCarousel_unmade" role="button" data-slide="next" style="background:none !important">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</a>
							</div>

							<div class='row row-no-gutters top-buffer3'>
								<div class='col-xs-2 col-xs-offset-5' style='text-align: center'>
									<button onclick="gotoEdit('')">Make a New Level</button>
								</div>
							</div>
						</div>

						<!-- rate screen -->
						<div id='rate_screen' class='tab-pane' style='color: #707070'>
							<div class='row row-no-gutters top-buffer1'>
								<div class='col-xs-12'>
									<!-- Level headers -->
									<div class='row row-no-gutters'>
										<div class='col-xs-3 col-xs-offset-2 txt-color' style='text-align: center;font-size:3vw;'>
											A
										</div>
										<div class='col-xs-3 col-xs-offset-2 txt-color' style='text-align: center;font-size:3vw;'>
											B
										</div>
									</div>

									<!-- levels and shuffle -->
									<div class='row row-no-gutters'>
										<?php makeRatePair(); ?>
									</div>

									<!-- sliders -->
									<div class='row row-no-gutters top-buffer1'>
										<div class='col-xs-2 col-xs-offset-5 txt-color' style='text-align: center'>
											<label for="challengeSlide">More Challenging</label>
										</div>
										
									</div>
									<div class='row row-no-gutters aligned-row'>
										<div class='col-xs-1 col-xs-offset-2 txt-color' style='text-align: center;font-size: 2vw'>
											A
										</div>
										<div class='col-xs-6'>
											<input type="range" class="custom-range" min="1" max="5" id="challengeSlide">
										</div>
										<div class='col-xs-1 txt-color'  style='text-align: center;font-size: 2vw'>
											B
										</div>
									</div>
									<div class='row row-no-gutters top-buffer2'>
										<div class='col-xs-2 col-xs-offset-5 txt-color' style='text-align: center'>
											<label for="designSlide">Better Design</label>
										</div>
									</div>
									<div class='row row-no-gutters'>
										<div class='col-xs-1 col-xs-offset-2 txt-color' style='text-align: center;font-size: 2vw'>
											A
										</div>
										<div class='col-xs-6'>
											<input type="range" class="custom-range" min="1" max="5" id="designSlide">
										</div>
										<div class='col-xs-1 txt-color'  style='text-align: center;font-size: 2vw'>
											B
										</div>
									</div>

									<!-- submit -->
									<div class='row row-no-gutters top-buffer2'>
										<div class='col-xs-2 col-xs-offset-5' style='text-align: center'>
											<button onclick='submitRating()'>SUBMIT</button>
										</div>
									</div>
									
								</div>
							</div>
							<!-- footer credits -->
							<div class='row top-buffer3'>
								<div class='col text-center'>UI Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
							</div>
						</div>

						<!-- search screen -->
						<div id='search_screen' class='tab-pane' style='color: #707070; bordercolor: #707070'>

							<div class='row row-no-gutters top-buffer05'>
								<div class='col-xs-12 text-center'>
									<div class='row row-no-gutters'>
										<div class='col-xs-6 col-xs-offset-3'>
											<!-- select screen (filter search or results) -->
											<ul class='nav nav-pills center-pills ' id='search_nav'>
												<li class='active'><a  class='txt-color' data-toggle='pill' style="font-size:15px;" href='#filter'>Filter</a></li>
												<li><a class='txt-color' data-toggle='pill' style="font-size:15px;"  href='#result_levels'>Results</a></li>
											</ul>
										</div>
									</div>
									<div class='row row-no-gutters top-buffer05'>
										<div class='col-xs-12'>
											<div class='tab-content'>
												<!-- FILTER SCREEN --> 
												<div id='filter' class='tab-pane active text-center'>
													<div class='row row-no-gutters'>
														<div class='col-xs-8 col-xs-offset-2 text-center'>
															<form autocomplete="false">	<!-- prevent chrome autocomplete on this -->
															<!-- search by username -->
															<div class='row row-no-gutters top-buffer1'>
																<div class='col-xs-8 col-xs-offset-2 text-center txt-color'>
																	USER: <input type='text' id='usernameSearch' style='color:#000'>
																</div>
															</div>
															<!-- search by name -->
															<div class='row row-no-gutters top-buffer1'>
																<div class='col-xs-8 col-xs-offset-2 text-center txt-color'>
																	LEVEL NAME: <input type='text' id='levelnameSearch' style='color:#000'>
																</div>
															</div>
															<!-- search by level number -->
															<div class='row row-no-gutters top-buffer1'>
																<div class='col-xs-8 col-xs-offset-2 text-center txt-color'> 
																	LEVEL #: <input type='text' id='levelNoSearch' size='7' autocomplete="chrome-off" style='color:black'>
																</div>
															</div>
															</form>
															<!-- search by rule --> 
															<div class='row row-no-gutters'>
																<div class='col-xs-6 col-xs-offset-3'>
																	<div class='row row-no-gutters top-buffer1' id='obj_table' style='border:2px solid white'>
																		<div class='col text-center'>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 border-right thick-border-bottom obj_text'>OBJECTIVE</div>
																				<div class='col-xs-2 text-center border-right thick-border-bottom obj_text'>Init</div>
																				<div class='col-xs-2 text-center thick-border-bottom obj_text'>End</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_AX'>X-IS-X</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_A1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_A2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_BX'>X-IS-Y</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_B1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_B2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_CX'>X-IS-PUSH</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_C1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_C2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_DX'>X-IS-MOVE</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_D1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_D2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_EX'>X-IS-STOP</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_E1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_E2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_FX'>X-IS-KILL</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_F1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_F2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_GX'>X-IS-SINK</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_G1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_G2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_HX'>X-IS-[PAIR]</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_H1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_H2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																			<div class='row row-no-gutters'>
																				<div class='col-xs-8 obj_lmcell obj_text' id='obj_IX'>[X,Y]-IS-YOU</div>
																				<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_I1' onclick='toggleSortRule(this)'>&nbsp;</div>
																				<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_I2' onclick='toggleSortRule(this)'>&nbsp;</div>
																			</div>
																		</div>
																	</div>

																	<script>resetSortRules();</script>

																	<!-- reset options -->
																	<div class='row row-no-gutters top-buffer1'>
																		
																		<div class='col-xs-6'>
																			<button onclick='ruleTypeToggle(this)' class='fs1' style='color:black'>Levels with ANY rule</button>
																		</div>
																		
																		<div class='col-xs-6'>
																			<button onclick='clearTable()' class='fs1' style='color:black'>CLEAR TABLE</button>
																		</div>

																	</div>

																</div>
															</div>
															
															<!-- search button-->
															<div class='row row-no-gutters top-buffer2'>
																<div class='col-xs-8 col-xs-offset-2'>
																	<img src='flaticon/search.png' class='img-responsive' width='75' height='75' style='margin:auto;cursor: pointer;' onclick='searchLevels()'>
																</div>
															</div>
															<!-- footer credits -->
															<div class='row top-buffer2'>
																<div class='col text-center'>UI Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
															</div>
														</div>
													</div>
												</div>



												<!-- RESULTS SCREEN -->
												<div id='result_levels' class='tab-pane'>

													<div class='row row-no-gutters'>
														<div class='col-xs-10 col-xs-offset-1'>
															<div id='resultsCarousel' class="carousel slide" data-interval="false">
																<?php

																	makeBoxSet('empty');

																?>
															</div>
															<a class="left carousel-control" href="#resultsCarousel" role="button" data-slide="prev" style="background:none !important">
																<span class="glyphicon glyphicon-chevron-left" style="color:#707070"></span>
															</a>
															<a class="right carousel-control" href="#resultsCarousel" role="button" data-slide="next" style="background:none !important">
																<span class="glyphicon glyphicon-chevron-right" style="color:#707070"></span>
															</a>
														</div>
													</div>
												</div>

											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>


						<!-- USER SCREEN -->
						<div id='user_screen' class='tab-pane'>
							<div class='row row-no-gutters top-buffer05'>
								<!-- user info -->
								<div class='col-xs-3'>
								<div class='affix' style='width:20%'>
									<div id='userTitle' class='babaStyle' style="margin-top: 5%">
										BABA
									</div>
									<div class='row row-no-gutters top-buffer1'>
										<div class='col-xs-12' style='text-align: center'>
											<button style='font-size:0.9vw;' onclick="gotoEdit('')">New Level</button>
										</div>
									</div>
									<!-- stats -->
									<div style='margin-right: 5%;margin-top: 8%'>
										<div class='row row-no-gutters top-buffer05'>
											<div class='col-xs-6 fs125 txt-color' style='text-align: right;'>
												<p><b># Levels:</b></p>
											</div>
											<div class='col-xs-6 fs125 txt-color'>
												<p id='numLevelStat' style='text-align: center;'></p>
											</div>
										</div>
										<div class='row row-no-gutters'>
											<div class='col-xs-6 fs125 txt-color' style='text-align: right;'>
												<p><b># Rule Reps:</b></p>
											</div>
											<div class='col-xs-6 fs125 txt-color'>
												<p id='repLevelStat' style='text-align: center;'></p>
											</div>
										</div>
										<div class='row row-no-gutters'>
											<div class='col-xs-6 fs125 txt-color' style='text-align: right;'>
												<p><b>Top Level ID:</b></p>
											</div>
											<div class='col-xs-6 fs125 txt-color'>
												<p id='topLevelStat' style='text-align: center;'></p>
											</div>
										</div>
									</div>
									<!-- login / register form -->
									<div id='loginBox' class='fs1' style='text-align: center;margin-top: 2%'>
									<form name="frmUser" method="post" action="">
										<div id='noUser'>
											<div class="message" style='margin-bottom: 5px;color:#f00'><?php if($message!="") { echo $message; } ?></div>
											<label for='usernameLogin' class='txt-color'>USERNAME</label><br><input type='text' name="usernameLogin" id='usernameLogin' style='width:70%'><br>
											<label for='passwordLogin' style='margin-top: 5%;' class='txt-color'>PASSWORD</label><br><input type='password' name='passwordLogin' id='passwordLogin' style='width:70%'><br>
											<div class='row row-no-gutters top-buffer1'>
												<div class='col-xs-4 col-xs-offset-2'><input type="submit" name="submit" value="Login" class="btnSubmit"></div>
												<div class='col-xs-4'><input type="submit" name="submit" value="Register" class="btnSubmit"></div>
											</div>
											
										</div>
										<div id='isUser'>
											<input type="submit" name="submit" value="Logout" class="btnSubmit" style='margin-top: 10%'>
										</div>
									</form>
									</div>
								</div>
								</div>
								<!-- levels -->
								<div class='col-xs-9 offset-xs-9'  style='border-left: 2px solid black;padding-left: 2%'>
									<?php
										getUserLevels();
									?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<!-- end map list and add marquee space at bottom-->
			<div class='row' style='margin-top: 1%;margin-bottom: 5%' id='marq_users'>
				<div class='col-xs-12'>
					<!--
					<div class='newMarquee'>
						<?php
							topUserMarquee();
						?>
					</div>
				-->
				</div>
			</div>
			<script>
				//reload saved tab
				if(hash){
					document.getElementById("new_screen").classList.remove("active");
					document.getElementById(hash.replaceAll("#","")).classList.add("active");
				}

				//show certain form based on username
				if(CURRENT_USER == "Baba"){		//show login
					document.getElementById("noUser").style.display = 'block';
					document.getElementById("isUser").style.display = 'none';
				}else{							//show logout
					document.getElementById("noUser").style.display = 'none';
					document.getElementById("isUser").style.display = 'block';
				}

				//help with form submission on refresh
				if ( window.history.replaceState ) {
				  window.history.replaceState( null, null, window.location.href );
				}	
			</script>
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