(function($) {
	$(function() {


		/**
		 * Activate DataTables for all instances.
		 * Ony works if VidiFrontend is defined.
		 */
		if (typeof(VidiFrontend) === 'object') {

			for (var i = 0; i < VidiFrontend.instances.length; i++) {
				var instance = VidiFrontend.instances[i];
				var options = {
					columns: instance.columns
				};
				$('#' + instance.id).dataTable(options);
			}

			$('table.dataTable').find('tbody').find('tr').on('click', function(e) {
				var url;
				if (e.target instanceof HTMLInputElement || e.target instanceof HTMLAnchorElement){
					return;
				}
				url = $(this).closest('tr').find('.link-show').attr('href');
				window.location.href = url;
			});
		}
	});
})(jQuery);
