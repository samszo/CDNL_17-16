var moveSlider = function(slider, direction) {
	var value = slider.value;
	var svg = document.getElementById("circle-svg"); 
	var svgDoc = svg.contentDocument;
	var circle = svgDoc.getElementById("my-circle");
	circle.setAttributeNS(null, "c" + direction, value * 5);
}