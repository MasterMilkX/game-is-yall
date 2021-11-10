<?php

	// setup the connection
	$config = parse_ini_file('../config.ini'); 
	$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
	if(!$conn){die('BAD CONNECTION');}


	//set to local variables for ease of access
	$levelA = intval($_POST['levelA']);
	$levelB = intval($_POST['levelB']);
	$rateA = intval($_POST['rateA']);
	$rateB = intval($_POST['rateB']);


	//get the list of level ids
	$accessData = "SELECT LEVEL_ID from levels";
	$sql = $conn->query($accessData);
	$ids = array();
	if(!$sql)
		die("PHP/MYSQL Error (retrieving levels): " . $conn->error);
	//get level data
	while($row = $sql->fetch_assoc()){	
		$ids[] = $row['LEVEL_ID'];	
	}


	// validation
	if(!is_int($levelA) || !in_array($levelA,$ids)){
		die("Error! Level A (#" . $levelA . ") is not a valid ID number! \nRefresh the page (and please don't hack the application code)");
	}else if(!is_int($levelB) || !in_array($levelB,$ids)){
		die("Error! Level B (#" . $levelB . ") is not a valid ID number! \nRefresh the page (and please don't hack the application code)");
	}

	if(!is_int($rateA) || $rateA > 7 || $rateA < -1){
		die("Error! Level A rating is an invalid value! \nRefresh the page (and please don't hack the application code)");
	}else if(!is_int($rateB) || $rateB > 7 || $rateB < -1){
		die("Error! Level B rating is an invalid value! \nRefresh the page (and please don't hack the application code)");
	}


	// get the ratings of the two levels to increment and average
	$levA_query =  $conn->prepare("SELECT LEVEL_ID, RATING, TOTAL_RATINGS from levels where LEVEL_ID = ?;");
	$levA_query->bind_param("i", $levelA);
	$levA_query->execute();
	$sqlA = $levA_query->get_result();

	$past_Arate = 0;
	$past_Atot = 0;
	if(!$sqlA)
		die("PHP/MYSQL Error : " . $levA_query->error);

	//should only be one row so save the data to an array
	while($row = $sqlA->fetch_assoc()){	
		$past_Arate = floatval($row['RATING']);
		$past_Atot = intval($row['TOTAL_RATINGS']);
	}

	$levB_query =  $conn->prepare("SELECT LEVEL_ID, RATING, TOTAL_RATINGS from levels where LEVEL_ID = ?;");
	$levB_query->bind_param("i", $levelB);
	$levB_query->execute();
	$sqlB = $levB_query->get_result();

	$past_Brate = 0;
	$past_Btot = 0;
	if(!$sqlB)
		die("PHP/MYSQL Error : " . $levB_query->error);

	//should only be one row so save the data to an array
	while($row = $sqlB->fetch_assoc()){	
		$past_Brate = floatval($row['RATING']);
		$past_Btot = intval($row['TOTAL_RATINGS']);
	}


	//calculate new rating by multiplying current rating by total ratings, adding the new rating, and dividing by the new total numbers
	$newTotA = ($past_Atot+1);
	$newTotB = ($past_Btot+1);

	$newRateA = (($past_Arate*$past_Atot)+$rateA)/$newTotA;
	$newRateB = (($past_Brate*$past_Btot)+$rateB)/$newTotB;

	//echo "" . $past_Arate . " -> " . $newRateA . " | " . $past_Atot . " -> " . "$newTotA";

	//update the level A rating
	$updateA_query = $conn->prepare("UPDATE levels SET RATING = ?, TOTAL_RATINGS = ? WHERE LEVEL_ID = ?;");
	$updateA_query->bind_param("dii", $newRateA, $newTotA, $levelA);

	//execute insertion of new level
	if(!$updateA_query->execute()){
		die("PHP/MYSQL Error (updating level A): " . $updateA_query->error);
	}


	//update the level B rating
	$updateB_query = $conn->prepare("UPDATE levels SET RATING = ?, TOTAL_RATINGS = ? WHERE LEVEL_ID = ?;");
	$updateB_query->bind_param("dii", $newRateB, $newTotB, $levelB);

	//execute insertion of new level
	if(!$updateB_query->execute()){
		die("PHP/MYSQL Error (updating level A): " . $updateB_query->error);
	}

	echo "Level ratings updated! Thank you!";

	mysqli_close($conn);

?>