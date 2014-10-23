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

Once the extension activated, **load the static TypoScript template** in your root TS template.

.. _TER: typo3.org/extensions/repository/
.. _master branch: https://github.com/TYPO3-extensions/vidi.git


Load assets files (JS / CSS)
----------------------------

In order the plugin to work, it is required to load some JavaScript and CSS. It will transform the raw list of content into a smart Grid thanks to the jQuery `DataTable`_.

Having `Bootstrap`_ integration, you can use::

	# JavaScript
	EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.bootstrap.min.js

	# CSS
	EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.bootstrap.min.css

For your information there is an un-compressed JavaScript version for debug purpose, simply remove the ``min`` segment at the end of the file:


For layout not relying on Bootstrap, simply use the following. However, note this version has be less tested as of this writing::


	# JavaScript
	EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.min.js

	# CSS
	EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.min.css


.. _DataTable: http://www.datatables.net/
.. _Bootstrap: http://getbootstrap.com/



Configuration
=============

Configuration is provided mainly by TypoScript or in the plugin record itself.

Register a new template
-----------------------

The detail view of the content can be personalized per plugin record. To register more templates, simply define them in your TS::
This TS part is to be put under ``plugin.tx_vidifrontend.settings``::

	templates {

		# Default TS
		1 {
			title = Default detail view
			path = EXT:vidi_frontend/Resources/Private/Templates/Content/Show.html
		}

		# Add your own detail view
		2 {
			title = My title
			path = EXT:your_ext/../Show.html
		}
	}
