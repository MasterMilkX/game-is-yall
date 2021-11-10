<?php
	
	/* get current user for the submission validation */
	session_start();
	$username = (isset($_SESSION['username']) ? $_SESSION['username'] : 'Baba');

	// get all of the other levels (for validation)
	$config = parse_ini_file('../config.ini'); 
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
	if(!$conn){die('BAD CONNECTION');}

	$accessData = "SELECT LEVEL_ID, ASCII_MAP, AUTHOR from levels";
	$sql = $conn->query($accessData);

	$authors = array();
	$maps = array();
	$ids = array();

	if(!$sql)
		die("PHP/MYSQL Error (retrieving levels): " . $conn->error);
	
	//get level data
	while($row = $sql->fetch_assoc()){	
		$maps[] = $row['ASCII_MAP'];
		$authors[] = $row['AUTHOR'];		
		$ids[] = $row['LEVEL_ID'];	
	}



	//set to local variables for ease of access
	$author = $_POST['author'];
	$chromo_rep = $_POST['chromosome_rep'];
	$asciiLevel = $_POST['ascii_map'];
	$mapW = intval($_POST['map_width']);
	$mapH = intval($_POST['map_height']);
	$solution = $_POST['solution'];
	$levelName = $_POST['level_name'];


	// CHECK VALIDATION OF LEVEL (IN CASE HACKERS MESSED WITH JS DATA)
	
	// 1. check author [PCG.js, USERNAME, or USERNAME + PCG.js]
	if(($author != "PCG.js") && ($author != $username) && ($author != ($username . " + PCG.js"))){
		die("Invalid author! \nRefresh the page (and please don't hack the application code)");
	}

	// 2. check if chromosome representation is a valid string [length, 0's and 1's, matches initial and end state]
	if(strlen($chromo_rep) != 18){
		die("Invalid chromosome representation length! \nRefresh the page (and please don't hack the application code)");
	}else if(!preg_match('/[01]/', $chromo_rep)){
		die("Invalid chromosome representation! \nRefresh the page (and please don't hack the application code)");
	}
	//CHECK SIMULATION HERE

	//3. check map width and height [levelAscii == w*h, w and h are integers]
	if(!is_int($mapW) || !is_int($mapH)){
		die("Invalid map width and/or height! \nRefresh the page (and please don't hack the application code)");
	}else if(strlen($asciiLevel) != ((($mapW+1)*$mapH)-1)){
		die("Invalid map width and/or height! \nRefresh the page (and please don't hack the application code)");
	}

	//4. check solution [valid string, leads to win state]
	if(!preg_match('/[udlrs]/', $solution)){
		die("Invalid solution representation! \nRefresh the page (and please don't hack the application code)");
	}
	//simulate here

	//5. check ascii map [level saved already, ]
	$savedLevel = array_search($asciiLevel, $maps);
	if($savedLevel){
		die("Level already created (ID: #" . $ids[$savedLevel] . " by " . $authors[$savedLevel] . ")!");
	}

	/*
	 * 
	 * TODO
	 *
	 */


	//submit the level

	$insert_query = $conn->prepare("INSERT into levels (LEVEL_ID, CHROMOSOME_REP, ASCII_MAP, MAP_WIDTH, MAP_HEIGHT, SOLUTION, AUTHOR, RATING, TOTAL_RATINGS, DATE_CREATED,LEVEL_NAME,VIEWS) values (null, ?, ?, ?, ?, ?, ?, null, 0, CURDATE(),?,0);");
	$insert_query->bind_param("ssiisss", $chromo_rep, $asciiLevel, $mapW, $mapH, $solution, $author, $levelName);

	//execute insertion of new level
	if(!$insert_query->execute()){
		die("PHP/MYSQL Error (submitting level): " . $insert_query->error);
	}else{
		if(empty($levelName)){
			echo("Level successfully submitted!");
		}
		else{
			echo("Level [" . $levelName ."] successfully submitted!");
		}
	}

	mysqli_close($conn);

?>