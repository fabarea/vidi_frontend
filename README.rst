Vidi for TYPO3 CMS
==================

Generic List Component for the Frontend where Content can be filtered in an advanced way... Veni, vidi, vici!

In the Plugin record, you can configure what content type to render along with a template that you can customize to your need.

Project info and releases
-------------------------

.. Stable version:
.. http://typo3.org/extensions/repository/view/vidi

Development version:
https://github.com/fudriot/vidi_frontend

::

	git clone https://github.com/fudriot/vidi_frontend.git

Flash news about latest development are also announced on
http://twitter.com/fudriot


Installation and requirement
============================

Install the extension as normal in the Extension Manager or download the Git version as follows::

	# local installation
	cd typo3conf/ext

	# download the source
	git clone https://github.com/fudriot/vidi_frontend.git

	-> next step, is to open the Extension Manager in the BE.

.. _TER: typo3.org/extensions/repository/
.. _master branch: https://github.com/TYPO3-extensions/vidi.git


You are almost there! Check out next paragraph how to load the JavaScript and CSS and then create a Content Element of type "Vidi Frontend" in General Plugin > "Generic List Component".

Load assets files (JS / CSS)
----------------------------

In order the plugin to work, it is required to load some JavaScript and CSS. It will transform the raw list of content into a smart Grid thanks to the jQuery `DataTable`_.

Having `Bootstrap`_ integration, you can use::

	# CSS
	EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.bootstrap.min.css

	# JavaScript
	EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.bootstrap.min.js

For your information there is an un-compressed JavaScript version for debug purpose, simply remove the ``min`` segment at the end of the file:


For layout not relying on Bootstrap, simply use the following. However, note this version has be less tested as of this writing::

	# CSS
	EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.min.css

	# JavaScript
	EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.min.js

.. _DataTable: http://www.datatables.net/
.. _Bootstrap: http://getbootstrap.com/


Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.


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

	# This will populate the directory Resources/Public/WebComponents.
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

	cd Resources/Public/WebComponents/visualsearch/
	grep -lr "options.app.searchBox.searchEvent(e)"

	-> it must be 2 occurrences

.. _Bower: http://bower.io/
.. _Grunt: http://gruntjs.com/
.. _Visual Search: http://documentcloud.github.io/visualsearch/