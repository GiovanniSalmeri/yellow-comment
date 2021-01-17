"use strict";
// https://stackoverflow.com/questions/454202/creating-a-textarea-with-auto-resize
window.addEventListener("load", function() {
	var tx = document.getElementsByTagName('textarea');
	for (var i = 0; i < tx.length; i++) {
		tx[i].setAttribute('style', 'height:' + tx[i].scrollHeight + 'px;overflow-y:hidden;resize:none');
		tx[i].addEventListener("input", OnInput);
	}
	function OnInput() {
		this.style.height = 'auto';
		this.style.height = this.scrollHeight + 'px';
		if (this.nextSibling.className == 'comment-charcount') {
			this.nextSibling.firstChild.data = this.value.length + ' / ' + this.maxLength;
		}
	}
});
