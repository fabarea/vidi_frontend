/**
 * Just initialize the VidiFrontend object.
 * This file must be loaded first.
 */
if (window.VidiFrontend === undefined) {
	window.VidiFrontend = {};
}

/**
 * Collection of method dealing with the Data Tables.
 *
 * @type {{initialize: Function}}
 */
VidiFrontend.Grid = {

	/**
	 * @param {object} $
	 * @return void
	 */
	initialize: function($) {

		_.each(VidiFrontend.settings, function(settings, identifier) {

			// Initialize labels and values for the search
			_.each(settings.suggestions, function(values, facetName) {

				var labels = [];
				var valueObject = {};

				_.each(values, function(value) {

					if (typeof(value) === 'object') {
						// retrieve keys.
						var keys = Object.keys(value);
						var key = keys[0];
						var label = value[key];

						// Feed array labels.
						labels.push(label);

						// Feed value object.
						valueObject[key] = label;
					} else {
						labels.push(value);
					}
				});

				settings.search.labels[facetName] = labels;
				settings.search.values[facetName] = valueObject; // store for retrieving ID of facet when searching
			});

			var options = {
				columns: settings.columns,
				language: settings.language,
				lengthMenu: settings.lengthMenu,
				stateSave: true,
				sorting: [], // default is not sorted.

				/**
				 * @param {object} settings
				 * @param {object} data
				 */
				stateSaveCallback: function(settings, data) {
					VidiFrontend.Session.set('dataTables' + identifier, JSON.stringify(data));
				},

				/**
				 * @param {object} settings
				 */
				stateLoadCallback: function(settings) {

					var state = JSON.parse(VidiFrontend.Session.get('dataTables' + identifier));

					// Set default search by overriding the session data if argument is passed.
					//if (state) {
					//
					//	// Override search if given in URL.
					//	var uri = new Uri(window.location.href);
					//	if (uri.getQueryParamValue('search')) {
					//		var search = uri.getQueryParamValue('search');
					//		state.oSearch.sSearch = search.replace(/'/g, '"');
					//	}
					//
					//	// Also stores value to be used in visual search.
					//	if (uri.getQueryParamValue('query')) {
					//		VidiFrontend.Session.set('visualSearch.query' + identifier, uri.getQueryParamValue('query'));
					//	}
					//}

					return state;
				},

				/**
				 * Override the default Ajax call of DataTable.
				 *
				 * @param {string} source
				 * @param {object} data
				 * @param {function} callback
				 * @param {object} settings
				 */
				serverData: function(source, data, callback, settings) {

					source += "&dataType=" + VidiFrontend.settings[identifier].dataType;
					source += "&identifier=" + VidiFrontend.settings[identifier].contentElementIdentifier;
					source += "&format=json";

					settings.jqXHR = $.ajax({
						'dataType': 'json',
						'type': "GET",
						'url': source,
						'data': data,
						'success': callback,
						'error': function() {
							var message = 'Oups! Something went wrong with the Ajax request... Investigate the problem in the Network Monitor. <br />';
							console.log(message);
							//Vidi.FlashMessage.add(message, 'error');
							//var fadeOut = false;
							//Vidi.FlashMessage.showAll(fadeOut);
						}
					});
				},

				/**
				 * Add Ajax parameters from plug-ins
				 *
				 * @param {object} data dataTables settings object
				 * @return void
				 */
				serverParams: function(data) {

					// Get the parameter related to filter from the URL and "re-inject" them into the Ajax request
					var uri = new Uri(window.location.href);
					for (var index = 0; index < uri.queryPairs.length; index++) {
						var queryPair = uri.queryPairs[index];
						var parameterName = queryPair[0];
						var parameterValue = queryPair[1];

					//	// Transmit filter parameter.
					//	var regularExpression = new RegExp(Vidi.module.parameterPrefix);
					//	if (regularExpression.test(parameterName)) {
					//		data.push({ 'name': decodeURI(parameterName), 'value': parameterValue });
					//	}

						// Transmit a few other parameters as well, e.g the page id if present
						var transmittedParameters = ['id', 'L'];
						for (var parameterIndex = 0; parameterIndex < transmittedParameters.length; parameterIndex++) {
							var transmittedParameter = transmittedParameters[parameterIndex];
							if (transmittedParameter === parameterName) {
								data.push({ 'name': decodeURI(parameterName), 'value': parameterValue });
							}
						}
					}

					// Transmit visible columns to the server so that id does not need to process not displayed stuff.
					var columns = $(this).dataTable().fnSettings().aoColumns;
					$.each(columns, function(index, column) {
						if (column['bVisible']) {
							data.push({name: VidiFrontend.parameterPrefix + '[columns][]', value: column['columnName'] });
						}
					});

					var settings = VidiFrontend.settings[identifier];
					data.push({ 'name': VidiFrontend.parameterPrefix + '[contentData]', 'value': settings.contentElementIdentifier });

					// Handle the search term parameter coming from the Visual Search bar.
					$.each(data, function(index, object) {
						if (object['name'] === 'sSearch') {
							object['value'] = VidiFrontend.VisualSearch.convertExpression(object['value'], settings);
							data.push({ 'name': VidiFrontend.parameterPrefix + '[searchTerm]', 'value': object['value'] });
						}
					});

					// Visual effect
					//VidiFrontend.Session.set('lastEditedUid' + identifier, 1);
					$('#grid-' + identifier).css('opacity', 0.3);
				},
				processing: settings.loadContentByAjax,
				serverSide: settings.loadContentByAjax,
				ajaxSource: settings.loadContentByAjax ? '?type=1416239670' : '',

				/**
				 *
				 */
				initComplete: function() {
					//Vidi.VisualSearch.initialize();

					var query = VidiFrontend.Session.get('visualSearch.query' + identifier);
					if (VidiFrontend.bars[identifier]) {
						VidiFrontend.bars[identifier].searchBox.setQuery(query);
					}
				},

				/**
				 * Override the default Ajax call of DataTable.
				 *
				 * @param {object} transaction
				 */
				drawCallback: function(transaction) {

					// Restore visual
					$('#grid-' + identifier).css('opacity', 1);

					// Possibly animate row
					VidiFrontend.Grid.animateRow($, identifier);
				}
			};

			options = VidiFrontend.Grid.initializeDefaultSearch(options, identifier);
			VidiFrontend.grids[identifier] = $('#grid-' + identifier).dataTable(options);
		});

	},

	/**
	 * Set a default search at the data table configuration level.
	 * This case is needed when there is no data saved in session yet.
	 *
	 * @param {array} options
	 * @param {string} identifier
	 * @return {array}
	 * @private
	 */
	initializeDefaultSearch: function(options, identifier) {

		var state = JSON.parse(VidiFrontend.Session.get('dataTables' + identifier));

		// special case if no session exists.
		if (!state) {
			// Override search if given in URL.
			var uri = new Uri(window.location.href);
			if (uri.getQueryParamValue('search')) {
				var search = uri.getQueryParamValue('search');
				options.oSearch = {
					'sSearch': search.replace(/'/g, '"')
				};
			}

			// Also stores value to be used in visual search.
			if (uri.getQueryParamValue('query')) {
				VidiFrontend.Session.set('visualSearch.query'  + identifier, uri.getQueryParamValue('query'));
			}
		}
		return options;
	},

	/**
	 * Apply effect telling the User a row was edited.
	 *
	 * @param {object} $
	 * @param {string} identifier
	 * @return void
	 * @private
	 */
	animateRow: function($, identifier) {

		// Only if User has previously edited a record.
		if (VidiFrontend.Session.has('lastEditedUid' + identifier)) {
			var uid = VidiFrontend.Session.get('lastEditedUid' + identifier);

			// Wait a little bit before applying fade-int class. Look nicer.
			setTimeout(function() {
				$('#row-' + uid).addClass('fade-in');
			}, 100);
			setTimeout(function() {
				$('#row-' + uid).addClass('fade-out').removeClass('fade-in');

				// Reset last edited uid
				VidiFrontend.Session.reset('lastEditedUid' + identifier);
			}, 500);
		}
	},

	/**
	 * @return void
	 */
	attachHandler: function($) {
		_.each(VidiFrontend.settings, function(settings, identifier) {

			/**
			 * Select or deselect all rows at once.
			 */
			$('#grid-' + settings.gridIdentifier + ' .checkbox-row-top').click(function() {
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
				$(document).on('click', '#grid-' + settings.gridIdentifier + ' tbody tr a.link-show', function(e) {
					// Store the last opened row to allow an fancy animation on link back to "list" view from "detail" view.
					var lastEditedUid = $(this).closest('tr').attr('id').replace('row-', '');
					var gridIdentifier = $(this).closest('table').attr('id').replace('grid-', '');
					VidiFrontend.Session.set('lastEditedUid' + gridIdentifier, lastEditedUid);
				});

				/**
				 * Clicking on a row will open the detail view and store the row id.
				 */
				$(document).on('click', '#grid-' + settings.gridIdentifier + ' tbody tr', function (e) {

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
	}
};