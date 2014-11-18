/**
 * Collection of method dealing with the Visual Search.
 *
 * @type {{retrieveFacetName: Function, suggest: Function, convertExpression: Function, retrieveValue: Function}}
 */
VidiFrontend.VisualSearch = {

	/**visualSearch.query
	 * @param {object} $
	 * @return void
	 */
	initialize: function($) {
		_.each(VidiFrontend.settings, function(settings, identifier) {

			/**
			 * Init Visual Search bar and store the instance.
			 */
			VidiFrontend.bars[identifier] = VS.init({
				container: $('.visual-search-container-' + identifier),
				query: '',
				callbacks: {
					search: function(query, searchCollection) {

						var jsonQuery = JSON.stringify(searchCollection.facets());

						// Store in session the visual search query.
						VidiFrontend.Session.set('visualSearch.query' + identifier, query);

						// Inject value in data table search and trigger a refresh.
						$('input[aria-controls=grid-' + identifier + ']').val(jsonQuery).keyup().keyup(); // Weird... we must call twice keyup so that it works.
						//console.log(jsonQuery);
						//console.log(VidiFrontend.visualSearch.searchBox.value());
					},
					facetMatches: function(callback) {
						var facets = [];

						var settings = VidiFrontend.settings[identifier];
						_.each(settings.facets, function(label) {
							facets.push(label);
						});
						callback(facets);
					},
					valueMatches: function(facetLabel, searchTerm, callback) {

						// "text" is a special facet and must never suggest values.
						if (facetLabel === 'text') {
							return;
						}

						var settings = VidiFrontend.settings[identifier];

						// Retrieve the facet name and suggest values to the User.
						var facetName = VidiFrontend.VisualSearch.retrieveFacetName(facetLabel, settings);
						if (facetName) {
							VidiFrontend.VisualSearch.suggest(facetName, searchTerm, callback, settings);
						}
					}
				}
			});
		});
	},

	/**
	 * Retrieve a facet name according to a label.
	 *
	 * @param {string} facetLabel
	 * @param {object} settings
	 * @return string
	 */
	retrieveFacetName: function(facetLabel, settings) {

		// If no facet name is found for a label (e.g for "text"), returns the facet label as such.
		// The server will know how to handle that.
		var facetName = facetLabel;

		_.each(settings.facets, function(label, _facetName) {
			if (label === facetLabel) {
				facetName = _facetName;
			}
		});

		return facetName;
	},

	/**
	 * Suggest values to the User.
	 * Fetch the values from the value storage if possible, otherwise query the server.
	 *
	 * @param {string} facetName
	 * @param {string} searchTerm
	 * @param {function} callback
	 * @param {array} settings
	 * @return void
	 * @private
	 */
	suggest: function(facetName, searchTerm, callback, settings) {

		if (settings.suggestions[facetName] === undefined) {

			// BEWARE! This code is never used as implemented but should be in the future.
			// @todo suggestions[facetName] must be destroyed in some cases after inline editing.

			// Fetch the suggestion values for the facet.
			//$.ajax({
			//	url: $('#link-facet-suggest').attr('href'),
			//	dataType: "json",
			//	data: Vidi.VisualSearch.getParameters(facetName, searchTerm),
			//	success: function(data) {
			//		Vidi.module.grid.suggestions[facetName] = data;
			//		callback(Vidi.module.grid.suggestions[facetName])
			//	},
			//	error: function() {
			//		Vidi.VisualSearch.showError();
			//	}
			//});
		} else {
			callback(settings.suggestions[facetName]);
		}
	},

	/**
	 * Convert the Visual Search expression containing labels to values
	 * to be understand by the server such as field name and numerical value.
	 *
	 * @param {string} searchExpression
	 * @param {object} settings
	 * @return string
	 */
	convertExpression: function(searchExpression, settings) {

		var convertedExpression = [];
		console.log(searchExpression);
		if (searchExpression) {

			// In case the search expression has been fetched from the URL.
			searchExpression = decodeURIComponent(searchExpression);

			var facets = JSON.parse(searchExpression);
			_.each(facets, function(facet) {

				_.each(facet, function(searchTerm, facetLabel) {
					var facetName = VidiFrontend.VisualSearch.retrieveFacetName(facetLabel, settings);
					var value = VidiFrontend.VisualSearch.retrieveValue(facetName, searchTerm, settings);

					var convertedFacets = {};
					convertedFacets[facetName] = value;
					convertedExpression.push(convertedFacets);
				});
			});
		}

		return JSON.stringify(convertedExpression);
	},

	/**
	 * Retrieve the real value of a search term.
	 * If a corresponding value is not found, simply returns the search term.
	 *
	 * @param {string} facetName
	 * @param {string} searchTerm
	 * @param {object} settings
	 * @return string
	 */
	retrieveValue: function(facetName, searchTerm, settings) {

		var value = searchTerm;

		// Search for an equivalence label <-> value.
		_.each(settings.suggestions[facetName], function(label, _value) {
			if (label === searchTerm) {
				value = _value;
			}
		});

		return value;
	}
};
