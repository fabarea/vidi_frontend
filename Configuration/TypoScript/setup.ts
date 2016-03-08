########################
# plugin.tx_vidifrontend
########################
plugin.tx_vidifrontend {
	view {
		templateRootPath = {$plugin.tx_vidifrontend.view.templateRootPath}
		#partialRootPath = {$plugin.tx_vidifrontend.view.partialRootPath}
		layoutRootPath = {$plugin.tx_vidifrontend.view.layoutRootPath}
	}

	settings {

		templates {
			1 {
				title = Default detail view
				path = EXT:vidi_frontend/Resources/Private/Templates/Content/Show.html
			}
			2 {
				# Restrict visibility of this template for "fe_users" only.
				dataType = fe_users
				title = User detail view
				path = EXT:vidi_frontend/Resources/Private/Templates/Content/ShowUser.html
			}
		}

		# Fluid variable mappings to be used in the detail view of your Fluid template.
		fluidVariables {
			fe_users = user
		}

		asset {

			vidiCss {
				# For none Bootstrap replace by EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.min.css
				path = EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.bootstrap.min.css
				type = css

				# Optional key if loading assets through EXT:vhs.
				dependencies = mainCss
			}

			vidiJs {
				# For none Bootstrap replace by EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.min.js
				path = EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.bootstrap.min.js
				type = js

				# Optional key if loading assets through EXT:vhs.
				dependencies = mainJs
			}
		}

		loadAssetWithVhsIfAvailable = 1
	}
}

page_1416239670 = PAGE
page_1416239670 {
    typeNum = 1416239670
    config {
        xhtml_cleaning = 0
        admPanel = 0
        disableAllHeaderCode = 1
        disablePrefixComment = 1
        debug = 0
        additionalHeaders = Content-type:application/json
    }
	10 = USER_INT
	10 {
		userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
		extensionName = VidiFrontend
		pluginName = Pi1
		vendorName = Fab
		switchableControllerActions {
			Content {
				1 = list
			}
		}
	}
}

page_1457381088 = PAGE
page_1457381088 {
	typeNum = 1457381088
	config {
		xhtml_cleaning = 0
		admPanel = 0
		disableAllHeaderCode = 1
		disablePrefixComment = 1
		debug = 0
		#additionalHeaders = Content-type:application/json
	}
	10 = USER_INT
	10 {
		userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
		extensionName = VidiFrontend
		pluginName = Pi1
		vendorName = Fab
		switchableControllerActions {
			Content {
				1 = execute
				2 = warn
			}
		}
	}
}