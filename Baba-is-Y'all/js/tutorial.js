// TUTORIAL.JS - SCRIPT FOR THE TUTORIAL OF BABA IS Y'ALL
// Version 2.0
// Code by Milk  

//var screen_canvas = document.getElementById('tutCanvas');
//var stx = screen_canvas.getContext('2d');

var tutScreens = {}		//store the canvas (tutCanv) and text (tutQuest) of each tutorial screen
var activeTut = null;

/// PRESAVED INSTRUCTIONS

//demo level instructions
var demoLevel_Instructions = {
	0 : "<p>Welcome to the Baba is You Demo levels!<br>Use the <span class='babaColor'>ARROW KEYS</span> to move the character that looks like me to the <span style='color:#E5C11E;'>FLAG</span><br>Press Space to go to the next level.</p>",
	1 : "<p>You can <span class='babaColor'>push word blocks together</span> to form phrases such as 'BABA IS YOU' or 'FLAG IS WIN' and <span class='babaColor'>activate their rules</span>.</p>",
	2 : "<p>The <span style='color:#E5C11E;'>FLAG</span> object doesn't always have to be the WIN, but there needs to be a player to win the level.<br>Press <span class='babaColor'>R</span> to restart the level or <span class='babaColor'>Z</span> to undo the last step.</p>",
	3 : "<p>Likewise, YOU doesn't always have to be BABA! YOU can be anything!</p>", 
	4 : "<p>Objects can also be changed into different things! <br>This is the <br><span class='babaColor'>X-IS-Y</span><br>rule</p>", 
	5 : "<p>Unless a rule says otherwise... <br>This is the <br><span class='babaColor'>X-IS-X</span><br>rule</p>", 
	6 : "<p>Some rules can allow objects to be pushed.<br>This is the <br><span class='babaColor'>X-IS-PUSH</span><br>rule</p>", 
	7 : "<p>Some rules can make objects move on their own and push word blocks too! <br>This is the <br><span class='babaColor'>X-IS-MOVE</span><br>rule<br>Press <span class='babaColor'>SPACE</span> to wait a step.</p>", 
	8 : "<p>Some rules can make objects block the player or even word blocks! <br>This is the <br><span class='babaColor'>X-IS-STOP</span><br>rule</p>", 
	9 : "<p>Some rules can make objects kill the player if touched. <br>This is the <br><span class='babaColor'>X-IS-KILL</span><br>rule<br>(Press R = restart)</p>", 
	10 : "<p>Certain rules will cause objects to sink! <br>This is the <br><span class='babaColor'>X-IS-SINK</span><br>rule<br>(Press R = restart)</p>", 
	11 : "<p>However, the player isn't the only one that can sink...<br>(Press R = restart)</p>",
	12 : "<p>Objects that have paired properties such as HOT and MELT can destroy each other.<br>This is the <br><span class='babaColor'>X-IS-[PAIR]</span><br>rule<br>(Press R = restart)</p>",
	13 : "<p>You can change the player character to another object!<br>This is the <br><span class='babaColor'>[X,Y]-is-YOU</span><br>rule<br>(Press R = restart)</p>"
}

//tutorial gif instructions 
var curGifIndex = 0;
var gifInstructions = {

//pages
1 : {
	0 : {'label': 'Play Levels', 	'gif': 'tut_gifs/pages/play_level.gif', 	'txt': "<p>You can <span class='babaColor'>play levels</span> by hovering over the level and clicking the <span class='babaColor'>PLAY</span> button. This will open a new window where you can play the Baba game. If you get stuck, click the <span class='babaColor'>Tester button</span> to switch to the AI solver.</p>"},
	1 : {'label': 'Edit Levels', 	'gif': 'tut_gifs/pages/edit_levels.gif', 	'txt': "<p>You can also <span class='babaColor'>edit any level</span> by clicking the EDIT button underneath. This will take you to the level editor page, where you can modify the level however you want!</p>"},
	2 : {'label': 'New Page', 		'gif': 'tut_gifs/pages/new_page.gif', 		'txt': "<p>The <span class='babaColor'>New Page</span> contains freshly made levels. The 72 most recent levels are shown on this page, so be sure to check them out!</p>"},
	3 : {'label': 'Top Page', 		'gif': 'tut_gifs/pages/top_page.gif', 		'txt': "<p>The <span class='babaColor'>Top Page</span> has the highest rated levels. These are calculated from user ratings on the <span class='babaColor'>Rate page</span> and are the highest quality levels available.</p>"},
	4 : {'label': 'Unmade Page', 	'gif': 'tut_gifs/pages/obj_page.gif', 		'txt': "<p>The <span class='babaColor'>Unmade Page</span> contains levels with specific rules that haven't been made yet. Clicking the left will show levels with more rule combinations, clicking to the right will show levels with fewer rules.</p>"},
	5 : {'label': 'Unmade Levels', 	'gif': 'tut_gifs/pages/obj_edit.gif', 		'txt': "<p>From the Unmade page, you can click on the <span class='babaColor'>EDIT</span> button in the icon. This will take you to the level editor where the <span class='babaColor'>objective rules</span> are already set to act as a guide for making the level. Creating these unmade levels will make the database more diverse. There's <span class='babaColor'>over 200,000 possible level combinations to be made!</span></p>"}
},

//level edit
2 : {
	0 : {'label': "Make New Levels", 	'gif': 'tut_gifs/level_editor/le_p1-make-new-level.gif','txt': "<p>You can make a new level from scratch by pressing the <span class='babaColor'>'Make a New Level'</span> button at the bottom of the page on the home map screen</p>"},
	1 : {'label': "Paint Tool", 		'gif': 'tut_gifs/level_editor/le_p2-paint.gif',			'txt': "<p>You can <span class='babaColor'>place sprites</span> on the canvas on the right-hand side by clicking the <span class='babaColor'>LMB</span>. Holding the LMB and dragging will let you paint the canvas.</p>"},
	2 : {'label': "Eraser Tool", 		'gif': 'tut_gifs/level_editor/le_p3-eraser.gif',		'txt': "<p>The eraser tool works in the same way as the paint tool, but <span class='babaColor'>removes</span> sprites from the canvas</p>"},
	3 : {'label': "Undo/Redo Tool", 	'gif': 'tut_gifs/level_editor/le_p4-undo_redo.gif',		'txt': "<p>Clicking the <span class='babaColor'>pink 'Undo' button</span> will <span class='babaColor'>undo the last change</span> made on the canvas. Clicking the <span class='babaColor'>green 'Redo' button</span> will <span class='babaColor'>redo the most recent undo.</span></p>"},
	4 : {'label': "Sprite Types", 		'gif': 'tut_gifs/level_editor/le_p5-sprite_types.gif',	'txt': "<p>Different sprite types can be found under the tools buttons. The first group is <span class='babaColor'>object</span> sprites, the second group is <span class='babaColor'>word sprites</span>, and the third group is <span class='babaColor'>keywords</span>.</p>"},
	5 : {'label': "Select Tool", 		'gif': 'tut_gifs/level_editor/le_p6-select_tool.gif',	'txt': "<p>The select tool can lasso segments of the canvas. Use the <span class='babaColor'>LMB</span> to <span class='babaColor'>draw a box</span> around the area you want to select. Then <span class='babaColor'>click and drag the box</span> where you want to move the segment to and <span class='babaColor'>release</span> the LMB</p>"},
	6 : {'label': "Change Map Dimensions", 'gif': 'tut_gifs/level_editor/le_p7-dimensions.gif',	'txt': "<p>You can change the size of the map by adjusting the <span class='babaColor'>width</span> and <span class='babaColor'>height</span> values at the top of the canvas. <br>Careful, making the map smaller will erase any tiles placed outside the bounds</p>"},
	7 : {'label': "Testing Your Level", 'gif': 'tut_gifs/level_editor/le_p8-testing.gif',		'txt': "<p>You can <span class='babaColor'>play your level</span> by clicking the <span class='babaColor'>Baba icon</span> at the bottom or have an <span class='babaColor'>AI try to solve your level</span> by clicking the <span class='babaColor'>Keke</span> icon. Clicking the <span class='babaColor'>Tester button</span> also lets you switch between human and AI testing.</p>"}
},

//mutations
3 : {
	0 : {'label': "Mutation Tab", 		'gif':'tut_gifs/mutations/m_p1-tab.gif', 'txt':"<p>The <span class='babaColor'>mutation</span> window can be found by clicking the <span class='babaColor'>DNA icon</span> at the top of the left window (second icon from the left).</p>"},
	1 : {'label': "Random Mutation", 	'gif':'tut_gifs/mutations/m_p2-dice.gif', 'txt':"<p>The <span class='babaColor'>Dice icon</span> will <span class='babaColor'>randomly replace one or more tiles</span> on the map with similar tiles</p>"},
	2 : {'label': "Chunk Mutation", 	'gif':'tut_gifs/mutations/m_p3-dna.gif', 'txt':"<p>The <span class='babaColor'>DNA icon</span> will <span class='babaColor'>randomly replace a 3x3 section of the map</span> with another 3x3 section from an already made map.</p>"},
	3 : {'label': "Evolution", 			'gif':'tut_gifs/mutations/m_p4-evolve.gif', 'txt':"<p>The <span class='babaColor'>Progress icon</span> will <span class='babaColor'>evolve the map</span>. Clicking the icon will start/stop the evolution. The <span class='babaColor'>fitness value</span> shown in the map tries to <span class='babaColor'>improve the quality</span> of the level.</p>"},
	4 : {'label': "Alter Fitness", 		'gif':'tut_gifs/mutations/m_p5-fitness.gif',	'txt':"<p>The <span class='babaColor'>Minimum Fitness</span> at the bottom can set the stop value for the evolver. The closer the value is to 0 means the stricter the evolution."},
	5 : {'label': "Alter Iterations", 	'gif':'tut_gifs/mutations/m_p6-iterations.gif',	'txt':"<p>The <span class='babaColor'>Max Iterations</span> value will make the evolver <span class='babaColor'>run</span> a certain number of times. If set to <span class='babaColor'>-1</span>, the evolver will run until it reaches the minimum fitness."}

},

//rule objectives
4 : {
	0 : {'label': "Objective Tab", 'gif':'tut_gifs/objectives/tab.gif', 'txt':"<p>The <span class='babaColor'>objective</span> window can be found by clicking the <span class='babaColor'>clipboard icon</span> at the top of the left window (second icon from the right).</p>"},
	1 : {'label': "Selecting Objectives", 'gif':'tut_gifs/objectives/select.gif', 'txt':"<p><span class='babaColor'>Select an objective</span> by <span class='babaColor'>clicking in the box</span> under the 'Init' or 'End' columns.</p>"},
	2 : {'label': "'Init' Objectives", 'gif':'tut_gifs/objectives/init.gif', 'txt':"<p><span class='babaColor'>Init</span> objectives are rules that are present at the start of the level. These become highlighted when the player <span class='babaColor'>paints on the map or generates it through mutation.</span></p>"},
	3 : {'label': "'End' Objectives", 'gif':'tut_gifs/objectives/end.gif', 'txt':"<p><span class='babaColor'>End</span> objectives are rules that are present at the end of the level once it is solved. These become highlighted when the player <span class='babaColor'>successfully solves the level in the tester window.</span></p>"},
	4 : {'label': "Simple/Advanced List", 'gif':'tut_gifs/objectives/simple_advanced.gif', 'txt':"<p>Simplify the table by clicking the <span class='babaColor'>Simple List</span> button. The list will highlight the objective if it is present in either the <span class='babaColor'>init or end of the level.</span> The <span class='babaColor'>Advanced List</span> will split the objectives back to separate columns.</p>"},
	5 : {'label': "Mutated Objectives", 'gif':'tut_gifs/objectives/mutation.gif', 'txt':"<p>Pressing the <span class='babaColor'>mutate</span> button with objectives selected will cause the mutator to <span class='babaColor'>optimize</span> towards building levels <span class='babaColor'>with the chosen objectives</span></p>"},
	6 : {'label': "Clear List", 'gif':'tut_gifs/objectives/clear.gif', 'txt':"<p><span class='babaColor'>Clear List</span> will remove all of the X's from the objectives table.</p>"},
	7 : {'label': "New List", 'gif':'tut_gifs/objectives/new.gif', 'txt':"<p><span class='babaColor'>New List</span> will <span class='babaColor'>generate a random set of objectives</span>. You can use this to create a challenge for designing your levels!</p>"}
},

//elite maps
5 : {
	0 : {'label': 'Maps Tab', 			'gif': 'tut_gifs/maps/tab.gif', 	'txt': "<p>The <span class='babaColor'>maps</span> window can be found by clicking the <span class='babaColor'> green grid icon</span> at the top of the left window (farthest right).</p>"},
	1 : {'label': 'Confirm/Reject Maps','gif': 'tut_gifs/maps/confirm_reject.gif', 	'txt': "<p><span class='babaColor'>Click on the map</span> you\'d like to use and it will appear in the level. You can <span class='babaColor'>confirm to use the map</span> by clicking the <span class='babaColor'>green checkmark</span> at the bottom. You can <span class='babaColor'>reject the map</span> by clicking the <span class='babaColor'>red X</span></p>"},
	2 : {'label': 'Blank Maps',  		'gif': 'tut_gifs/maps/blank.gif', 	'txt': "<p><span class='babaColor'>Blank</span> maps contain an empty map with the same dimensions as the current map in the editor</p>"},
	3 : {'label': 'Basic Maps',  		'gif': 'tut_gifs/maps/basic.gif', 	'txt': "<p><span class='babaColor'>Basic</span> maps contain a simple level with a <span class='babaColor'>X-IS-YOU</span> rule and a <span class='babaColor'>Y-IS-WIN</span> rule</p>"},
	4 : {'label': 'Random Maps',  		'gif': 'tut_gifs/maps/random.gif', 	'txt': "<p><span class='babaColor'>Random</span> maps contain a map with randomly chosen and randomly placed tiles</p>"},
	5 : {'label': 'Elite Maps',   		'gif': 'tut_gifs/maps/elite.gif', 	'txt': "<p><span class='babaColor'>Elite</span> maps contain levels with <span class='babaColor'>high ratings</span> (these are considered 'elite' by the algorithm). Levels are rated on the <span class='babaColor'>Rate screen</span> on the main level selection page.</p>"},
	6 : {'label': 'User Maps',   		'gif': 'tut_gifs/maps/my_level.gif', 	'txt': "<p><span class='babaColor'>'My Level'</span> maps are maps made by the <span class='babaColor'>currently user</span>. If you\'re not logged in, the <span class='babaColor'>anonymous Baba levels</span> are used instead</p>"},
	7 : {'label': 'Shuffle Maps',   	'gif': 'tut_gifs/maps/shuffle.gif', 	'txt': "<p>You can <span class='babaColor'>generate a new selection of maps</span> by clicking the <span class='babaColor'>crossing blue arrows</span> (or shuffle button) at the bottom.</p>"},
	8 : {'label': 'Random Dimensions',  'gif': 'tut_gifs/maps/dimensions.gif', 	'txt': "<p>For the Basic, Blank, and Random maps you can randomize dimensions by <span class='babaColor'>clicking the lock and expanding square icon</span> at the bottom. An <span class='babaColor'>open lock</span> will <span class='babaColor'>randomize</span> dimensions. A <span class='babaColor'>closed lock</span> keep the <span class='babaColor'>same</span> dimensions.</p>"},
	9 : {'label': 'Use Objectives',   	'gif': 'tut_gifs/maps/objectives.gif', 	'txt': "<p>For the Elite and My Levels maps you can use levels with specific objectives by <span class='babaColor'>clicking the lock and clipboard icon</span> at the bottom. An <span class='babaColor'>open lock</span> will <span class='babaColor'>use any objectives</span>. A <span class='babaColor'>closed lock</span> will try to find levels with the <span class='babaColor'>same objectives</span> as the ones selected in the objective table.</p>"}
	

},

//rating
6 : {
	0: {'label': 'Rating Page', 'gif': 'tut_gifs/rating/page.gif', 'txt': "<p>The <span class='babaColor'>Rating Page</span> can be found on the level selection screen under the 'Rate' tab (fourth from the left).</p>"},
	1: {'label': 'Level A and B', 'gif': 'tut_gifs/rating/AB.gif', 'txt': "<p>Each rating has <span class='babaColor'>2 levels</span> that are <span class='babaColor'>compared between each other</span>. You can change the pairing by pressing the <span class='babaColor'>refresh button</span> in the middle of them.</p>"},
	2: {'label': 'Playing the Levels', 'gif': 'tut_gifs/rating/play.gif', 'txt': "<p>To get a feel for each level, <span class='babaColor'>play them both</span> by pressing the <span class='babaColor'>Play</span> button in the middle of their images. Make sure you've <span class='babaColor'>played the levels before rating</span> to give a fair review!</p>"},
	3: {'label': 'Using the Sliders', 'gif': 'tut_gifs/rating/slider.gif', 'txt': "<p>The 2 sliders in the middle represent the <span class='babaColor'>quality</span> of levels. <span class='babaColor'>Drag the green rectangle</span> towards the level you think <span class='babaColor'>represents the feature the most</span>. Or keep it in the <span class='babaColor'>middle</span> if you think they are <span class='babaColor'>equal in quality</span> for that feature.</p>"},
	4: {'label': 'Submit the Rating', 'gif': 'tut_gifs/rating/submit.gif', 'txt': "<p>When you're done rating the levels, press the <span class='babaColor'>Submit</span> below the sliders to submit your rating to the algorithm. <span class='babaColor'>More ratings will help the AI learn how to make better levels.</span></p>"}

}, 

//search
7 : {
	0: {'label': 'Search Page', 'gif': 'tut_gifs/search/page.gif', 'txt': "<p>The <span class='babaColor'>Search Page</span> can be found on the level selection screen under the 'Search' tab (second from the right).</p>"},
	1: {'label': 'Level results', 'gif': 'tut_gifs/search/filter.gif', 'txt': "<p>When you're done selecting your options, click the <span class='babaColor'>magnifying glass icon</span>. This will take you to the <span class='babaColor'>Results tab</span> to show you the search results. You can <span class='babaColor'>click the arrows on the side</span> of the matrix to <span class='babaColor'>show more results</span>. To get back to selecting options, click the <span class='babaColor'>Filter tab</span></p>"},
	2: {'label': 'Search by User or Level ID', 'gif': 'tut_gifs/search/name_number.gif', 'txt': "<p>You can search by a specific <span class='babaColor'>user</span> or <span class='babaColor'>level number</span> by typing in their respective fields."},
	3: {'label': 'Search by Objective', 'gif': 'tut_gifs/search/rules.gif', 'txt': "<p>You can <span class='babaColor'>specify the rules used in a level</span> by clicking in the <span class='babaColor'>objective table</span>. Clicking the <span class='babaColor'>toggle button</span> at the bottom will show levels with the <span class='babaColor'>Exactly or All</span> of the rules selected in the table or <span class='babaColor'>Any</span> combination of the rules in the table.</p>"},
	4: {'label': 'No Level Found', 'gif': 'tut_gifs/search/make_level.gif', 'txt': "<p>If your search comes up empty, the algorithm will encourage you to design a new level with your objective specifications!</p>"}
},

//user profiles
8 : {
	0: {'label': 'Why make an Account?', 'gif': 'tut_gifs/profile/why.gif', 'txt': "<p>Making an account can help you <span class='babaColor'>find levels you made</span>! You can use these specific levels in the editor under the <span class='babaColor'>My Levels</span> tab. Having an account will also let you <span class='babaColor'>claim authorship on levels</span> shown on the main page. It's not required but it helps us with this research study!</p>"},
	1: {'label': 'Profile Page', 'gif': 'tut_gifs/profile/page.gif', 'txt': "<p>The <span class='babaColor'>User Profile Page</span> can be found on the level selection screen under the 'My Levels' or 'Baba's Levels' tab (farthest right).</p>"},
	2: {'label': 'Baba\'s Levels', 'gif': 'tut_gifs/profile/baba.gif', 'txt': "<p>If you're not logged in, the <span class='babaColor'>anonymously made levels</span> can be seen on this page. <span class='babaColor'>Scrolling up and down</span> will show more and they are <span class='babaColor'>ordered by Level ID</span>. You can also see the <span class='babaColor'>rule codes</span> they use.</p>"},
	3: {'label': 'Register Account', 'gif': 'tut_gifs/profile/register.gif', 'txt': "<p>You can <span class='babaColor'>create a new account</span> by entering in a username and password into the text fields at the left of the screen. If the username is taken, a message will appear. Press the <span class='babaColor'>Register button</span> to create your user profile for the site.</p>"},
	4: {'label': 'Login', 'gif': 'tut_gifs/profile/login.gif', 'txt': "<p>You can <span class='babaColor'>login to your account</span> by entering in your saved username and password and by clicking the <span class='babaColor'>Login button.</span>.</p>"},
	5: {'label': 'Logout', 'gif': 'tut_gifs/profile/logout.gif', 'txt': "<p>You can logout from your account at anytime by pressing the <span class='babaColor'>Logout button</span>. If you leave the site while you are still logged in, it will remember you and will not automatically log you out.</p>"}
	
}


}

//template for tutorial
// {'label': '', 'gif': '', 'txt': "<p></p>"}

// CHANGE PAGE
function gotoURL(url){location.href = url;}


// SAVES ALL OF THE TUTORIAL SCREENS IN THE DICTIONARY FOR EASY ACCESS
function storeTutScreens(){
	//get the main carousel and the itemed screens 
	let carousel = document.getElementById("tutCarousel");
	let screens = carousel.getElementsByClassName("item");

	//add each tutCanvas div and tutQuest div
	for(let s=0;s<screens.length;s++){
		let item = screens[s];
		tutScreens[s] = {"question":item.getElementsByClassName("tutQuest")[0], "canvas":item.getElementsByClassName("tutCanv")[0]}
	}
}

// RETURN CURRENTLY ACTIVE TUTORIAL SCREEN INDEX
function getCurTut(){
	let screens = document.getElementById("tutCarousel").getElementsByClassName("item");

	//search for tutorial screen with "active" class 
	for(let s=0;s<screens.length;s++){
		if(screens[s].classList.contains("active"))
			return s;
	} 
	return -1;
}

// SHOWS THE TUTORIAL CANVAS
function showCanvas(index){
	resetTut();

	tutScreens[index]['question'].style.display = "none";
	tutScreens[index]['canvas'].style.display = "block";

	//special events
	if(index == 0){		//show demo game (first screen)
		curLevel = 0;
		aiControl = false;
		localStorage.control = "user";
		newLevel(0);
		setLevelTab(0);
		demo = true;
		importData = 0;
		activeTut = getCurTut();
		newInstrTxt(0,demoLevel_Instructions[0]);
		//console.log("ye")
	}
}

// RESETS ALL OF THE TUTORIAL SCREENS TO THE TEXT
function resetTut(){
	for(let i=0;i<Object.keys(tutScreens).length;i++){
		tutScreens[i]['canvas'].style.display = "none";
		tutScreens[i]['question'].style.display = "block";
	}

	//remove demo
	demo = false;
	curLevel = 0;
	curGifIndex = 0;
	wonGame = false;
}

//////////////////////          WALKTHROUGH FUNCTIONS          ///////////////////////////

//iterate to next demo level
function nextLevelInstr(levelIndex){
	let li = levelIndex%2;

	/*
	for(let i=levelIndex;i<15;i++){
		document.getElementById("game"+i).styles.backgroundColor = "#cdcdcd";
	}
	for(let i=levelIndex-1;i>0;i--){
		document.getElementById("game"+i).styles.backgroundColor = "#FFF554";
	}
	*/

	//if(document.getElementById('tutTxtDemo'+li).style.visibility != 'visible'){		//since being hidden
		newInstrTxt(li,demoLevel_Instructions[levelIndex]);
	//}
}

//change properties to show gif demonstration slide
function showTutGif(tutIndex, txtIndex){
	let s = gifInstructions[tutIndex];
	let i = s[txtIndex];

	document.getElementById("tutGif"+tutIndex).src = i['gif'];
	document.getElementById("tutLabel"+tutIndex).innerHTML = i['label'];
	document.getElementById("tutTxt"+tutIndex).innerHTML = i['txt'];
}

//goes to the next tutorial
function nextTutSlides(i){
	let set = gifInstructions[i];
	curGifIndex++;

	//at end of the slides
	if(curGifIndex == Object.keys(set).length){
		resetTut();
		$('#tutCarousel').carousel('next');
	}else{
		showTutGif(i,curGifIndex)
	}
	
}

//goes to the previous tutorial
function prevTutSlides(i){
	let set = gifInstructions[i];
	curGifIndex--;

	//at end of the slides
	if(curGifIndex == -1){
		curGifIndex = 0;
	}else{
		showTutGif(i,curGifIndex)
	}
	
}

//change level for tutorial
function setLevelTut(level){
	wonGame = false;
	curLevel = level;
	nextLevelInstr(level);
	newLevel(level);
	setLevelTab(level);
}

//set background color of current level
function setLevelTab(level){
	let tabs = document.getElementsByClassName("levelTab");
	for(let t=0;t<tabs.length;t++){
		tabs[t].style.backgroundColor = "#efefef";
	}
	document.getElementById("game"+(level+1)).style.backgroundColor = "#E5C11E";
}


//////////////////////      UPDATE/STATE FUNCTIONS      ///////////////////////////



// FUNCTIONS THAT INITALIZE WHEN THE PAGE LOADS
function super_init(){

	localStorage.control = "user";
	//renderScreen();
	storeTutScreens();
}


function newInstrTxt(index,text){
	let other = (index == 0 ? 1 : 0);
	document.getElementById('tutTxtDemo'+other).style.visibility = 'hidden';
	document.getElementById('tutTxtDemo'+index).style.visibility = 'visible';
	document.getElementById('tutTxtDemo'+index).innerHTML = text;

}