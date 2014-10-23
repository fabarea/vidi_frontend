<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

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

use Fab\VidiFrontend\Tca\FrontendTcaService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Return a possible column header.
 */
class HeaderViewHelper extends AbstractViewHelper {

	/**
	 * Returns a column header.
	 *
	 * @param string $name the column Name
	 * @return boolean
	 */
	public function render($name) {
		$dataType = $this->templateVariableContainer->get('dataType');
		return FrontendTcaService::grid($dataType)->getHeader($name);
	}

}
