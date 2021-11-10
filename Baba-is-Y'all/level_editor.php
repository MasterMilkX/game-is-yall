<?php
	/* get current user for the editing */
	session_start();
	$username = (isset($_SESSION['username']) ? $_SESSION['username'] : 'Baba');
?>
<script>
	var CURRENT_USER = '<?php echo $username;?>';
</script>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Level Editor - Baba is Y'all V2</title>
		<meta charset="utf-8">

		<!-- bootstrap stuff -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

		<!-- main stylesheet -->
		<link rel="stylesheet" href="layout_style.css">

		<!-- extra styles-->
		<style>
			/* generic html/css elements */
			body{
				background-color: #150E16;
			}
			div{
				/*border: 1px solid black;*/
				color: var(--text);
			}
			input{
				color:black;
			}
			button{
				color:black;
			}

			/* editor canvas */
			#editCanvas{
				background-color: #343434;
				border:2px solid #007C13;
				width: 100%;
				height: auto;
			}

			/* nav pill color modify */
			.nav-pills > li > a{
				color: var(--text);
			}
			.nav-pills > li > a:hover{
				color: black;
			}

			.nav-pills > li.active > a, .nav-pills > li.active > a:focus {
		        color: black;
		        background-color: var(--text);
		    }

		    .nav-pills > li.active > a:hover {
		        background-color: var(--text);
		        color:black;
		    }

			/* clickable image indicator */
			/* hover */
			.selImg{
				border: none;
			}
			.selImg:hover{
				border: 3px solid white;
			}
			.selImg1{
				border: 2px solid yellow;
			}

			.selImg2{
				opacity: 0.7
			}
			.selImg2:hover{
				opacity: 1.0
			}
			.selImg3{
				border:4px solid yellow;
				opacity: 1.0
			}

			label {
				cursor: pointer;
			}


			/*#play-pause:checked + #evolveBtn::after {
				font-size: 6vw;
				content: '❙❙';
				background-color: transparent;
			}

			#evolveBtn::after {
				font-size: 6vw;
				content: '►';
				background-color: transparent;
			}*/

			#playPause{
				position: absolute;
				top:50%;
				left:50%;
				transform: translate(-50%, -50%);
				font-size:6vw;
				background-color: transparent;
			}
			/* border */
			.colImg{
				background-color: transparent;
			}
			.colImg:hover{
				background-color: white;
			}

			.functImg{
				background-color: yellow;
			}
			.lockImg{
				background-color: red;
			}

			#controlTxt{
				font-size: 1.5em;
			} 

			/* helper div */
			.helpDiv {
				z-index: 100;
				position: relative;
				width:100px;
				height:100px;
				background-color: #cdcdcd;
			}


		</style>

		<!-- javascript -->
		<script src="js/baba.js"></script>
		<script src="js/map.js"></script>

	</head>


	<?php
		/* get the ELITE levels for use with javascript later */
		$config = parse_ini_file('../config.ini'); 

				$conn = new mysqli($config['servername'], $config['username'], $config['password'], 'baba-is-yall');
				if(!$conn){die('BAD CONNECTION');}

				#$conn = mysql_connect($dbhost, $dbuser, $dbpass);
				#if(!$conn){die('Could not connect: ' . mysql_error());}

				//get all levels 
				//$sql = $conn->query('SELECT CHROMOSOME_REP, ASCII_MAP from elite_levels;');
				$sql = $conn->query('SELECT CHROMOSOME_REP, ASCII_MAP, RATING, TOTAL_RATINGS from levels;');
				$elite_levels = array();
				$elite_rep = array();
				$elite_rating_ratio = array();

				//get the levels
				while($row = $sql->fetch_assoc()){
					array_push($elite_rep, $row['CHROMOSOME_REP']);
					array_push($elite_levels, $row['ASCII_MAP']);
					array_push($elite_rating_ratio, [$row['RATING'],$row['TOTAL_RATINGS']]);
				}

				//get the player levels
				$sql2 = $conn->query("SELECT CHROMOSOME_REP, ASCII_MAP, RATING, TOTAL_RATINGS from levels WHERE AUTHOR = '" . $username . "';");
				$user_levels = array();
				$user_rep = array();
				$user_rating_ratio = array();
				//get the levels
				while($row2 = $sql2->fetch_assoc()){
					array_push($user_rep, $row2['CHROMOSOME_REP']);
					array_push($user_levels, $row2['ASCII_MAP']);
					array_push($user_rating_ratio, [$row['RATING'],$row['TOTAL_RATINGS']]);
				}

				//ALL DONE! close connection
				mysqli_close($conn);

				//echo $objective;

	?>
	<script>
		//save all levels to variables (called elite)
		var eliteLevels = <?php echo json_encode($elite_levels); ?>;
		var eliteChromos = <?php echo json_encode($elite_rep); ?>;
		var eliteRatings = <?php echo json_encode($elite_rating_ratio); ?>;

		//save user levels to variables
		var userLevels = <?php echo json_encode($user_levels); ?>;
		var userChromos = <?php echo json_encode($user_rep); ?>;
		var userRatings = <?php echo json_encode($user_rating_ratio); ?>;
		
	</script>


	<body onload='init()'>
		<div class='container top-buffer1 thick-border' style='border-color: white'>
			<!-- editor row -->
			<div class='row top-buffer2'>
				<!-- tool and mode selection -->
				<div class='col-md-6 text-center'>
					<div style='float:left;margin-left:2%;z-index: 2;position: absolute;cursor:pointer'><img class='img-responsive' src='img/help.png' width='40' height='40' id='helpIcon' title='Toggle whether to show the helper gif popups' onclick='toggleHelpHover(this)'></div>
					<!-- label -->
						<div style='text-align: center;color:#fff;font-size:1.2vw;margin-top: -0.5vw;position: relative' id='icon_label'>
							&nbsp;
						</div>

					<!-- mode navigator -->
					<ul class='nav nav-pills center-pills top-buffer1'>
						<li class='active'><a data-toggle='pill' href='#edit_mode'><img class='nav_icon img-responsive' src='flaticon/paint.png' width='50' height='50' title='Paint sprites manually onto the map' onmouseover='iconLabel("Painter")' onmouseout="iconLabel('&nbsp;')"></a></li>
						<li><a data-toggle='pill' href='#mutate_mode'><img class='nav_icon img-responsive' src='flaticon/dna.png' width='50' height='50' title='Mutates and evolves the map with AI' onmouseover='iconLabel("Mutator and Evolver")' onmouseout="iconLabel('&nbsp;')"></a></li>
						<li><a data-toggle='pill' href='#objective_mode'><img class='nav_icon img-responsive' src='flaticon/list.png' width='50' height='50' title='Select rule objectives for the level' onmouseover='iconLabel("Objectives Table")' onmouseout="iconLabel('&nbsp;')"></a></li>
						<li><a data-toggle='pill' href='#level_mode'><img class='nav_icon img-responsive' src='flaticon/grid.png' width='50' height='50' title='Select an AI recommended generated/pre-made map' onmouseover='iconLabel("Map Selection")' onmouseout="iconLabel('&nbsp;')"></a></li>
					</ul>
					<!-- mode tools -->
					<div class='tab-content'>

						<!-- edit mode -->
						<div id='edit_mode' class='tab-pane active'>
							<!-- tool select -->
							<div class='row row-no-gutters' style='margin-top: 1.5vw'>
								<div class='col-xs-2 col-xs-offset-1 text-center'>
									<img src='flaticon/draw.png' width='30' height='30' class='selImg' onclick="changeTool('paint')" id='paintTool' title="Draws sprites onto the canvas&#10;Press or hold LMB and drag on canvas" onmouseenter="showHelpPopup(event,this.id)" onmouseleave="hideHelpPopup()">
								</div>
								<div class='col-xs-2 text-center'>
									<img src='flaticon/eraser.png' width='30' height='30' class='selImg' onclick="changeTool('erase')" id='eraseTool' title="Erases sprites on the canvas&#10;Press or hold LMB and drag on canvas" onmouseenter="showHelpPopup(event,this.id)" onmouseleave="hideHelpPopup()">
								</div>
								<div class='col-xs-2 text-center'>
									<img src='flaticon/box_outline.png' width='30' height='30' class='selImg' onclick="changeTool('select')" id='selectTool' title="Moves groups of sprites on the canvas&#10;Draw a box around the area with LMB then click and drag box to designated location" onmouseenter="showHelpPopup(event,this.id)" onmouseleave="hideHelpPopup()">
								</div>
								<div class='col-xs-2 text-center'>
									<img src='flaticon/undo.png' width='30' height='30' class='selImg' onclick='undo()' title='Undo the last change to the map' onmouseenter="showHelpPopup(event,'undoTool')" onmouseleave="hideHelpPopup()">
								</div>
								<div class='col-xs-2 text-center'>
									<img src='flaticon/redo.png' width='30' height='30' class='selImg' onclick='redo()' title='Redo the last change to the map' onmouseenter="showHelpPopup(event,'redoTool')" onmouseleave="hideHelpPopup()">
								</div>
							</div>
							<!-- sprite object select -->
							<div class='row row-no-gutters top-buffer2'>
								<div class='col-xs-8 col-xs-offset-2'>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2 selImg3' src='img/baba_obj.png' width='60' height='60' onclick="changeTile('baba_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/skull_obj.png' width='60' height='60' onclick="changeTile('skull_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/flag_obj.png' width='60' height='60' onclick="changeTile('flag_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/wall_obj.png' width='60' height='60' onclick="changeTile('wall_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/grass_obj.png' width='60' height='60' onclick="changeTile('grass_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/lava_obj.png' width='60' height='60' onclick="changeTile('lava_obj', this)">
										</div>
									</div>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/rock_obj.png' width='60' height='60' onclick="changeTile('rock_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/floor_obj.png' width='60' height='60' onclick="changeTile('floor_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/keke_obj.png' width='60' height='60' onclick="changeTile('keke_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/goop_obj.png' width='60' height='60' onclick="changeTile('goop_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/love_obj.png' width='60' height='60' onclick="changeTile('love_obj', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/empty.png' width='60' height='60' onclick="changeTile('empty', this)">
										</div>
									</div>
								</div>
							</div>
							<!-- word object select -->
							<div class='row row-no-gutters top-buffer1'>
								<div class='col-xs-8 col-xs-offset-2'>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/baba_word.png' width='60' height='60' onclick="changeTile('baba_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/skull_word.png' width='60' height='60' onclick="changeTile('skull_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/flag_word.png' width='60' height='60' onclick="changeTile('flag_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/wall_word.png' width='60' height='60' onclick="changeTile('wall_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/grass_word.png' width='60' height='60' onclick="changeTile('grass_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/lava_word.png' width='60' height='60' onclick="changeTile('lava_word', this)">
										</div>
									</div>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/rock_word.png' width='60' height='60' onclick="changeTile('rock_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/floor_word.png' width='60' height='60' onclick="changeTile('floor_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/keke_word.png' width='60' height='60' onclick="changeTile('keke_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/goop_word.png' width='60' height='60' onclick="changeTile('goop_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/love_word.png' width='60' height='60' onclick="changeTile('love_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/empty.png' width='60' height='60' onclick="changeTile('empty', this)">
										</div>
									</div>
								</div>
							</div>
							<!-- keyword select -->
							<div class='row row-no-gutters top-buffer1'>
								<div class='col-xs-8 col-xs-offset-2'>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center col-xs-offset-1'>
											<img class='img-responsive tilespr selImg2' src='img/you_word.png' width='60' height='60' onclick="changeTile('you_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/is_word.png' width='60' height='60' onclick="changeTile('is_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/win_word.png' width='60' height='60' onclick="changeTile('win_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/stop_word.png' width='60' height='60' onclick="changeTile('stop_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/move_word.png' width='60' height='60' onclick="changeTile('move_word', this)">
										</div>
									</div>
									<div class='row row-no-gutters'>
										<div class='col-xs-2 text-center col-xs-offset-1'>
											<img class='img-responsive tilespr selImg2' src='img/push_word.png' width='60' height='60' onclick="changeTile('push_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/sink_word.png' width='60' height='60' onclick="changeTile('sink_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/kill_word.png' width='60' height='60' onclick="changeTile('kill_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/hot_word.png' width='60' height='60' onclick="changeTile('hot_word', this)">
										</div>
										<div class='col-xs-2 text-center'>
											<img class='img-responsive tilespr selImg2' src='img/melt_word.png' width='60' height='60' onclick="changeTile('melt_word', this)">
										</div>
									</div>
								</div>
							</div>
						</div>



						<!-- mutate mode -->
						<div id='mutate_mode' class='tab-pane fill'>
							<div class='row row-no-gutters  top-buffer2'>
								<div class='col-xs-10 vcenter'>

									<div class='row row-no-gutters top-buffer05'>
										<div class='col-xs-6 text-center'>
											<img class='img_responsive selImg' src='flaticon/dice.png' width='125' height='125' onclick='randomAlterMap();' title='Randomly change tiles on the map'  onmouseenter="showHelpPopup(event,'diceTool')" onmouseleave="hideHelpPopup()">
										</div>
										<div class='col-xs-6 text-center'>
											<img class='img_responsive selImg' src='flaticon/dna.png' width='125' height='125' onclick='mutateMap();' title='Replace chunks of tiles on the map' onmouseenter="showHelpPopup(event,'dnaTool')" onmouseleave="hideHelpPopup()">
										</div>
									</div>
									<div class='row row-no-gutters  top-buffer2'>
										<div class='col-xs text-center'>
											<div style='position: relative;text-align: center;color:white'>
						
												<img class='img_responsive selImg' src='flaticon/evolve2.png' width='240' height='180' id="evolveBtn" onclick='evolve()' title="Evolve the current map with the AI's algorithm"  onmouseenter="showHelpPopup(event,'evolveTool')" onmouseleave="hideHelpPopup()">
												<div id='playPause'>►</div>
											</div>
										<!-- <input type="checkbox" id="play-pause"/><label for="play-pause" id="evolveBtn" class="win-button" onclick='evolve()' title="Evolve the current map with the AI's algorithm"  onmouseenter="showHelpPopup(event,'evolveTool')" onmouseleave="hideHelpPopup()"></label> -->
										</div>
									</div>
								
									<div class='row row-no-gutters  top-buffer1'>
										<div class='col-xs-5 text-center col-xs-offset-1'>
											Quality: <input value='0.75' type='number' id='minFit' title='How good to make the level (<0 = worst quality, 1 = best quality' onmouseenter="showHelpPopup(event,'fitness')" onmouseleave="hideHelpPopup()">
										</div>
										<div class='col-xs-5 text-center'>
											Max Iterations: <input value='-1' type='number' id='maxIter' title='Number of times to evolve (-1 = until fitness threshold is reached)' onmouseenter="showHelpPopup(event,'iterations')" onmouseleave="hideHelpPopup()">
										</div>
									</div>
								
								</div>
							</div>
						</div>




						<!-- objective mode -->
						<div id='objective_mode' class='tab-pane'>
							<!--<h3>LIST OF OBJECTIVES GO HERE</h3>-->
							<div class='row row-no-gutters top-buffer4'>
								<div class='col-xs-8 col-xs-offset-2 text-center'>
									<!-- complex objective table -->
									<div class='row row-no-gutters' id='obj_table'>
										<div class='col text-center'>
											<div class='row row-no-gutters'>
												<div class='col-xs-8 border-right thick-border-bottom obj_text' title='Type of rule present in the level'>OBJECTIVE</div>
												<div class='col-xs-2 text-center border-right thick-border-bottom obj_text' title='Rules present at the beginning of the level'>Init</div>
												<div class='col-xs-2 text-center thick-border-bottom obj_text' title='Rules present at the completion of the level'>End</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-x')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_AX' title='Ex. BABA-is-BABA'>X-IS-X</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_A1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_A2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-y')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_BX' title='Ex. KEKE-is-BABA'>X-IS-Y</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_B1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_B2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-push')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_CX' title='Ex. ROCK-is-PUSH'>X-IS-PUSH</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_C1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_C2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-move')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_DX' title='Ex. KEKE-is-MOVE'>X-IS-MOVE</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_D1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_D2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-stop')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_EX' title='Ex. WALL-is-STOP'>X-IS-STOP</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_E1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_E2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-kill')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_FX' title='Ex. BONE-is-KILL'>X-IS-KILL</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_F1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_F2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-sink')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_GX' title='Ex. GOOP-is-SINK'>X-IS-SINK</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_G1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_G2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-pair')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_HX' title='Ex. LAVA-is-HOT + BABA-is-MELT'>X-IS-[PAIR]</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_H1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_H2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'xy-is-you')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_IX' title='Ex. BABA-is-YOU + KEKE-is-YOU'>[X,Y]-IS-YOU</div>
												<div class='col-xs-2 text-center obj_lmcell obj_text obj_click' id='obj_I1' onclick='toggleActiveRule(this)'>&nbsp;</div>
												<div class='col-xs-2 text-center obj_rcell obj_text obj_click' id='obj_I2' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
										</div>
									</div>
									<!-- simple objective table -->
									<div class='row row-no-gutters' id='obj_table_simp' style='display:none'>
										<div class='col text-center'>
											<div class='row row-no-gutters'>
												<div class='col-xs-8 border-right thick-border-bottom obj_text' title='Type of rule present in the level'>OBJECTIVE</div>
												<div class='col-xs-4 text-center border-right thick-border-bottom obj_text' title='If rule is present at beginning or end of the level'>In Level</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-x')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_AX_0' title='Ex. BABA-is-BABA'>X-IS-X</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_A1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-y')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_BX_0' title='Ex. WALL-is-FLOOR'>X-IS-Y</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_B1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-push')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_CX_0' title='Ex. ROCK-is-PUSH'>X-IS-PUSH</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_C1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-move')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_DX_0' title='Ex. KEKE-is-MOVE'>X-IS-MOVE</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_D1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-stop')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_EX_0' title='Ex. WALL-is-STOP'>X-IS-STOP</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_E1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-kill')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_FX_0' title='Ex. BONE-is-KILL'>X-IS-KILL</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_F1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-sink')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_GX_0' title='Ex. GOOP-is-SINK'>X-IS-SINK</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_G1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'x-is-pair')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_HX_0' title='Ex. LAVA-is-HOT + BABA-is-MELT'>X-IS-[PAIR]</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_H1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
											<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'xy-is-you')" onmouseleave="hideHelpPopup()">
												<div class='col-xs-8 obj_lmcell obj_text' id='obj_IX_0' title='Ex. BABA-is-YOU + KEKE-is-YOU'>[X,Y]-IS-YOU</div>
												<div class='col-xs-4 text-center obj_lmcell obj_text obj_click' id='obj_I1_0' onclick='toggleActiveRule(this)'>&nbsp;</div>
											</div>
										</div>
									</div>
									<div class='row row-no-gutters top-buffer5'>
										<div class='col-xs-4 text-center' title='Generate a new set of rules' onmouseenter="showHelpPopup(event,'newlist')" onmouseleave="hideHelpPopup()">
											<button onclick='newObjList()'>New List</button>
										</div>
										<div class='col-xs-4 text-center' title='Remove all rules' onmouseenter="showHelpPopup(event,'clearlist')" onmouseleave="hideHelpPopup()">
											<button onclick='clearObjList()'>Clear List</button>
										</div>
										<div class='col-xs-4 text-center' title='Toggle simplified/advanced version of the list' onmouseenter="showHelpPopup(event,'simpadvlist')" onmouseleave="hideHelpPopup()">
											<button onclick='toggleTable(this)'>Simple List</button>
										</div>
									</div>
								</div>
							</div>
						</div>




						<!-- level mode -->
						<div id='level_mode' class='tab-pane'>
							<div class='row row-no-gutters top-buffer05'>
								<div class='col-xs-10 text-center col-xs-offset-1'>
									<!-- select level type -->
									<ul class='nav nav-pills center-pills top-buffer05' id='maps'>
										<li class='active'><a data-toggle='pill' href='#blank_levels' title='Empty map' onmouseenter="showHelpPopup(event,'blankMaps')" onmouseleave="hideHelpPopup()">Blank</a></li>
										<li><a data-toggle='pill' href='#basic_levels' title='Map with X-is-YOU and Y-is-WIN rules and objects placed' onmouseenter="showHelpPopup(event,'basicMaps')" onmouseleave="hideHelpPopup()">Basic</a></li>
										<li><a data-toggle='pill' href='#random_levels' title='Map with randomly placed tiles' onmouseenter="showHelpPopup(event,'randomMaps')" onmouseleave="hideHelpPopup()">Random</a></li>
										<li><a data-toggle='pill' href='#elite_levels' title='Top rated maps' onmouseenter="showHelpPopup(event,'eliteMaps')" onmouseleave="hideHelpPopup()">Elite</a></li>
										<li><a data-toggle='pill' href='#my_levels' title='Maps made by the currently logged in user' onmouseenter="showHelpPopup(event,'userMaps')" onmouseleave="hideHelpPopup()">My Levels</a></li>
									</ul>

									<!-- display levels -->
									<div class='tab-content top-buffer05'>
										<!-- blank level (singular) -->
										<div id='blank_levels' class='tab-pane active text-center'>
											<img src='demo_maps/blank_map.png' width='300' height='300' class='selImg2' onclick='importSelectMap(0);'>
										</div>
										<!-- basic levels -->
										<div id='basic_levels' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map1.png' width='150' height='150' class='selImg2' onclick="importSelectMap(0);"> 
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map7.png' width='150' height='150' class='selImg2' onclick="importSelectMap(1);">
												</div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map3.png' width='150' height='150' class='selImg2' onclick="importSelectMap(2);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map11.png' width='150' height='150' class='selImg2' onclick="importSelectMap(3);">
												</div>
											</div>
										</div>
										<!-- random levels -->
										<div id='random_levels' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map9.png' width='150' height='150' class='selImg2' onclick="importSelectMap(0);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map11.png' width='150' height='150' class='selImg2' onclick="importSelectMap(1);">
												</div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map4.png' width='150' height='150' class='selImg2' onclick="importSelectMap(2);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map13.png' width='150' height='150' class='selImg2' onclick="importSelectMap(3);">
												</div>
											</div>
										</div>
										<!-- elite levels -->
										<div id='elite_levels' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map6.png' width='150' height='150' class='selImg2' onclick="importSelectMap(0);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map8.png' width='150' height='150' class='selImg2' onclick="importSelectMap(1);">
												</div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map9.png' width='150' height='150' class='selImg2' onclick="importSelectMap(2);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map7.png' width='150' height='150' class='selImg2' onclick="importSelectMap(3);">
												</div>
											</div>
										</div>
										<!-- my levels -->
										<div id='my_levels' class='tab-pane'>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map10.png' width='150' height='150' class='selImg2' onclick="importSelectMap(0);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map5.png' width='150' height='150' class='selImg2' onclick="importSelectMap(1);">
												</div>
											</div>
											<div class='row row-no-gutters'>
												<div class='col-xs-6 text-right'>
													<img src='demo_maps/map3.png' width='150' height='150' class='selImg2' onclick="importSelectMap(2);">
												</div>
												<div class='col-xs-6 text-left'>
													<img src='demo_maps/map12.png' width='150' height='150' class='selImg2' onclick="importSelectMap(3);">
												</div>
											</div>
										</div>
									</div>
									<!-- shuffle and confirmation -->
									<div class='row row-no-gutters top-buffer2'>
										<div class='col-xs-3 text-center'>
											<img src='flaticon/shuffle.png' width='50' height='50' class='selImg' onclick='shuffleLevels()' title='Shuffle current selection' onmouseenter="showHelpPopup(event,'shuffle')" onmouseleave="hideHelpPopup()">
										</div>
										<div class='col-xs-3 text-center'>
											<img src='flaticon/scale-lock.png' width='50' height='50' class='selImg' onclick='toggleLock()' id='lockBtn' title='Lock the dimensions or the rules so only levels that apply are shown' onmouseenter="showHelpPopup(event,'lock')" onmouseleave="hideHelpPopup()">
										</div>
										<div class='col-xs-3 text-center'>
											<img src='flaticon/check.png' width='50' height='50' class='selImg' onclick='confirmSelection()' title='Replace map in editor with currently selected map' onmouseenter="showHelpPopup(event,'confirm')" onmouseleave="hideHelpPopup()">
										</div>
										<div class='col-xs-3 text-center'>
											<img src='flaticon/cancel.png' width='50' height='50' class='selImg' onclick='cancelSelection()' title='Cancel current selection in editor' onmouseenter="showHelpPopup(event,'reject')" onmouseleave="hideHelpPopup()">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>







				<!-- level editor -->
				<div class='col-md-6'   style="border-left:3px dashed white">
					<div class='row row-no-gutters'>
						<div class='col-xs-10 col-xs-offset-1 text-center'>

							<!-- level stats -->
							<div class='row row-no-gutters' onmouseenter="showHelpPopup(event,'map_dim')" onmouseleave="hideHelpPopup()">
								<div class='col-xs-6 text-center'>
									WIDTH: <input value='10' type='number' size='3' min='6' max='20' maxlength='100' id="mapW" onchange='changeMapSize()' title='Change number of x-axis tiles'>
								</div>
								<div class='col-xs-6 text-center'>
									HEIGHT: <input value='10' type='number' size='3'  min='6' max='20' maxlength="100" id="mapH" onchange='changeMapSize()' title='Change number of y-axis tiles'>
								</div> 
							</div>


							<!-- level editor canvas -->
							<div class='row row-no-gutters top-buffer1'>
								<div class='col text-center'>
									<canvas width='250' height='250' id='editCanvas'>
										Level editor goes here. Change your browser if you see this message.
									</canvas>
								</div>
							</div>


							<!-- level tester (HUMAN/AI) -->
							<div class='row row-no-gutters top-buffer1' onmouseenter="showHelpPopup(event,'tester')" onmouseleave="hideHelpPopup()">
								<div class='col-xs-4 text-center'>
									<img src='img/baba_obj.png' class='selImg' width='75' height='60' onclick="levelTest('human');" onmouseover="showControlType('HUMAN')" onmouseout="noControlType()" title='Manually test the level as a human player'>
								</div>
								<div class='col-xs-4 text-center' id='controlTxt'>
									&nbsp;
								</div>
								<div class='col-xs-4 text-center'>
									<img src='img/keke_obj.png' class='selImg' width='75' height='60' onclick="levelTest('keke');" onmouseover="showControlType('AI')" onmouseout="noControlType()" title='AI tests the level by trying to solve it'>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</div>








			<!-- footer row -->
			<div class='row row-no-gutters'>
				<div class='col top-buffer2 text-center'>
					<button onclick="location.href='map_home.php';">Back to Level Selection</button>
				</div>
			</div>
			<div class='row row-no-gutters top-buffer1' style='margin-bottom: 1%'>
				<div class='col text-center'>UI Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik" target="_blank">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon" target="_blank">www.flaticon.com</a></div>
			</div>

		</div>

		<!-- add empty space at bottom -->
		<div class='row' style='margin-top: 0.5%;'>
			<br>
		</div>

		<!-- add editor javascript last (get all elements loaded first)-->
		<script src="js/edit-pcg.js"></script>
		<script src="js/help.js"></script>
		
	</body>
</html>
