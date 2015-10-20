Vidi for TYPO3 CMS
==================

Generic List Component where Content can be filtered in an advanced way... Veni, vidi, vici! This extension is based on Vidi with
the aim to provide the same feature set but for the Frontend.

In the plugin record in the Backend, it can be configured what content type to render associated with a customizable template.

SCREENSHOT!

For the Grid, we use the excellent `Data Tables`_ which is a powerful jQuery plugin smart and fast to sort and filter data.
The Filter bar is provided by the `Visual Search`_ jQuery plugin which offers nice facet capabilities and intuitive search.


Project info and releases
-------------------------

.. Stable version:
.. http://typo3.org/extensions/repository/view/vidi

Development version:
https://github.com/fabarea/vidi_frontend

::

	git clone https://github.com/fabarea/vidi_frontend.git

Flash news about latest development are also announced on
http://twitter.com/fudriot


Installation and requirement
============================


The extension **requires TYPO3 6.2**. Install the extension as normal in the Extension Manager from the `TER`_ or download the Git version::

	# local installation
	cd typo3conf/ext

	# download the source
	git clone https://github.com/fabarea/vidi_frontend.git

	-> next step, is to open the Extension Manager in the BE.

.. _TER: typo3.org/extensions/repository/
.. _master branch: https://github.com/TYPO3-extensions/vidi.git

You are almost there! Load the static TS file

SCREENHSOT!!

Finally create a Content Element of type "Vidi Frontend" in General Plugin > "Generic List Component".

Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.

Please make sure jQuery is well loaded.

Configure the Assets::


	#############################
	# plugin.tx_vidifrontend
	#############################
	plugin.tx_vidifrontend {

		settings {

			asset {

				vidiCss {
					# For none Bootstrap replace "vidi_frontend.bootstrap.min.css" by "vidi_frontend.min.css"
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

Register a new Content type
---------------------------

In order to have a new Content Type (such as fe_users, ...), some configuration must be added in the TCA.
Best is to learn by example and look at the example provided within file ``EXT:vidi_frontend/Configuration/TCA/fe_users.php``.

Basically, what you have to do is to create a file into your extension, **if not yet existing**::

	touch EXT:foo/Configuration/TCA/tx_domain_model_foo.php


And copy & paste and adjust the dummy example. It will declare a new Grid for the Frontend::

	$tca = array(
		'grid_frontend' => array(
			'columns' => array(

				# The field "title" of your table.
				'title' => array(),

				... <-- add your fields

				# System column where to contain some
				'__buttons' => array(
					'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
					'sortable' => FALSE
				),
			),
		),
	);


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

	cd typo3conf/ext/vidi_upload

	# This will populate the directory Resources/Public/BowerComponents.
	bower install

	# Install the necessary NodeJS package.
	npm install


Then, you can run the Grunt of the extension to generate a build::

	cd typo3conf/ext/vidi_upload
	grunt build

While developing, you can use the ``watch`` which will generate the build as you edit files::

	grunt watch


Patch VisualSearch
------------------

To improve the User experience, `Visual Search`_ plugin has been patched avoiding the drop down menu to appear inopportunely.
It means when making a fresh build, the patch must be (for now) manually added::

	cd Resources/Public/BowerComponents/visualsearch/
	grep -lr "options.app.searchBox.searchEvent(e)" .

	-> it must be 2 occurrences

.. _Bower: http://bower.io/
.. _Grunt: http://gruntjs.com/
.. _Visual Search: http://documentcloud.github.io/visualsearch/
.. _DataTable: http://www.datatables.net/
