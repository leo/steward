window.onload = function() {

	var key_field = document.querySelector('.steward-key');

	key_field.onfocus = function() {

    	var el = this;
		
    	requestAnimationFrame(function() {
	    	var range = document.createRange();
	    	range.selectNodeContents(el);
	    	var sel = window.getSelection();
	    	sel.removeAllRanges();
	    	sel.addRange(range);
    	});

	};

	key_field.onkeydown = function(e) {
		
		if(e.metaKey !== true) {
			return false;
		}
		
	};

}