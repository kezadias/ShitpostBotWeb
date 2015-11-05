var minRectSize = 15;
var maxRectCount = 10;
var maxWidth = 600;

var canvas;
var ctx;
var penId = 1;
var styles = ["#000000","#ff0000","#00ff00","#0000ff","#5C2A83"];

var isDragging;

var rectStartX;
var rectStartY;
var mouseX;
var mouseY;

var rects;

var size = {};

//is called when the page has finished loading
function init(width, height){
	var scale = maxWidth / width;
	size.x = width * scale;
	size.y = height * scale;
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
	canvas.width = size.x;
	canvas.height = size.y;
}

//draws based on the mouse info and rects variable
function draw(){
	//clear the screen
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	
	//first, the rectangle being currently drawn
	ctx.strokeStyle = styles[penId];
	if(isDragging){
		ctx.setLineDash([3,3]);
		ctx.strokeRect(rectStartX, rectStartY, mouseX - rectStartX, mouseY - rectStartY);
		ctx.setLineDash([1,0]);
	}
	
	//then all the rectangles that have been drawn already
	var fmt = format();
	$('#json').text(JSON.stringify(fmt));
	$('#log').text(JSON.stringify(fmt, null, 4));
	for(var p = 0; p < rects.length; p++){
		var rdata = rects[p];
		var rect = rdata[1];
		if(typeof rdata[2] !== 'undefined') {
			ctx.fillStyle = '#'+rdata[2];
			ctx.fillRect(rect[0], rect[1], rect[2] - rect[0], rect[3] - rect[1]);
		}
		ctx.strokeStyle = styles[rdata[0]];
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
	var x1 = Math.min(mouseX, rectStartX);
	var y1 = Math.min(mouseY, rectStartY);
	var x2 = Math.max(mouseX, rectStartX);
	var y2 = Math.max(mouseY, rectStartY);
	var width = x2 - x1;
	var height = y2 - y1;
	if (penId == 0) {
		rects = rects.filter(function(d) {
			var r = d[1];
			if (x1 <= r[0] && y1 <= r[1] && x2 >= r[2] && y2 >= r[3]) {
				return false;
			}
			return true;
		});
	} else if (width > minRectSize && height > minRectSize && getRectCount() < maxRectCount && !checkIntersection(x1, y1, x2, y2)){
		if(shouldAddFillColour()){
			if(isFillColourValid()){
				rects.push([penId, [x1, y1, x2, y2], getSelectedFillColour()]);
			}
		} else{
			rects.push([penId, [x1, y1, x2, y2]]);
		}
	}
	draw();
}

//returns true if the specified coordinates intersect with any other coordinates
function checkIntersection(x1, y1, x2, y2){
	for(var p = 0; p < rects.length; p++){
		var rect = rects[p][1];
		var xOverlap = Math.max(0, Math.min(x2, rect[2]) - Math.max(x1, rect[0]));
		var yOverlap = Math.max(0, Math.min(y2, rect[3]) - Math.max(y1, rect[1]));
		if(xOverlap * yOverlap != 0){
			return true;
		}
	}
	return false;
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

function shouldAddFillColour(){
	return $('#fillcolourchk').is(':checked');
}

function isFillColourValid(){
	var colour = getSelectedFillColour();
	return /^[0-9A-F]{6}$/i.test(colour);
}

function getSelectedFillColour(){
	return $('#fillcolour').val();
}

//registers all the click listeners, including the ones for the canvas
function registerListeners(){
	$('#red').click(function(){
		penId = 1;
	});
	
	$('#green').click(function(){
		penId = 2;
	});
	
	$('#blue').click(function(){
		penId = 3;
	});
	
	$('#purple').click(function(){
		penId = 4;
	});
	
	$('#eraser').click(function(){
		penId = 0;
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
		if(typeof cur[2] === 'undefined'){
			var	pos = [ecl[0] / size.x, ecl[1] / size.y,
					   ecl[2] / size.x, ecl[3] / size.y];
		} else{
			var	pos = [ecl[0] / size.x, ecl[1] / size.y,
					   ecl[2] / size.x, ecl[3] / size.y,
					   cur[2]];
		}
		locs.push(pos);
	}
	return out;
}