(function($) {
	$(function() {

		/**
		 * Activate the Grid and the Visual Search bar for all instances.
		 */
		if (typeof(VidiFrontend) === 'object') {
			VidiFrontend.Grid.initialize($);
			VidiFrontend.VisualSearch.initialize($);
		}

		_.each(VidiFrontend.settings, function(settings, identifier) {

			/**
			 * Select or deselect all rows at once.
			 */
			$('#grid-' + settings.id + ' .checkbox-row-top').click(function() {
				var $table = $(this).closest('table');
				var checkboxes = $($table).find('.checkbox-row');
				if ($(this).is(':checked')) {
					checkboxes.filter(':not(:checked)').click();
				} else {
					checkboxes.filter(':checked').click();
				}
			});

			if (settings.hasDetailView) {

				/**
				 * Clicking on a icon "detail" view should store the row id.
				 */
				$(document).on('click', '#grid-' + settings.id + ' tbody tr a.link-show', function(e) {
					// Store the last opened row to allow an fancy animation on link back to "list" view from "detail" view.
					var lastEditedUid = $(this).closest('tr').attr('id').replace('row-', '');
					var gridIdentifier = $(this).closest('table').attr('id').replace('grid-', '');
					VidiFrontend.Session.set('lastEditedUid' + gridIdentifier, lastEditedUid);
				});

				/**
				 * Clicking on a row will open the detail view and store the row id.
				 */
				$(document).on('click', '#grid-' + settings.id + ' tbody tr', function (e) {

					// Store the last opened row to allow an fancy animation on link back to "list" view from "detail" view.
					var lastEditedUid = this.id.replace('row-', '');
					var gridIdentifier = $(this).closest('table').attr('id').replace('grid-', '');
					VidiFrontend.Session.set('lastEditedUid' + gridIdentifier, lastEditedUid);

					// Redirect to the detail view
					var url;
					if (e.target instanceof HTMLInputElement || e.target instanceof HTMLAnchorElement) {
						return;
					}
					url = $(this).closest('tr').find('.link-show').attr('href');
					window.location.href = url;
				});
			}
		});


	});
})(jQuery);
