<?php
namespace Fab\VidiFrontend\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to load a JavaScript file
 *
 * = Examples =
 *
 * <code title="Default">
 * <p:script file="{f:uri.resource(path:'JavaScript/Libraries/Select2/select2.min.js')}"/>
 * </code>
 * <output>
 * Loads the given file and adds it to the Frontend
 * </output>
 */
class ScriptViewHelper extends AbstractViewHelper {

	/**
	 * @param string $file JavaScript file to load in the backend module
	 */
	public function render($file) {
		$this->getPageRenderer()->addJsFooterFile($file);
	}

	/**
	 * @return \TYPO3\CMS\Core\Page\PageRenderer
	 */
	protected function getPageRenderer() {
		return $this->getFrontendObject()->getPageRenderer();
	}

	/**
	 * Returns an instance of the Frontend object.
	 *
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getFrontendObject() {
		return $GLOBALS['TSFE'];
	}

}
