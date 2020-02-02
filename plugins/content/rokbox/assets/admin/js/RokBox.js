(function(){

	var RokBox = this.RokBox = {

		init: function() {
			RokBox.fixOverflow();
		},

		fixOverflow: function(){
			var hook = document.getElement('.rokbox-break'),
				slider;

			if (hook) slider = hook.getParent('.pane-slider');
			if (slider) slider.setStyle('overflow', 'visible');
		}

	};



	window.addEvent('load', RokBox.init);

})();
