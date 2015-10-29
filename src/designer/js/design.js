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

var size = { x: 625, y: 790}; //placeholder

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
	draw();
}

//resizes the canvas based on the image loaded
//this is a prototype, so it's hard coded for now
function resizeCanvas(){
	canvas.width  = size.x;
	canvas.height = size.y;
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
	$('#log').text(JSON.stringify(format(), null, 4));
	for(var p = 0; p < rects.length; p++){
		var rdata = rects[p];
		ctx.strokeStyle = styles[rdata[0]];
		var rect = rdata[1];
		ctx.strokeRect(rect[0], rect[1], rect[2] - rect[0], rect[3] - rect[1]);
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
	//var i = rects[penId].length;
	var x1 = Math.min(mouseX, rectStartX);
	var y1 = Math.min(mouseY, rectStartY);
	var x2 = Math.max(mouseX, rectStartX);
	var y2 = Math.max(mouseY, rectStartY);
	var width = x2 - x1;
	var height = y2 - y1;
	if(width > minRectSize && height > minRectSize && getRectCount() < maxRectCount){
		rects.push([penId, [x1, y1, x2, y2]]);
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
	return rects.length;
}

function undo() {
	if (rects.length) {
		rects.pop();
		draw();
	}	
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
	
	$('#undo').click(undo);
	
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

function format() {
	var all = rects.slice().sort(function(d, f){ return d[0] - f[0]});
	var	lst = null;
	var	out = [];
	for (var i = 0; i < all.length; i++) {
		var cur = all[i];
		var	pen = cur[0];
		if (lst != pen) {
			var locs = [];
			out.push(locs);
			lst = pen;
		}
		var ecl = cur[1];
		var	pos = [ecl[0] / size.x, ecl[1] / size.y,
				   ecl[2] / size.x, ecl[3] / size.y];
		locs.push(pos);
	}
	return out;
}

$(document).ready(function(){
	init();
});