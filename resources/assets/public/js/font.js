// FontFaceObserver
// https://github.com/bramstein/fontfaceobserver
import FontFaceObserver from 'fontfaceobserver';
window.FontFaceObserver = FontFaceObserver;

var font = new FontFaceObserver('Inter');

(function() {

	// 5s timeout
	// If exceeded, FOUT is the least of my problems
	font.load(null, 5000).then(function () {
		document.querySelector("body").classList.add('isLoaded');
	}, function () {
		// Apply the class even without all fonts loaded
		// to avoid problems with really slow connections
		document.querySelector("body").classList.add('isLoaded');
	});

})();