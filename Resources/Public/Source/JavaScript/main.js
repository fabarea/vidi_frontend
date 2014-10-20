(function($) {
	$(function() {

		/**
		 * Activate DataTables for all instances
		 */
		for (var i = 0; i < VidiFrontend.instances.length; i++) {
			var instance = VidiFrontend.instances[i];
			$('#' + instance.id).dataTable();
		}

	});
})(jQuery);
