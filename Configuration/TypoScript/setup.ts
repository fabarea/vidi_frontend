#############################
# plugin.tx_vidifrontend
#############################
plugin.tx_vidifrontend {
	view {
		templateRootPath = {$plugin.tx_vidifrontend.view.templateRootPath}
		partialRootPath = {$plugin.tx_vidifrontend.view.partialRootPath}
		layoutRootPath = {$plugin.tx_vidifrontend.view.layoutRootPath}
	}

	settings {

		grid {
			className = {$plugin.tx_vidifrontend.settings.grid.className}
		}

		templates {
			1 {
				title = Default detail view
				path = EXT:vidi_frontend/Resources/Private/Templates/Content/Show.html
			}
			2 {
				title = User detail view
				path = EXT:vidi_frontend/Resources/Private/Templates/Content/ShowUser.html
			}
		}

		# Fluid variable mappings to be used in the detail view of your Fluid template.
		fluidVariables {
			fe_users = user
		}
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
    10 = COA_INT
    10 {
        10 = USER_INT
        10 {
            userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
            extensionName = VidiFrontend
            pluginName = Pi1
			switchableControllerActions {
				Content {
					1 = list
				}
			}
        }
    }
}