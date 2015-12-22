Vidi for TYPO3 CMS
==================

Generic List Component where Content can be filtered in an advanced way... Veni, vidi, vici! This extension is based on `Vidi`_ which provides more or
less the same feature set but in the Backend.

.. image:: https://raw.github.com/Ecodev/vidi_frontend/master/Documentation/Frontend-01.png

Once installed, it can be configured what content type to render associated with a customizable template in the plugin record in the Backend.
On the Frontend side, we use the excellent `DataTables`_ which is a powerful jQuery plugin smart and fast to sort and filter data.
The Filter bar is provided by the `Visual Search`_ jQuery plugin which offers nice facet capabilities and intuitive search.

Live example http://www.washinhcf.org/resources/publications/

.. _Vidi:: https://github.com/fabarea/vidi

Project info and releases
-------------------------

Stable version:
http://typo3.org/extensions/repository/view/vidi_frontend

Development version:
https://github.com/Ecodev/vidi_frontend

::

	git clone https://github.com/Ecodev/vidi_frontend.git

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 6.2 or greater** . Install the extension as normal in the Extension Manager from the `TER`_ or download the Git version::

	# local installation
	cd typo3conf/ext

	# download the source
	git clone https://github.com/Ecodev/vidi_frontend.git

	-> next step, is to open the Extension Manager in the BE.

.. _TER: http://typo3.org/extensions/repository/view/vidi_frontend

You are almost there! Create a Content Element of type "Vidi Frontend" in `General Plugin` > `Generic List Component` and configure at your convenience.

.. image:: https://raw.github.com/Ecodev/vidi_frontend/master/Documentation/Backend-01.png

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

Custom Grid Renderer
--------------------

Assumming we want a complete customized output for a column, we can can achieve this by implementing a Grid Render.
Here is an exemple for table `fe_users`.
We first have to register the new column in the TCA in some `Configuration/TCA/Override/fe_users.php`.

::

	$tca = [
		'grid_frontend' => [
			'columns' => [

				# The key is totally free here. However we prefix with "__" to distinguish between a "regular" column associated to a field.
				'__my_custom_column' => [
					'renderers' => array(
						'Vendor\MyExt\Grid\MyColumnRenderer',
					),
					'sorting' => FALSE,
					'sortable' => FALSE,
					'label' => '',
				],
			],
		],
	];

	\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);

The corresponding class to be placed in `EXT:MyExt/Classes/Grid/MyColumnRenderer`::

	namespace Vendor\MyExt\Grid;

	/**
	 * Class to render a custom output.
	 */
	class MyColumnRenderer extends Fab\Vidi\Grid\ColumnRendererAbstract {

		/**
		 * Render a publication.
		 *
		 * @return string
		 */
		public function render() {
			return $output;
	}


Adjust column configuration
---------------------------

Configuration of the columns is taken from the TCA. Sometimes we need to adjust its configuration for the Frontend and we can simply enriches it.
Best is to learn by example and get inspired by ``EXT:vidi_frontend/Configuration/TCA/fe_users.php``::

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

::

	$tca = [
		'grid_frontend' => [
			'facets' => [
				new \Vendor\MyExt\Facets\MyCustomFacet(),
			],
		],
	];

	\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);

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
