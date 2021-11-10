<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Stats and About - Baba is Y'all V2</title>
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
			}
			div{
				/*border: 1px solid black;*/
				color: var(--text);
			}
			h2{
				font-size:2.25vw;
			}
			h3{
				font-size:1.7vw;
			}
			h4{
				font-size:1.3vw;
			}
			h5{
				font-size:0.75vw;
			}

			/* navigation pills */
			#info > li.active > a, 
			#info > li.active > a:hover, 
			#info > li > a:hover, 
			#info > li.active > a:focus{
				background-color: #DC386A;
				color:#ffffff;
			}
			#info > li > a{
				color: #DC386A;
				font-size: 1.5vw;
				font-family: monospace;	
				width:20vw;
			}

			/* navigation pills */
			#statsNav > li.active > a, 
			#statsNav > li.active > a:hover, 
			#statsNav > li > a:hover, 
			#statsNav > li.active > a:focus{
				background-color: #fff;
				color:#000;
			}
			#statsNav > li > a{
				color: #fff;
				font-size: 1.2vw;
				font-family: monospace;	
				width:10vw;
			}

			p {
				font-size:1.15vw;
			}

			p > a{
				color: #DC386A;
			}

			.babaCol{
				color:#DC386A;
			}
			.babaCol2{
				color:#02BB4D;
			}

			/* arrows */
			.arrow {
			  border: solid white;
			  border-width: 0 0.3vw 0.3vw 0;
			  display: inline-block;
			  padding: 0.3vw;
			  text-align:center;
			}
			.up {
			  transform: rotate(-135deg);
			  -webkit-transform: rotate(-135deg);
			}

			.down {
			  transform: rotate(45deg);
			  -webkit-transform: rotate(45deg);
			}

			.chartJS{
				width:100%;
				height:30vw;
			}

			@font-face {font-family: BabaFont; src: url('KeyLime.ttf');}

		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

		<!-- get level and user data -->
		<?php

			//setup database connection
			$config = parse_ini_file('../config.ini'); 
			$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
			if(!$conn){die('BAD CONNECTION');}


			// get all of the levels
			$accessData = "SELECT LEVEL_ID, CHROMOSOME_REP, AUTHOR, RATING, SOLUTION from levels";
			$sql = $conn->query($accessData);
			if(!$sql)
				die("PHP/MYSQL Error : " . $conn->error);

			//save them to arrays to map to dictionaries later
			$level_ID = array();
			$chromo_rep = array();
			$author = array();
			$rating = array();
			$solution = array();
			while($row = $sql->fetch_assoc()){	
				$level_ID[] = $row['LEVEL_ID'];			// key 
				$chromo_rep[] = $row['CHROMOSOME_REP'];	// values 
				$author[] = $row['AUTHOR'];				//  |
				$rating[] = $row['RATING'];				//  |
				$solution[] = $row['SOLUTION'];			//  |
			}


			//get number of users registered
			$numUsersSQL=$conn->query("SELECT COUNT(*) as num_users from users");
			if(!$numUsersSQL)
				die("PHP/MYSQL Error : " . $conn->error);
			$numUsers=$numUsersSQL->fetch_assoc()['num_users'];

		?>


		<!-- statistic calculations -->
		<?php

			//removes PCG.js as co-author
			function remPCG($x){
				return str_replace(" + PCG.js", "", $x);
			}
			//get the number of rules from a chromosome
			function numRules($c){
				return substr_count($c, "1");
			}
			//returns number of levels with a specific index of a rule
			function ruleLevNum($rule){
				global $chromo_rep;
				$t = 0;
				for($c=0;$c<count($chromo_rep);$c++){
					$l = $chromo_rep[$c];
					if($l[$rule] == "1"){
						$t += 1;
					}
				}
				return $t;
			}


			// USER STATS


			//users with most levels made
			$author_cts = array_count_values(array_map('remPCG', $author));
			unset($author_cts['Baba']);	
			unset($author_cts['PCG.js']);
			arsort($author_cts);
			//print_r($author_cts);
			$mostLevelsAuthors = array_slice(array_keys($author_cts),0,3);
			$mostLevelsAuthors[] = "Baba";
			$mostLevelsAuthors[] = "PCG.js";

			//users with highest rated levels
			$authorRate = array();
			for($i=0;$i<count($rating);$i++){
				$a = remPCG($author[$i]);
				if(!array_key_exists($a, $authorRate)){
					$authorRate[$a] = 0;
				}
				$authorRate[$a] += floatval($rating[$i]);
			}
			unset($authorRate['Baba']);	
			unset($authorRate['PCG.js']);
			arsort($authorRate);
			//print_r($authorRate);
			$highRateAuthors = array_slice(array_keys($authorRate),0,3);
			$highRateAuthors[] = "Baba";
			$highRateAuthors[] = "PCG.js";
			
			//most rules covered
			$authorLevels = array();
			for($i=0;$i<count($chromo_rep);$i++){
				$a = remPCG($author[$i]);
				if(!array_key_exists($a, $authorLevels)){
					$authorLevels[$a] = array();
				}
				$authorLevels[$a][] = bindec($chromo_rep[$i]);
			}
			//print_r($authorLevels);
			//get number of distinct rules for author
			$authRules = array(); 
			foreach (array_keys($authorLevels) as $a){
				$authRules[$a] = count(array_count_values($authorLevels[$a]));
			}
			unset($authRules['Baba']);	
			unset($authRules['PCG.js']);
			arsort($authRules);
			//print_r($authRules);
			$mostRulesAuthors = array_slice(array_keys($authRules),0,3);
			$mostRulesAuthors[] = "Baba";
			$mostRulesAuthors[] = "PCG.js";



			// COVERAGE

			$rulesCovered = count(array_count_values($chromo_rep));
			$allRuleNum = pow(2,18);


			// RULE STATS
			//count number of levels with each rule rep
			$ruleNumStats = array();
			for($i=0;$i<18;$i++){
				$ruleNumStats[$i] = ruleLevNum($i);
			}
			//get percentage
			$ruleNumPerc = array();
			for($i=0;$i<18;$i++){
				$ruleNumPerc[$i] = $ruleNumStats[$i]/(count($level_ID));
			}
			//print_r($ruleNumStats);

			//prints a boostrap style table about rule numbers and percentages
			$ruleLit = array("X-IS-X", "X-IS-Y", "X-IS-PUSH", "X-IS-MOVE", "X-IS-STOP", "X-IS-KILL", "X-IS-SINK", " X-IS-[PAIR]", "[X,Y]-IS-YOU");
			function makeRuleTable(){
				global $ruleNumStats, $ruleNumPerc, $ruleLit;

				//header
				echo "<div class='row row-no-gutters border' style='border-color:white'>";
					echo "\t<div class='col-xs-5 col-xs-offset-1'><h4>RULE</h4></div>";
					echo "\t<div class='col-xs-2'><h4>#</h4></div>";
					echo "\t<div class='col-xs-2'><h4>%</h4></div>";
					echo "\t<div class='col-xs-2'></div>";
					echo "</div>";

				for($r=0;$r<9;$r++){
					$s = $r*2;
					$e = $s+1;
				
					//total
					echo "<div class='row row-no-gutters' onclick='showHideSubObj(" . $r .")'>";
					echo "\t<div class='col-xs-5 col-xs-offset-1'><h4>" . $ruleLit[$r] . "</h4></div>";
					echo "\t<div class='col-xs-2'><h4 class='babaCol'>" . ($ruleNumStats[$s] + $ruleNumStats[$e]) . "</h4></div>";
					echo "\t<div class='col-xs-2'><h4 class='babaCol'>" . number_format(($ruleNumPerc[$s] + $ruleNumPerc[$e])*100,2) . "</h4></div>";
					echo "\t<div class='col-xs-2' style='text-align:center'><i class='arrow down' id='ruleArr" . $r ."'></i></div>";
					echo "</div>";

					
					//indvidual
					echo "<div class='row row-no-gutters hidden' id='hideObjA" . $r ."'>";
					echo "\t<div class='col-xs-5 col-xs-offset-1'><h5>Start (1)</h5></div>";
					echo "\t<div class='col-xs-2'><h5 class='babaCol'>" . ($ruleNumStats[$s]) . "</h5></div>";
					echo "\t<div class='col-xs-2'><h5 class='babaCol'>" . number_format(($ruleNumPerc[$s]*100),2) . "</h5></div>";
					echo "\t<div class='col-xs-2'></div>";
					echo "</div>";

					//total
					echo "<div class='row row-no-gutters hidden' id='hideObjB" . $r ."'>";
					echo "\t<div class='col-xs-5 col-xs-offset-1'><h5>End (2)</h5></div>";
					echo "\t<div class='col-xs-2'><h5 class='babaCol'>" . ($ruleNumStats[$e]) . "</h5></div>";
					echo "\t<div class='col-xs-2'><h5 class='babaCol'>" . number_format(($ruleNumPerc[$e]*100),2) . "</h5></div>";
					echo "\t<div class='col-xs-2'></div>";
					echo "</div>";
					

				}
			}


			// AUTHOR CATEGORY
			$userOnly = 0;
			$pcgOnly = 0;
			$mixedOnly = 0;

			$babaName = 0;
			$realName = 0;

			for($u=0;$u<count($author);$u++){
				$a = $author[$u];
				if($a == "PCG.js"){
					$pcgOnly += 1;
				}
				else if(strpos($a, ' + ') !== false){
					$mixedOnly += 1;
					if((strpos($a, 'Baba') !== false)){
						$babaName += 1;
					}else{
						$realName += 1;
					}
				}
				else if((strpos($a, 'PCG.js') == false)){
					$userOnly += 1;

					if($a == "Baba"){
						$babaName += 1;
					}else{
						$realName += 1;
					}
				}
			}

			// AVERAGES CATEGORY
			$avgLvlUser = array_sum(array_values($author_cts))/count($author_cts);
			$numRuleArr = array_map('numRules', $chromo_rep);
			$avgRules = array_sum($numRuleArr)/count($chromo_rep);

			//determine rule per author type
			$avgRuleRepArr = array("Mixed"=>array(), "PCG"=>array(), "User"=>array());

			for($i=0;$i<count($numRuleArr);$i++){
				//determine author type
				$userType = "";
				if(strpos($author[$i], ' + ') !== false){
					$userType = "Mixed";
				}else if($author[$i] == "PCG.js"){
					$userType = "PCG";
				}else{
					$userType = "User";
				}

				$avgRuleRepArr[$userType][] = $numRuleArr[$i];

			}

			$avgUserRule = array_sum(array_values($avgRuleRepArr["User"]))/count($avgRuleRepArr["User"]);
			$avgPCGRule = array_sum(array_values($avgRuleRepArr["PCG"]))/count($avgRuleRepArr["PCG"]);
			$avgMixedRule = array_sum(array_values($avgRuleRepArr["Mixed"]))/count($avgRuleRepArr["Mixed"]);

			$avgSolLen = array_sum(array_map('strlen',$solution))/count($solution);


		?>

		<script>
			//toggle sub objectives
			function showHideSubObj(index){
				let hid = document.getElementById("hideObjA" + index).classList.contains("hidden");
				//hide
				if(!hid){
					document.getElementById("hideObjA" + index).classList.add("hidden");
					document.getElementById("hideObjB" + index).classList.add("hidden");
					document.getElementById("ruleArr" + index).classList.add("down");
					document.getElementById("ruleArr" + index).classList.remove("up");
				}
				//show
				else{
					document.getElementById("hideObjA" + index).classList.remove("hidden");
					document.getElementById("hideObjB" + index).classList.remove("hidden");
					document.getElementById("ruleArr" + index).classList.add("up");
					document.getElementById("ruleArr" + index).classList.remove("down");
				}

			}


		</script>


	</head>

	<body>
		<div class='container top-buffer2'>

			<!-- navigation -->
			<div class='row text-center'>
				<div class='col-xs-12'>
					<ul class='nav nav-pills center-pills top-buffer05' id='info'>
						<li class='active' style='margin-right:5vw'><a data-toggle='pill' href='#stats'>STATS</a></li>
						<li style='margin-left:5vw'><a data-toggle='pill' href='#about'>ABOUT</a></li>
					</ul>
				</div>
			</div>

			<!-- content -->
			<div class='row top-buffer2 thick-border text-center' style='border-color:white'>
				<div class='col-xs-12'>
					<div class='tab-content'>
						<!-- STATS TAB -->
						<div id='stats' class='tab-pane active text-center'>
							<!-- stats nav -->
							<div class='row text-center top-buffer05'>
								<div class='col-xs-12'>
									<ul class='nav nav-pills center-pills top-buffer05' id='statsNav'>
										<li class='active'><a data-toggle='pill' href='#users'>Users</a></li>
										<li><a data-toggle='pill' href='#coverage'>Coverage</a></li>
										<li><a data-toggle='pill' href='#rules'>Rules</a></li>
										<li><a data-toggle='pill' href='#author'>Authors</a></li>
										<li><a data-toggle='pill' href='#avg'>Averages</a></li>
									</ul>
								</div>
							</div>
							<div class='row text-center top-buffer1'>
								<div class='col-xs-10 col-xs-offset-1 text-center'>
									<div class='tab-content'>
										<!-- user statistics -->
										<div id='users' class='tab-pane active'>
											<div class='row'>
												<div class='col-xs-12'><h2>Users Registered: <span class='babaCol'><?php echo $numUsers?></span></h2></div>
											</div>
											<div class='row top-buffer2'>
												<div class='col-xs-12'><h2 class='babaCol2'><u>Top Users</u></h2></div>
											</div>
											<div class='row'>
												<div class='col-xs-12'><h3>Most Rule Combinations Covered:</h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-4 '><h4 class='babaCol'><?php echo $mostRulesAuthors[0] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $mostRulesAuthors[1] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $mostRulesAuthors[2] ?></h4></div>
											</div>
											<div class='row top-buffer1'>
												<div class='col-xs-12'><h3>Most Levels Made:</h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $mostLevelsAuthors[0] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $mostLevelsAuthors[1] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $mostLevelsAuthors[2] ?></h4></div>
											</div>
											<div class='row top-buffer1'>
												<div class='col-xs-12'><h3>Highest Rated Levels:</h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $highRateAuthors[0] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $highRateAuthors[1] ?></h4></div>
												<div class='col-xs-4'><h4 class='babaCol'><?php echo $highRateAuthors[2] ?></h4></div>
											</div>
											<br>
											<br>
										</div>


										<!-- level coverage statistics -->
										<div id='coverage' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3># Level Made: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo count($level_ID);?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3># Rule Combinations: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo $rulesCovered; ?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>% Rules Covered: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo $rulesCovered . " / " . $allRuleNum  . " = " . number_format((($rulesCovered/$allRuleNum)*100.0),4) . "%"; ?></h3></div>
											</div>
											<br>
										</div>
										<!-- rules statistics -->
										<div id='rules' class='tab-pane'>
											<div class='row row-no-gutters'>
												<!-- table -->
												<div class='col-xs-4'>
													<div class='row row-no-gutters'>
														<div class='col-xs-12 border' style='border-color: white'>
															<?php makeRuleTable(); ?>
														</div>
													</div>
												</div>
												<!-- pie chart -->
												<div class='col-xs-7 col-xs-offset-1'>
													<div class='row row-no-gutters'>
														<div class='col-xs-12 border' style='border-color: white'>
															<div id ='ruleChart' class='chartJS'>
															<!-- create pie chart from the rule percentages calculated by the php --> 
															<script>
																//turns a dictionary into data for Canvas.JS formatted chart
																function dict2Chart(d){
																	let dat = [];
																	for(let k in d){
																		dat.push({'y':d[k], 'label':k});
																	}
																	return dat;
																}

																// get combined percentages per rule
																let rnp = <?php echo json_encode($ruleNumPerc); ?>;
																let rnpCombine = [];
																let rules = ["X-IS-X", "X-IS-Y", "X-IS-PUSH", "X-IS-MOVE", "X-IS-STOP", "X-IS-KILL", "X-IS-SINK", " X-IS-[PAIR]", "[X,Y]-IS-YOU"];
																for(let p=0;p<9;p++){
																	let y = (rnp[p*2]+rnp[(p*2)+1])*100;
																	let l = rules[p];
																	rnpCombine.push({'y':y, 'label':l});
																}

																CanvasJS.addColorSet("BabaTheme",
												                [//colorSet Array
													                "#fff",
													                "#FF0102",
													                "#7F8103",
													                '#00FF04',
													                "#028100",
													                "#8A0101",
													                '#1C01FF',
													                '#FE8D01',
													                '#FF01FF'
													                               
												                ]);

																var chart = new CanvasJS.Chart("ruleChart", {
																	animationEnabled: false,
																	title: {
																		text: "Rule Percentages"
																	},
																	theme: 'dark2',
																	colorSet: 'BabaTheme',
																	backgroundColor: '#150E16',
																	data: [{
																		type: "pie",
																		startAngle: 270,
																		yValueFormatString: "##0.00\"%\"",
																		indexLabel: "{label} {y}",
																		dataPoints: rnpCombine
																	}]
																});
																chart.render();

															</script>
															</div>
														</div>
													</div>
												</div>
											</div>

											
											
											<br>
										</div>
										<!-- level author statistics -->
										<div id='author' class='tab-pane'>
											<div class='row row-no-gutters'>
												<!-- author pcg table -->
												<div class='col-xs-5 border' style='border-color:white'>

													<div class='row row-no-gutters border' style='border-color:white'>
														<div class='col-xs-6'><h4>Author Type</h4></div>
														<div class='col-xs-3'><h4>#</h4></div>
														<div class='col-xs-3'><h4>%</h4></div>
													</div>
													<div class='row row-no-gutters'>
														<div class='col-xs-6'><h4>User only</h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo $userOnly; ?></h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo number_format(($userOnly/count($author)*100),2); ?></h4></div>
													</div>
													<div class='row row-no-gutters'>
														<div class='col-xs-6'><h4>PCG.js only</h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo $pcgOnly; ?></h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo number_format(($pcgOnly/count($author)*100),2); ?></h4></div>
													</div>
													<div class='row row-no-gutters'>
														<div class='col-xs-6'><h4>Mixed Author</h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo $mixedOnly; ?></h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo number_format(($mixedOnly/count($author)*100),2); ?></h4></div>
													</div>
												</div>
												<!-- author percentage chart -->
												<div class='col-xs-5 col-xs-offset-2 border' style='border-color:white'>

													<div class='row row-no-gutters border' style='border-color:white'>
														<div class='col-xs-6'><h4>User Type</h4></div>
														<div class='col-xs-3'><h4>#</h4></div>
														<div class='col-xs-3'><h4>%</h4></div>
													</div>
													<div class='row row-no-gutters'>
														<div class='col-xs-6'><h4>Registered User</h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo $realName; ?></h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo number_format(($realName/($userOnly+$mixedOnly))*100,2); ?></h4></div>
													</div>
													<div class='row row-no-gutters'>
														<div class='col-xs-6'><h4>Anonymous (Baba)</h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo $babaName; ?></h4></div>
														<div class='col-xs-3'><h4 class="babaCol"><?php echo number_format(($babaName/($userOnly+$mixedOnly))*100,2); ?></h4></div>
													</div>
												</div>

											</div>
											<div class='row row-no-gutters top-buffer2'>
												<!-- user author type table -->
												<div class='col-xs-5 border' style='border-color:white'>
													<div id='authorChart' class='chartJS'>
														<script>
															// get combined percentages per rule
															let userPerc = <?php echo $userOnly/count($author); ?>;
															let pcgPerc = <?php echo $pcgOnly/count($author); ?>;
															let mixedPerc = <?php echo $mixedOnly/count($author); ?>;


															CanvasJS.addColorSet("BabaTheme2",
											                [//colorSet Array
												                "#fff",
												                "#FF0102",
												                '#FE8D01'
												                               
											                ]);

															var chart = new CanvasJS.Chart("authorChart", {
																animationEnabled: false,
																title: {
																	text: "Author Type Percentage"
																},
																theme: 'dark2',
																colorSet: 'BabaTheme2',
																backgroundColor: '#150E16',
																data: [{
																	type: "pie",
																	startAngle: 270,
																	yValueFormatString: "##0.00\"%\"",
																	indexLabel: "{label} {y}",
																	dataPoints: [
																		{'y':userPerc*100,'label':'User only'},
																		{'y':pcgPerc*100,'label':'PCG only'},
																		{'y':mixedPerc*100,'label':'Mixed'}
																	]
																}]
															});
															chart.render();
														</script>
													</div>
												</div>
												<!-- author percentage chart -->
												<div class='col-xs-5 col-xs-offset-2 border'  style='border-color:white'>
													<div id='userChart' class='chartJS'>
														<script>
															// get combined percentages per rule
															let babaPerc = <?php echo $babaName/($userOnly+$mixedOnly); ?>;
															let realPerc = <?php echo $realName/($userOnly+$mixedOnly); ?>;


															CanvasJS.addColorSet("BabaTheme3",
											                [//colorSet Array
												                "#DC386A",
												                "#02BB4D"
												                               
											                ]);

															var chart = new CanvasJS.Chart("userChart", {
																animationEnabled: false,
																title: {
																	text: "User Type Percentage"
																},
																theme: 'dark2',
																colorSet: 'BabaTheme3',
																backgroundColor: '#150E16',
																data: [{
																	type: "pie",
																	startAngle: 270,
																	yValueFormatString: "##0.00\"%\"",
																	indexLabel: "{label} {y}",
																	dataPoints: [
																		{'y':babaPerc*100,'label':'Baba user'},
																		{'y':realPerc*100,'label':'Real user'}
																	]
																}]
															});
															chart.render();
														</script>
													</div>
												</div>

											</div>
											<br>

										</div>
										<!-- averages statistics -->
										<div id='avg' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg # levels per user: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgLvlUser,3);?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg # rules per level: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgRules,3); ?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg # rules per User author: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgUserRule,3); ?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg # rules per PCG author: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgPCGRule,3); ?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg # rules per Mixed author: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgMixedRule,3); ?></h3></div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6' style='text-align: right'><h3>Avg length of solutions: </h3></div>
												<div class='col-xs-4'><h3 class='babaCol'><?php echo number_format($avgSolLen,3); ?></h3></div>
											</div>
											
											<br>
										</div>
									</div>
								</div>
							</div>

						</div>




						<!-- ABOUT TAB -->
						<div id='about' class='tab-pane'>
							<div class='row row-no-gutters'>
								<div id='about_content' class='col-xs-9' style='text-align: left;padding: 3%'>
									<h2><u>BABA IS Y'ALL</u></h2>
									<p>This site was made for non-profit research purposes only.</p>
									<p>This is a systems project experimenting with Mixed-Initiative PCG and crowdsourcing.</p>
									<p>We are trying to generate high quality, but diverse solvable levels across a feature set (Baba is You rules) using both PCG AI and human input</p>
									<p>This is the second iteration of the project; developed with more focus on user-experience</p>
									<p>You can read more about the first prototype project 
										<a href="https://arxiv.org/abs/2003.14294" target="_blank">here</a> 
										and the new updated project with the user study 
										<a href="#">here</a> (PAPER PENDING)</p>
									<br>
									<p>The original <a href='https://store.steampowered.com/app/736260/Baba_Is_You/' target="_blank">Baba is You</a> game was created by <a href="https://twitter.com/ESAdevlog" target="_blank">Hempuli.</a></p>
									<p>This is a clone game made in JavaScript to allow level editing, AI simulation, and content generation.</p> 
									<p>The sprites used are from the Baba is You <a href='https://hempuli.itch.io/baba-is-you' target="_blank">jam version</a> and belong to Hempuli but were used with permission (Thank You, Hempuli!)</p>
									<p>This project was made by 
										<a href="https://mastermilkx.github.io" target="_blank">M</a> 
										<a href="http://mastermilkx.itch.io" target="_blank">"Milk"</a> 
										<a href="https://twitter.com/MasterMilkX" target="_blank">Charity</a>, 
										<a href="https://www.linkedin.com/in/ishavdave/" target="_blank">Isha</a>
										<a href="" target="_blank">Dave</a>,
										<a href="http://akhalifa.com/blog/" target="_blank"> Ahmed</a> 
										<a href="https://amidos2006.itch.io/" target="_blank"> "amidos2006"</a> 
										<a href="https://twitter.com/amidos2006?lang=en" target="_blank"> Khalifa</a>, and 
										<a href="http://julian.togelius.com/" target="_blank">Julian</a> 
										<a href="https://twitter.com/togelius" target="_blank">Togelius</a> in the 
										<a href="http://game.engineering.nyu.edu/" target="_blank">NYU Game Innovation Lab</a></p>
									</div>
								<div class='col-xs-3 text-center' style='padding: 3%'>
									<p>Click me for a surprise!</p>
									<a href="extra_title.html" target='_blank'><img src='extra_title.png' style='width:90%;' ></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- footer row -->
			<div class='row row-no-gutters'>
				<div class='col top-buffer2 text-center'>
					<button onclick="location.href='map_home.php';" style='color:black'>Back to Home Screen</button>
				</div>
			</div>
		</div>
	</body>
</html>