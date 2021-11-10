<html>
	<head>
		<title>Tutorial - BiY</title>
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
			#gameWindow{
				min-width:200px;
				min-height:200px;
				width:100%;
			}
			.editCanvas{
				background-color: #343434;
				border:2px solid #007C13;
				width: 100%;
				height: auto;
			}

			canvas{
				background-color:#000;
			}

			body{
				background-color:#000;
			}
			body.light-mode{
				background-color:#fff;
			}

			#mainScreen{
				border: 32px solid transparent;
				border-image:url('demo_maps/blank_map.png') 79 repeat;
				height:100%;
				min-height: 600px;
				color: #fff;	
			}

			body.light-mode #mainScreen{
				border: 32px solid transparent;
				border-image:url('demo_maps/blank_map.png') 79 repeat;
				height:100%;
				min-height: 600px;
				color: #000;	
			}

			.equal {
			  display: flex;
			  display: -webkit-flex;
			  flex-wrap: wrap;
			}

			/* tutorial stuff */

			.tutQuest{
				width:100%;
				background-color:#000;
				color:#fff
			}
			body.light-mode .tutQuest{
				width:100%;
				background-color:#fff;
				color:#000
			}


			.tutCanv{
				margin-top: 3%;
				margin-bottom: 3%;
			}

			#tutCarousel .carousel-indicators{
				height:20%;
			}

			.carousel-indicators li{
				border: 1px solid #DC386A;
			} 
			.carousel-indicators .active{
				background-color:#DC386A;
			}


			/* demo levels */

			.helperSpr{
				width:40%;
				margin:auto;
			}
			.helperTxtCol{
				height: 21vw;
				vertical-align: bottom;
				position: relative;
			}
			.hTxt{
				width:80%;
				margin: 1vw auto 1vw auto;
				font-size:1vw;
				text-align: center;
				justify-content: center;
				vertical-align: middle;
				border:2px solid #fff;
				padding:0.5vw;
				border-radius: 10px;
				color:#fff;
				display: flex;
  				align-items: center;
  				position: absolute; 
  				bottom: 0;
  				left:1.5vw;
			}
			body.light-mode .hTxt{
				width:80%;
				margin: 1vw auto 1vw auto;
				font-size:1.25vw;
				text-align: center;
				justify-content: center;
				vertical-align: middle;
				border:2px solid #000;
				padding:0.5vw;
				border-radius: 10px;
				color:#000;
				display: flex;
  				align-items: center;
  				position: absolute; 
  				bottom: 0;
  				left:1.5vw;
			}

			.tutGif{
				width:45vw;
				height:30vw;
			}

			.tutLabel{
				font-size:2vw;
				color:#DC386A;
			}

			.tutBut{
				width: 4vw;
				font-size:2vw;
				color: #000;
			}

			a, .babaColor{
				color:#DC386A;
			}

			body.light-mode button{
				color:#000;
			}
			body button{
				color:#000;
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
			body.light-mode .babaStyle{
				margin:auto;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				width:85%;
				background-color: #fff;
				padding:1%;
				color:#DC386A;
				text-align: center;
				font-size:2.8vw;
				text-decoration-line: underline;
				text-decoration-style: dashed;
				text-decoration-color: #000;
				font-family: 'BabaFont';
			}
			
		</style>


		<!-- external scripts -->
		<script src='js/baba.js'></script>
		<script src='js/tutorial.js'></script>
		
	</head>
	<body onload='super_init();'>
		<div id='mainScreen'>
			<div id='loginBox' class='container top-buffer05'>
				<div class='row row-no-gutters'>
					<div class='col-xs-4 col-xs-offset-4 text-center'>
						<h3 id='tutMode' style='font-size:2vw;' >TUTORIAL</h3>
					</div>
				</div>
				
				<div class='row row-no-gutters top-buffer1'>
					<div class='col-xs-4 col-xs-offset-4 text-center'>
						&nbsp;
					</div>
				</div>

				<!-- carousel canvas tutorials -->
				<div class='row row-no-gutters top-buffer05'>
					<div class='col-xs-10 col-xs-offset-1' style='border:3px dashed #DC386A'>

						<div id="tutCarousel" class="carousel slide" data-interval="false">
					  		<ol class="carousel-indicators" style='top:-50px;'>
					    		<li data-target="#tutCarousel" data-slide-to="0" class="active"></li>
					    		<li data-target="#tutCarousel" data-slide-to="1"></li>
					    		<li data-target="#tutCarousel" data-slide-to="2"></li>
					    		<li data-target="#tutCarousel" data-slide-to="3"></li>
					    		<li data-target="#tutCarousel" data-slide-to="4"></li>
					    		<li data-target="#tutCarousel" data-slide-to="5"></li>
					    		<li data-target="#tutCarousel" data-slide-to="6"></li>
					    		<li data-target="#tutCarousel" data-slide-to="7"></li>
					    		<li data-target="#tutCarousel" data-slide-to="8"></li>
					    		<li data-target="#tutCarousel" data-slide-to="9"></li>
					  		</ol>
						  	<div class="carousel-inner">
						    	<div class="item active">
						    		<!-- demo game -->
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Have you ever played <br><a href='https://hempuli.itch.io/baba-is-you' target='blank' style='color:#DC386A'> "Baba is You" </a><br> before?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters' style='margin-top: 8%;margin-bottom: 4%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(0)'>No</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">Yes</button></div>
						      			</div>
						      			<div class='row row-no-gutters' style='margin-bottom: 5%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<div class='row row-no-gutters'>
						      				<!-- baba txt -->
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxtDemo0' style='visibility: hidden;'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/baba_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      				</div>
						      				<!-- game -->
						      				<div class='col-xs-6 text-center'>
						      				<canvas width='100' height='100' id='gameWindow'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>
						      				</div>
						      				<!-- keke txt -->
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxtDemo1' style='visibility: hidden;'>This is also sample helper text</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/keke_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer05'>
						      				<div class='col-xs-6 col-xs-offset-3 text-center'>
						      						<button id='game1' class='levelTab' onclick='setLevelTut(0)'>1</button>
						      						<button id='game2' class='levelTab' onclick='setLevelTut(1)'>2</button>
						      						<button id='game3' class='levelTab' onclick='setLevelTut(2)'>3</button>
						      						<button id='game4' class='levelTab' onclick='setLevelTut(3)'>4</button>
						      						<button id='game5' class='levelTab' onclick='setLevelTut(4)'>5</button>
						      						<button id='game6' class='levelTab' onclick='setLevelTut(5)'>6</button>
						      						<button id='game7' class='levelTab' onclick='setLevelTut(6)'>7</button>
						      						<button id='game8' class='levelTab' onclick='setLevelTut(7)'>8</button>
						      						<button id='game9' class='levelTab' onclick='setLevelTut(8)'>9</button>
						      						<button id='game10' class='levelTab' onclick='setLevelTut(9)'>10</button>
						      						<button id='game11' class='levelTab' onclick='setLevelTut(10)'>11</button>
						      						<button id='game12' class='levelTab' onclick='setLevelTut(11)'>12</button>
						      						<button id='game13' class='levelTab' onclick='setLevelTut(12)'>13</button>
						      						<button id='game14' class='levelTab' onclick='setLevelTut(13)'>14</button>
						      				</div>
						      			</div>
						      		</div>
						    	</div>


						    	<!-- pages -->

						    	<div class="item">
						    		
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn how to <span class='babaColor'>play community levels</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(1);showTutGif(1,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif1' src='tut_gifs/pages/play_level.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt1' style='font-size:1vw'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/baba_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel1'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev1' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(1)'><</button>

				      							<button id='next1' class='tutBut' onclick='nextTutSlides(1)'>></button>
				      						</div>
				      					</div>
									</div>
						    	</div>


						    	<!-- level editor -->

						    	<div class="item">
						    		
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn about the <span class='babaColor'>level editor</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(2);showTutGif(2,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif2' src='tut_gifs/level_editor/le_p1-make-new-level.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt2'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/baba_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel2'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev2' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(2)'><</button>

				      							<button id='next2' class='tutBut' onclick='nextTutSlides(2)'>></button>
				      						</div>
				      					</div>
									</div>
						    	</div>




						    	<!-- mutations -->



						    	<div class="item">
						    		
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn how to <span class='babaColor'>mutate</span> and <span class='babaColor'>evolve</span> a level?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters' style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(3);showTutGif(3,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters' style='margin-bottom: 8%'></div>
						      		</div>

						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif3' src='placeholder_gifs/2.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt3'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/keke_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel3'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev3' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(3)'><</button>

				      							<button id='next3' class='tutBut' onclick='nextTutSlides(3)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>




						    	<!-- objectives -->


						    	<div class="item">
						    	
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn about <span class='babaColor'>rule objectives</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(4);showTutGif(4,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif4' src='placeholder_gifs/3.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt4' style='font-size:1vw;'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/keke_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel4'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev4' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(4)'><</button>

				      							<button id='next4' class='tutBut' onclick='nextTutSlides(4)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>






						    	<!-- elite maps -->
						    	<div class="item">
						    		
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn about <span class='babaColor'>map selection</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(5);showTutGif(5,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif5' src='placeholder_gifs/5.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt5' style="font-size:1.1vw;">This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/keke_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel5'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev5' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(5)'><</button>

				      							<button id='next5' class='tutBut' onclick='nextTutSlides(5)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>









						    	<div class="item">
						    		<!-- ratings -->
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn how to <span class='babaColor'>rate</span> levels?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(6);showTutGif(6,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif6' src='placeholder_gifs/6.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt6' style="font-size:1vw;">This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/keke_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel6'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev6' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(6)'><</button>

				      							<button id='next6' class='tutBut' onclick='nextTutSlides(6)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>





						    	<div class="item">
						    		<!-- search tool -->
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn about the <span class='babaColor'>search tool</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(7);showTutGif(7,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif7' src='placeholder_gifs/7.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt7' style='font-size:1vw'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/baba_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel7'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev7' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(7)'><</button>

				      							<button id='next7' class='tutBut' onclick='nextTutSlides(7)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>





						    	<div class="item">
						    		<!-- user profiles -->
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<h2 style='font-size:3.5vw;text-align: center'>Would you like to learn about <span class='babaColor'>user profiles</span>?</h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-top: 8%'>
						      				<div class='col-xs-2 col-xs-offset-3 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick='showCanvas(8);showTutGif(8,0);'>Yes</button></div>
						      				<div class='col-xs-2 col-xs-offset-2 text-center'><button style='color:#000;width:100%;font-size:1.75vw' onclick="$('#tutCarousel').carousel('next');">No</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-9 text-center' style='border-right:1px dashed #DC386A;'>
						      					<img id='tutGif8' src='placeholder_gifs/8.gif' class='tutGif'>
						      				</div>
						      				<div class='col-xs-3 text-center'>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center helperTxtCol'>
						      							<div class='hTxt' id='tutTxt8' style='font-size:1vw'>This is sample helper text<br>one<br>two<br>three<br>four<br>five<br>six<br>seven</div>
						      						</div>
						      					</div>
						      					<div class='row row-no-gutters'>
						      						<div class='col text-center'>
						      							<img src='img/baba_obj_tr.png' class='helperSpr'>
						      						</div>
						      					</div>
						      					
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters top-buffer1'>
						      				<div class='col-xs-9 text-center'>
				      							<span class='tutLabel' id='tutLabel8'>Make a New Level</span>
				      						</div>
				      						<div class='col-xs-3 text-center'>
				      							<button id='prev8' class='tutBut' style='margin-right: 10%' onclick='prevTutSlides(8)'><</button>

				      							<button id='next8' class='tutBut' onclick='nextTutSlides(8)'>></button>
				      						</div>
				      					</div>
						      		</div>
						    	</div>




						    	<div class="item">
						    		<!-- end of tutorial! -->
						      		<div class='tutQuest'>
						      			<div class='row row-no-gutters'>
						      				<div class='col-xs-8 col-xs-offset-2 text-center' style='margin-top: 8%'>
						      					<img src='img/baba_obj_tr.png' style='width:5vw'><img src='img/keke_obj_tr.png' style='width:5vw'>
						      					<h2 style='font-size:3.5vw;text-align: center'><span class='babaColor'>END OF TUTORIAL!</span></h2>
						      				</div>
						      			</div>
						      			<div class='row row-no-gutters col-xs-12 text-center'  style='margin-top: 8%; font-size:1.75vw'>
						      				<div class='col-xs-offset-2 col-xs-3'><button style='width:100%; height:5vw' onclick='gotoURL("map_home.php")'>Go to Level Home</button></div>
						      				<div class='col-xs-offset-2 col-xs-3'><button style='width:100%; height:5vw' onclick="$('#tutCarousel').carousel(0);">Back to Start</button></div>
						      			</div>
						      			<div class='row row-no-gutters'  style='margin-bottom: 8%'></div>
						      		</div>
						      		<div class='tutCanv' style='display:none;'>
						      			<!--<canvas id='tutCanvas'>Must have HTML5 canvas ability to see tutorial (and use the rest of the site tbh)</canvas>-->
						      		</div>
						    	</div>
						  	</div>
						</div>




					</div>
				</div>

				<!-- other screen buttons -->
				<div class='row row-no-gutters top-buffer2' style='color:#000; width:100%' >
					<div class='col-xs-3 col-xs-offset-1 text-center'><button onclick='gotoURL("index.php")' style='width:60%'>Home Screen</button></div>
					<div class='col-xs-4 text-center'><button style='text-align:center; background-color:#DC386A; width:60%;' class='btn-toggle'>Toggle Dark Mode</button></div>
					<div class='col-xs-3 text-center'><button onclick='gotoURL("map_home.php")' style='width:60%'>Level Map</button></div>
					
					<script>
						const btn = document.querySelector('.btn-toggle');
						btn.addEventListener('click', function() {document.body.classList.toggle('light-mode');})
					</script>
				</div>

				<div class='row row-no-gutters top-buffer3'>
					<div class='col-xs-12'>
						&nbsp;
					</div>
				</div>


			</div>
		</div>
		<script src='js/game.js'></script>
		<script src='js/tut_update.js'></script>

	</body>
</html>