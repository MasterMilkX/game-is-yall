
// CHECKS THE STATE OF THE TUTORIAL TO CONTINUE TO THE NEXT
function tutMain(){
	requestAnimationFrame(tutMain);

	if(demo){
		nextLevelInstr(curLevel);
	}

	//goto next screen when the player wins the last demo level
	if(demo && curLevel == demoLevels.length && wonGame && getCurTut() == 0){
		demo = false;
		wonGame = false;
		curLevel = 0;
		resetTut();
		$('#tutCarousel').carousel('next');
	}
}


tutMain();