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
