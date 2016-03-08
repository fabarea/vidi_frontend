(function($) {
	$(function() {

		/**
		 * Activate the Grid and the Visual Search bar for all instances.
		 */
		if (typeof(VidiFrontend) === 'object') {
			VidiFrontend.Grid.setJQuery($);
			VidiFrontend.Grid.initialize();
			VidiFrontend.VisualSearch.initialize($);
			VidiFrontend.Grid.attachHandler();
		}

	});
})(jQuery);
