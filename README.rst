Vidi for TYPO3 CMS
==================

Generic List Component where Content can be filtered in an advanced way... Veni, vidi, vici! This extension is based on Vidi with
the aim to provide the same feature set but for the Frontend.

.. image:: https://raw.github.com/fabarea/vidi_frontend/master/Documentation/Frontend-01.png

In the plugin record in the Backend, it can be configured what content type to render associated with a customizable template.

For the Grid, we use the excellent `DataTables`_ which is a powerful jQuery plugin smart and fast to sort and filter data.
The Filter bar is provided by the `Visual Search`_ jQuery plugin which offers nice facet capabilities and intuitive search.

Live example http://www.washinhcf.org/resources/publications/

Project info and releases
-------------------------

Stable version:
http://typo3.org/extensions/repository/view/vidi_frontend

Development version:
https://github.com/fabarea/vidi_frontend

::

	git clone https://github.com/fabarea/vidi_frontend.git

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 6.2**. Install the extension as normal in the Extension Manager from the `TER`_ or download the Git version::

	# local installation
	cd typo3conf/ext

	# download the source
	git clone https://github.com/fabarea/vidi_frontend.git

	-> next step, is to open the Extension Manager in the BE.

.. _TER: http://typo3.org/extensions/repository/view/vidi_frontend

You are almost there! Create a Content Element of type "Vidi Frontend" in General Plugin > "Generic List Component" and configure at your convenience.

.. image:: https://raw.github.com/fabarea/vidi_frontend/master/Documentation/Backend-01.png

Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.

**Important** by default, the CSS + JS files are loaded for Bootstrap. For a more Vanilla flavor, edit the `path` in the `settings` key in TypoScript and
load the right assets for you. See below the comments::

	#############################
	# plugin.tx_vidifrontend
	#############################
	plugin.tx_vidifrontend {

		settings {

			asset {

				vidiCss {
					# For none Bootstrap replace "vidi_frontend.bootstrap.min.css" by "vidi_frontend.min.css"
					path = EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.bootstrap.min.css
					type = css
				}

				vidiJs {
					# For none Bootstrap replace "vidi_frontend.bootstrap.min.js" by "vidi_frontend.min.js"
					path = EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.bootstrap.min.js
					type = js
				}
			}
		}
	}

Custom columns
--------------

In order to customize columns for the Frontend, configuration can be added in the TCA. Best is to learn by example and get inspired by
``EXT:vidi_frontend/Configuration/TCA/fe_users.php``::

	$tca = array(
		'grid_frontend' => array(
			'columns' => array(

				# Custom fields for the FE goes here
				'title' => array(),
			),
		),
	);


Custom Facets
-------------

Facets are visible in the Visual Search and enable the search by criteria. Facets are generally mapped to a field but it is not mandatory ; it can be arbitrary values. To provide a custom Facet, the interface `\Fab\Vidi\Facet\FacetInterface` must be implemented. Best is to take inspiration of the `\Fab\Vidi\Facet\StandardFacet` and provide your own implementation.

Register a new template
-----------------------

The detail view of the content can be personalized per plugin record. To register more templates, simply define them in your TypoScript configuration
This TypoScript will typically be put under within ``EXT:foo/Configuration/TypoScript/setup.txt``::

	plugin.tx_vidifrontend {
		settings {
			templates {

				# Key "1", "2" is already taken by this extension.
				# Use key "10", "11" and following for your own templates to be safe.
				10 {
					title = Foo detail view
					path = EXT:foo/Resources/Private/Templates/VidiFrontend/ShowFoo.html
					dataType = fe_users
				}
			}
		}
	}


Building assets in development
==============================

The extension provides JS / CSS bundles which included all the necessary code. If you need to make a new build for those JS / CSS files,
consider that `Bower`_ and `Grunt`_ must be installed on your system as prerequisite.

Install the required Web Components::

	cd typo3conf/ext/vidi_frontend

	# This will populate the directory Resources/Public/BowerComponents.
	bower install

	# Install the necessary NodeJS package.
	npm install


Then, you can run the Grunt of the extension to generate a build::

	cd typo3conf/ext/vidi_frontend
	grunt build

While developing, you can use the ``watch`` which will generate the build as you edit files::

	grunt watch


Patch VisualSearch
------------------

To improve the User experience, `Visual Search`_ plugin has been patched avoiding the drop down menu to appear inopportunely.
It means when making a fresh build, the patch must be (for now) manually added::

	cd Resources/Public/BowerComponents/visualsearch/
	grep -lr "app.searchBox.searchEvent(e)" .

	-> There should be 2 occurrences. Comment lines below related to "_.defer".

.. _Bower: http://bower.io/
.. _Grunt: http://gruntjs.com/
.. _Visual Search: http://documentcloud.github.io/visualsearch/
.. _DataTables: http://www.datatables.net/
