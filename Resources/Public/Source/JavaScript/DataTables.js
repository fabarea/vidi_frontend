/**
 * DataTables integration for Bootstrap 3. This requires Bootstrap 3 and
 * DataTables 1.10 or newer.
 *
 * This file overrides:
 * EXT:vidi_frontend/Resources/Public/WebComponents/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.js
 */
(function(window, document, undefined) {

	// Ony works if VidiFrontend is defined.
	if (typeof(VidiFrontend) === 'object') {

		var factory = function($, DataTable) {
			"use strict";

			/* Set the defaults for DataTables initialisation */
			$.extend(true, DataTable.defaults, {
				dom: VidiFrontend.dataTable.dom,
				renderer: 'bootstrap'
			});

		}; // /factory


		// Define as an AMD module if possible
		if (typeof define === 'function' && define.amd) {
			define(['jquery', 'datatables'], factory);
		}
		else if (typeof exports === 'object') {
			// Node/CommonJS
			factory(require('jquery'), require('datatables'));
		}
		else if (jQuery) {
			// Otherwise simply initialise as normal, stopping multiple evaluation
			factory(jQuery, jQuery.fn.dataTable);
		}
	}


})(window, document);

