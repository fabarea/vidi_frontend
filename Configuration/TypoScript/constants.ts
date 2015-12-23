plugin.tx_vidifrontend {
	view {
		# cat=plugin.tx_vidifrontend/file; type=string; label=Path to template root (FE)
		templateRootPath = EXT:vidi_frontend/Resources/Private/Templates/
		# cat=plugin.tx_vidifrontend/file; type=string; label=Path to template partials (FE)
		partialRootPath = EXT:vidi_frontend/Resources/Private/Partials/
		# cat=plugin.tx_vidifrontend/file; type=string; label=Path to template layouts (FE)
		layoutRootPath = EXT:vidi_frontend/Resources/Private/Layouts/
	}
	persistence {
		# cat=plugin.tx_vidifrontend//a; type=string; label=Default storage PID
		# storagePid =
	}
	settings {
	}
}
