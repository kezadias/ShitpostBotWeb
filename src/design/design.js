var minRectSize = 15;
var maxRectCount = 10;

var canvas;
var ctx;
var penId = 0;
var styles = ["#ff0000","#00ff00","#0000ff","#5C2A83"];

var isDragging;

var rectStartX;
var rectStartY;
var mouseX;
var mouseY;

var rects;

//is called when the page has finished loading
function init(){
	canvas = document.getElementById("canvas");
	ctx = canvas.getContext("2d");
	resizeCanvas();
	registerListeners();
	resetRects();
}

//reinitializes the rects variable, then redraws. used for initialization and clearing
function resetRects(){
	rects = [];
	for(var i = 0; i < styles.length; i++){
		rects[i] = [];
	}
	draw();
}

//resizes the canvas based on the image loaded
//this is a prototype, so it's hard coded for now
function resizeCanvas(){
	canvas.width  = 625;
	canvas.height = 790;
}

//draws based on the mouse info and rects variable
function draw(){
	//clear the screen
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	
	//first, the rectangle being currently drawn
	ctx.strokeStyle = styles[penId];
	if(isDragging){
		ctx.setLineDash([3,1]);
		ctx.strokeRect(rectStartX, rectStartY, mouseX - rectStartX, mouseY - rectStartY);
		ctx.setLineDash([1,0]);
	}
	
	//then all the rectangles that have been drawn already
	$('#log').text(JSON.stringify(rects, null, 4));
	for(var p = 0; p < styles.length; p++){
		ctx.strokeStyle = styles[p];
		if(rects[p] != undefined){
			for(var i = 0; i < rects[p].length; i++){
				var rect = rects[p][i];
				ctx.strokeRect(rect[0], rect[1], rect[2] - rect[0], rect[3] - rect[1]);
			}
		}
	}
}

//starts the rectangle drawing process
//called when the mouse is pressed down
function startDrawingRect(event){
	updateMouseCoords(event);
	rectStartX = mouseX;
	rectStartY = mouseY;
	draw();
}

//updates the rectangle drawing process
//called when the mouse is dragged
function updateDrawingRect(event){
	updateMouseCoords(event);
	draw();
}

//finishes the rectangle drawing process
//called when the mouse is released
function addRectangle(){
	var i = rects[penId].length;
	var x1 = Math.min(mouseX, rectStartX);
	var y1 = Math.min(mouseY, rectStartY);
	var x2 = Math.max(mouseX, rectStartX);
	var y2 = Math.max(mouseY, rectStartY);
	var width = x2 - x1;
	var height = y2 - y1;
	if(width > minRectSize && height > minRectSize && getRectCount() < maxRectCount){
		rects[penId][i] = [x1, y1, x2, y2];
	}
	draw();
}

//updates the saved mouse coordinates
function updateMouseCoords(event){
	var rect = canvas.getBoundingClientRect();
    mouseX = event.clientX - rect.left;
    mouseY = event.clientY - rect.top;
}

function getRectCount(){
	var count = 0;
	for(var p = 0; p < styles.length; p++){
		count += rects[p].length;
	}
	return count;
}

//registers all the click listeners, including the ones for the canvas
function registerListeners(){
	$('#red').click(function(){
		penId = 0;
	});
	
	$('#green').click(function(){
		penId = 1;
	});
	
	$('#blue').click(function(){
		penId = 2;
	});
	
	$('#purple').click(function(){
		penId = 3;
	});
	
	$('#clear').click(resetRects);
	
	$(canvas).mousedown(function(event) {
		event.preventDefault(); //disables the text select cursor from showing up
		isDragging = true;
		startDrawingRect(event);
	})
	.mousemove(function(event) {
		event.preventDefault();
		if (isDragging) {
			updateDrawingRect(event);
		}
	})
	.mouseup(function() {
		isDragging = false;
		addRectangle();
	});
}

$(document).ready(function(){
	init();
});