(function($) {
	$(function() {

		/**
		 * Activate the Grid and the Visual Search bar for all instances.
		 */
		if (typeof(VidiFrontend) === 'object') {
			VidiFrontend.Grid.initialize($);
			VidiFrontend.VisualSearch.initialize($);
		}

		/**
		 * Clicking on a row will open the detail view.
		 */
		$('table.dataTable').find('tbody').find('tr').on('click', function(e) {
			var url;
			if (e.target instanceof HTMLInputElement || e.target instanceof HTMLAnchorElement) {
				return;
			}
			url = $(this).closest('tr').find('.link-show').attr('href');
			window.location.href = url;
		});

	});
})(jQuery);
