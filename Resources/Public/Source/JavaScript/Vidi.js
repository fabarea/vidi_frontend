/** @namespace Vidi */
(function($) {
	$(function() {
		"use strict";

		/**
		 * Initialize Grid
		 */
		Vidi.grid = $('#content-list').dataTable(Vidi.Grid.getOptions());

		// Add place holder for the search
		$('.dataTables_filter input').attr('placeholder', Vidi.translate('search'));

	});
})(jQuery);
