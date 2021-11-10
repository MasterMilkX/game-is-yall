
var showHelp = false;

var gifID = {
	"paintTool" : "tut_gifs/level_editor/le_p2-paint.gif",
	"eraseTool": "tut_gifs/level_editor/le_p3-eraser.gif",
	"selectTool": "tut_gifs/level_editor/le_p6-select_tool.gif",
	"undoTool": "tut_gifs/level_editor/le_p4-undo_redo.gif",
	"redoTool": "tut_gifs/level_editor/le_p4-undo_redo.gif",

	"diceTool": "tut_gifs/mutations/m_p2-dice.gif",
	"dnaTool": "tut_gifs/mutations/m_p3-dna.gif",
	"evolveTool": "tut_gifs/mutations/m_p4-evolve.gif",
	"fitness":"tut_gifs/mutations/m_p5-fitness.gif",
	"iterations":"tut_gifs/mutations/m_p6-iterations.gif",

	"x-is-x":"tut_gifs/rules/x-is-x.gif",
	"x-is-y":"tut_gifs/rules/x-is-y.gif",
	"x-is-push":"tut_gifs/rules/x-is-push.gif",
	"x-is-move":"tut_gifs/rules/x-is-move.gif",
	"x-is-stop":"tut_gifs/rules/x-is-stop.gif",
	"x-is-kill":"tut_gifs/rules/x-is-kill.gif",
	"x-is-sink":"tut_gifs/rules/x-is-sink.gif",
	"x-is-pair":"tut_gifs/rules/x-is-pair.gif",
	"xy-is-you":"tut_gifs/rules/xy-is-you.gif",
	"newlist":"tut_gifs/objectives/new.gif",
	"clearlist":"tut_gifs/objectives/clear.gif",
	"simpadvlist":"tut_gifs/objectives/simple_advanced.gif",

	"blankMaps":"tut_gifs/maps/blank.gif",
	"basicMaps":"tut_gifs/maps/basic.gif",
	"randomMaps":"tut_gifs/maps/random.gif",
	"eliteMaps":"tut_gifs/maps/elite.gif",
	"userMaps":"tut_gifs/maps/my_level.gif",
	"shuffle":"tut_gifs/maps/shuffle.gif",
	"lock":"tut_gifs/maps/objectives.gif",
	"confirm":"tut_gifs/maps/confirm_reject.gif",
	"reject":"tut_gifs/maps/confirm_reject.gif",

	"map_dim":"tut_gifs/level_editor/le_p7-dimensions.gif",
	"tester":"tut_gifs/level_editor/le_p8-testing.gif"


}



//helper div
let offset = [20,50];
var helpDiv;
helpDiv = document.createElement("div");
helpDiv.style.position = "absolute";
helpDiv.style.left = "0px";
helpDiv.style.top = "0px";
helpDiv.style.border = "3px solid red";
helpDiv.style.background = "black";
helpDiv.style.zIndex = "2";
helpDiv.style.display = "none";
helpDiv.style.margin = "auto";

var helpGif = document.createElement("img");
helpGif.style.width = "275px";
helpGif.style.height = "200px";
helpGif.src = "placeholder_gifs/1.gif";
helpDiv.appendChild(helpGif);

document.body.appendChild(helpDiv);

//show helper div 
function showHelpPopup(e,id){
	if(!showHelp)
		return;

	mousePosition = {
        x : e.clientX,
        y : e.clientY

    };
    helpDiv.style.left = (mousePosition.x + offset[0]) + 'px';
    helpDiv.style.top  = (mousePosition.y + offset[1]) + 'px';
	helpDiv.style.display = "block";
	helpGif.src = gifID[id];

	//special cases
	if(id == "lock" && document.getElementById("lockBtn").src == "flaticon/scale-lock.png")
		helpGif.src = "tut_gifs/maps/dimensions.gif"

	if(id.includes("-is-")){
		helpGif.style.width = "200px";
		helpGif.style.height = "200px";
	}else{
		helpGif.style.width = "275px";
		helpGif.style.height = "200px";
	}
}

//hide helper div
function hideHelpPopup(){
	helpDiv.style.display = "none";
}

//toggle whether to show help gifs when hovering over an item
function toggleHelpHover(i){
	showHelp = !showHelp;
	i.src = (showHelp ? "img/help_on.png" : "img/help.png");
	i.title = (showHelp ? "Hover over an icon to see what it does" : "Click me to toggle help gifs")
}
