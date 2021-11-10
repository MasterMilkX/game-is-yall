// EDIT.JS - EDITOR SCREEN SCRIPTING 
// PCG.JS - MUTATION SCREEN SCRIPTING 
// Version 2.0
// Code by Milk 


/////////////////////     GLOBAL VARIABLES     /////////////////////////

// ----- EDIT.JS VARIABLES ------- //

//editor canvas variables
var editor = document.getElementById("editCanvas");
var etx = editor.getContext('2d');
editor.width = 640;
editor.height = 640;

//baba game tile img hash
var imgHash = {};
var tileType = "keyword";

//editor tool variables
var curTile = "baba_obj";
var curTool = "paint";
var mouseIsDown = false;

//history array (list of 2d maps)
var histMap = [];
var redoSet = [];
var maxHistory = 10;

//lasso variables
var lasso = {x1: -1, y1: -1, x2: -1, y2: -1, started: false, locked: false, canLasso : true};		//x1,y1 --> top left, x2, y2 --> bottom right
var ghostLasso = {x1: -1, y1: -1, x2: -1, y2: -1, px: -1, py: -1, active: false}

//map dimension variables
var mapInW = document.getElementById("mapW");
var mapInH = document.getElementById("mapH");
var mapWidth = Number(mapInW.value);
var mapHeight = Number(mapInH.value);
var dimensionRange = [6, 20];

//ascii and tile sizing variables
var curMap = [];
var sf = 64;
var offX = 0;
var offY = 0;

// placeholder canvas for the level thumbnails 
// NOTE: not actually added to the screen
var canvas = document.createElement("canvas");
var ctx = canvas.getContext("2d");
canvas.width = 640;
canvas.height = 640;

let mapShuffleSet = {};
let premapAscii = null;
var probElite = {};			//probabilistically sorted highest rated user levels (elites)


//other variables
var mapAllReady = false;	//map render check
var testWindow = null;		//test game level window 
var controlTxt = document.getElementById("controlTxt");

var authorEdits = {'user':false, 'pcg':false};
var usingBlank = false;


// ----- PCG.JS VARIABLES ------- //

//map dimensions
var DEF_WIDTH = 10;
var DEF_HEIGHT = 10;
var randomDimension = false;

//ascii map character lists
var allChars = Object.keys(map_key);
allChars.splice(allChars.indexOf("_"), 1);
var allWords = [];
var allObjs = [];
var allAdjWords = [];
var allObjWords = [];

var genMap = [];		//prerendered generated map from mutation
var objBias = 0.8;		//bias for object-based tiles

let st = 0;				//mutation interval value
let minFit = 0.25;
let maxIter = -1;

let curBestFit = 0.0;
let curBestObj = 0.0;
let showCurFit = false;
let curIter = 0;

let sort_eliteLevels = [];


// ----- MAP.JS (OBJECTIVE) VARIABLES ----- //

var activeRules = {};

//table modes
var objMode = "advanced";
var tableSim = document.getElementById("obj_table_simp");
var tableAdv = document.getElementById("obj_table");
var highlight_color = "#028301";

var ar = [];			//active map rules

var OBJ_CONS_THR = 0.5;		//objective constraint threshold score to start looking at fitness

////////////////////    GENERIC HELPER FUNCTIONS   ///////////////////////////

//CHECKS IF AN ELEMENT IS IN AN ARRAY
function inArr(arr, e){
	if(arr.length == 0)
		return false;
	return arr.indexOf(e) !== -1
}

function iconLabel(title){
	document.getElementById("icon_label").innerHTML = title;
}



/////////////////////      BASIC MAP FUNCTIONS     //////////////////////////

// RESETS THE AUTHORS OF THE MAP
function resetAuthors(){
	//console.log("no author");
	authorEdits['user'] = false;
	authorEdits['pcg'] = false;
}

function resetRules(){
	ar = getActiveMapRules(curMap);
	endRules = []
	localStorage.setItem("endRules", JSON.stringify(endRules));
	showActiveRules();
}

// RESETS THE PROPERTIES OF THE MAP
function resetMap(){
	calcOffset();
	renderMap();
	resetRules();
}

// IMPORTS A MAP FROM AN ASCII REPRESENTATION (2D MAP FORM)
function importMap(m2d){
	curMap = m2d;
	mapHeight = m2d.length;
	mapWidth = m2d[0].length;

	mapInW.value = mapWidth;
	mapInH.value = mapHeight;

	resetMap();

	usingBlank = false;
}

// MAKES A BLANK MAP ON THE CANVAS
function clearMap(){

	//reset the ascii representation of the map
	curMap = makeEmptyMap(mapWidth, mapHeight);

	resetMap();
	resetAuthors();
}

// CALCULATE THE OFFSET OF THE MOUSE PLACEMENT TO THE ACTUAL TILE RELATIVE TO THE CANVAS SIZE
// TAKES THE LARGEST OF THE DIMENSION RATIOS TO USE AS THE SIZE FACTOR (SF)
function calcOffset(){
	//if width is greater than height
	if((mapWidth/editor.offsetWidth) > (mapHeight/editor.offsetHeight)){
		sf = editor.width / mapWidth;
		offX = 0;
		offY = (editor.height - (sf*mapHeight)) / 2;
	}
	//if height is greater than or equal to width
	else{
		sf = editor.height / mapHeight;
		offY = 0;
		offX = (editor.width - (sf*mapWidth)) / 2;
	}
}

// CHANGES THE WIDTH X HEIGHT SIZE OF THE MAP BASED ON INPUT BOXES ABOVE THE CANVAS
function changeMapSize(){
	//check if invalid map size (out of bounds)
	if(mapInW.value < dimensionRange[0])
		mapInW.value = dimensionRange[0];
	else if(mapInW.value > dimensionRange[1])
		mapInW.value = dimensionRange[1];
	else if(mapInH.value < dimensionRange[0])
		mapInH.value = dimensionRange[0];
	else if(mapInH.value > dimensionRange[1])
		mapInH.value = dimensionRange[1];

	/*
	if(mapInW.value < dimensionRange[0] || mapInW.value > dimensionRange[1]){
		alert("Invalid map width! Must be between " + dimensionRange[0] + " and " + dimensionRange[1] + " tiles");
		mapInW.value = mapWidth;
		return;
	}else if(mapInH.value < dimensionRange[0] || mapInH.value > dimensionRange[1]){
		alert("Invalid map height! Must be between " + dimensionRange[0] + " and " + dimensionRange[1] + " tiles");
		mapInH.value = mapHeight;
		return;
	}
	*/

	makeHistory();	//add to history

	//if width updated
	if(mapInW.value != mapWidth){
		var newMap = [];
		var diff = (mapInW.value-mapWidth);

		//extend map
		if(diff > 0){
			for(var r=0;r<mapHeight;r++){
				var newRow = [];
				//add the original columns back
				for(var c=0;c<mapWidth-1;c++){
					newRow.push(curMap[r][c]);
				}
				for(var d=0;d<diff;d++){
					//add empty space or a border
					if(r == 0 || r == mapHeight-1){
						newRow.push("_");
					}else{
						newRow.push(" ");
					}
				}
				//add a border back
				newRow.push("_");
				newMap.push(newRow);
			}
		}
		//reduce the map
		else if(diff < 0){
			for(var r=0;r<mapHeight;r++){
				var newRow = [];
				for(var c=0;c<mapInW.value-1;c++){
					newRow.push(curMap[r][c]);
				}
				//add border
				newRow.push("_");
				newMap.push(newRow);
			}

		}

		//replace and update
		curMap = newMap;
		mapWidth = Number(mapInW.value);
		
		resetMap();

		//console.log("changed width");

		
	}
	//if height updated
	if(mapInH.value != mapHeight){
		var newMap = [];
		var diff = (mapInH.value-mapHeight);

		//extend map
		if(diff > 0){
			//add the rows back
			for(var r=0;r<mapHeight-1;r++){
				newMap.push(curMap[r]);
			}
			//add it
			for(var d=0;d<diff;d++){
				//make a new row
				var newRow = [];
				newRow.push("_");
				for(var c=0;c<mapWidth-2;c++){newRow.push(" ");}
				newRow.push("_");
				newMap.push(newRow);
			}
			//add the last row
			newMap.push(curMap[mapHeight-1]);
		}
		//reduce the map
		else if(diff < 0){
			//add first x rows
			for(var r=0;r<mapInH.value-1;r++){
				newMap.push(curMap[r]);
			}
			//add the bottom border back
			newMap.push(curMap[mapHeight-1]);
		}

		//replace and update
		curMap = newMap;
		mapHeight = Number(mapInH.value);
		
		resetMap();
		
		//console.log("changed height");

		//makeHistory();	//add to history
	}

	//update levels in recommender
	if(!randomDimension)
		shuffleAllLevels();

}



// REDRAWS THE MAP
function renderMap(){
	etx.save();
	etx.clearRect(0, 0, editor.width, editor.height);
	
	//check if all of the tile images on the map are loaded and ready for rendering
	if(!mapAllReady){
		mapAllReady = mapReady();
		return false;
	}


	//draw the generation pre-map over it as an overlay
	if(genMap.length > 0){
		//get offset of fake map
		let tmw = genMap[0].length;
		let tmh = genMap.length;
		let tsf = 0;
		let toffX = 0;
		let toffY = 0;
		if((tmw/editor.width) > (tmh/editor.height)){
			tsf = editor.width / tmw;
			toffX = 0;
			toffY= (editor.height - (tsf*tmh)) / 2;
		}else{
			tsf = editor.height / tmh;
			toffY = 0;
			toffX = (editor.width - (tsf*tmw)) / 2;
		}

		for(var r=0;r<genMap.length;r++){
			for(var c=0;c<genMap[0].length;c++){
				var img = imgHash[genMap[r][c]][0]
				etx.drawImage(img, 0, 0, img.width, img.height,
					c*tsf+toffX, r*tsf+toffY, tsf, tsf);
			}
		}

		//draw transparency to show that its A PHONY! A BIG FAT PHONY!
		etx.globalAlpha = 0.3;
		etx.fillStyle = "#ffffff";
		etx.fillRect(0,0,editor.width,editor.height);
		etx.globalAlpha = 1.0;
	}
	//draw actual active map
	else{
		//draw each tile from the BABA image tile hashmap onto the canvas
		for(var r=0;r<curMap.length;r++){
			for(var c=0;c<curMap[0].length;c++){
				var img = imgHash[curMap[r][c]][0]
				etx.drawImage(img, 0, 0, img.width, img.height,
					c*sf+offX, r*sf+offY, sf, sf);
			}
		}
	}

	//draw the lasso if enabled and started
	if(curTool == 'select'){
		//normal lasso
		if(lasso.started){
			etx.strokeStyle = "#FFB337";
			etx.setLineDash([16]);
			etx.lineWidth = 3;
			etx.strokeRect(lasso.x1*sf+offX,lasso.y1*sf+offY,(lasso.x2-lasso.x1+1)*(sf), (lasso.y2-lasso.y1+1)*(sf));
		}
		//ghost lasso
		if(ghostLasso.active){
			etx.strokeStyle = "#ffffff";
			etx.setLineDash([16])
			etx.lineWidth = 2;
			etx.strokeRect(ghostLasso.x1*sf+offX,ghostLasso.y1*sf+offY,(ghostLasso.x2-ghostLasso.x1+1)*(sf), (ghostLasso.y2-ghostLasso.y1+1)*(sf));
		}
		
	}	

	//draw label for best fitness if enabled
	if(showCurFit){
		//draw box
		etx.globalAlpha = 0.7;
		etx.fillStyle = "#ffffff";
		let dx = sf*4;
		let dy = sf*2;
		let ox = Math.max(200, dx);
		etx.fillRect((editor.width/2) - (ox/2), (editor.height/2) - (dy/2), ox, dy);

		//draw value
		etx.font = "bold 24px Consolas";
		etx.fillStyle = "#000000";
		etx.textAlign = "center";
		etx.fillText("Quality %: " + Math.max((1.0-curBestFit),0).toFixed(2), editor.width/2, editor.height/2-10);

		let periods = ".";
		for(let p=0;p<((curIter/20)%3);p++){periods += ".";}
		etx.fillText("Evolving" + periods, editor.width/2, editor.height/2+24);
	}

	etx.restore();
}

// MAKES A THUMBNAIL VERSION OF A LEVEL AND RETURNS AS A PNG 
function makeThumbnail(a_map){
	let miniMap = parseMap(a_map);
	let tmh = miniMap.length;
	let tmw = miniMap[0].length;

	//determine the offset and sizing
	let tsf = 0;
	let toffX = 0;
	let toffY = 0;
	if((tmw/canvas.width) > (tmh/canvas.height)){
		tsf = canvas.width / tmw;
		toffX = 0;
		toffY= (canvas.height - (tsf*tmh)) / 2;
	}else{
		tsf = canvas.height / tmh;
		toffY = 0;
		toffX = (canvas.width - (tsf*tmw)) / 2;
	}


	//draw the map first
	ctx.save();
	//ctx.translate(-camera.x, -camera.y);		//camera
	ctx.clearRect(0, 0, canvas.width, canvas.height);

	//make background color
	ctx.fillStyle = "#343434";
	ctx.fillRect(0,0,canvas.width,canvas.height);

	//draw bounding rectangle
	ctx.beginPath();
	ctx.lineWidth = "2";
	ctx.strokeStyle = "#007C13";
	ctx.rect(0,0,canvas.width,canvas.height);
	ctx.stroke();
	
	//make the map ready
	if(!mapAllReady){
		return;
	}

	//draw the minimap
	for(var r=0;r<miniMap.length;r++){
		for(var c=0;c<miniMap[0].length;c++){
			var img = imgHash[miniMap[r][c]][0];
			ctx.drawImage(img, 0, 0, img.width, img.height,
				c*tsf+toffX, r*tsf+toffY, tsf, tsf);
		}
	}

	ctx.restore();

	return canvas.toDataURL("map.png");

}




/////////////////////      MAP GENERATION FUNCTIONS     //////////////////////////



// CREATES AN EMPTY BORDERED MAP WITH SET MAPWIDTH AND MAPHEIGHT
function makeEmptyMap(mw,mh){
	var new_map = [];
	for(var r=0;r<mh;r++){
		var row = [];
		for(var w=0;w<mw;w++){
			if(r == 0 || r == mh-1 || w == 0 || w == mw - 1)
				row.push("_");
			else
				row.push(" ");
		}
		new_map.push(row);
	}
	return new_map;
}

// ADDS BASIC RULES IN THE GAME
function makeBasicMap(){
	let mw = mapWidth;
	let mh = mapHeight;
	if(randomDimension){
		mw = Math.floor(Math.random()*(dimensionRange[1]-dimensionRange[0]))+dimensionRange[0];
		mh = Math.floor(Math.random()*(dimensionRange[1]-dimensionRange[0]))+dimensionRange[0];
	}

	var bmap = makeEmptyMap(mw,mh);
	add_XISYOU(bmap);
	add_XISWIN(bmap);
	return bmap;
}

// MAKE A MAP WITH BASIC RULES AND RANDOM TILES
function makeRandomMap(emptyProb){
	var rmap = makeBasicMap();
	randomFill(rmap, emptyProb);
	return rmap;
}

// MAKE A MPA WITH A BIAS TOWARDS A GROUP TYPE
function makeBiasMap(emptyProb){
	var imap = makeBasicMap();
	biasFill(imap, emptyProb, objBias);
	return imap;
}

// ADDS A X-IS-YOU RULE
function add_XISYOU(m){
	var orientation = (Math.random() < 0.5 ? "ver" : "hor");
	var rand_obj = (Math.random() < 0.5 ? "B" : allObjs[Math.floor(Math.random()*allObjs.length)].toUpperCase());   //baba is 50% chance

	var mh = m.length;
	var mw = m[0].length;

	//place on map
	if(orientation == "ver"){
		var x = 0;
		var y = 0;

		do{
			x = Math.floor(Math.random()*(mw-2))+1	//x doesn't matter
			y = Math.floor(Math.random()*(mh-4))+1;	//y allows room for whole set
		}while(m[y][x] != " " || m[y+1][x] != " " || m[y+2][x] != " ");

		m[y][x] = rand_obj;		//X
		m[y+1][x] = "1";		//IS
		m[y+2][x] = "2";		//YOU
	}
	if(orientation == "hor"){
		var x = 0;
		var y = 0;
		do{
			y = Math.floor(Math.random()*(mh-2))+1	//y doesn't matter
			x = Math.floor(Math.random()*(mw-4))+1;	//x allows room for whole set
		}while(m[y][x] != " " || m[y][x+1] != " " || m[y][x+2] != " ");

		

		m[y][x] = rand_obj;		//X
		m[y][x+1] = "1";		//IS
		m[y][x+2] = "2";		//YOU
	}

	//place physical object at random point
	var x_obj = 0;
	var y_obj = 0;
	do{
		x_obj = Math.floor(Math.random()*(mw-2))+1;
		y_obj = Math.floor(Math.random()*(mh-2))+1;
	}while(m[y_obj][x_obj] != " ");

	m[y_obj][x_obj] = rand_obj.toLowerCase();
	

}

// ADDS A X-IS-WIN RULE
function add_XISWIN(m){
	var orientation = (Math.random() < 0.5 ? "ver" : "hor");

	var rand_obj = (Math.random() < 0.5 ? "F" : allObjs[Math.floor(Math.random()*allObjs.length)].toUpperCase());   //flag is 50% chance

	var mh = m.length;
	var mw = m[0].length;

	//place on map based on the orientation (up-down, left-right)
	if(orientation == "ver"){
		var x = 0;
		var y = 0;

		do{
			x = Math.floor(Math.random()*(mw-2))+1	//x doesn't matter
			y = Math.floor(Math.random()*(mh-4))+1;	//y allows room for whole set
		}while(m[y][x] != " " || m[y+1][x] != " " || m[y+2][x] != " ");

		m[y][x] = rand_obj;		//X
		m[y+1][x] = "1";		//IS
		m[y+2][x] = "3";		//WIN
	}
	if(orientation == "hor"){
		var x = 0;
		var y = 0;
		do{
			y = Math.floor(Math.random()*(mh-2))+1	//y doesn't matter
			x = Math.floor(Math.random()*(mw-4))+1;	//x allows room for whole set
		}while(m[y][x] != " " || m[y][x+1] != " " || m[y][x+2] != " ");


		m[y][x] = rand_obj;		//X
		m[y][x+1] = "1";		//IS
		m[y][x+2] = "3";		//WIN
	}

	//place physical object at random point
	var x_obj = 0;
	var y_obj = 0;
	do{
		x_obj = Math.floor(Math.random()*(mw-2))+1;
		y_obj = Math.floor(Math.random()*(mh-2))+1;
	}while(m[y_obj][x_obj] != " ");

	m[y_obj][x_obj] = rand_obj.toLowerCase();
}

// FILL WITH RANDOM ASCII TILES
function randomFill(m, emptyProb){
	let w = setAllWords();
	let o = setAllObjs();

	for(var r=0;r<m.length;r++){
		for(var c=0;c<m[0].length;c++){
			if(m[r][c] == " " && Math.random() > emptyProb){
				//m[r][c] = allChars[Math.floor(Math.random()*allChars.length)];
				let s = (Math.random() > 0.75 ? "word" : "object");
				m[r][c] = (s == "word" ? w[Math.floor(Math.random()*w.length)] : o[Math.floor(Math.random()*o.length)])
			}
		}
	}
}

// FILL BASED ON SOME BIAS PROBABILITY FOR A SPECIFIC TILE GROUP (WORD or OBJECT)
function biasFill(m, emptyProb){
	for(var r=0;r<m.length;r++){
		for(var c=0;c<m[0].length;c++){
			if(m[r][c] == " " && Math.random() > emptyProb){
				var char = (Math.random() > objBias ? allWords[Math.floor(Math.random()*allWords.length)] : allObjs[Math.floor(Math.random()*allObjs.length)])
				m[r][c] = char;
			}
				
		}
	}
}



///////////////////      PAINT FUNCTIONS     ////////////////////////

// EDITOR MOUSE FUNCTIONS
editor.onmousedown = function(e){

	//save old map to history
	if(!mouseIsDown)
		makeHistory();	

	if(curTool == 'select'){
		let modX = (e.offsetX * editor.width) / editor.offsetWidth;
		let modY = (e.offsetY * editor.height) / editor.offsetHeight;
		let x = Math.floor((modX - offX) / sf);  
		let y = Math.floor((modY - offY) / sf); 
		let x2 = Math.round((modX-offX)/sf);
		let y2 = Math.round((modY-offY)/sf); 

		//make starting point
		if(!lasso.started && lasso.canLasso){
			lasso.started = true;
			lasso.x1 = x2;
			lasso.x2 = x2;
			lasso.y1 = y2;
			lasso.y2 = y2;

			//console.log("init: " + lasso.x1 + "," + lasso.y1)
		}

		//make pinch point for ghost lasso
		if(lasso.locked && !ghostLasso.active && !outsideLasso(e)){
			ghostLasso.px = x;
			ghostLasso.py = y;
			//console.log("pinch pt: " + x + "," + y)
		}
	}

	//deselect lasso
	if(curTool == 'select' && lasso.locked && outsideLasso(e)){		
		resetLasso();
		lasso.canLasso = false;
	}
	

	mouseIsDown = true;
  	
};
editor.onmouseup = function(e){
  mouseIsDown = false;

  if(curTool == 'select'){
  	if(lasso.started && !lasso.locked)		//lock lasso if applicable
  		lockLasso();
  	if(ghostLasso.active){			//move lasso contents if active
  		relocateLasso();
  		resetGhostLasso();
  	}					
  			
  }
  	
};
editor.onmousemove = function(e){
  if(mouseIsDown)
	paint(e);
  
};

// IF Q KEY PRESSED - DISABLE LASSO
document.body.addEventListener("keypress", function (e) {
	if(e.keyCode == 113){	//q key
		resetLasso();
	}	
});


// CHANGES THE CURRENT TOOL
function changeTool(t){
	curTool = t;

	if(curTool == "paint")
		editor.style.cursor = "url('flaticon/draw.png'), pointer";
	else if(curTool == 'erase')
		editor.style.cursor = "url('flaticon/eraser.png'), pointer";
	else if(curTool == 'select')
		editor.style.cursor = "crosshair";	

	//disable other tools
	let tools = ['paint', 'erase', 'select'];
	tools.splice(tools.indexOf(curTool), 1);
	for(let i=0;i<tools.length;i++)
		document.getElementById(tools[i]+"Tool").classList.remove('selImg1');

	document.getElementById(curTool+"Tool").classList.add('selImg1');
}

// CHAGES THE CURRENT TILE TO PAINT
function changeTile(t, imgTile){
	//change tool and tile
	curTile = t;
	changeTool('paint');

	//reset highlight on the rest of the tiles
	tileImgs = document.getElementsByClassName('selImg2');
	for(let t=0;t<tileImgs.length;t++){
		tileImgs[t].classList.remove('selImg3')
	}

	imgTile.classList.add('selImg3')
}


// EDITOR METHOD FOR PAINTING ON SQUARES
let noneditMapAlert = false;
function paint(ev){

	if(genMap.length > 0){
		if(!noneditMapAlert){
			noneditMapAlert = true;
			alert("You must confirm whether to use this map or not before you can edit!");
		}
		return;
	}

	//realign cursor coordinates
	var modX = (ev.offsetX * editor.width) / editor.offsetWidth;
	var modY = (ev.offsetY * editor.height) / editor.offsetHeight;
	let x = Math.floor((modX - offX) / sf);  
	let y = Math.floor((modY - offY) / sf); 

	//get the ascii character for the tile
	var char = reverseChar(curTile);

	//check for out of bounds
	if(x < 1 || x >= curMap[0].length-1)
		return;
	else if(y < 1 || y >= curMap.length-1)
		return;

	//update the map
	if(curTool == "paint"){
		//already there
		if(curMap[y][x] == char)
			return;

		curMap[y][x] = char;
	}else if(curTool == "erase"){
		//already blank
		if(curMap[y][x] == ' ')
			return;

		curMap[y][x] = " ";
	}else if(curTool == 'select'){
		//create a new lasso if able to
		if(lasso.canLasso && !lasso.locked){
			let cursX = Math.round((modX-offX-sf)/sf);
			let cursY = Math.round((modY-offY-sf)/sf);
			makeLasso(cursX,cursY);
		}
		//boundary already set - move elsewhere if selected in border
		else if(lasso.locked && ghostLasso.px >= 0 && ghostLasso.py >= 0 && (!outsideLasso(ev) || ghostLasso.active)){
			moveLasso(x,y);
		}
		
	}

	//update the currently active rules if any
	resetRules();

	//make user an author
	authorEdits['user'] = true;

	//allow evolving since map was changed
	document.getElementById('evolveBtn').classList.remove('lockImg');
}

// RESETS THE PROPERTIES OF THE LASSO
function resetLasso(){
	lasso.started = false;
	lasso.locked = false;

	lasso.x1 = -1;
	lasso.x2 = -1;
	lasso.y1 = -1;
	lasso.y2 = -1;

	//console.log("new lasso")
}

// RESETS THE PROPERTIES OF THE GHOST LASSO
function resetGhostLasso(){
	ghostLasso.px = -1;
	ghostLasso.py = -1;
	ghostLasso.active = false;
}

// SETS LASSO PARAMETERS FOR MOVING TILES AROUND THE MAP
function makeLasso(x,y){
	lasso.x2 = x;
	lasso.y2 = y;
}

// SWITCHES THE LASSO COORDINATES FOR THE CORNERS IF CREATED BACKWARDS
function switchLassCoords(){
	let lx = (lasso.x1 > lasso.x2 ? lasso.x2+1 : lasso.x1);
	let ly = (lasso.y1 > lasso.y2 ? lasso.y2+1 : lasso.y1);

	let rx = (lasso.x1 == lx ? lasso.x2 : lasso.x1-1);
	let ry = (lasso.y1 == ly ? lasso.y2 : lasso.y1-1);

	lasso.x1 = lx;
	lasso.x2 = rx;
	lasso.y1 = ly;
	lasso.y2 = ry;
}

// LOCK THE LASSO AND THE CONTENTS FROM THE MAP
function lockLasso(){

	switchLassCoords();	//switch the corners if opposite

	lasso.contents = [];
	let w = Math.abs(lasso.x2 - lasso.x1 + 1);
	let h = Math.abs(lasso.y2 - lasso.y1 + 1);

	//not a rectangle --> reset lasso
	if(w <= 0 || h <= 0){
		resetLasso();
		return;
	}
	//otherwise lock into place
	else{
		lasso.locked = true;
		lasso.canLasso = false;
	}

	for(let r=0;r<h;r++){
		let row = [];
		for(let c=0;c<w;c++){
			row.push(curMap[lasso.y1+r][lasso.x1+c]);
		}
		lasso.contents.push(row);
	}
}

// CHECK IF USER CLICKED OUTSIDE THE LOCKED LASSO
function outsideLasso(ev){
	//realign cursor coordinates
	var modX = (ev.offsetX * editor.width) / editor.offsetWidth;
	var modY = (ev.offsetY * editor.height) / editor.offsetHeight;

	return ((modX-offX) > (lasso.x2+1)*sf || (modX-offX) < lasso.x1*sf || (modY-offY) > (lasso.y2+1)*sf || (modY-offY) < lasso.y1*sf);

}

// MOVE THE LASSO AND ITS CONTENTS TO ANOTHER PART OF THE MAP
function moveLasso(x,y){
	ghostLasso.active = true;

	ghostLasso.x1 = lasso.x1-(ghostLasso.px-x)
	ghostLasso.x2 = lasso.x2-(ghostLasso.px-x)
	ghostLasso.y1 = lasso.y1-(ghostLasso.py-y)
	ghostLasso.y2 = lasso.y2-(ghostLasso.py-y)
}

// FINALIZE NEW LOCATION OF THE LASSO
function relocateLasso(){
	//get properties of new content and location
	let w = lasso.contents.length;
	let h = lasso.contents[0].length;

	let tx = lasso.x1;
	let ty = lasso.y1;

	//change previous area to blank spot
	for(let r=0;r<w;r++){
		for(let c=0;c<h;c++){
			curMap[ty+r][tx+c] = " ";
		}
	}

	//move lasso position
	lasso.x1 = ghostLasso.x1;
	lasso.x2 = ghostLasso.x2;
	lasso.y1 = ghostLasso.y1;
	lasso.y2 = ghostLasso.y2;

	tx = lasso.x1;
	ty = lasso.y1;

	//replace contents
	for(let r=0;r<w;r++){
		for(let c=0;c<h;c++){
			if((ty+r) > curMap.length || (tx+c) > curMap[0].length || curMap[ty+r][tx+c] == '_')		//out of bounds
				continue;
			else
				curMap[ty+r][tx+c] = lasso.contents[r][c];

		}
	}

	//add to history
	//makeHistory();

	resetGhostLasso();	//reset the ghost lasso

	//console.log("new digs")
}


// ADDS TO THE HISTORY SET OF LAST MAP ACTION
function makeHistory(){
	let newMap = map2Str(curMap);
	if(histMap.length > 0 && histMap[histMap.length-1] == newMap)
		return;

	//add to the history and remove top if maxed out
	histMap.push(newMap);
	if(histMap.length > maxHistory)
		histMap.shift(0);

	redoSet = [];	//reset the redo list (can no longer redo after this point)
}

// UNDOES THE LAST ACTION TO THE PREVIOUS MAP LAYOUT
function undo(){
	if(histMap.length < 1)
		return;
	
	redoSet.push(map2Str(curMap));		//add to the redo set

	//set the map
	importMap(parseMap(histMap[histMap.length-1]));	//set map to latest history item

	let lastMap = histMap.pop();		//pop off the last element of the 

	resetLasso();  			//deselect lasso in case it's out
}

// REDOES THE LAST ACTION TO THE NEXT MAP LAYOUT
function redo(){
	if(redoSet.length == 0)
		return;

	//add to the history and remove top if maxed out
	histMap.push(map2Str(curMap));
	if(histMap.length > maxHistory)
		histMap.shift(0);

	//set the map
	importMap(parseMap(redoSet.pop()));

	resetLasso();  			//deselect lasso in case it's out
	
}

//////////////////      MAP SELECTION FUNCTIONS     //////////////////////

// DETERMINE WHICH TAB IS CURRENTLY ACTIVE
function getActiveTab(poss_tabs){
	for(let p=0;p<poss_tabs.length;p++){
		if (document.getElementById(poss_tabs[p]).classList.contains('active'))
			return poss_tabs[p];
	}
	return "";
}


// CHECK HOW MATCHING A CHROMOSOME IS TO ANOTHER BASED ON BINARY VALUES
function chromoMatch(a,b){
	let m = 0;
	for(let i=0;i<a.length;i++){
		if(a[i] == b[i] && a[i] == 1)
			m++;
	}
	//console.log(a + " | " + b + " = " + m)
	return m;
}

// PICKS A RANDOM DEMO LEVEL
function getRandDemoLevel(excludeAscii, useObj=false){
	//map the chromsome reps to the ascii reps
	let mapSet = {};
	for(let i=0;i<demoLevelChromos.length;i++){
		let chr = demoLevelChromos[i];

		//initialize a new chromosome group
		if(!(chr in mapSet))
			mapSet[chr] = [];
		
		//add the level to the set if not excluded
		let am = map2Str(demoLevels[i])
		if(!inArr(excludeAscii, am)){
			mapSet[chr].push(am);
		}
	}
	let sort_demo = Object.keys(mapSet);	//get list of all distinct chromosomes
	sort_demo = sort_demo.filter(x => mapSet[x].length > 0);

	//if using objective - sort by closest to objective
	if(useObj){
		let actRulesChromo = activeRules2Chromo();
		sort_demo.sort(function(a,b){
			return chromoMatch(b,actRulesChromo) - chromoMatch(a,actRulesChromo);
		})

		//return best chromosome match level
		let c = sort_demo[0];
		return parseMap(mapSet[c][Math.floor(Math.random()*mapSet[c].length)]);

	}
	//otherwise just return a random level
	else{	
		let c = sort_demo[Math.floor(Math.random()*sort_demo.length)]
		return parseMap(mapSet[c][Math.floor(Math.random()*mapSet[c].length)]);
	}
	

}

// SORT TO MAKE "ELITE" LEVELS AND PROBABILISITCALLY CHOOSE THEM
function makeProbElite(){
	probElite = {};		//reset

	//map levels to ratings
	let lev2Rating = {};
	for(let i=0;i<eliteLevels.length;i++){
		let rat = eliteRatings[i];
		lev2Rating[eliteLevels[i]] = (parseInt(rat[0])*parseInt(rat[1]));
	}

	//sort levels by ratings
	let levs = Object.keys(lev2Rating);
	levs.sort(function(a,b){
		return lev2Rating[a] - lev2Rating[b];
	});

	//map levels to probability number based on ranking
	let total = 0;
	for(let i=0;i<eliteLevels.length;i++){total+=i+1;}

	let added = 0;
	for(let i=levs.length;i>0;i--){
		let frac = i/total;
		probElite[added+frac] = levs[i-1];
		added += frac;
	}
}

// PICK A RANDOMLY AND POSSIBLY HIGH RATING LEVEL FROM THE PROBELITE
function pickProbElite(excl){
	//pick from the probability elite
	let p = Object.keys(probElite);
	p.sort()
	let r = Math.random();
	for(let i=0;i<p.length;i++){
		if(r < p[i] && !inArr(excl,probElite[p[i]])){
			return probElite[p[i]];
		}
	}
}

// RETRIEVES AN USER MADE OR ELITE MAP SAVED TO THE DATABASE
function getDatabaseMap(mapType, excludeAscii, useObj=false){
	let chrs = [];
	let levels = [];
	let ratings = [];

	//identify the type of set to use
	if(mapType == 'elite'){
		chrs = eliteChromos;
		levels = eliteLevels;
		ratings = eliteRatings;
	}else if(mapType == 'user'){
		chrs = userChromos;
		levels = userLevels;
		ratings = userRatings;
	}

	//map the chromsome reps to the ascii reps
	let mapSet = {};
	for(let i=0;i<chrs.length;i++){
		let chr = chrs[i];

		//initialize a new chromosome group
		if(!(chr in mapSet))
			mapSet[chr] = [];
		
		//add the level to the set if not excluded
		if(!inArr(excludeAscii, levels[i])){
			mapSet[chr].push(levels[i]);
		}
	}

	//map the levels to their ratings
	let ratSet = {};
	for(let i=0;i<ratings.length;i++){
		let rat = ratings[i];
		ratSet[levels[i]] = (parseInt(rat[0])*parseInt(rat[1]))
	}

	let usableChromos = Object.keys(mapSet);	//get list of all distinct chromosomes
	usableChromos = usableChromos.filter(x => mapSet[x].length > 0);
	
	//no maps left dummy! return a random demo map
	if(usableChromos.length == 0){
		return getRandDemoLevel(excludeAscii, useObj);
	}		
		

	//if objectives are used - sort by most matches
	if(useObj){
		let actRulesChromo = activeRules2Chromo();

		//find the matches
		usableChromos.sort(function(a,b){
			let ca = chromoMatch(a,actRulesChromo);
			let cb = chromoMatch(b,actRulesChromo);

			return chromoMatch(b,actRulesChromo) - chromoMatch(a,actRulesChromo);
		});

		//return a randomly selected map from the randomly (or best) chromosome
		let auc = usableChromos.filter(k => mapSet[k].length > 0);
		let chr = auc[0];

		//sort by highest rated maps
		let mapChoice = mapSet[chr];
		mapChoice.sort(function(a,b){
			return ratSet[b] - ratSet[a];
		});

		return parseMap(mapChoice[0]);
	}

	//probabilistically use highest rated levels
	else{
		//pick from the probability elite
		let pe = pickProbElite(excludeAscii);
		return parseMap(pe);
	}

	

}

// CREATE A NEW SET OF LEVELS FOR THE MAP SELECTION SETS
function newLevelThumbnails(level_type){
	//make brand new maps and thunbnails
	let newset_img = [];
	let newset_ascii = [];
	let savedLevels = [];		//for use with the user and elite levels only


	for(let l=0;l<4;l++){

		//get the map type
		new_map = [];
		if(level_type == "blank_levels"){
			new_map = makeEmptyMap(mapWidth, mapHeight);
		}else if(level_type == "basic_levels"){
			new_map = makeBasicMap();
		}else if(level_type == "random_levels"){
			new_map = makeRandomMap((Math.random()*0.79)+0.2);
		}else if(level_type == "elite_levels"){
			new_map = getDatabaseMap('elite', savedLevels, !randomDimension);	//use the lock for objectives and locked dimensions
			savedLevels.push(map2Str(new_map));
		}else if(level_type == "my_levels"){
			new_map = getDatabaseMap('user', savedLevels, !randomDimension);	//use the lock for objectives and locked dimensions
			savedLevels.push(map2Str(new_map));
		}

		//save to the ascii rep set
		newset_ascii.push(new_map);

		//get the thumbnail
		thumbnail_map = makeThumbnail(map2Str(new_map));
		newset_img.push(thumbnail_map);

		//only one blank map possible
		if(level_type == "blank_levels")
			break;
	}

	//set all the images to the thumbnails
	let curTab = document.getElementById(level_type);
	let allLevelImgs = curTab.getElementsByTagName("img");

	for(let i=0;i<allLevelImgs.length;i++){		//should be 4 images total (or 1 for the blank map)
		allLevelImgs[i].src = newset_img[i];
	}

	//export the ascii set
	mapShuffleSet[level_type] = newset_ascii;

}

// FOR SHUFFLING LEVEL TYPES ON A SPECIFIC TAB
function shuffleLevels(){
	//get active level selection screen name
	let tabs = ["blank_levels","basic_levels","random_levels","elite_levels","my_levels"];
	let lt = getActiveTab(tabs);

	//make tab's thumbnails
	newLevelThumbnails(lt);

}

// SHUFFLE THE LEVELS ON ALL THE TABS
function shuffleAllLevels(){
	let tabs = ["blank_levels", "basic_levels","random_levels","elite_levels","my_levels"];
	for(let t=0;t<tabs.length;t++){
		newLevelThumbnails(tabs[t]);
	}
}

// IMPORTS THE MAP FROM THE SUGGESTIONS OF THE LEVEL SHUFFLER
function importSelectMap(index, img){
	let tabs = ["blank_levels","basic_levels","random_levels","elite_levels","my_levels"];
	let lt = getActiveTab(tabs);

	premapAscii = mapShuffleSet[lt][index];
	genMap = premapAscii;
	noneditMapAlert = false;

	if(lt == "blank_levels")
		usingBlank = true;
}

// CONFIRMS THE SELECTION OF ONE OF THE SHUFFLE LEVELS
function confirmSelection(){
	//confirmation of reset
	//if(curMap.length > 0 && !confirm("Are you sure you want to erase the current map in the editor?"))
	//	return;

	if(genMap.length == 0)
		return;

	resetAuthors();

	//blank map == no author | imported map == pcg (recommender)
	if(!usingBlank){
		//console.log("imported");
		authorEdits['pcg'] = true;
	}

	importMap(genMap);

	genMap = [];
	noneditMapAlert = true;

}

// REMOVES THE OVERLAY OF THE PRESELECTED SHUFFLE MAP
function cancelSelection(){
	genMap = [];
	noneditMapAlert = false;
	usingBlank = false;
}

// CHANGES THE LOCK BUTTON IMAGE DEPENDING ON THE PANE
function changeLockBtn(pane){
	pane = pane.replace("#","");
	document.getElementById("lockBtn").src = "flaticon/" + ((pane == "elite_levels" || pane == "my_levels") ? "obj-" : "scale-") + (!randomDimension ? "" : "un") + "lock.png"	//replace the image
}

// CHANGES THE LOCK BUTTON ACCORDINGLY
$('#maps a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
  var target = $(e.target).attr("href") // activated tab
  changeLockBtn(target);
});

//LOCKS THE DIMENSIONS WHEN SUGGESTING NEW LEVELS
function toggleLock(){
	let tabs = ["blank_levels","basic_levels","random_levels","elite_levels","my_levels"];
	let lt = getActiveTab(tabs);

	randomDimension = !randomDimension;
	changeLockBtn(lt)
	shuffleAllLevels();
} 


////////////////      ASCII DEFINITION FUNCTIONS     ////////////////////

// SORT ALL THE WORD CHARACTERS
function setAllWords(){
	var words = [];
	for(var c=0;c<allChars.length;c++){
		if(map_key[allChars[c]].includes("_word"))
			words.push(allChars[c]);
	}
	return words;
}

// SET ALL OF THE WORD TILES (OBJECT AND WORD)
function setAllWordTypes(){
	var adjWords = [];
	var objWords = [];
	for(var c=0;c<allWords.length;c++){
		if(allWords[c].match(/[A-Z]/))
			objWords.push(allWords[c]);
		else
			adjWords.push(allWords[c]);
	}
	return [adjWords, objWords];
}

// SORT ALL THE OBJECT CHARACTERS
function setAllObjs(){
	var objs = [];
	for(var c=0;c<allChars.length;c++){
		if(map_key[allChars[c]].includes("_obj"))
			objs.push(allChars[c]);
	}
	return objs;
}


/////////////////////     MAP MUTATION FUNCTIONS    //////////////////////////


//RANDOMLY SELECT BETWEEN MUTATING OR ALTERING
function randomAlterMap(){ 
	let coinFlip = 0.85;		//coin flip probability to determine how many times to mutate

	//to keep track of changes
	let before = "";
	let after = "";

	do {
		do{
			before = map2Str(curMap);
			if(Math.random() > 0.5)
				randomTile(0.85);
			else
				alterTileType();
			after = map2Str(curMap);
		}while(before === after);		//ensure map has been changed
	}while(Math.random() < coinFlip)

	//allow more evolution since map was changed
	document.getElementById('evolveBtn').classList.remove('lockImg');

	resetRules();

	//make PCG an author for modifying
	authorEdits['pcg'] = true;
	
}

//MUTATES THE LEVEL BY CHANGING A TILE TO A RANDOM CHARACTER BASED ON PROBABILITIES OF TILE GROUPS
function randomTile(emptyProb){
	//get random position
	var x = Math.floor(Math.random()*(mapWidth-2))+1;
	var y = Math.floor(Math.random()*(mapHeight-2))+1;

	//replace with a new character
	var newChar = "";
	do{
		if(Math.random() <= emptyProb)		//make an empty character
			newChar = " ";
		else if(Math.random() > objBias)
			newChar = allWords[Math.floor(Math.random()*allWords.length)];		//make a word character
		else
				newChar = allObjs[Math.floor(Math.random()*allObjs.length)];		//make an object character
	}while(curMap[y][x] == newChar);
	curMap[y][x] = newChar;

	//make PCG an author for modifying
	authorEdits['pcg'] = true;

}

// CHANGES A SINGLE TILE TO ANOTHER TILE WITHIN ITS TILE GROUP - THIS IS TO CREATE NEW RULES AND OBJECT INTERACTIONS
// i.e. BABA_OBJ -> KEKE_OBJ | WALL_OBJ ; KILL_WORD -> SINK_WORD
function alterTileType(){
	//get random position
	var x = Math.floor(Math.random()*(mapWidth-2))+1;
	var y = Math.floor(Math.random()*(mapHeight-2))+1;

	//replace with a new character
	var newChar = " ";

	//change based on the selected tile type
	let curTile = curMap[y][x];
	let keyTile = map_key[curTile];

	if(keyTile.includes("_obj") || curTile == " "){		//object tile (or empty tile)
		let obj2 = [" "];
		obj2.push.apply(obj2, allObjs);
		do{
			newChar = obj2[Math.floor(Math.random()*obj2.length)];		//make an object character
		}while(curTile == newChar);
	}
	else if(keyTile.includes("_word")){
		if(inArr(allAdjWords,curTile)){
			do{
				newChar = allAdjWords[Math.floor(Math.random()*allAdjWords.length)];		//make an object character
			}while(curTile == newChar);
		}else if(inArr(allObjWords,curTile)){
			do{
				newChar = allObjWords[Math.floor(Math.random()*allObjWords.length)];		//make an object character
			}while(curTile == newChar);
		}else{
			do{
				newChar = allWords[Math.floor(Math.random()*allWords.length)];		//make an object character
			}while(curTile == newChar);
		}
	}

	curMap[y][x] = newChar;

	//make PCG an author for modifying
	authorEdits['pcg'] = true;

}

// REPLACE A 3X3 SEGMENT OF THE MAP WITH ANOTHER SEGMENT FROM AN ELITE LEVEL
function mutateMap(){
	let seg = 3;		//segment size = 3x3

	//select an elite to use
	let inputElites = makeLevelInputSet();
	let elite = inputElites[Math.floor(Math.random()*inputElites.length)];

	//get a random tile pattern 
		//coords chosen for top left of 3x3 and offset to account for border and length of segment
	let tx = Math.floor(Math.random()*(elite[0].length-(seg+1)))+1;		
	let ty = Math.floor(Math.random()*(elite.length-(seg+1)))+1;		
	let tp = getTilePattern(elite, tx, ty, 3);

	//overwrite map with tile pattern
	let mx = Math.floor(Math.random()*(curMap[0].length-(seg+1)))+1;		
	let my = Math.floor(Math.random()*(curMap.length-(seg+1)))+1;		

	for(let s=0;s<tp.length;s++){
		let nc = tp.charAt(s);
		if(curMap[mx+Math.floor(s/seg)][my+(s%seg)] != '_')		//failsafe for border
			curMap[mx+Math.floor(s/seg)][my+(s%seg)] = (nc == "." ? " " : nc);
	}

	//allow more evolution since map was changed
	document.getElementById('evolveBtn').classList.remove('lockImg');
	resetRules();

	//make PCG an author for modifying
	authorEdits['pcg'] = true;
}



// GET THE ELITE LEVEL SET TO USE FOR MUTATION AND EVOLUTION
function makeLevelInputSet(){
	let a = [];

	//get original demo levels to act as "elite"
	for(let d=0;d<demoLevels.length;d++){
		a.push(demoLevels[d]);
	}

	//check if the elite levels have already been made (if not make them)
	if(sort_eliteLevels.length == 0){
		//map levels to ratings
		let lev2Rating = {};
		for(let i=0;i<eliteLevels.length;i++){
			let rat = eliteRatings[i];
			lev2Rating[eliteLevels[i]] = (parseInt(rat[0])*parseInt(rat[1]));
		}

		//sort levels by ratings
		let levs = Object.keys(lev2Rating);
		levs.sort(function(a,b){
			return lev2Rating[b] - lev2Rating[a];
		});

		//add top 50 elite levels
		sort_eliteLevels = [];
		for(let i=0;i<Math.min(50,levs.length);i++){
			sort_eliteLevels.push(levs[i]);
		}
	}

	//get elite database levels
	if(sort_eliteLevels.length > 0){
		for(let e=0;e<sort_eliteLevels.length;e++){
			a.push(parseMap(sort_eliteLevels[e]));
		}
	}
	return a;
}

// RETURNS A STRING REPRESENTATION OF THE TILE PATTERN IN THE LEVEL AT A SPECIFIC POSITION
function getTilePattern(lvl, x, y, windowSize){
	let tp = "";
	for(let w1=0;w1<windowSize;w1++){
		for(let w2=0;w2<windowSize;w2++){
			if(lvl[y+w1][x+w2] == " ")
				tp += ".";
			else
				tp += lvl[y+w1][x+w2];
		}
		//tp+="\n";
	}

	return tp;
}

////////////////////        ETPKL-DIV FUNCTIONS      ///////////////////////////

// STEP THROUGH THE EVOLUTIONARY ALGORITHM AND UPDATE THE MAP
function evolve(){
	let interval = 50;			//interval for showing updates to the map

	//update variables
	minFit = 1.0 - parseFloat(document.getElementById("minFit").value);
	maxIter = parseInt(document.getElementById("maxIter").value);

	//start
	if(st == 0){
		let inputSet = makeLevelInputSet();

		document.getElementById('evolveBtn').classList.remove('lockImg');
		document.getElementById('evolveBtn').classList.add('functImg');

		document.getElementById('playPause').innerHTML = "❙❙";

		let poppy = new Population(10, inputSet, 3);
		st = setInterval(function(){
			step(poppy);

			let best_map = poppy.chromosomes[0];
			curMap = parseMap(best_map.ascii_map);
			mapWidth = curMap[0].length;
			mapHeight = curMap.length;

			//show current best fitness on screen
			curBestObj = best_map.obj_constraint;
			curBestFit = best_map.fitness;
			showCurFit = true;
			curIter = poppy.iteration;

			resetMap();

			if(best_map.fitness <= minFit || (maxIter != -1 && poppy.iteration >= maxIter)){
				clearInterval(st);
				st = 0;
				document.getElementById('evolveBtn').classList.remove('functImg');
				document.getElementById('evolveBtn').classList.add('lockImg');

				document.getElementById('playPause').innerHTML = "&nbsp;";

				resetRules();

				showCurFit = false;
			}

		}, interval);

		//make PCG an author for modifying
		authorEdits['pcg'] = true;
	}
	//stop/pause
	else{
		clearInterval(st);
		st = 0;
		document.getElementById('evolveBtn').classList.remove('functImg');

		document.getElementById('playPause').innerHTML = "►";

		resetRules();

		showCurFit = false;
	}
}


// REPRESENTATION FOR THE EVOLUTION
function Chromosome(amap, constraint, tp_fitness, fitness, objConstr=0.0){
	this.ascii_map = amap;
	this.constraint = constraint;
	this.tp_fitness = tp_fitness;
	this.fitness = fitness;
	this.obj_constraint = 0.0;
}

// DUPLICATE A CHROMOSOME AND COPY ITS PROPERTIES
function copyChromosome(c){
	return new Chromosome(c.ascii_map, c.constraint, c.tp_fitness, c.fitness, c.obj_constraint);
}

// POPULATION REPRESENTATION CLASS
function Population(maxPopSize, inputLevels, windowSize){
	this.maxPopSize = maxPopSize;
	this.chromosomes = [];
	this.windowSize = windowSize;
	this.inputTPS = makeBabaTPDict(inputLevels, windowSize);
	this.iteration = 0;
}


// MAKES OCCURRENCE DICTIONARY FOR THE TILE PATTERNS FOUND IN A SET OF INPUT LEVELS
function makeBabaTPDict(input_levels, windowSize){
	let levelDicts = {};
	for(let l=0;l<input_levels.length;l++){
		levelDicts[l] = makeLevelTPDict(input_levels[l], windowSize);
	}
	return levelDicts;
}	

// MAKES AN OCCURREENCE DICTIONARY FOR THE TILE PATTERNS IN A SINGLE LEVEL
function makeLevelTPDict(lvl, windowSize){
	let tpDict = {};
	let rows = lvl.length;
	let cols = lvl[0].length;
	for(let y=0;y<=(rows-windowSize);y++){
		for(let x=0;x<=(cols-windowSize);x++){
			let tp = getTilePattern(lvl, x,y, windowSize);
			if(tp in tpDict)
				tpDict[tp]++;
			else
				tpDict[tp] = 1;
		}
	}

	return tpDict;
}

// CALCULATES THE FITNESS FROM 2 TP DICTIONARIES (2 LEVELS) WITH SOME WEIGHT VALUE
// BASED ON SIMON LUCAS PAPER
function calcFitness(p,q,w,e){
	//get the total values
	let p_total = Object.values(p).reduce((a,b)=>a+b);
	let q_total = Object.values(q).reduce((a,b)=>a+b);

	//get the unique tile patterns from both dictionaries
	let bothTPS = [];
	bothTPS.push.apply(bothTPS, Object.keys(p));
	bothTPS.push.apply(bothTPS, Object.keys(q));
	let allTPs = bothTPS.filter((value, index, self) => {return self.indexOf(value) === index;});

	let d_pq = 0;
	let d_qp = 0;
	for(let k=0;k<allTPs.length;k++){
		let x = allTPs[k];

		let pp = ((x in p ? p[x] : 0)+e)/((p_total+e)*(1+e));
		let qp = ((x in q ? q[x] : 0)+e)/((q_total+e)*(1+e));

		d_pq += pp*Math.log(pp/qp);
		d_qp += qp*Math.log(qp/pp);
	}

	return ((w*d_pq)+((1-w)*d_qp));
}

// CALCULATE FITNESS VALUES FOR A WHOLE POPULATION
function calcPopulationFitness(p, weight){
	for(let c=0;c<p.chromosomes.length;c++){
		calcChromosomeFitness(p.chromosomes[c], p.inputTPS, p.windowSize, weight);
	}
}

// GET RANDOM SELECTION OF KEYS FROM A SET
function randKeys(keys, n){
	let arr = [];
	for(let k=0;k<n;k++){
		arr.push(keys[Math.floor(Math.random()*keys.length)]);
	}
	return arr;
}

// COMPARES A CHROMOSOME MAP TO THE INPUT SET TO GET THE CLOSEST MATCH 
function calcChromosomeFitness(c, tp_set, windowSize, weight){
	let c_tp = makeLevelTPDict(parseMap(c.ascii_map), windowSize);
	let f_set = [];
	let tKeys = Object.keys(tp_set);

	//use a random set of keys for the input
	//tKeys = randKeys(tKeys, Math.floor(Math.random()*(tKeys.length-3))+3);

	for(let t=0;t<tKeys.length;t++){
		let f = calcFitness(c_tp, tp_set[tKeys[t]], weight, 1);
		f_set.push(Math.abs(f));
	}

	//save the smallest fitness value as the fitness
	c.tp_fitness = Math.min.apply(null, f_set);
	c.fitness = alterFitness(c);
}

// MAKES A SET OF RANDOM CHROMOSOMES OF A SET POPULATION SIZE
function randomChromosomes(size){
	let chroms = [];
	for(let p=0;p<size;p++){
		//let rando_map = makeBiasMap(0.8, 0.9);
		let c;
		if(curMap.length == 0){
			let rando_map = makeRandomMap(0.0);
			let rms = map2Str(rando_map);
			c = new Chromosome(rms, isSemiPlayableMap(rms),1, 0.0);		//makeRandomMap() guarentees playability
		}else{
			let cms = map2Str(curMap)
			c = new Chromosome(cms,isSemiPlayableMap(cms),cms,0.0);
		}

		
		chroms.push(c);
	}
	return chroms;
}

// SORTS THE CHROMOSOMES BASED ON THEIR FITNESS
// => MAXIMUM OBJ_CONSTRAINT AND MINIMUM FITNESS
function sortChromosomes(chromos){
	chromos.sort((c1, c2) => 
		{
			if((c1.obj_constraint == c2.obj_constraint) || (c1.obj_constraint > OBJ_CONS_THR && c2.obj_constraint > OBJ_CONS_THR))
				return (c1.fitness - c2.fitness);
			return (c2.obj_constraint - c1.obj_constraint);
		}
	);
}

// APPLIES OBJECTIVE CONSTRAINT TO CHROMOSOME BASED ON ACTIVE OBJECTS
function objectiveConstr(m){
	//get current rules and potential future rules
	let curRules = getActiveMapRules(parseMap(m));
	let futRules = findFutureRules(m);

	//get active objectives
	objList = Object.keys(activeRules);
	actObjs = objList.filter(r => activeRules[r]);

	//no active objectives so constraint is 1 (freestyle) -- prevent divide by 0 error
	if(actObjs.length == 0)
		return 1.0;

	//get the potential chromosome representation
	let potRep = getChromosomeRep(curRules,futRules);

	//count number of activated objectives within potential rules
	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
	let matchNum = 0;
	for(let a=0;a<actObjs.length;a++){
		let i = allRules.indexOf(actObjs[a]);
		if(potRep[i] == "1")
			matchNum++;
	}

	//console.log(matchNum + " / " + actObjs.length);

	//return match number over number of active objectives
	return matchNum / actObjs.length;

}

// CALCULATES THE OBJECTIVE CONSTRAINT FOR A WHOLE POPULATION OF CHROMOSOMES
function calcPopulationObjConstraint(p){
	for(let i=0;i<p.length;i++){
		p[i].obj_constraint = objectiveConstr(p[i].ascii_map);
	}
}

// FINDS THE POTENTIAL FOR FUTURE RULES TO OCCUR IN A MAP
function findFutureRules(m){
	let futureRules = []

	//break up map into tile sets
	let tcts = getTileCts(m);							//counts of all tiles
	let ats = Object.keys(tcts);						//all tiles
	let aow = ats.filter(x => x.match(/[A-Z]/));		//all object word tiles
	let akw = ats.filter(x => x.match(/[0-9]/));		//all keyword tiles

	//can't have rules without an "IS" keyword
	if(!inArr(akw, '1'))
		return futureRules;



	//a - x is x = 2 copies of word + x
	for(let a=0;a<aow.length;a++){
		if(tcts[aow[a]] > 1){
			futureRules.push("x-is-x");
			break;
		}
	}

	//b - x is y = x and y word
	if(aow.length > 1)
		futureRules.push("baba-is-keke");		//arbitrary expression for use with getChromosomeRep

	//c - x is push = push word on map
	if(inArr(akw,'5'))
		futureRules.push("x-is-push");

	//d - x is move = move word on map
	if(inArr(akw,'7'))
		futureRules.push("x-is-move");
 
	//e - x is stop = stop word on map
	if(inArr(akw,'6'))
		futureRules.push("x-is-stop");

	//f - x is kill = kill word on map
	if(inArr(akw,'4'))
		futureRules.push("x-is-kill");

	//g - x is sink = sink word on map
	if(inArr(akw,'5'))
		futureRules.push("x-is-sink");

	//h - x-is-[pair] = melt and hot word on map
	if(inArr(akw,'8') && inArr(akw, '9'))
		futureRules.push("x-is-[pair]");

	//i - [x,y] is you = x and y word (same as x-is-y)
	if(aow.length > 1){
		futureRules.push("x-is-you");
		futureRules.push("y-is-you");
	}
		

	return futureRules;
}

// RETURNS ALL THE WORD TILES FROM A MAP
function getTileCts(m){
	let wt = {};
	for (let i=0;i<m.length;i++){
		let ch = m[i]
		if(ch.match(/[a-zA-Z0-9]/)){
			if(!(ch in wt))
				wt[ch] = 1;
			else
				wt[ch]++;
		}
	}
	return wt;
}

// ALTERNATIVE FITNESS FUNCTION
// USES ETPKLDiv FITNESS, PLAYABILITY OF THE LEVEL, RATIO OF USELESS OBJECTS+WORDS, AND RATIO OF EMPTY SPACE
function alterFitness(c){
	let f = c.tp_fitness;
	let s = c.constraint == 0 ? 1 : 0;
	let o = uselessObjects(c.ascii_map);
	let w = uselessWords(c.ascii_map);
	let e = emptyRatio(c.ascii_map);

	let u = ((0.85)*o+(0.15)*w);		//useless words+objects
	return f+s+u+(0.1)*e;
}

// GET THE RATIO OF OBJECTS THAT ARE UNUSED TO ALL OBJECTS
function uselessObjects(m){

	let objTot = {};
	for(let r=0;r<m.length;r++){
		let charac = m[r];
		if(charac.match(/[a-z]/)){
			if(!(charac in objTot))
				objTot[charac] = 0;
			if(!m.includes(charac.toUpperCase()))
				objTot[charac]+=1;
		}
		
	}

	//no objects at all - max out
	if(Object.keys(objTot).length == 0)
		return 1;

	return Object.values(objTot).reduce((a,b) => a+b, 0) / (Object.keys(objTot).length);
}

// GET THE RATIO OF WORDS THAT ARE UNUSED TO ITS OBJECTS ON THE MAP
function uselessWords(m){
	let wordTot = {};
	for(let r=0;r<m.length;r++){
		let charac = m[r];
		if(charac.match(/[A-Z]/)){
			if(!(charac in wordTot))
				wordTot[charac] = 0;
			if(!m.includes(charac.toLowerCase()))
				wordTot[charac]+=1;
		}
	}

	//no words at all - max out
	if(Object.keys(wordTot).length == 0)
		return 1;

	return Object.values(wordTot).reduce((a,b) => a+b, 0) / (Object.keys(wordTot).length);
}

// GETS THE RATIO OF EMPTY TILES ON THE MAP TO TOTAL TILES
function emptyRatio(m){
	let m2d = parseMap(m);
	let totalChars = m.replace("\n", "").length;

	let totEmpty = 0;
	for(let r=0;r<m2d.length;r++){
		for(let c=0;c<m2d[0].length;c++){
			if(m2d[r][c] == " ")
				totEmpty++;
		}
	}
	return totEmpty / totalChars;
}

// CHECK IF MAP CONTAINS X-IS-YOU RULE
function has_XISYOU(m2d){
	let m = map2Str(m2d);

	//no "YOU" or "IS" on map = not playable
	if(!m.includes("1") || !m.includes("2"))
		return false;

	for(let r=1;r<m2d.length-1;r++){
		for(let c=1;c<m2d[0].length-1;c++){
			if(m2d[r][c] == "1"){ 
				if(m2d[r][c+1] == "2" && m2d[r][c-1].match(/[A-Z]/) && m.includes(m2d[r][c-1].toLowerCase()))
					return true;
				if(m2d[r+1][c] == "2" && m2d[r-1][c].match(/[A-Z]/)  && m.includes(m2d[r-1][c].toLowerCase()))
					return true;
			}
		}
	}

	return false;
}


// CHECK IF MAP CAN BE THEORETICALLY BEATABLE BY CONTAING X-IS-YOU AND WIN WORD TILE
function isSemiPlayableMap(m){
	return (has_XISYOU(m) && map2Str(m).includes("3") ? 1 : 0);
}

// RETURNS RANDOMLY SELECTED CHROMOSOME - LAST HAS HIGHEST PROBABILITY
// BASED ON AMIDOS2006'S CODE
function rankChromosomes(chromos){
	let prob=[];
	for(let i=0;i<chromos.length;i++){
		prob.push(i+1);
		if(i>0)
			prob[i] += prob[i-1];
	}

	let total = prob[prob.length-1];
	let r = Math.random();
	for(let i=0;i<chromos.length;i++){
		if(r < (prob[i] / total))
			return chromos[i];
	}
	return chromos[chromos.length-1];
}


// MUTATES ONCE BY FLIPPING A TILE
function mutateFlipChromosome(c){
	let m = parseMap(c.ascii_map);

	var x = Math.floor(Math.random()*(m[0].length-2))+1;
	var y = Math.floor(Math.random()*(m.length-2))+1;

	//replace with a new character
	var newChar = "";
	do{
		newChar = allChars[Math.floor(Math.random()*allChars.length)];		//make an object character
	}while(m[y][x] == newChar);
	m[y][x] = newChar;

	return new Chromosome(map2Str(m), isSemiPlayableMap(m), 0.0, 0.0);
}


// REPLACES SEGMENTS OF A CHROMOSOME WITH A RANDOM SEGMENT
function mutateTPReplaceChromosome(c, tp_set){
	let m = parseMap(c.ascii_map);

	//pick random tile 
	let lvlKeys = Object.keys(tp_set);
	let lvl = tp_set[lvlKeys[Math.floor(Math.random()*lvlKeys.length)]];
	let tpKeys = Object.keys(lvl);
	let tp = tpKeys[Math.floor(Math.random()*tpKeys.length)];
	let size = parseInt(Math.sqrt(tp.length));

	//determine if specific border area (corner or wall)
	let hardPos = tpIsWall(tp, size, m[0].length, m.length);

	//assign placement of tp
	var x = Math.floor(Math.random()*((m[0].length-size-1)-2))+2;
	var y = Math.floor(Math.random()*((m.length-size-1)-2))+2;

	if(hardPos[0] != -1)
		x = hardPos[0];
	if(hardPos[1] != -1)
		y = hardPos[1];

	//replace tiles
	let index = 0;
	for(let r=y;r<y+size;r++){
		for(let c=x;c<x+size;c++){
			m[r][c] = tp.charAt(index);
			index++;
		}
	}

	//return the new map
	return new Chromosome(map2Str(m), isSemiPlayableMap(m), 0.0, 0.0);
}

// DETERMINES IF A TILE PIECE IS A CORNER OR WALL
function tpIsWall(tp, size, width, height){
	//corners
	if(tp.charAt(0) == "_" && tp.charAt(1) == "_" && tp.charAt(size) == "_")										//top -left corner
		return [0,0];
	if(tp.charAt(size-1) == "_" && tp.charAt(2*size-1) == "_" && tp.charAt(size-2) == "_")							//top-right corner
		return [width-size, 0];
	if(tp.charAt((size-1)*size) == "_" && tp.charAt(((size-1)*size)+1) == "_" && tp.charAt((size-2)*size) == "_")	//bottom-left corner
		return [0, height-size];
	if(tp.charAt((size*size)-1) == "_" && tp.charAt((size*size)-2) == "_" && tp.charAt(((size-1)*size)-1) == "_")	//bottom-right corner
		return [width-size, height-size];

	//walls
	if(tp.charAt(1) == "_")					//top 
		return [-1, 0];
	if(tp.charAt(size) == "_")				//left
		return [0, -1];
	if(tp.charAt((2*size)-1) == "_")		//right
		return [width-size, -1];
	if(tp.charAt((size*size)-2) == "_")		//bottom
		return [-1, height-size];

	return [-1,-1];
}


// STEP THROUGH POPULATION MUTATION ITERATION
// BASED ON AMIDOS2006'S [ETPKLDIV] CODE 
function step(population){
	//initialize random chromosomes and get their evaulations
	if(population.chromosomes.length == 0){		
		population.chromosomes = randomChromosomes(population.maxPopSize);

		calcPopulationObjConstraint(population.chromosomes);
		calcPopulationFitness(population, 0.5);
		sortChromosomes(population.chromosomes);
	}

	//create a copy mutated population
	let mutate_chromos = [];
	for(let m=0;m<population.maxPopSize;m++){
		let mc = rankChromosomes(population.chromosomes);
		//mutate_chromos.push(mutateFlipChromosome(mc));
		mutate_chromos.push(mutateTPReplaceChromosome(mc, population.inputTPS));
	}


	//evaluate
	population.chromosomes.push.apply(population.chromosomes, mutate_chromos);
	calcPopulationObjConstraint(population.chromosomes);
	calcPopulationFitness(population, 0.5);
	sortChromosomes(population.chromosomes);

	//console.log("and....");
	//showChromoFit(population.chromosomes);

	//remove last half
	population.chromosomes.splice(population.maxPopSize);

	//console.log("SPLIT!");
	//showChromoStats(population.chromosomes);

	//increase iteration
	population.iteration += 1;
}

// DEBUG TO SHOW INFORMATION ABOUT THE CHROMOSOME SET
function showChromoStats(chromos){
	for(let c=0;c<chromos.length;c++){
		console.log(chromos[c].ascii_map +"\n" + chromos[c].fitness + "\n" + chromos[c].constraint);
		//console.log("f: " + chromos[c].tp_fitness + "\nu: " + uselessObjects(chromos[c].ascii_map) + "\ne: " + emptyRatio(chromos[c].ascii_map) + "\ns: " + (isSemiPlayableMap(chromos[c].ascii_map) == 0 ? 1 : 0));
	}
}

// DEBUG TO SHOW ONLY THE FITNESS INFO ABOUT THE CHROMOSOME SET
function showChromoFit(chromos){
	let totFit = [];
	for(let c=0;c<chromos.length;c++){
		totFit.push(chromos[c].obj_constraint + " - " + chromos[c].fitness.toFixed(2));
		//console.log("f: " + chromos[c].tp_fitness + "\nu: " + uselessObjects(chromos[c].ascii_map) + "\ne: " + emptyRatio(chromos[c].ascii_map) + "\ns: " + (isSemiPlayableMap(chromos[c].ascii_map) == 0 ? 1 : 0));
	}
	console.log(totFit);
}

/////////////////          OBJECTIVE LIST FUNCTIONS         ///////////////////

// CONVERT THE LOCAL STORY ACTIVE RULES STRING TO HASH REPRESENTATION
function setInitActiveRules(s){
	if(s == "NO-RULES")
		return;

	let ruleset = ["A","B","C","D","E","F","G","H","I"];
	let parts = (s != "" ? s.split("_") : []);

	//fill in active rules
	for(let r=0;r<ruleset.length;r++){
		//set default to false
		activeRules[ruleset[r]+"1"] = false;
		activeRules[ruleset[r]+"2"] = false;

		//find active rules
		if(inArr(parts, '1'))
			activeRules[ruleset[r]+"1"] = true;
		if(inArr(parts, '2'))
			activeRules[ruleset[r]+"2"] = true;
	}
}

// UPDATE THE LOCAL STORAGE REPRESENTATION STRING BASED ON THE TABLE
function updateRepStr(){
	let ruleset = ["A","B","C","D","E","F","G","H","I"];
	let repstr = "";

	//concatenate the string based on active rules
	for(let r=0;r<ruleset.length;r++){
		let cr = ruleset[r];
		if(activeRules[cr+"1"] && activeRules[cr+"2"])			//[A-I]12
			repstr += (repstr == "" ? "" : "_") + (cr+"12");
		else if(activeRules[cr+"1"] && !activeRules[cr+"2"])	//[A-I]1
			repstr += (repstr == "" ? "" : "_") + (cr+"1");
		else if(!activeRules[cr+"1"] && activeRules[cr+"2"])	//[A-I]2
			repstr += (repstr == "" ? "" : "_") + (cr+"2");
	}	

	//update the string
	localStorage.objRuleRep = repstr;
}

// SET ALL STARTING ACTIVE RULES IN THE OBJECTIVE TAB TABLE
function setActiveRuleTable(){
	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
	
	for(let r=0;r<allRules.length;r++){
		let curRule = allRules[r];
		document.getElementById("obj_"+curRule).innerHTML = (activeRules[curRule] || activeRules[curRule.charAt(0)+"0"] ? "X" : "&nbsp;");

		if(curRule.charAt(1) == "1")
			document.getElementById("obj_"+curRule+"_0").innerHTML = (activeRules[curRule] || activeRules[curRule.charAt(0)+"2"] ? "X" : "&nbsp;");
	}
}

// MAKES THE ACTIVE RULE SET FROM AN IMPORTED CHROMOSOME
function makeActiveRuleFromChromo(chromo){
	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];

	//activate rules
	for(let r=0;r<allRules.length;r++){
		activeRules[allRules[r]] = chromo.charAt(r) == "1";
	}
	setActiveRuleTable();

}

// TOGGLES AN ACTIVE COLUMN FOR THE OBJECTIVE TABLE 
function toggleActiveRule(col){
	let rule = col.id.split("_")[1]   							//parse the rule down to letter and number combination (i.e. A2, E1)
	let active = (col.innerHTML == "X" ? true : false);			//check if already active

	//toggle active state of objective
	col.innerHTML = (active ? "&nbsp;" : "X");
	active = !active;	
	activeRules[rule] = active;
	
	updateRepStr();
	
}	


// CONVERTS THE ACTIVE RULES FROM THE OBJECTIVE TABLE TO CHROMOSOME BINARY FORMAT
function activeRules2Chromo(){
	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
	let chr = "";
	for(let a=0;a<allRules.length;a++){
		chr += (activeRules[allRules[a]] ? "1" : "0");
	}
	return chr;
}

// GET THE CURRENTLY ACTIVE RULE SETS FROM THE MAP
function getActiveMapRules(m){
	let arules = [];
	let this_map = m;

	//find is connectors
	let iconn = [];
	for(let r=0;r<this_map.length;r++){
		for(let c=0;c<this_map[0].length;c++){
			if(this_map[r][c] == '1')
				iconn.push([r,c]);	//x,y format
		}
	}

	//get the surrounding words if any
	for(let i=0;i<iconn.length;i++){
		let is = iconn[i];
		//vertical
		if(this_map[is[0]-1][is[1]].match(/[A-Z]/) && this_map[is[0]+1][is[1]].match(/[0-9A-Z]/))
			arules.push(map_key[this_map[is[0]-1][is[1]]].split("_")[0] + "-is-" + map_key[this_map[is[0]+1][is[1]]].split("_")[0]);
		//horizontal
		if(this_map[is[0]][is[1]-1].match(/[A-Z]/) && this_map[is[0]][is[1]+1].match(/[0-9A-Z]/))
			arules.push(map_key[this_map[is[0]][is[1]-1]].split("_")[0] + "-is-" + map_key[this_map[is[0]][is[1]+1]].split("_")[0]);

	}

	return arules;
}



// CREATE NEW RANDOMIZED LIST
function newObjList(){

	//  -----  SELECT LEAST FREQUENT AND SIMPLEST RULE COMBOS ---- //

	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];

	let maxSet = 20;
	let choiceSet = [];

	let levChromos = eliteChromos.filter((item, i, ar) => ar.indexOf(item) === i);

	//go through all rule combinations and use the ones with the fewest unmade rules
	for(let i=0;i<Math.pow(2,allRules.length);i++){				//all rule combinations
		for(let j=0;j<allRules.length;j++){


			let s = i.toString(2).padEnd(allRules.length-j,"0").padStart(allRules.length,"0");

			//unmade rule
			if(!inArr(levChromos,s) && !inArr(choiceSet,s)){
				choiceSet.push(s)
			}

			//reached limit
			if(choiceSet.length >= maxSet)
				break

			//console.log(s)
		}
		//reached limit
		if(choiceSet.length >= maxSet)
			break
	}

	//console.log(choiceSet);

	let randoChromo = choiceSet[Math.floor(Math.random()*choiceSet.length)];
	let chosenRules = [];
	for(let c=0;c<allRules.length;c++){
		if(randoChromo[c] == "1"){
			chosenRules.push(allRules[c])
		}
	}

	/*

	// ----- USE RANDOM SELECTION OF RULES ----- //

	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
	let ruleAmt = Math.floor(Math.random()*(allRules.length))+1;		//pick random number of rules to use
	let chosenRules = [];

	//select subset of rules
	if(ruleAmt != allRules.length){
		//choose x random rules
		for(let i=0;i<ruleAmt;i++){
			let nextRule = "";
			do{
				nextRule = allRules[Math.floor(Math.random()*allRules.length)];
			}while(inArr(chosenRules, nextRule));
			chosenRules.push(nextRule);
		}
	}
	//use all of the rules
	else{
		chosenRules = allRules;
	}

	*/

	//reset hash table
	for(let r=0;r<allRules.length;r++){
		activeRules[allRules[r]] = false;
	}

	//add chosen rules to active rules and update
	for(let c=0;c<chosenRules.length;c++){
		activeRules[chosenRules[c]] = true;
	}
	setActiveRuleTable();
	updateRepStr();


}

// REMOVES ALL OBJECTIVES FROM THE TABLE
function clearObjList(){
	let allRules = ["A1","A2","B1","B2","C1","C2","D1","D2","E1","E2","F1","F2","G1","G2","H1","H2","I1","I2"];
	//reset hash table
	for(let r=0;r<allRules.length;r++){
		activeRules[allRules[r]] = false;
	}

	setActiveRuleTable();
	updateRepStr();				//update the local storage rep string

}

// TOGGLE BETWEEN THE SIMPLE AND ADVANCED OBJECTIVE LIST TABLES
function toggleTable(btn){

	setActiveRuleTable();

	objMode = (objMode == "simple" ? "advanced" : "simple");

	if(objMode == "advanced"){
		tableSim.style.display = "none";
		tableAdv.style.display = "block";
	}else{
		tableSim.style.display = "block";
		tableAdv.style.display = "none";
	}

	btn.innerHTML = (objMode == "simple" ? "Advanced" : "Simple") + " List";
}

// DISPLAY IN THE TABLES THE CURRENTLY ACTIVE RULES ON THE LEVEL
function showActiveRules(){

	//get the initial rules and the end rules (if they exist)
	let ir = ar;
	let er = JSON.parse(localStorage.getItem("endRules"));
	if(er == null)
		er = [];

	//no rules so display nothing
	if(ir.length == 0 && er.length == 0)
		return;

	//reset background color for all objective text cells
	let obj_texts = document.getElementsByClassName("obj_text");
	for(let o=0;o<obj_texts.length;o++){
		obj_texts[o].style.backgroundColor = "transparent";
	}


	let chromoStr = translateChromo(getChromosomeRep(ir,er));		//translate to chromosome string

	//no rules so display nothing
	if(chromoStr == "NO-RULES")
		return;

	//change background color of all table cells based on activation of chromosome
	let ruleSplit = chromoStr.split("_");

	//simple table
	for(let r=0;r<ruleSplit.length;r++){
		let ruleChar = ruleSplit[r].charAt(0);
		document.getElementById("obj_" + ruleChar+"1_0").style.backgroundColor = highlight_color;
		document.getElementById("obj_" + ruleChar+"X_0").style.backgroundColor = highlight_color;
	}

	//advanced table
	for(let r=0;r<ruleSplit.length;r++){
		let rule = ruleSplit[r];
		let ruleChar = rule.charAt(0);
		document.getElementById("obj_" + ruleChar+"X").style.backgroundColor = highlight_color;

		//both rules present
		if(rule.length > 2){
			document.getElementById("obj_" + ruleChar+"1").style.backgroundColor = highlight_color;
			document.getElementById("obj_" + ruleChar+"2").style.backgroundColor = highlight_color;
		}
		//first rule
		else if(rule.charAt(1) == "1")
			document.getElementById("obj_" + ruleChar+"1").style.backgroundColor = highlight_color;
		//second rule
		else if(rule.charAt(1) == "2")
			document.getElementById("obj_" + ruleChar+"2").style.backgroundColor = highlight_color;
	}

}



/////////////////////        TESTER FUNCTIONS        //////////////////////////

// TEST THE EDITED MAP IN THE GAME WINDOW
function levelTest(control){
	var url = "mini_game.php";
	if (!window.screenTop && !window.screenY) {		//if the window is full screened - use an alternative game screen
	    url = "game.php";
	}
	var width = 660;
	var height = 610;

	//check for valid map
	if(!checkValidity())
		return;

	//export the author
	localStorage.author = CURRENT_USER;

	
	if(!authorEdits['user'] && authorEdits['pcg'])
		localStorage.author = "PCG.js";
	else if(authorEdits['user'] && authorEdits['pcg'])
		localStorage.author = (CURRENT_USER + " + PCG.js");
	

	//export the map
	localStorage.testMap = map2Str(curMap);
	localStorage.editMap = map2Str(curMap);		//save same map on refresh (draft)
	localStorage.control = control;
	localStorage.bestSolution = "";

	//close any already open test windows
	if(testWindow && !testWindow.closed){
		testWindow.close();
	}

	//set popup window properties
	var winWidth = width;
	  var winHeight = height;
	  var winLeft = (screen.width-winWidth)/2;
	  var winTop = (screen.height - winHeight)/2 - 30;
	  var winOptions = ",width=" + winWidth;
	  winOptions += ",height=" + winHeight;
	  winOptions += ",left=" + winLeft;
	  winOptions += ",top=" + winTop;
	  winOptions += ",menubar=no,location=no,resizable=no,scrollbars=no, toolbar=no"
	  testWindow = window.open(url, "newWindow", winOptions);
	  testWindow.onbeforeunload = function(){
	  	path = null;
	  	EXIT_LOOP = true;
	  }
	  setTimeout(function(){
	    if(!testWindow || testWindow.outerHeight === 0)
	      alert("Please disable the pop up blocker to test your level!");
	  },10);


}

// CHECK IF ALL OF THE SELECTED OBJECTIVES FROM THE TABLE CAN BE COMPLETED
function allObjOnMap(){
	//advanced rules check if rule is active (cheat by using highlight) or if potentially rule is available
	if(objMode == "advanced"){
		//initial rules
		let allRules = ["A1","B1","C1","D1","E1","F1","G1","H1","I1"];
		for(let r=0;r<allRules.length;r++){
			let o = document.getElementById("obj_" + allRules[r]);
			if(o.innerHTML == "X" && o.style.backgroundColor == "transparent"){
				return false;
			}
		}

		//ending rules (check if keyword on map)
		allRules = ["A2","B2","C2","D2","E2","F2","G2","H2","I2"];
		condChar = ["","","5","7","6","4","0","89",""]
		for(let r=0;r<allRules.length;r++){
			let o = document.getElementById("obj_" + allRules[r]);
			if(condChar[r] == "" || o.innerHTML != "X")
				continue;
			let chs = condChar[r].split("");
			for(let c=0;c<chs.length;c++){
				if(!map2Str(curMap).includes(chs[c])){
					return false;
				}
			}
			
		}
	}
	//check if character on the map at all to have potential for rule (simple)
	else{
		let allRules = ["A1_0","B1_0","C1_0","D1_0","E1_0","F1_0","G1_0","H1_0","I1_0"];
		condChar = ["","","5","7","6","4","0","89",""]
		for(let r=0;r<allRules.length;r++){
			let o = document.getElementById("obj_" + allRules[r]);
			if(condChar[r] == "" || o.innerHTML != "X")
				continue;
			let chs = condChar[r].split("");
			for(let c=0;c<chs.length;c++){
				if(!map2Str(curMap).includes(chs[c])){
					return false;
				}
			}
			
		}
	}
	return true;
}

// CHECK TO SEE IF THE LEVEL IS VALID TO BE WON AND PLAYED
function checkValidity(){
	var mapStr = map2Str(curMap);
	var hasWin = mapStr.includes(reverseChar("win_word"));
	var hasYou = mapStr.includes(reverseChar("you_word"));

	if(!hasWin)
		return confirm("Your map is missing the 'WIN' word\nPlay anyways?");
	else if(!hasYou)
		return confirm("Your map is missing the 'YOU' word\nPlay anyways?");
	else if(!allObjOnMap())
		return confirm("Your map may not complete all of the selected objectives\nPlay anyways?")
	else
		return true;
}



/////////////////////        SCREEN FUNCTIONS        //////////////////////////

function showControlType(con){
	controlTxt.innerHTML = con + "<br>TESTER";
}
function noControlType(){
	controlTxt.innerHTML = "&nbsp;";
}


// INITIALIZING FUNCTION CALLED WHEN SCREEN IS LOADED
function init(){
	makeProbElite();
	if(!localStorage.editMap || (localStorage.editMap == ""))
		startBlank();
	else
		startTemp(localStorage.editMap);
}

// START EDITOR WITH A BLANK CANVAS
function startBlank(){
	//get the tiles ready
	makeImgHash();

	//draw the first map
	clearMap();
	renderMap();

	//input listeners
	editor.addEventListener("click", paint, false);
	editor.onselectstart = function(){return false};
	mapInW.onchange = function(){changeMapSize()};
	mapInH.onchange = function(){changeMapSize()};

	//calculate initial map offset
	calcOffset();
	changeTool('paint');

	makeHistory();

	//set tile lists
	allWords = setAllWords();
	allObjs = setAllObjs();
	let ww = setAllWordTypes();
	allAdjWords = ww[0];
	allObjWords = ww[1];

	//shuffle all of the maps
	shuffleAllLevels();

	//set the current active rules
	let curActiveRuleStr = (localStorage.objRuleRep != undefined ? localStorage.objRuleRep : "");
	setInitActiveRules(curActiveRuleStr);
	setActiveRuleTable();


	//set stored rules if available from localStorage
	if(localStorage.chromo && localStorage.chromo != "")
		makeActiveRuleFromChromo(localStorage.chromo);
}

// START EDITOR WITH A PREMADE LEVEL
function startTemp(map){
//get the tiles ready
	makeImgHash();

	//draw the first map
	importMap(parseMap(map));

	//input listeners
	editor.addEventListener("click", paint, false);
	editor.onselectstart = function(){return false};
	mapInW.onchange = function(){changeMapSize()};
	mapInH.onchange = function(){changeMapSize()};

	//calculate initial map offset
	calcOffset();
	changeTool('paint');

	makeHistory();

	//set tile lists
	allWords = setAllWords();
	allObjs = setAllObjs();
	let ww = setAllWordTypes();
	allAdjWords = ww[0];
	allObjWords = ww[1];

	//shuffle all of the maps
	shuffleAllLevels();

	//set the current active rules
	let curActiveRuleStr = translateChromo(localStorage.chromo);
	setInitActiveRules(curActiveRuleStr);
	setActiveRuleTable();
}

// UPDATE THE CANVAS AND RERENDER
function update(){
	requestAnimationFrame(update);
	renderMap();

	if(testWindow && !testWindow.closed && localStorage.getItem("endRules") != null)
		showActiveRules();

	if(!mouseIsDown){
		if(!lasso.locked)
			lasso.canLasso = true;
	}
}
update();
