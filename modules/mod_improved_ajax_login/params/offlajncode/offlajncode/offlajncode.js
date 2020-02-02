dojo.declare("OfflajnCode", null, {

	constructor: function(args) {
		dojo.mixin(this,args);
		this.input = dojo.byId(this.id);
		if ('selectionStart' in this.input && 'execCommand' in document)
			dojo.connect(this.input, 'onkeydown', this, 'onKeyDown');
	},

	onKeyDown: function(e) {
		if (!canEditInput) return;
		var keyCode = e.keyCode || e.which;

		if (keyCode === 9) { // TAB
			e.preventDefault();
			var start = this.input.selectionStart;
			var end = this.input.selectionEnd;
			var selected = this.input.value.substring(start, end);
			var re = e.shiftKey ? (/^\t/gm) : (/^/gm);
			var replacement = e.shiftKey ? '' : '\t';
			var match = selected.match(re);
			if (match) {
				var count = match.length;
				count *= e.shiftKey ? -1 : 1;
				document.execCommand('inserttext', false, selected.replace(re, replacement));
				this.input.selectionStart = start < end ? start : end + count;
				this.input.selectionEnd = end + count;
			}
		}
		else if (keyCode === 13) { // ENTER
			var start = this.input.selectionStart;
			var before = this.input.value.substr(0, start).split(/^/m);
			before = before[before.length - 1];
			if (before) {
				e.preventDefault();
				var tabs = before.match(/^\s*/)[0];
				if (before.match(/\{\s*$/)) tabs += '\t';
				document.execCommand('inserttext', false, '\n' + tabs);
			}
		}
	}

});

dojo.ready(function() {
	window.canEditInput = (function() {
		try {
			var t = document.createElement('textarea');
			document.body.appendChild(t);
			t.focus();
			document.execCommand('insertText', false, 'x');
			document.body.removeChild(t);
			return t.value === 'x';
		} catch (e) {
			return false;
		}
	})();
});