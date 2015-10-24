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

function init(){
	canvas = document.getElementById("canvas");
	ctx = canvas.getContext("2d");
	resizeCanvas();
	registerListeners();
	resetRects();
}

function resetRects(){
	rects = [];
	for(var i = 0; i < styles.length; i++){
		rects[i] = [];
	}
	draw();
}

function resizeCanvas(){
	canvas.width  = 625;
	canvas.height = 790;
}

function draw(){
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	setStyleFromPenColour();
	if(isDragging){
		ctx.setLineDash([3,1]);
		ctx.strokeRect(rectStartX, rectStartY, mouseX - rectStartX, mouseY - rectStartY);
		ctx.setLineDash([1,0]);
	}
	
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

function setStyleFromPenColour(){
	ctx.strokeStyle = styles[penId];
}

function startDrawingRect(event){
	updateMouseCoords(event);
	rectStartX = mouseX;
	rectStartY = mouseY;
	draw();
}

function updateDrawingRect(event){
	updateMouseCoords(event);
	draw();
}

function addRectangle(){
	var i = rects[penId].length;
	rects[penId][i] = [Math.min(mouseX, rectStartX), Math.min(mouseY, rectStartY), Math.max(mouseX, rectStartX), Math.max(mouseY, rectStartY)];
	draw();
}

function updateMouseCoords(event){
	var rect = canvas.getBoundingClientRect();
    mouseX = event.clientX - rect.left;
    mouseY = event.clientY - rect.top;
}

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
		event.preventDefault();
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